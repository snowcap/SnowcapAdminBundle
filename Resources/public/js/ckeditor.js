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
        }
        if(dialogName === 'table') {
            dialogDefinition.removeContents('advanced');
            dialogDefinition.getContents('info').get('txtBorder')['default'] = 1;
            dialogDefinition.getContents('info').get('txtBorder')['onLoad'] = function(a, b, c) {
                console.log(this.getInputElement());
            };
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
        wysiwygConfig.stylesSet = 'my_styles:' + $(wysiwyg).attr('data-stylefileurl');
        $('.widget-wysiwyg').ckeditor(function () {
        }, wysiwygConfig);
    });


});
