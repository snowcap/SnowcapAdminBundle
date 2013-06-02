SnowcapAdmin.Content = (function() {
    /**
     * Admin modal form view
     *
     */
    var Modal = SnowcapBootstrap.Modal.extend({
        initialize: function() {
            SnowcapBootstrap.Modal.prototype.initialize.apply(this);
            this.off('modal:render');
            this.on('modal:render', SnowcapAdmin.Form.collectionFactory);
            this.on('modal:render', SnowcapAdmin.Form.textAutocompleteFactory);
            this.on('modal:render', SnowcapAdmin.Form.autocompleteFactory);
        }
    });

    /**
     * Modal factory function
     *
     * @param $context
     */
    var modalFactory = function() {
        var $context = (0 === arguments.length) ? $('body') : arguments[0];
        $context.find('[data-admin=content-modal]').each(function(offset, modalTrigger) {
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
    };

    return {
        Modal: Modal,
        modalFactory: modalFactory
    }
})();

(function($) {

    SnowcapAdmin.Content.modalFactory();

})(jQuery);