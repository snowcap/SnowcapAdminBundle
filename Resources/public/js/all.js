jQuery(document).ready(function ($) {

    var Slugger = function (element) {
        var _this = this;
        var _element = $(element);
        var _target;
        var _currentSlug = '';
        var _modal = _element.parent().parent().find('.modal');
        /**
         * Append a "lock" button to control slug behaviour (auto or manual)
         */
        this.appendLockButton = function () {
            _modal.find('a[data-accept=modal]').click(function (event) {
                _this.unlock();
                _modal.modal('hide');
            });
            _this.lockButton = _element.parent().find('a');
            _this.lockButton.click(function (event) {
                event.preventDefault();
                if (_this.lockButton.attr('href') === '#locked') {
                    _modal.modal('show');
                }
                else {
                    _this.lock();
                }
            });
        };
        /**
         * Unlock the widget input (manual mode)
         *
         */
        this.unlock = function () {
            _this.lockButton.attr('href', '#unlocked');
            _this.lockButton.find('i').toggleClass('icon-pencil icon-magnet');
            _element.removeAttr('readonly');

        };
        this.lock = function () {
            /**
             * Lock the widget input (auto mode)
             */
            _this.lockButton.attr('href', '#locked');
            _this.lockButton.find('i').toggleClass('icon-pencil icon-magnet');

            if (_currentSlug !== '') {
                _element.val(_currentSlug);
            }
            else {
                _element.val(_this.makeSlug(_target.val()));
            }
            _element.attr('readonly', 'readonly');
        };
        /**
         * Transform a string into a slug
         *
         * @param string string
         * @return string
         */
        this.makeSlug = function (string) {
            var lowercased = string.toLowerCase();
            var hyphenized = lowercased.replace(/\s/g, '-');
            var slug = hyphenized
                .replace(/[àâä]/g,'a')
                .replace(/[éèêë]/g,'e')
                .replace(/[îï]/g,'i')
                .replace(/[ôö]/g,'o')
                .replace(/[ûüù]/g,'u')
                .replace(/[ýÿ]/g,'y')
                .replace(/[ç]/g,'c')
                .replace(/[œ]/g,'oe')
                .replace(/[^a-zA-Z0-9\-]/g, '')
                .replace(/\-+/g, '-')
                .replace(/\-+$/, '');
            return slug;
        };
        /**
         * Observe the target field and slug it
         *
         */
        this.startSlug = function () {
            _target.keyup(function (event) {
                if (_element.attr('readonly') === 'readonly') {
                    _element.val(_this.makeSlug($(this).val()));
                }
            });
        };
        /**
         * Instance init
         */
        this.init = function () {
            _modal.modal({show: false});
            var elementIdSplitted = _element.attr('id').split('_');
            elementIdSplitted.pop();
            _target = $('#' + elementIdSplitted.join('_') + '_' +  _element.attr('data-target'));
            if(_target.length === 0) {
                throw "wrong target specified for slug widget (" + _element.attr('data-target') + ")";
            }
            _element.attr('readonly', 'readonly');
            initialState = 'locked';
            if (_element.val() === '') {
                _element.val(_this.makeSlug(_target.val()));
                _this.startSlug();
            }
            else {
                _currentSlug = _element.val();
            }
            _this.appendLockButton();
        };
        this.init();
    };
    /**
     * Namespace in jQuery
     */
    $.fn.slugger = function () {
        return this.each(function () {
            new Slugger(this);
        });
    };

    /**
     * Markdown peviewer
     *
     * @param DOMElement element
     */
    var MarkdownPreviewer = function (element) {
        var _element = $(element);
        var latestPreviewContent = "";
        var previewContent = "";

        var previewElement = _element.parents('.controls').find('.markdown-previewer');
        var previewTrigger = _element.parents('.controls').find('.preview-trigger');
        $(previewTrigger).click(function (event) {
            previewContent = _element.val();

            if (previewContent != latestPreviewContent) {
                $.post(_element.attr('data-url'), { content:previewContent }, function (data) {
                    previewElement.html(data);
                    latestPreviewContent = previewContent;
                });
            }
        });

    };
    $.fn.markdownPreviewer = function () {
        return this.each(function () {
            new MarkdownPreviewer(this);
        });
    };

    /**
     * Inline widget
     *
     * @param DOMElement row
     */
    var EntityWidget = function (container) {
        var container = $(container);
        var $trigger = container.find('a[data-admin=form-type-entity-add]');
        var $select = container.find('select');

        $trigger.click(function (event) {
            event.preventDefault();
            var contentModal = new SnowcapAdmin.Content.Modal({url: $trigger.attr('href')});
            contentModal.on('content:modal:success', function(result){
                var
                    option = $('<option>'),
                    entity_id = result[0]
                    entity_name = result[1];
                option.val(entity_id);
                option.html(entity_name);
                $select.append(option);
                $select.val(entity_id);
            });
        });
    };

    /**
    * Autocomplete widget
    *
    * @param DOMElement row
    */
    var AutocompleteWidget = function (container) {
        var self = this;
        var container = $(container);
        var $trigger = container.find('a[rel=add]');
        var modal = $('#modal');
        var textInput = container.find('input[type=text]');
        var valueInput = container.find('input[type=hidden]');

        var labels, mapped;
        var listUrl = container.data('list-url');
        var mode = container.data('mode');

        /**
         * Autocomplete widget init
         *
         */
        self.init = function () {
            // Observe the "add" button
            $trigger.click(function (event) {
                event.preventDefault();
                var contentModal = new SnowcapAdmin.Content.Modal({url: $trigger.attr('href')});
                contentModal.on('content:modal:success', function(result){
                    var
                        option = $('<option>'),
                        entity_id = result[0]
                        entity_name = result[1];
                    if('single' === mode) {
                        // TODO refactor with autocomplete updater
                        textInput.val(entity_name);
                        valueInput.val(entity_id).trigger('change');
                    }
                    else {
                        var prototype = container.data('prototype');
                        var $prototype = $(prototype.replace(/__name__/g, container.find('input[type=hidden]').length));
                        $prototype.val(entity_id);
                        container.prepend($prototype);
                        $prototype.trigger('change');

                        $token = $('<li>').addClass('token').html($('<span>').html(data.result[1])).append($('<a>').html('&times;').addClass('close').attr('rel', 'remove'));
                        container.find('.tokens').append($token);
                    }
                });
            });
            // Initialize typeahead
            textInput.typeahead({
                source: function(query, process) {
                    var replacedUrl = listUrl.replace('__query__', query);
                    $.getJSON(replacedUrl, function(data) {
                        labels = [];
                        mapped = {};
                        $.each(data.result, function (i, item) {
                            mapped[item[1]] = item[0];
                            labels.push(item[1]);
                        })

                        process(labels);
                    });
                },
                minLength: 3,
                matcher: function(item) {
                    var existingTokens = container.find('.token span').map(function() {
                        return $(this).html();
                    });

                    return -1 === $.inArray(item, existingTokens);
                },
                updater: function(item) {
                    if('single' === mode) {
                        container.find('input[type=hidden]').val(mapped[item]).trigger('change');
                        return item;
                    }
                    else {
                        var prototype = container.data('prototype');
                        var $prototype = $(prototype.replace(/__name__/g, container.find('input[type=hidden]').length));
                        $prototype.val(mapped[item]);
                        container.prepend($prototype);
                        $prototype.trigger('change');

                        $token = $('<li>').addClass('token').html($('<span>').html(item)).append($('<a>').html('&times;').addClass('close').attr('rel', 'remove'));
                        container.find('.tokens').append($token);

                        return "";
                    }
                }
            });
            // Handle focus / blur
            textInput.focus(function(e){
                $(this).data('prev', $(this).val());
                $(this).val('');
            }).blur(function(e){
                if($(this).val() === '') {
                    $(this).val($(this).data('prev'));
                }
            });
            // Remove associations
            if('multiple' === mode) {
                $('ul.tokens').on('click', 'a[rel=remove]', function(event) {
                    event.preventDefault();
                    var value = $(this).parent('li').data('value');
                    $(this).parent('li').remove();
                    container.find('input[value=' + value + ']').remove();
                });
            }
        };
        self.init();
    };

    // Slug
    $('.widget-slug').slugger();
    // Markdown
    $('.widget-markdown').markdownPreviewer();

    // Admin entity widgets
    $('[data-admin=form-type-entity]').each(function (offset, container) {
        new EntityWidget(container);
    });

    // Autocomplete widgets
    var launchAutocompletes = function() {
        new AutocompleteWidget($(this));
    };
    $('[data-admin=form-type-autocomplete]').each(launchAutocompletes);
    $('.collection-container').on('new_collection_item', function() {
        $(this).find('[data-admin=form-type-autocomplete]').each(launchAutocompletes);
    });

    // autosize for textareas
    $('.catalogue-translation textarea').autosize();

    // Handle "content changed" event
    $('body').data('admin-form-changed', false);
    $('[data-admin=form-change-warning]').change(function(event) {
        $('body').data('admin-form-changed', true);
    });

    // Handle "content changed" event
    $('a:not([href^=#])').click(function(event) {
        var formHasChanged = $('body').data('admin-form-changed');
        if(formHasChanged) {
            event.preventDefault();
            var href = $(this).attr('href');
            var modal = $('#modal');
            $.get(SNOWCAP_ADMIN_CONTENT_CHANGE_URL, function (data) {
                modal.html(data);
                modal.find('.cancel').click(function(event){
                    modal.html('');
                    modal.modal('hide');
                });
                modal.find('.proceed').click(function(event){
                    window.location.href = href;
                });
                modal.find('.save').click(function(event){
                    $('[data-admin=form-change-warning]').submit();
                });
                modal.modal('show');
            });
        }
    });
});
