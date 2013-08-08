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
            var contentModal = new SnowcapAdmin.Ui.Modal({url: $trigger.attr('href')});
            contentModal.on('ui:modal:success', function(data){
                var
                    option = $('<option>'),
                    entity_id = data.result.entity_id
                entity_name = data.result.entity_name;
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


        // Slug
    $('.widget-slug').slugger();
    // Markdown
    $('.widget-markdown').markdownPreviewer();

    // Admin entity widgets
    $('[data-admin=form-type-entity]').each(function (offset, container) {
        new EntityWidget(container);
    });

    // autosize for textareas
    $('.catalogue-translation textarea').autosize();

    // Handle "content changed" event
    $('body').data('admin-form-changed', false);
    $('[data-admin=form-change-warning]').change(function() {
        $('body').data('admin-form-changed', true);
    });

    // Handle "content changed" event
    $('a:not([href^=#])').not('[data-admin]').click(function(event) {
        var formHasChanged = $('body').data('admin-form-changed');
        if(formHasChanged) {
            event.preventDefault();
            var href = $(this).attr('href');
            var modal = $('#modal');
            $.get(SNOWCAP_ADMIN_CONTENT_CHANGE_URL, function (data) {
                modal.html(data);
                modal.find('.cancel').click(function(){
                    modal.html('');
                    modal.modal('hide');
                });
                modal.find('.proceed').click(function(){
                    window.location.href = href;
                });
                modal.find('.save').click(function(){
                    $('[data-admin=form-change-warning]').submit();
                });
                modal.modal('show');
            });
        }
    });
});
