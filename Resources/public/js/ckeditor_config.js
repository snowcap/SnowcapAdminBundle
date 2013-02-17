CKEDITOR.editorConfig = function( config )
{
    config.entities = false;
    config.language = $('html').attr('lang');
    config.toolbar_Basic = [
        //['PasteText', 'Bold', 'Italic', 'RemoveFormat', 'NumberedList', 'BulletedList', 'Outdent','Indent', 'CreateDiv', 'Blockquote', 'Link', 'Unlink', 'HorizontalRule', 'Image', 'Table', 'Templates', 'Styles', 'Source']
        ['Format', 'Bold', 'Italic', 'NumberedList', 'BulletedList', 'Link', 'Unlink', '-', 'RemoveFormat', 'PasteText', 'Source']
    ];
    config.format_tags = 'h2;h3;p';
    config.toolbar= 'Basic';
    config.forcePasteAsPlainText = true;
    config.startupOutlineBlocks = true;
    config.filebrowserBrowseUrl = '/admin/wysiwyg/browser';
    config.filebrowserImageWindowWidth = '960';
    config.filebrowserImageWindowHeight = '720';
};