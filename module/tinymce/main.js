ciderbit.setBehavior('tinymce', function() {
  tinymce.init({
      theme: 'modern',
      skin: 'light',

      selector: "textarea",
      plugins: [
          "advlist autolink lists link image charmap print preview anchor",
          "searchreplace visualblocks code fullscreen",
          "insertdatetime media table contextmenu paste"
      ],
      menubar: "",
      toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fullscreen"
  });
////  $('textarea.wysiwyg').tinymce({
//    // Location of TinyMCE script
////      script_url : '/module/tinymce/3.5.4.1//jscripts/tiny_mce/tiny_mce.js',
//
//    width: 600,
//    height: 300,
//
//    // General options
//    theme : "advanced",
//
//    content_css : "css/pstyle.css",
//
//    plugins : "youtubeIframe,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
//    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,|,bullist,numlist,|,blockquote,|,link,unlink,image,youtubeIframe,code",
////		theme_advanced_buttons3 : "undo,redo,|,hr,removeformat,|,sub,sup,|,charmap,|,print,|,ltr,rtl,|,insertdate,inserttime",
////		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect", //fontselect,fontsizeselect",
////		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,image,youtubeIframe,code",
////		theme_advanced_buttons3 : "undo,redo,|,hr,removeformat,|,sub,sup,|,charmap,|,print,|,ltr,rtl,|,insertdate,inserttime",
////				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
//
//    theme_advanced_toolbar_location : "top",
//    theme_advanced_toolbar_align : "left",
//    theme_advanced_statusbar_location : "bottom",
//    theme_advanced_resizing : true
//  });
})