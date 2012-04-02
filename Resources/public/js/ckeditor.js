jQuery(document).ready(function ($) {
    CKEDITOR.on('dialogDefinition', function (ev) {
        var dialogName = ev.data.name;
        var dialogDefinition = ev.data.definition;
        if (dialogName === 'link') {
            dialogDefinition.removeContents('advanced');
            var targetTab = dialogDefinition.getContents('target');
            var targetField = targetTab.get('linkTargetType');
            var infoTab = dialogDefinition.getContents('info');
            infoTab.add(targetField);
            dialogDefinition.removeContents('target');
        }
        if (dialogName === 'image') {
            dialogDefinition.removeContents('advanced');
            var infoTab = dialogDefinition.getContents('info');
        }
        if(dialogName === 'table') {
            // No need for the advanced tab
            dialogDefinition.removeContents('advanced');
            var infoTab = dialogDefinition.getContents('info');
            // Set border to 1 and hide field
            infoTab.get('txtBorder')['default'] = 1;
            infoTab.get('txtBorder').hidden = true;
            infoTab.remove('cmbAlign');
            infoTab.remove('txtCellSpace');
            infoTab.remove('txtCellPad');
        }
    });

    /**
     * Common config options
     */
    var wysiwygConfig = {
        'skin':'BootstrapCK-Skin',
        'toolbar_Full':[
            ['PasteText', 'Bold', 'Italic', 'RemoveFormat', 'NumberedList', 'BulletedList', 'Blockquote', 'Link', 'Unlink', 'Image', 'Table', 'Styles', 'Source']
        ],
        'forcePasteAsPlainText':true,
        'startupOutlineBlocks':true

    };

    /* Loop over each wysiwyg textarea */
    $('.widget-wysiwyg').each(function (offset, wysiwyg) {
        var thisConfig = $.extend({
            'stylesSet': 'my_styles:' + $(wysiwyg).attr('data-stylefileurl'),
            'contentsCss': $(wysiwyg).attr('data-cssfileurl'),
            'filebrowserBrowseUrl': $(wysiwyg).attr('data-browserurl'),
            'filebrowserImageWindowWidth': '960',
            'filebrowserImageWindowHeight': '720'
        }, wysiwygConfig);
        $('.widget-wysiwyg').ckeditor(function () {
        }, thisConfig);
    });

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
