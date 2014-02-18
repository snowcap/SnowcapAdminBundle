SnowcapAdmin.Form = (function ($) {
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
        initialize: function () {
            SnowcapCore.Form.Collection.prototype.initialize.apply(this);
        },
        /**
         * Remove a collection item
         *
         * @param event
         */
        removeItem: function (event) {
            event.preventDefault();
            var
                $target = $(event.currentTarget),
                $collectionItem;

            $collectionItem = $target.parents('[data-admin=form-collection-item]');
            if (0 === $collectionItem.length) {
                $collectionItem = $target.parent();
            }

            if (this.options.confirmDelete) {
                var modal = new SnowcapBootstrap.Modal({'url': this.options.confirmDeleteUrl});
                modal.on('modal:confirm', _.bind(function () {
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
        fadeAndRemoveItem: function ($item) {
            $item.fadeOut(function () {
                $item.remove();
            });
        }
    });

    /**
     * Form collection factory function
     *
     */
    var collectionFactory = function () {
        var context = arguments[0] || 'body';
        $('[data-admin=form-collection]', context).each(function (offset, container) {
            if (!$(container).data('widget')) {
                $(container).data('widget', new Collection({
                    el: container,
                    confirmDelete: $(container).data('options-confirm-delete-url') ? true : false,
                    confirmDeleteUrl: $(container).data('options-confirm-delete-url') ? $(container).data('options-confirm-delete-url') : null
                }));
            }
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
        initializeTypeahead: function () {
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
        source: function (query, process) {
            var replacedUrl = this.listUrl.replace('__query__', query);
            $.getJSON(replacedUrl, _.bind(function (data) {
                this.labels = [];
                $.each(data.result, _.bind(function (i, item) {
                    this.labels.push(item);
                }, this));
                process(this.labels);
            }, this));
        }
    });

    /**
     * textAutocompleteFactory factory function
     */
    var textAutocompleteFactory = function () {
        var context = arguments[0] || 'body';
        $('[data-admin=form-text-autocomplete]', context).each(function (offset, container) {
            if (!$(container).data('widget')) {
                $(container).data('widget', new TextAutocomplete({el: container}));
            }
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
            this.$close = null;

            if ('multiple' === this.mode) {
                $('ul.tokens').on('click', 'a[rel=remove]', _.bind(function (event) {
                    event.preventDefault();
                    var $closeButton = $(event.currentTarget);
                    var value = $closeButton.parent('li').data('value');
                    $closeButton.parent('li').remove();
                    this.$el.find('input[value=' + value + ']').remove();
                }, this));
            }
            else {
                if (this.$valueInput.val() && !(this.$textInput.attr('disabled') || this.$textInput.attr('readonly'))) {
                    this.appendClearButton();
                }
            }
        },
        /**
         * Append a close button
         *
         */
        appendClearButton: function () {
            if(null === this.$close) {
                this.$close = $('<a href="#" data-admin="form-autocomplete-clear" class="close">&times;</button>');
                this.$textInput.after(this.$close);
            }
        },
        /**
         * Initialize typeahead widget
         *
         */
        initializeTypeahead: function () {
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
        source: function (query, process) {
            this.invalidate();
            var replacedUrl = this.listUrl.replace('__query__', query);
            $.getJSON(replacedUrl, _.bind(function (data) {
                this.mapped = {};
                this.labels = [];
                $.each(data.result, _.bind(function (i, item) {
                    this.mapped[item[1]] = item[0];
                    this.labels.push(item[1]);
                }, this));
                process(this.labels);
            }, this));
        },

        invalidate: function () {
            if ('single' === this.mode) {
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
        matcher: function (item) {
            var existingTokens = this.$el.find('.token span').map(function () {
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
        updater: function (item) {
            if ('single' === this.mode) {
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

                var $token = $('<li>').addClass('token').data('value', this.mapped[item]).html($('<span>').html(item)).append($('<a>').html('&times;').addClass('close').attr('rel', 'remove'));
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
        add: function (event) {
            event.preventDefault();
            var $trigger = $(event.currentTarget);
            var contentModal = new SnowcapAdmin.Ui.Modal({url: $trigger.attr('href')});
            contentModal.on('ui:modal:success', _.bind(function (data) {
                var
                    option = $('<option>'),
                    entity_id = data.result.entity_id,
                    entity_name = data.result.entity_name;
                if ('single' === this.mode) {
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
        clear: function (event) {
            event.preventDefault();
            this.$textInput.val('');
            this.invalidate();

            if ('single' === this.mode) {
                this.$close.remove();
                this.$close = null;
            }
        }
    });

    /**
     * Autocomplete factory function

     */
    var autocompleteFactory = function () {
        var context = arguments[0] || 'body';
        $('[data-admin=form-autocomplete]', context).each(function (offset, container) {
            if (!$(container).data('widget')) {
                $(container).data('widget', new Autocomplete({el: container}));
            }
        });
    };

    var MultiUpload = Backbone.View.extend({
        events: {
            'click .multiupload-file-preview .remove': 'removeFile'
        },

        initialize: function () {
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

        start: function () {
            this.$progress.css('display', 'block');
        },

        progressall: function (event, data) {
            this.$progress.find('.bar').width(
                parseInt(data.loaded / data.total * 100, 10) + '%'
            );
        },

        done: function (event, data) {
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

            $link.attr('href', function () {
                return url;
            });

            switch (this.$el.data('type')) {
                case 'snowcap_admin_multiupload_url':
                    $link.text(url);
                    break;
                case 'snowcap_admin_multiupload_image':
                    $file.find('img').attr('src', function () {
                        return this.src + url;
                    });
                    break;
            }

            this.$el.data('index', index + 1);
            this.$uploader.before($file);
        },

        fail: function (event, data) {
            console.log('error while uploading');
        },

        removeFile: function (event) {
            // prevent default behavior
            event.preventDefault();

            // remove the current node from the DOM
            $(event.currentTarget).parents('.multiupload-file-preview').remove();
        }
    });

    var multiuploadFactory = function () {
        var context = arguments[0] || 'body';
        $('[data-multi-upload]', context).each(function (offset, container) {
            if (!$(container).data('widget')) {
                $(container).data('widget', new MultiUpload({el: container}));
            }
        });
    };

    var Slugger = Backbone.View.extend({
        /**
         * Instance init
         */
        initialize: function () {
            this.locked = true;
            this.$target = '';
            this.currentSlug = '';
            this.$modal = $(this.$el.data('modal'));
            this.$modal.modal({show: false});
            var elementIdSplitted = this.$el.attr('id').split('_');
            elementIdSplitted.pop();
            this.$target = $('#' + elementIdSplitted.join('_') + '_' + this.$el.data('target'));
            if (this.$target.length === 0) {
                throw "wrong target specified for slug widget (" + this.$el.data('target') + ")";
            }
            this.$el.attr('readonly', 'readonly');
            if (this.$el.val() === '') {
                this.$el.val(this.makeSlug(this.$target.val()));
                this.listenTarget();
            }
            else {
                this.currentSlug = this.$el.val();
            }
            this.appendLockButton();
        },
        /**
         * Append a "lock" button to control slug behaviour (auto or manual)
         */
        appendLockButton: function () {
            this.$modal.find('a[data-accept=modal]').on('click', _.bind(function () {
                this.unlock();
                this.$modal.modal('hide');
            }, this));
            this.lockButton = this.$el.parent().find('a');
            this.lockButton.on('click', _.bind(function (event) {
                event.preventDefault();
                if (this.locked) {
                    this.$modal.modal('show');
                }
                else {
                    this.lock();
                }
            }, this));
        },
        /**
         * Unlock the widget input (manual mode)
         *
         */
        unlock: function () {
            this.locked = false;
            this.lockButton.find('i').toggleClass('icon-pencil icon-magnet');
            this.$el.removeAttr('readonly');

        },
        lock: function () {
            /**
             * Lock the widget input (auto mode)
             */
            this.locked = true;
            this.lockButton.find('i').toggleClass('icon-pencil icon-magnet');

            if (this.currentSlug !== '') {
                this.$el.val(this.currentSlug);
            }
            else {
                this.$el.val(this.makeSlug(this.$target.val()));
            }
            this.$el.attr('readonly', 'readonly');
        },
        /**
         * Transform a string into a slug
         *
         * @param string string
         * @return string
         */
        makeSlug: function (string) {
            var hyphenized = string.toLowerCase()
                .replace(/^\s+|\s+$/g, '')
                .replace(/\s+/g, '-');

            return hyphenized
                .replace(/[àâä]/g, 'a')
                .replace(/[éèêë]/g, 'e')
                .replace(/[îï]/g, 'i')
                .replace(/[ôö]/g, 'o')
                .replace(/[ûüù]/g, 'u')
                .replace(/[ýÿ]/g, 'y')
                .replace(/[ç]/g, 'c')
                .replace(/[œ]/g, 'oe')
                .replace(/[^a-zA-Z0-9\-]/g, '')
                .replace(/^\-+|\-+$/, '');
        },
        /**
         * Observe the target field and slug it
         *
         */
        listenTarget: function () {
            this.$target.keyup(_.bind(function () {
                if (this.$el.attr('readonly') === 'readonly') {
                    this.$el.val(this.makeSlug(this.$target.val()));
                }
            }, this));
        }
    });

    var sluggerFactory = function () {
        var context = arguments[0] || 'body';
        $('.widget-slug, [data-admin=form-slugger]', context).each(function (offset, container) {
            if (!$(container).data('widget')) {
                $(container).data('widget', new Slugger({'el': container}));
            }
        });
    };

    var EntityWidget = Backbone.View.extend({
        events: {
            'click a[data-admin=form-type-entity-add]': 'onEntityAddClick'
        },
        initialize: function () {
            this.$select = this.$el.find('select');
        },
        onEntityAddClick: function (event) {
            event.preventDefault();
            var contentModal = new SnowcapAdmin.Ui.Modal({url: event.currentTarget.href});
            contentModal.on('ui:modal:success', this.onModalSuccess, this);
        },
        onModalSuccess: function (data) {
            var e_id = data.result.entity_id;
            this.$select
                .append($('<option>').html(data.result.entity_name).val(e_id))
                .val(e_id);
        }
    });

    var entityWidgetFactory = function () {
        var context = arguments[0] || 'body';
        $('[data-admin=form-type-entity]', context).each(function (offset, container) {
            if (!$(container).data('widget')) {
                $(container).data('widget', new EntityWidget({'el': container}));
            }
        });
    };


    // Manage collapsible content
    var CollapsibleFieldset = Backbone.View.extend({
        initialize: function () {
            CollapsibleFieldset.instanceCount++;
            var $content = this.$el.children(':not(legend)').detach();
            this.$legend = this.$el.children('legend').addClass('no-border');
            this.$icon = $('<span></span>').addClass('icon icon-chevron-up');
            this.$legend.append(this.$icon);
            this.$collapsibleContent = $('<div></div>').addClass('well well-light collapsible-content').append($content);
            this.$el.append(this.$collapsibleContent);

            this.$legend.on('click', _.bind(this.toggle, this));
            this.isOpen = true;
        },
        toggle: function () {
            this.$legend.toggleClass('no-border');
            this.$icon.toggleClass('icon-chevron-down icon-chevron-up');
            this.$collapsibleContent.slideToggle();
            this.isOpen = !this.isOpen;

            var state = this.isOpen ? 'open' : 'close';
            this.trigger('form:collapsible:' + state);
            this.$el.trigger('form:collapsible:' + state);
        },
        close: function() {
            this.$legend.removeClass('no-border');
            this.$icon.addClass('icon-chevron-down').removeClass('icon-chevron-up');
            this.$collapsibleContent.slideUp();
            this.isOpen = false;
            this.trigger('form:collapsible:close');
            this.$el.trigger('form:collapsible:close');
        }
    }, {
        instanceCount: 0
    });
    var collapsibleFieldsetFactory = function () {
        var context = arguments[0] || 'body';
        var $containers = $('fieldset.collapsible, [data-admin=form-collapsible-fieldset]', context)
        if($containers.length > CollapsibleFieldset.instanceCount) {
            $containers.each(function (offset, container) {
                var widget = $(container).data('widget');
                if (!widget) {
                    $(container).data('widget', widget = new CollapsibleFieldset({'el': container}));
                }
                if ((offset > 0 || widget.$el.hasClass('metas')) && widget.$('.error').length === 0) {
                    widget.close();
                }
            });
        }
    };

    /**
     * Form RichEditor view
     * Used to manage CKEditor textarea
     *
     */
    var WysiwygEditor = Backbone.View.extend({
        initialize: function () {
            CKEDITOR.replace(this.el, {customConfig: this.$el.data('wysiwyg')});

            this.editor = CKEDITOR.instances[this.$el.attr('id')];

            this.editor.on('blur', _.bind(function () {
                if (true === this.editor.checkDirty()) {
                    this.$el.parents('form').trigger('change');
                }
            }, this));
        }
    });

    var wysiwygEditorFactory = function () {
        var context = arguments[0] || 'body';
        $('.widget-wysiwyg, [data-admin=form-wysiwyg-editor]', context).each(function (offset, container) {
            if (!$(container).data('widget')) {
                $(container).data('widget', new WysiwygEditor({'el': container}));
            }
        });
    };

    var Manager = SnowcapCore.Form.Manager.extend({
        initialize: function() {
            SnowcapCore.Form.Manager.prototype.initialize.apply(this);
            this.$('.catalogue-translation textarea').autosize();
            this.addErrorClasses();
            $('a:not([href^=#])').not('[data-admin]').on('click', _.bind(this.onExternalLinkClick, this));
        },
        addErrorClasses: function() {
            this.$('form .tab-pane:has(.error)').each(function() {
                $('a[href="#' + $(this).attr('id') + '"]').addClass('error');
            });
            this.$('fieldset').has('.error').each(function() {
                $('legend', $(this)).first().addClass('error');
            });
        },
        onExternalLinkClick: function(event) {
            if(this.hasChanged) {
                event.preventDefault();
                var modal = $('#modal');
                $.get(SNOWCAP_ADMIN_CONTENT_CHANGE_URL, function (data) {
                    modal.html(data);
                    modal.find('.cancel').click(function(){
                        modal.html('');
                        modal.modal('hide');
                    });
                    modal.find('.proceed').click(function(){
                        window.location.href = $(event.currentTarget).attr('href');
                    });
                    modal.find('.save').click(function(){
                        $('[data-admin=form-change-warning]').submit();
                    });
                    modal.modal('show');
                });
            }
        }
    });

    return {
        Manager: Manager,
        Collection: Collection,
        TextAutocomplete: TextAutocomplete,
        Autocomplete: Autocomplete,
        Multiupload: MultiUpload,
        Slugger: Slugger,
        EntityWidget: EntityWidget,
        CollapsibleFieldset: CollapsibleFieldset,
        WysiwygEditor: WysiwygEditor,
        factories: {
            collectionFactory: collectionFactory,
            textAutocompleteFactory: textAutocompleteFactory,
            autocompleteFactory: autocompleteFactory,
            multiuploadFactory: multiuploadFactory,
            sluggerFactory: sluggerFactory,
            entityWidgetFactory: entityWidgetFactory,
            collapsibleFieldsetFactory: collapsibleFieldsetFactory,
            wysiwygEditorFactory: wysiwygEditorFactory
        },
        instances : {
            managers: []
        }
    };
})(jQuery);

jQuery(document).ready(function () {
    $('[data-admin=form-manager]').each(function (offset, container) {
        if (!$(container).data('widget')) {
            var manager = new SnowcapAdmin.Form.Manager({el: container});
            _.each(SnowcapAdmin.Form.factories, function(factory) {
                manager.registerFactory(factory);
            });
            SnowcapAdmin.Form.instances.managers.push(manager);
            $(container).data('widget', manager);

        }
    });
});
