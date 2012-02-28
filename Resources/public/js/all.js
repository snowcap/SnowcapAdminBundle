jQuery(document).ready(function ($) {

    var AddElementForm = function (element, collectionHolder) {
        var _this = this;
        var _element = $(element);

        // Get the data-prototype we explained earlier
        //var prototype = collectionHolder.attr('data-prototype');
        // Replace '$$name$$' in the prototype's HTML to
        // instead be a number based on the current collection's length.
        //form = prototype.replace(/\$\$name\$\$/g, collectionHolder.children().length);
        $.ajax('get_embeded_form/form_name/3')
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
            $(this).addElementForm(_element);
        });


    };
    $.fn.manageDataPrototype = function () {
        return this.each(function () {
            new ManageDataPrototype(this);
        });
    };

    var InlineWidget = function (trigger) {
        //$(trigger).siblings('select').hide();
        var modal = $('#modal');
        $(trigger).click(function (event) {
            event.preventDefault();

            $.get($(trigger).attr('href'), function (data) {
                modal.html(data.html);
                modal.find('$[type=submit]').live('click', function (event) {
                    event.preventDefault();
                    var form = modal.find('form');
                    var data = new FormData(form[0]);
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', form.attr('action'), true);
                    xhr.onload = function(e) {
                        if(this.status === 200){
                            var responseJSON = JSON.parse(this.response);
                            modal.html(responseJSON.html);
                        }
                        else if(this.status === 201){
                            var responseJSON = JSON.parse(this.response);
                            var inlineId = responseJSON.inline_id;
                            console.log(responseJSON);
                            modal.modal('hide');
                        }
                    };
                    xhr.send(data);
                    /*var form = modal.find('form');
                    $.post(form.attr('action'), form.formData(), function (data) {
                        modal.html(data.html);
                    });*/
                });
                modal.modal('show');
            });
        });
    };

    //TODO: create modals on demand
    $('#modal').modal({show:false});
    $('#modal').on('hidden', function(event){
       $(this).empty();
    });

    $('a[rel=inline]').each(function (offset, trigger) {
        new InlineWidget(trigger);
    });

    $('*[data-prototype]').manageDataPrototype();
});
