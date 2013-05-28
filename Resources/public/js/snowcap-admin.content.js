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
        }
    });

    return {
        'Modal': Modal
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
})(jQuery);