jQuery(document).ready(function ($) {

    var Slugger = function(element) {
        var _this = this;
        var _element = $(element);
        var _target;
        var _currentSlug = '';
        /**
         * Append a "lock" button to control slug behaviour (auto or manual)
         */
        this.appendLockButton = function() {
            _this.lockButton = _element.parent().find('a');
            _this.lockButton.tooltip();
            _this.lockButton.click(function(event) {
                event.preventDefault();
                if (_this.lockButton.attr('href') === '#locked') {
                    if (confirm("Are you sure you want to change this slug ?")) {
                        _this.unlock();
                    }
                }
                else {
                    _this.lock();
                }
            });
            //_element.after(_this.lockButton);
        };
        /**
         * Unlock the widget input (manual mode)
         *
         */
        this.unlock = function() {
            _this.lockButton.attr('href', '#unlocked');
            _this.lockButton.find('i').toggleClass('icon-pencil icon-magnet');
            _element.removeAttr('readonly');

        };
        this.lock = function() {
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
        this.makeSlug = function(string) {
            var lowercased = string.toLowerCase();
            var hyphenized = lowercased.replace(/\s/g, '-');
            var slug = hyphenized.replace(/[^a-zA-Z0-9\-]/g, '').replace('--', '-').replace(/\-+$/, '');
            return slug;
        };
        /**
         * Observe the target field and slug it
         *
         */
        this.startSlug = function() {
            _target.keyup(function(event) {
                if (_element.attr('readonly') === 'readonly') {
                    _element.val(_this.makeSlug($(this).val()));
                }
            });
        };
        /**
         * Instance init
         */
        this.init = function() {
            var targetId = $.grep(_element.attr('class').split(' '),
                function(element, offset) {
                    return element.indexOf('widget-slug-') !== -1;
                }).pop().split('-').pop();
            _target = $('#' + targetId);
            _element.attr('readonly', 'readonly');
            _element.addClass('off');
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
    $.fn.slugger = function() {
        return this.each(function() {
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


    var AddElementForm = function (element, collectionHolder) {
        var _this = this;
        var _element = $(element);

        // Get the data-prototype we explained earlier
        var prototype = collectionHolder.attr('data-prototype');
        // Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on the current collection's length.
        form = prototype.replace(/\$\$name\$\$/g, collectionHolder.children().length);
        // Display the form in the page
        collectionHolder.append(form);
    };

    $.fn.addElementForm = function (collectionHolder) {
        return this.each(function () {
            new AddElementForm(this, collectionHolder);
        });
    };

    var ManageDataPrototype = function (element) {
        var _this = this;
        var _element = $(element);
        var _button = $('<a href="#" class="btn btn-primary">+</a>');
        _element.parent().append(_button);
        // When the link is clicked we add the field to input another element
        _button.click(function (event) {
            event.preventDefault();
            $(this).addElementForm(_element);
        });


    };
    $.fn.manageDataPrototype = function () {
        return this.each(function () {
            new ManageDataPrototype(this);
        });
    };

    var InlineWidget = function (row) {

        var self = this;
        var addTrigger = $(row).find('a[rel=create]');
        var selectTrigger = $(row).find('a[rel=select]');
        var modal = $('#modal');

        /**
         * Observe what's cooking in the add form
         */
        self.observeAddForm = function () {
            modal.find('$[type=submit]').on('click', function (event) {
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
                        var select = $(addTrigger).siblings('select');
                        var option = $('<option>');
                        option.attr('value', responseJSON.entity_id);
                        option.attr('selected', 'selected');
                        option.html(responseJSON.entity_property);
                        select.append(option);
                        modal.modal('hide');
                    }
                };
                xhr.send(data);
            });
        };

        /**
         * Open the add popup
         */
        $(addTrigger).click(function (event) {
            event.preventDefault();
            $.get($(this).attr('href'), function (data) {
                modal.html(data.html);
                self.observeAddForm();
                modal.modal('show');
            });
        });

        $(selectTrigger).click(function (event) {
            event.preventDefault();
            $.get;
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

    $('*[data-prototype]').manageDataPrototype();
});
