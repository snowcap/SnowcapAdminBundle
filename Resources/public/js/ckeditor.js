jQuery(document).ready(function ($) {

    // Helper function to get parameters from the query string.
    var getUrlParam = function (paramName) {
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i');
        var match = window.location.search.match(reParam);

        return (match && match.length > 1) ? match[1] : '';
    };
    var sendUrlToWysiwyg = function (element) {
        var _element = $(element);
        var funcNum = getUrlParam('CKEditorFuncNum');
        var img = _element.find('img');
        var fileUrl = '';
        if (img.attr('data-src').length > 0) {
            fileUrl = img.attr('data-src');
        } else {
            fileUrl = img.attr('src');
        }
        window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
        window.close();
    };

    $('.wysiwyg-browser .thumbnail').click(function (event) {
        event.preventDefault();
        sendUrlToWysiwyg(this);
    });

    var CKEDITOR_UPDATE_WARNING = false;
    CKEDITOR.on('instanceReady', function (ev) {
        if (!CKEDITOR_UPDATE_WARNING) {
            _.each(ev.editor.config.toolbar_Basic, function (value) {
                _.each(value, function (value) {
                    if (value === 'Image') {
                        alert('CKEDITOR compatibilty break: Please use "image2" instead of "Image" in your toolbar');
                        CKEDITOR_UPDATE_WARNING = true;
                    }
                })
            })
        }
    });

});
