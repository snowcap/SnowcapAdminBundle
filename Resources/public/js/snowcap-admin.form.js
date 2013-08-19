SnowcapAdmin.Form = (function($) {
    /**
     * Form Collection view
     * Used to manage Symfony form collection type
     *
     */
    var Collection = SnowcapCore.Form.Collection.extend({
        events: {
            'click .add-element': 'addItem', // Legacy
            'click *[data-admin=form-collection-add]': 'addItem',
            'click .remove-element': 'removeItem', // Legacy
            'click [data-admin=form-collection-remove]': 'removeItem'
        },
        /**
         * Initialize
         *
         */
        initialize: function() {
            SnowcapCore.Form.Collection.prototype.initialize.apply(this);

            this.on('form:collection:add', textAutocompleteFactory);
            this.on('form:collection:add', autocompleteFactory);
        },
        /**
         * Remove a collection item
         *
         * @param event
         */
        removeItem: function(event) {
            event.preventDefault();
            var
                $target = $(event.currentTarget),
                $collectionItem;

            $collectionItem = $target.parents('[data-admin=form-collection-item]');
            if(0 === $collectionItem.length) {
                $collectionItem = $target.parent();
            }

            if(this.options.confirmDelete) {
                var modal = new SnowcapBootstrap.Modal({'url': this.options.confirmDeleteUrl});
                modal.on('modal:confirm', _.bind(function() {
                    this.fadeAndRemoveItem($collectionItem);
                }, this));
            }
            else {
                this.fadeAndRemoveItem($collectionItem);
            }

            this.trigger('form:collection:remove');
            this.$form.trigger('change');
        },
        /**
         * Fade and remove a collection item
         *
         * @param $item
         */
        fadeAndRemoveItem: function($item) {
            $item.fadeOut(function() {
                $item.remove();
            });
        }
    });

    /**
     * Form collection factory function
     *
     * @param $context
     */
    var collectionFactory = function() {
        var $context = (0 === arguments.length) ? $('body') : arguments[0];
        $context.find('[data-admin=form-collection]').each(function(offset, container) {
            var options = {
                el: $(container),
                confirmDelete: $(container).data('options-confirm-delete-url') ? true : false,
                confirmDeleteUrl: $(container).data('options-confirm-delete-url') ? $(container).data('options-confirm-delete-url') : null
            };

            new SnowcapAdmin.Form.Collection(options);
        });
    };

    /**
     * Form Autocomplete view
     * Used to handle snowcap_admin_autocomplete form type
     *
     */
    var TextAutocomplete = Backbone.View.extend({
        $textInput: null,
        listUrl: null,
        labels: [],
        /**
         * Initialize
         *
         */
        initialize: function () {
            this.$el.css('position', 'relative');
            this.$textInput = this.$el.find('input[type=text]');
            this.listUrl = this.$el.data('options-url');

            this.initializeTypeahead();
        },
        /**
         * Initialize typeahead widget
         *
         */
        initializeTypeahead: function() {
            // Initialize typeahead
            this.$textInput.typeahead({
                source: _.bind(this.source, this),
                minLength: 3,
                items: 10
            });
        },
        /**
         * Bootstrap typeahead source implementation
         *
         * @param query
         * @param process
         */
        source: function(query, process) {
            var replacedUrl = this.listUrl.replace('__query__', query);
            $.getJSON(replacedUrl, _.bind(function(data) {
                this.labels = [];
                $.each(data.result, _.bind(function (i, item) {
                    this.labels.push(item);
                }, this));
                process(this.labels);
            }, this));
        }
    });

    /**
     * Autocomplete factory function
     *
     * @param $context
     */
    var textAutocompleteFactory = function() {
        var $context = (0 === arguments.length) ? $('body') : arguments[0];
        $context.find('[data-admin=form-text-autocomplete]').each(function(offset, autocompleteContainer) {
            new TextAutocomplete({el: $(autocompleteContainer)});
        });
    };

    /**
     * Form Autocomplete view
     * Used to handle snowcap_admin_autocomplete form type
     *
     */
    var Autocomplete = TextAutocomplete.extend({
        $textInput: null,
        listUrl: null,
        mode: null,
        mapped: {},
        events: {
            'click a[data-admin=content-add]': 'add',
            'click a[data-admin=form-autocomplete-clear]': 'clear'
        },
        /**
         * Initialize
         *
         */
        initialize: function () {
            TextAutocomplete.prototype.initialize.apply(this);

            this.$valueInput = this.$el.find('input[type=hidden]');
            this.mode = this.$el.data('options-mode');

            if('multiple' === this.mode) {
                $('ul.tokens').on('click', 'a[rel=remove]', _.bind(function(event) {
                    event.preventDefault();
                    var $closeButton = $(event.currentTarget);
                    var value = $closeButton.parent('li').data('value');
                    $closeButton.parent('li').remove();
                    this.$el.find('input[value=' + value + ']').remove();
                }, this));
            }
            else {
                if(this.$valueInput.val() && !(this.$textInput.attr('disabled') || this.$textInput.attr('readonly'))) {
                    this.appendClearButton();
                }
            }
        },
        /**
         * Append a close button
         *
         */
        appendClearButton: function() {
            var $close = $('<a href="#" data-admin="form-autocomplete-clear" class="close">&times;</button>');
            this.$el.append($close);
        },
        /**
         * Initialize typeahead widget
         *
         */
        initializeTypeahead: function() {
            // Initialize typeahead
            this.$textInput.typeahead({
                source: _.bind(_.debounce(this.source, 400), this),
                minLength: 3,
                items: 10,
                matcher: _.bind(this.matcher, this),
                updater: _.bind(this.updater, this)
            });
        },
        /**
         * Bootstrap typeahead source implementation
         *
         * @param query
         * @param process
         */
        source: function(query, process) {
            this.invalidate();
            var replacedUrl = this.listUrl.replace('__query__', query);
            $.getJSON(replacedUrl, _.bind(function(data) {
                this.mapped = {};
                this.labels = [];
                $.each(data.result, _.bind(function (i, item) {
                    this.mapped[item[1]] = item[0];
                    this.labels.push(item[1]);
                }, this));
                process(this.labels);
            }, this));
        },

        invalidate: function() {
            if('single' === this.mode) {
                this.$el.find('input[type=hidden]').val("").trigger('change');
            }
            this.$el.parents('.control-group').addClass('warning');
        },

        /**
         * Bootstrap typeahed matcher implementation
         *
         * @param item
         * @returns {boolean}
         */
        matcher: function(item) {
            var existingTokens = this.$el.find('.token span').map(function() {
                return $(this).html();
            });

            return -1 === $.inArray(item, existingTokens);
        },
        /**
         * Bootstrap typeahed updater implementation
         *
         * @param item
         * @returns {*}
         */
        updater: function(item) {
            if('single' === this.mode) {
                this.$el.find('input[type=hidden]').val(this.mapped[item]).trigger('change');
                this.$el.parents('.control-group').removeClass('warning');
                this.appendClearButton();
                return item;
            }
            else {
                var prototype = $.trim(this.$el.data('prototype'));
                var $prototype = $(prototype.replace(/__name__/g, this.$el.find('input[type=hidden]').length));
                $prototype.val(this.mapped[item]);
                this.$el.prepend($prototype);

                var $token = $('<li>').addClass('token').html($('<span>').html(item)).append($('<a>').html('&times;').addClass('close').attr('rel', 'remove'));
                this.$el.find('.tokens').append($token);

                $prototype.trigger('change');
                this.$el.parents('.control-group').removeClass('warning');

                return "";
            }
        },
        /**
         * Add a new entity and select it within the widget
         *
         * @param event
         */
        add: function(event) {
            event.preventDefault();
            var $trigger = $(event.currentTarget);
            var contentModal = new SnowcapAdmin.Ui.Modal({url: $trigger.attr('href')});
            contentModal.on('ui:modal:success', _.bind(function(data){
                var
                    option = $('<option>'),
                    entity_id = data.result.entity_id,
                    entity_name = data.result.entity_name;
                if('single' === this.mode) {
                    // TODO refactor with autocomplete updater
                    this.$textInput.val(entity_name);
                    this.$valueInput.val(entity_id).trigger('change');
                }
                else {
                    var prototype = $.trim(this.$el.data('prototype'));
                    var $prototype = $(prototype.replace(/__name__/g, this.$el.find('input[type=hidden]').length));
                    $prototype.val(entity_id);
                    this.$el.prepend($prototype);
                    $prototype.trigger('change');

                    var $token = $('<li>').addClass('token').html($('<span>').html(entity_name)).append($('<a>').html('&times;').addClass('close').attr('rel', 'remove'));
                    this.$el.find('.tokens').append($token);
                }
            }, this));
        },
        /**
         * Clear both the hidden and the text fields
         * @param event
         */
        clear: function(event) {
            event.preventDefault();
            this.$textInput.val('');
            this.invalidate();
        }
    });

    /**
     * Autocomplete factory function
     *
     * @param $context
     */
    var autocompleteFactory = function() {
        var $context = (0 === arguments.length) ? $('body') : arguments[0];
        $context.find('[data-admin=form-autocomplete]').each(function(offset, autocompleteContainer) {
            new Autocomplete({el: $(autocompleteContainer)});
        });
    };

    var MultiUpload = Backbone.View.extend({
        events: {
            'click .multiupload-file-preview .remove': 'removeFile'
        },

        initialize: function() {
            this.entryTemplate = this.$el.attr('data-prototype');
            this.$uploader = this.$('.multiupload-file-uploader');
            this.$uploaderForm = this.$uploader.find('input');
            this.$progress = this.$uploader.find('.progress');
            this.$progressLoaded = this.$uploader.find('.progress-bar-loaded');

            this.$el.data('index', this.$el.find('.multiupload-file-preview').length);

            this.$uploaderForm.fileupload({
                start: _.bind(this.start, this),
                done: _.bind(this.done, this),
                progressall: _.bind(this.progressall, this),
                fail: _.bind(this.fail, this),
                dataType: 'json',
                limitConcurrentUploads: 3,
                progressInterval: 40
            });
        },

        start: function() {
            this.$progress.css('display', 'block');
        },

        progressall: function(event, data) {
            this.$progress.find('.bar').width(
                parseInt(data.loaded / data.total * 100, 10) + '%'
            );
        },

        done: function(event, data) {
            this.$progress.hide();
            this.$uploaderForm = this.$uploader.find('input');

            var index = this.$el.data('index');
            var $file = $(this.entryTemplate.replace(
                /__name__/g, index
            ));

            var url = data.result.url;

            // update the value of the hidden field
            $file.find('input[type=hidden]').val(url);

            // configure preview
            $link = $file.find('a');

            $link.attr('href', function() {
                return url;
            });

            switch (this.$el.data('type')) {
                case 'snowcap_admin_multiupload_url':
                    $link.text(url);
                    break;
                case 'snowcap_admin_multiupload_image':
                    $file.find('img').attr('src', function() {
                        return this.src + url;
                    });
                    break;
            }

            this.$el.data('index', index + 1);
            this.$uploader.before($file);
        },

        fail: function(event, data) {
            console.log('error while uploading');
        },

        removeFile: function(event) {
            // prevent default behavior
            event.preventDefault();

            // remove the current node from the DOM
            $(event.currentTarget).parents('.multiupload-file-preview').remove();
        }
    });

    var multiuploadFactory = function() {
        $('[data-multi-upload]').each(function () {
            new MultiUpload({el: this});
        });
    };

    return {
        Collection: Collection,
        collectionFactory: collectionFactory,
        TextAutocomplete: TextAutocomplete,
        textAutocompleteFactory: textAutocompleteFactory,
        Autocomplete: Autocomplete,
        autocompleteFactory: autocompleteFactory,
        Multiupload: MultiUpload,
        multiuploadFactory: multiuploadFactory
    }
})(jQuery);

jQuery(document).ready(function() {
    SnowcapAdmin.Form.collectionFactory();
    SnowcapAdmin.Form.textAutocompleteFactory();
    SnowcapAdmin.Form.autocompleteFactory();
    SnowcapAdmin.Form.multiuploadFactory();
});
