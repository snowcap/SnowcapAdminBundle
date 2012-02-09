(function($) {
    var MarkdownPreviewer = function(element) {
        this.element = $(element);
        this.latestPreviewContent = "";
        var _this = this;

        /**
         * Creating the previewer element
         */
        previewid = "markdown_" + this.element.attr('id');
        this.element.before('<div id="' + previewid + '" class="markdown_previewer">markdown previewer</div>');
        this.previewElement = $('#' + previewid);

        /**
         * Checking every second if the content has changed: if yes, a little AJAX call to convert the content into markdown format and update the previewer
         */
        setInterval(function() {
            content = _this.element.val();
            if (_this.latestPreviewContent != content) {
                $.post('/admin/markdown', { content: content }, function(data) {
                    _this.previewElement.html(data);
                    _this.latestPreviewContent = _this.element.val();
                });
            }
        }, 1000);
    };


    var Slugger = function(element) {
        var _this = this;
        var _element = $(element);
        var _target;
        var _currentSlug = '';
        /**
         * Append a "lock" button to control slug behaviour (auto or manual)
         */
        this.appendLockButton = function() {
            _this.lockButton = $('<a>').attr('href', 'locked').html('locked');
            _this.lockButton.css({
                "position": "absolute",
                "right": 0,
                "bottom": "3px"
            });
            _this.lockButton.button({
                text: false,
                icons: {
                    primary: 'ui-icon-locked'
                }
            });
            _this.lockButton.click(function(event) {
                event.preventDefault();
                if (_this.lockButton.attr('href') === 'locked') {
                    _this.unlock();
                }
                else {
                    _this.lock();
                }
            });
            _element.after(_this.lockButton);
        };
        /**
         * Unlock the widget input (manual mode)
         *
         */
        this.unlock = function() {
            _element.removeClass('off');
            _this.lockButton.attr('href', 'unlocked');
            _element.removeAttr('readonly');
            _this.lockButton.button('option', 'icons', {
                primary: 'ui-icon-unlocked'
            });
        };
        /**
         * Lock the widget input (auto mode)
         */
        this.lock = function() {
            if (confirm("Warning ! Locking this slug will override your changes")) {
                _element.addClass('off');
                _this.lockButton.attr('href', 'locked');
                if (_currentSlug !== '') {
                    _element.val(_currentSlug);
                }
                else {
                    _element.val(_this.makeSlug(_target.val()));
                }
                _element.attr('readonly', 'readonly');
                _this.lockButton.button('option', 'icons', {
                    primary: 'ui-icon-locked'
                });
            }
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
    $.fn.markdownPreviewer = function() {
        return this.each(function() {
            new MarkdownPreviewer(this);
        });
    };
    /**
     * Orderable grid constructor
     * 
     * @param DOMElement
     */
    var OrderableGrid = function(element) {
        var _this = this;
        var _element = $(element);
        var _grid = $('table', _element);
        var _orderForm = $('form', _element);
        _this.reorder = function() {
            _grid.find('.grid-drag a').each(function(offset, element){
                var updateField = $('#' + $(element).attr('href'));
                updateField.val($(element).parents('tr').index());
                
            });
        };
        _this.init = function() {
            _this.reorder();
            _orderForm.find('.grid-submit').hide();
            _grid.find('tbody').sortable({
                'handle': 'td.grid-drag a',
                'axis': 'y',
                'cursor': 'move',
                'update': function(event){
                    _this.reorder();
                    _orderForm.find('.grid-submit').show();
                }
            });
        };
        _this.init();
    };

    $.fn.orderableGrid = function() {
        return this.each(function() {
            new OrderableGrid(this);
        });
    };

    // DOMREADY
    $(document).ready(function(event) {
        // Icons / buttons
        $('button, .ui-button').each(function(offset, element) {
            var options = {};
            if ($(element).hasClass('ui-icon')) {
                var classes = $(element).attr('class').split(' ');
                $(element).removeClass('ui-icon');
                $.each(classes, function(offset, candidate) {
                    if (candidate.indexOf('ui-icon-') !== -1) {
                        options.icons = {
                            primary: candidate
                        };
                        $(element).removeClass(candidate);
                    }
                });
                if ($(element).hasClass('ui-button-icon-only')) {
                    options.text = false;
                }
            }
            $(element).button(options);
        });
        // Datepicker
        $('.widget-datepicker').datepicker({
            dateFormat: 'm/d/y'
        });
        // Slugs
        $('.widget-slug').slugger();
        // Markdown previewer
        $('.widget-markdown').markdownPreviewer();
        // Grids
        $('.grid-orderablecontent').orderableGrid();


        $('a.add_material').click(function(event) {
            event.preventDefault();
            var collectionHolder = $('#work_materials');
            var prototype = collectionHolder.attr('data-prototype');
            form = prototype.replace(/\$\$name\$\$/g, collectionHolder.children().length);
            collectionHolder.append(form);
        });

        $('a.add_image').click(function(event) {
            event.preventDefault();
            var collectionHolder = $('#page_images');
            var prototype = collectionHolder.attr('data-prototype');
            form = prototype.replace(/\$\$name\$\$/g, collectionHolder.children().length);
            collectionHolder.append(form);
        });

        $("a.confirm").click(function(e) {
           e.preventDefault();
           if(confirm('Are you sure you want to delete ?')) {
               window.location.href = $(this).attr("href");
           }
        });


	});
})(jQuery);
