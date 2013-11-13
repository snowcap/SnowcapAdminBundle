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

});
