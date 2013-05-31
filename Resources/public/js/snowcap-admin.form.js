SnowcapAdmin.Form = (function() {
    var Collection = Backbone.View.extend({
        $container: null,
        dataPrototype: null,
        events: {
            'click .add-element': 'addElement',
            'click *[data-admin=form-collection-add]': 'addElement',
            'click .remove-element': 'removeElement',
            'click [data-admin=form-collection-remove]': 'removeElement'
        },
        initialize: function() {
            this.$widget = this.$el.find('[data-prototype]');
            this.dataPrototype = this.$widget.data('prototype');
            this.on('form:collection:add', this.observeAddedForm);
        },
        removeElement: function(event) {
            event.preventDefault();
            var $target = $(event.currentTarget);
            var $parentCollectionItem = $target.parents('[data-admin=form-collection-item]');
            if($parentCollectionItem.length > 0) {
                $parentCollectionItem.remove();
            }
            else {
                $target.parent().remove();
            }
        },
        addElement: function(event) {
            event.preventDefault();
            var $form = $($.trim(this.dataPrototype.replace(/__name__/g, this.$widget.children().length)));
            this.$widget.append($form);
            this.trigger('form:collection:add', $form);
        },
        observeAddedForm: function($form) {
            $form.find('[data-admin=content-autocomplete]').each(function(offset, autocompleteContainer) {
                new SnowcapAdmin.Content.Autocomplete({el: $(autocompleteContainer)});
            });
        }
    });

    return {
        'Collection': Collection
    }
})();

(function($) {
    /**
     * Init collections
     */
    $('[data-admin=form-collection-container]').each(function(offset, container) {
        new SnowcapAdmin.Form.Collection({el: $(container)});
    });
})(jQuery);