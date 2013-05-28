SnowcapAdmin.Content = (function() {
    var Modal = SnowcapBootstrap.Modal.extend({
        $form: null,
        events: function() {
            return _.extend(SnowcapBootstrap.Modal.prototype.events, {
                'submit form': 'submitForm'
            });
        },
        initialize: function() {
            SnowcapBootstrap.Modal.prototype.initialize.apply(this);
        },
        submitForm: function(event) {
            event.preventDefault();
            var $form = this.$el.find('form');
            $.post($form.attr('action'), $form.serialize(), null, "json")
                .success(_.bind(function(data) {
                    this.close();
                    this.trigger('content:modal:success', data.result);
                }, this));
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