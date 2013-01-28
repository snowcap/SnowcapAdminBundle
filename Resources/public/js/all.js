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
    var InlineEntityWidget = function (container) {

        var self = this;
        var container = $(container);
        var trigger = container.find('a[rel=add]');
        var modal = $('#modal');
        var select = container.find('select');
        //var selected = row.find(".selected");

        /**
         * Observe what's cooking in the add form
         *
         */
        self.observeAddForm = function () {
            modal.find('*[type=submit]').on('click', function (event) {
                event.preventDefault();
                var form = modal.find('form');
                $.post(form.attr('action'), form.serialize(), null, "json")
                    .success(function(data, textStatus, jqXHR) {
                        modal.html('');
                        var option = $('<option>');
                        option.val(data.val);
                        option.html(data.html);
                        select.append(option);
                        select.val(data.val);
                        modal.modal('hide');
                    })
                    .error(function(data, textStatus, jqXHR) {
                        console.log(data);
                        alert('An error has occured');
                    });
            });
        };
        /**
         * Add an item to the selection
         *
         * @param DOMElement selectedItem
         */
        /*self.selectItem = function (selectedItem) {
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
        };*/
        /**
         * Remove an item from the selection (on click)
         *
         * @param DOMEvent event
         */
        /*self.removeSelection = function (event) {
            event.preventDefault();
            var entityId = $(this).prev('.identity').attr('href');
            $(this).parent().remove();
            select.find("option[value='" + entityId + "']").removeAttr('selected');
            if (selected.find('li').length === 0) {
                row.find('.empty').show();
            }
        };*/
        /**
         * Inline widget init
         *
         */
        self.init = function () {
            // Observe autocomplete field
            /*row.find('.autocomplete').keyup(function (event) {
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
            });*/
            // Observe existing selections and hide empty text
            /*selected.find('li a.identity').click(function(event){event.preventDefault()});
            selected.find('li a.close').click(self.removeSelection);
            if(selected.find('li').length !== 0) {
                row.find('.empty').hide();
            }*/
            // Hide autocomplete results on body click
            /*$('body').click(function (event) {
                row.find('.autocomplete-results').hide();
            });*/
            // Observe the "create" button
            trigger.click(function (event) {
                event.preventDefault();
                $.get($(this).attr('href'), function (data) {
                    modal.html(data);
                    self.observeAddForm();
                    modal.modal('show');
                });
            });
        };
        self.init();
    };

    /**
    * Autocomplete widget
    *
    * @param DOMElement row
    */
    var AutocompleteWidget = function (container) {
        var container = $(container);
        var listUrl = container.data('list-url');

        var textInput = container.find('input[type=text]');

        var labels, mapped;

        textInput.focus(function(e){
            $(this).data('prev', $(this).val());
            $(this).val('');
        }).blur(function(e){
            if($(this).val() === '') {
                $(this).val($(this).data('prev'));
            }
        });

        textInput.typeahead({
            source: function(query, process) {
                var replacedUrl = listUrl.replace('__query__', query);
                $.getJSON(replacedUrl, function(data) {
                    labels = []
                    mapped = {}
                    $.each(data, function (i, item) {
                        mapped[item[1]] = item[0];
                        labels.push(item[1]);
                    })

                    process(labels);
                });
            },
            minLength: 3,
            updater: function(item) {
                container.find('input[type=hidden]').val(mapped[item]);
                return item;
            }
        });
    };

    // Slug
    $('.widget-slug').slugger();
    // Markdown
    $('.widget-markdown').markdownPreviewer();

    // Admin entity widgets
    $('[data-admin=form-type-entity]').each(function (offset, container) {
        new InlineEntityWidget(container);
    });

    // Autocomplete widgets
    $('[data-admin=form-type-autocomplete]').each(function (offset, container) {
        new AutocompleteWidget(container);
    });

    // autosize for textareas
    $('.catalogue-translation textarea').autosize();

});
