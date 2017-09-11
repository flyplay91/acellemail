$(document).ready(function() {
    tinymce.init({
        selector: '.full-editor',
        height: 500,
        convert_urls: false,
        remove_script_host: false,
        forced_root_block: "",
        plugins: [
          'table advlist autolink lists link image charmap print preview anchor fullpage',
          'searchreplace visualblocks code fullscreen textcolor',
          'insertdatetime media contextmenu paste code'
        ],
        toolbar: 'insertfile undo redo | fontselect | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media',
        content_css: [
          APP_URL+'/tinymce/skins/lightgray/content.fixed.css',
          APP_URL+'/css/all.css',
          APP_URL+'/css/page.css',
        ],

        external_filemanager_path:APP_URL+"/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : APP_URL+"/filemanager/plugin.min.js"}
    });

    tinymce.init({
        selector: '.email-editor',
        height: 500,
        convert_urls: false,
        remove_script_host: false,
        forced_root_block: "",
        plugins: [
          'table advlist autolink lists link image charmap print preview anchor fullpage',
          'searchreplace visualblocks code fullscreen textcolor',
          'insertdatetime media contextmenu paste code'
        ],
        toolbar: 'insertfile undo redo | fontselect | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media',
        content_css: [
          APP_URL+'/tinymce/skins/lightgray/content.fixed.css',
          APP_URL+'/css/email.css',
        ],

        external_filemanager_path:APP_URL+"/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : APP_URL+"/filemanager/plugin.min.js"}
    });

    tinymce.init({
        selector: '.clean-editor',
        height: 500,
        convert_urls: false,
        remove_script_host: false,
        forced_root_block: "",
        plugins: [
          'table advlist autolink lists link image charmap print preview anchor fullpage',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media contextmenu paste code textcolor'
        ],
        toolbar: 'insertfile undo redo | fontselect | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media',
        valid_elements : '*[*]',
        valid_children : "+body[style]",
        content_css: [
            APP_URL+'/tinymce/skins/lightgray/content.fixed.css',
        ],
        external_filemanager_path:APP_URL+"/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : APP_URL+"/filemanager/plugin.min.js"}
    });

    tinymce.init({
        selector: '.builder-editor',
        height: 300,
        convert_urls: false,
        remove_script_host: false,
        forced_root_block: "",
        plugins: [
          'table advlist autolink lists link image charmap print preview anchor fullpage',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media contextmenu paste code textcolor'
        ],
        toolbar: 'insertfile undo redo | fontselect | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media',
        valid_elements : '*[*]',
        valid_children : "+body[style]",
        content_css: [
          APP_URL+'/css/res_email.css',
          APP_URL+'/css/editor.css',
        ],
        external_filemanager_path:APP_URL+"/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : APP_URL+"/filemanager/plugin.min.js"}
    });
});
