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
            "stylesSet": 'my_styles:' + $(wysiwyg).attr('data-stylefileurl'),
            "contentsCss": $(wysiwyg).attr('data-cssfileurl')
        }, wysiwygConfig);
        $('.widget-wysiwyg').ckeditor(function () {
        }, thisConfig);
    });


});
