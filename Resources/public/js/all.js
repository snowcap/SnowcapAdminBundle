jQuery(document).ready(function ($) {

    var Slugger = function (element) {
        var _this = this;
        var _element = $(element);
        var _target;
        var _currentSlug = '';
        /**
         * Append a "lock" button to control slug behaviour (auto or manual)
         */
        this.appendLockButton = function () {
            _modal = _element.parent().parent().find('.modal');
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
            var slug = hyphenized.replace(/[^a-zA-Z0-9\-]/g, '').replace('--', '-').replace(/\-+$/, '');
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
            var targetId = $.grep(_element.attr('class').split(' '),
                function (element, offset) {
                    return element.indexOf('widget-slug-') !== -1;
                }).pop().split('-').pop();
            _target = $('#' + targetId);
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

    var InlineWidget = function (row) {

        var self = this;
        var trigger = $(row).find('a[rel=select_or_create]');
        var modal = $('#modal');
        var select = $(trigger).parent().siblings('select');

        select.hide();

        /**
         * Observe what's cooking in the add form
         */
        self.observeAddForm = function () {
            modal.find('*[type=submit]').on('click', function (event) {
                event.preventDefault();
                var form = modal.find('form');
                var data = new FormData(form[0]);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', form.attr('action'), true);
                xhr.onload = function (e) {
                    if (this.status === 200) {
                        var responseJSON = JSON.parse(this.response);
                        modal.html(responseJSON.html);
                        self.observeAddForm();
                    }
                    else if (this.status === 201) {
                        var responseJSON = JSON.parse(this.response);

                        var preview = $(trigger).parent().parent().find(".inline-preview");
                        preview.find('li.empty').hide();
                        preview.append(responseJSON.preview);

                        var option = $('<option>');
                        option.attr('value', responseJSON.entity_id);
                        option.attr('selected', 'selected');
                        option.html(responseJSON.entity_id);
                        select.append(option);

                        modal.modal('hide');
                    }
                };
                xhr.send(data);
            });
        };

        // Inline unlinking
        $(row).find('a[rel=delete-inline]').live('click', function (event) {

            var previewBlock = $(this).parents("li");
            var entityId = previewBlock.attr('data-entity-id');

            previewBlock.remove();
            select.find("option[value='" + entityId + "']").removeAttr('selected');

            // TODO see if no li's left => showing the empty one again
            if ($(row).find('ul.inline-preview li').length == 1) {
                $(row).find('li.empty').show();
            }

        });

        $(row).find('.autocomplete').keyup(function (event) {
            var autocomplete = $(this);
            if ($(this).val().length >= 3) {
                $.get($(this).attr('data-url').replace('placeholder', $(this).val()), function (data) {
                    var results = autocomplete.siblings('.autocomplete-results');
                    results.css('min-width', autocomplete.css('width'));
                    results.html(data.html);
                    results.find('li').click(function (event) {
                        select.find('option[value=' + $(this).attr('data-identity') + ']').attr('selected', 'selected');
                        results.hide();
                        autocomplete.val('');
                        var preview = $(trigger).parent().parent().find(".inline-preview");
                        preview.find('li.empty').hide();
                        if(select.attr('multiple') === 'multiple'){
                            preview.append($(this));
                        }
                        else {
                            preview.html($(this));
                        }
                    });
                    results.show();
                });
            }
        });
        $(row).find('.autocomplete').blur(function (event) {
            //$(this).siblings('.autocomplete-results').hide();
        });


        /**
         * Open the select or create popup
         */
        $(trigger).click(function (event) {
            event.preventDefault();
            $.get($(this).attr('href'), function (data) {
                modal.html(data.html);
                self.observeAddForm();
                modal.modal('show');
            });
        });
    };

    // Slug
    $('.widget-slug').slugger();
    // Markdown
    $('.widget-markdown').markdownPreviewer();

    //TODO: create modals on demand
    $('#modal').modal({show:false});
    $('#modal').on('hidden', function (event) {
        $(this).empty();
    });

    $('.type_snowcap_admin_inline').each(function (offset, row) {
        new InlineWidget(row);
    });

});
