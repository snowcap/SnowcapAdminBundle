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

    CKEDITOR.on('instanceReady', function (ev) {
        // Ends self closing tags the HTML4 way, like <br>.
        ev.editor.dataProcessor.htmlFilter.addRules(
            {
                elements:{
                    $:function (element) {
                        // Output dimensions of images as width and height
                        if (element.name == 'img') {
                            var style = element.attributes.style;

                            if (style) {
                                // Get the width from the style.
                                var match = /(?:^|\s)width\s*:\s*(\d+)px/i.exec(style),
                                    width = match && match[1];

                                // Get the height from the style.
                                match = /(?:^|\s)height\s*:\s*(\d+)px/i.exec(style);
                                var height = match && match[1];

                                if (width) {
                                    element.attributes.style = element.attributes.style.replace(/(?:^|\s)width\s*:\s*(\d+)px;?/i, '');
                                    element.attributes.width = width;
                                }

                                if (height) {
                                    element.attributes.style = element.attributes.style.replace(/(?:^|\s)height\s*:\s*(\d+)px;?/i, '');
                                    element.attributes.height = height;
                                }
                            }
                        }

                        if (!element.attributes.style)
                            delete element.attributes.style;

                        return element;
                    }
                }
            }
        );
    });

    /**
     * Common config options
     */
    var wysiwygConfig = {
        'language': $('html').attr('lang'),
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

    $('body.wysiwyg .thumbnail').click(function (event) {
        event.preventDefault();
        sendUrlToWysiwyg(this);
    });

});
