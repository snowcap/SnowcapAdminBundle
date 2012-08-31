CKEDITOR.editorConfig = function( config )
{
    config.entities = false;
    config.language = $('html').attr('lang');
    config.skin = 'BootstrapCK-Skin';
    config.toolbar_Full = [
        //['PasteText', 'Bold', 'Italic', 'RemoveFormat', 'NumberedList', 'BulletedList', 'Outdent','Indent', 'CreateDiv', 'Blockquote', 'Link', 'Unlink', 'HorizontalRule', 'Image', 'Table', 'Templates', 'Styles', 'Source']
        ['PasteText', 'Bold', 'Italic', 'RemoveFormat', 'NumberedList', 'BulletedList', 'Outdent','Indent', 'Blockquote', 'Link', 'Unlink', 'HorizontalRule', 'Image', 'Styles', 'Source']
    ];
    config.forcePasteAsPlainText = true;
    config.startupOutlineBlocks = true;
    config.stylesSet = 'default:/bundles/snowcapadmin/vendor/ckeditor/plugins/styles/styles/default.js';
    config.contentsCss = '/bundles/snowcapadmin/vendor/ckeditor/contents.css';
    config.templates_files = ['/bundles/snowcapadmin/vendor/ckeditor/plugins/templates/templates/default.js'];
    config.filebrowserBrowseUrl = '/admin/wysiwyg/browser';
    config.filebrowserImageWindowWidth = '960';
    config.filebrowserImageWindowHeight = '720';
};