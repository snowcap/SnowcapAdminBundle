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
                .replace(/[^a-zA-Z0-9\-]/g, '')
                .replace('--', '-')
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
    var InlineWidget = function (row) {

        var self = this;
        var row = $(row);
        var trigger = row.find('a[rel=create]');
        var modal = $('#modal');
        var select = row.find('select');
        var selected = row.find(".selected");

        /**
         * Observe what's cooking in the add form
         *
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
                        var selectedItem = $(responseJSON['html']);
                        self.selectItem(selectedItem);
                        var selectedId = selectedItem.find('a.identity').attr('href');
                        var option = $('<option>');
                        option.attr('value', selectedId);
                        option.attr('selected', 'selected');
                        option.html(selectedId);
                        select.append(option);

                        modal.modal('hide');
                    }
                };
                xhr.send(data);
            });
        };
        /**
         * Add an item to the selection
         *
         * @param DOMElement selectedItem
         */
        self.selectItem = function (selectedItem) {
            row.find('.empty').hide();
            if (select.attr('multiple') !== 'multiple') {
                selected.find('li:not(.empty)').remove();
                select.find('option[selected=selected]').removeAttr('selected');
            }
            selectedItem.addClass('span2');
            selectedItem.find('a.identity').click(function(event){
                event.preventDefault();
            });
            var close = $('<a>').addClass('close').html('x');
            close.click(self.removeSelection);
            selectedItem.append(close);
            selected.append(selectedItem);
            select.find('option[value=' + $(selectedItem).find('a').attr('href') + ']').attr('selected', 'selected');
        };
        /**
         * Remove an item from the selection (on click)
         *
         * @param DOMEvent event
         */
        self.removeSelection = function (event) {
            event.preventDefault();
            var entityId = $(this).prev('.identity').attr('href');
            $(this).parent().remove();
            select.find("option[value='" + entityId + "']").removeAttr('selected');
            if (selected.find('li').length === 0) {
                row.find('.empty').show();
            }
        };
        /**
         * Inline widget init
         *
         */
        self.init = function () {
            select.hide();
            // Observe autocomplete field
            row.find('.autocomplete').keyup(function (event) {
                var autocomplete = $(this);
                if ($(this).val().length >= 3) {
                    $.get($(this).attr('data-url').replace('placeholder', $(this).val()), function (data) {
                        var results = autocomplete.siblings('.autocomplete-results');
                        results.css('min-width', autocomplete.css('width'));
                        results.html(data.html);
                        results.find('li a').click(function (event) {
                            event.preventDefault();
                            results.hide();
                            autocomplete.val('');
                            var selectedItem = $(this).parent().clone();
                            self.selectItem(selectedItem);
                        });
                        results.find('ul').show();
                        results.show();
                    });
                }
            });
            // Observe existing selections and hide empty text
            selected.find('li a.identity').click(function(event){event.preventDefault()});
            selected.find('li a.close').click(self.removeSelection);
            if(selected.find('li').length !== 0) {
                row.find('.empty').hide();
            }
            // Hide autocomplete results on body click
            $('body').click(function (event) {
                row.find('.autocomplete-results').hide();
            });
            // Observe the "create" button
            trigger.click(function (event) {
                event.preventDefault();
                $.get($(this).attr('href'), function (data) {
                    modal.html(data.html);
                    self.observeAddForm();
                    modal.modal('show');
                });
            });
        };
        self.init();
    };

    // Modal
    $('#modal').modal({show:false}); //TODO: create modals on demand
    $('#modal').on('hidden', function (event) {
        $(this).empty();
    });

    // Slug
    $('.widget-slug').slugger();
    // Markdown
    $('.widget-markdown').markdownPreviewer();

    // Inline widgets
    $('.type_snowcap_admin_inline').each(function (offset, row) {
        new InlineWidget(row);
    });

    // Reorderable
    $('.open-reorder').on('click', function(event) {
        event.preventDefault();
        var modal = $('#modal');
        $.get($(this).attr('href'), function(data) {
            modal.html(data.html);
            var tree = modal.find('.tree');
            tree
                .on('loaded.jstree', function(event) {
                    $(this).jstree('open_all');
                    modal.find('a.save').on('click', function(event) {
                        event.preventDefault();
                        var treeData = tree.jstree('get_json', -1, [], []);
                        $.post($(this).attr('href'), {'treeData': treeData}, function(data) {
                            modal.html("");
                            modal.modal('hide');
                        });
                    });
                })
                .jstree({
                    'plugins' : [ 'themes', 'html_data', 'json_data', 'dnd']
                });
            modal.modal('show');
        });
    });

    // Confirm actions
    $('*[data-confirm="confirm"]').on('click', function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        var confirmButton = $('<a>').addClass('btn btn-large btn-primary').html($(this).data('confirm_confirm'));
        confirmButton.on('click', function(event) {
            event.preventDefault();
            window.location.href = href;
        })
        var modal = $('#modal');
        var modalHeader = $('<div>').addClass('modal-header')
            .append($('<a>').addClass('close').attr('data-dismiss', 'modal').html('x'))
            .append($('<h3>').html($(this).data('confirm_title')));
        var modalBody = $('<div>').addClass('modal-header')
            .append($('<p>').html($(this).data('confirm_body')));
        var modalFooter = $('<div>').addClass('modal-footer')
            .append($('<a>').addClass('btn btn-large').attr('data-dismiss', 'modal').html($(this).data('confirm_cancel')))
            .append(confirmButton);
        modal.append(modalHeader).append(modalBody).append(modalFooter);

        modal.modal('show');
    });

});
