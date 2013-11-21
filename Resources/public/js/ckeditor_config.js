// Copy this file in your project and rename it as ckeditor/config.js
CKEDITOR.editorConfig = function( config )
{
    config.entities = false;
    config.language = $('html').attr('lang');

    // Override the toolbar as you needed
    config.toolbar= 'Basic';
    config.toolbar_Basic = [
        //['PasteText', 'Bold', 'Italic', 'RemoveFormat', 'NumberedList', 'BulletedList', 'Outdent','Indent', 'JustifyLeft', 'JustifyRight', 'JustifyCenter', 'JustifyBlock', 'CreateDiv', 'Blockquote', 'Link', 'Unlink', 'HorizontalRule', 'image2', 'Table', 'Templates', 'Styles', 'Source']
        ['Format', 'Bold', 'Italic', 'NumberedList', 'BulletedList', 'Link', 'Unlink', 'image2', '-', 'RemoveFormat', 'PasteText', 'Templates', 'ShowBlocks', 'Source', 'Maximize']
    ];

    // Add the tags you want in the format selector
    config.format_tags = 'h2;h3;h4;p';
    config.forcePasteAsPlainText = true;

    // Display by default the outline blocks
    config.startupOutlineBlocks = false;

    // Configuration for file and image browser
    config.filebrowserBrowseUrl = '/admin/wysiwyg/browser';
    config.filebrowserImageWindowWidth = '960';
    config.filebrowserImageWindowHeight = '720';

    // Configuration to apply css in the wysiwyg
    //config.contentsCss = ['/bundles/youprojectsite/css/normalize.css', '/bundles/yourprojectsite/css/page.css'];

    // Display the content as a full page with html tags
    config.fullPage = false;

    // Overrides allowed content (see http://docs.ckeditor.com/#!/guide/dev_allowed_content_rules)
    config.allowedContent = null;

    // Allows you to add extra allowed values
    // without overriding previous settings in allowContent
    config.extraAllowedContent = null;

    // Use this below if you want to provide custom templates
    // see an example in Resources/public/vendor/ckeditor/plugins/templates/templates/default.js
    //config.templates_files = ['/bundles/snowcapadmin/ckeditor/plugins/templates/templates/default.js', '/bundles/yourprojectadmin/ckeditor/templates.js'];
    //config.templates = 'default';


};

