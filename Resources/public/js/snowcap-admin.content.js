SnowcapAdmin.Content = (function() {
    var Modal = SnowcapBootstrap.Modal.extend({
        $form: null,
        events: function() {
            return _.extend(SnowcapBootstrap.Modal.prototype.events, {
                'submit form': 'submitForm',
                'click a[data-admin=content-delete]': 'delete'
            });
        },
        initialize: function() {
            SnowcapBootstrap.Modal.prototype.initialize.apply(this);
        },
        submitForm: function(event) {
            event.preventDefault();
            var $form = this.$el.find('form');
            $.post($form.attr('action'), $form.serialize(), null, "json")
                .done(_.bind(this.done, this))
                .fail(_.bind(this.fail, this));
        },
        delete: function(event) {
            event.preventDefault();
            var $link = $(event.currentTarget);
            $.getJSON($link.attr('href'))
                .done(_.bind(function(data) {
                    this.$el.html(data.content);
                }, this));
        },
        done: function(data) {
            this.close();
            this.trigger('content:modal:success', data.result);
        },
        fail: function(jqXHR, textStatus) {
            switch(jqXHR.status) {
                case 301: // REDIRECTION
                    var response = JSON.parse(jqXHR.responseText);
                    window.location.href = response.redirect_url;
                    break;
                default:
                    console.log(jqXHR);
                    console.log(textStatus);
                    break;
            }
        },
        postRender: function() {
            SnowcapBootstrap.Modal.prototype.postRender.apply(this);
            this.$('[data-admin=content-autocomplete]').each(function(offset, autocompleteContainer) { // TODO: a better way ?
                new SnowcapAdmin.Content.Autocomplete({el: $(autocompleteContainer)});
            });
        }
    });

    var Autocomplete = Backbone.View.extend({
        $textInput: null,
        $valueInput: null,
        listUrl: null,
        mode: null,
        labels: [],
        mapped: {},
        events: {
            'click a[data-admin=content-add]': 'add'
        },
        /**
         * Autocomplete widget init
         *
         */
        initialize: function () {
            this.$el.css('position', 'relative');
            this.$textInput = this.$el.find('input[type=text]');
            this.$valueInput = this.$el.find('input[type=hidden]');
            this.listUrl = this.$el.data('options-url');
            this.mode = this.$el.data('options-mode');

            // Initialize typeahead
            this.$textInput.typeahead({
                source: _.bind(this.source, this),
                minLength: 3,
                matcher: _.bind(this.matcher, this),
                updater: _.bind(this.updater, this)
            });
            // Handle focus / blur
            this.$textInput.on('focus', function(){
                $(this).data('prev', $(this).val());
                $(this).val('');
            }).on('blur', function(){
                if($(this).val() === '') {
                    $(this).val($(this).data('prev'));
                }
            });
            // Remove associations
            if('multiple' === this.mode) {
                $('ul.tokens').on('click', 'a[rel=remove]', _.bind(function(event) {
                    event.preventDefault();
                    var value = $(this).parent('li').data('value');
                    $(this).parent('li').remove();
                    this.$el.find('input[value=' + value + ']').remove();
                }, this));
            }
        },

        source: function(query, process) {
            console.log(query);
            var replacedUrl = this.listUrl.replace('__query__', query);
            $.getJSON(replacedUrl, _.bind(function(data) {
                $.each(data.result, _.bind(function (i, item) {
                    this.mapped[item[1]] = item[0];
                    this.labels.push(item[1]);
                }, this));
                process(this.labels);
            }, this));
        },

        matcher: function(item) {
            var existingTokens = this.$el.find('.token span').map(function() {
                return $(this).html();
            });

            return -1 === $.inArray(item, existingTokens);
        },

        updater: function(item) {
            if('single' === this.mode) {
                this.$el.find('input[type=hidden]').val(this.mapped[item]).trigger('change');
                return item;
            }
            else {
                var prototype = this.$el.data('prototype');
                var $prototype = $(prototype.replace(/__name__/g, this.$el.find('input[type=hidden]').length));
                $prototype.val(this.mapped[item]);
                this.$el.prepend($prototype);
                $prototype.trigger('change');

                $token = $('<li>').addClass('token').html($('<span>').html(item)).append($('<a>').html('&times;').addClass('close').attr('rel', 'remove'));
                this.$el.find('.tokens').append($token);

                return "";
            }
        },

        add: function(event) {
            event.preventDefault();
            var $trigger = $(event.currentTarget);
            var contentModal = new SnowcapAdmin.Content.Modal({url: $trigger.attr('href')});
            contentModal.on('content:modal:success', _.bind(function(result){
                var
                    option = $('<option>'),
                    entity_id = result.entity_id,
                    entity_name = result.entity_name;
                if('single' === this.mode) {
                    // TODO refactor with autocomplete updater
                    this.$textInput.val(entity_name);
                    this.$valueInput.val(entity_id).trigger('change');
                }
                else {
                    var prototype = this.$el.data('prototype');
                    var $prototype = $(prototype.replace(/__name__/g, this.$el.find('input[type=hidden]').length));
                    $prototype.val(entity_id);
                    this.$el.prepend($prototype);
                    $prototype.trigger('change');

                    var $token = $('<li>').addClass('token').html($('<span>').html(entity_name)).append($('<a>').html('&times;').addClass('close').attr('rel', 'remove'));
                    this.$el.find('.tokens').append($token);
                }
            }, this));
        }
    });

    return {
        'Modal': Modal,
        'Autocomplete': Autocomplete
    }
})();

(function($) {
    /**
     * Observe datalist triggers and create datalist
     * instances on click
     *
     */
    $('[data-admin=content-modal]').each(function(offset, modalTrigger) {
        var $modalTrigger = $(modalTrigger);
        $($modalTrigger).on('click', function(event) {
            event.preventDefault();
            var options = {
                url: $modalTrigger.attr('href')
            };
            if($modalTrigger.data('options-modal-class')) {
                options.modalClass = $modalTrigger.data('options-modal-class');
            }
            new SnowcapAdmin.Content.Modal(options);
        });
    });

    $('[data-admin=content-autocomplete]').each(function(offset, autocompleteContainer) {
        new SnowcapAdmin.Content.Autocomplete({el: $(autocompleteContainer)});
    });
})(jQuery);