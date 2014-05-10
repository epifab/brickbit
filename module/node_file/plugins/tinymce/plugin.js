brickbit.setBehavior('tinymceNodefile', function() {
  tinymce.PluginManager.add('nodefile', function(editor, url) {
      // Add a button that opens a window
      editor.addButton('nodefile', {
          icon: 'image',
          onclick: function() {
              // Open window
              editor.windowManager.open({
                  title: 'Node resource',
                  url: brickbit.data('nodeEditFilePluginUrl'),
                  width: 800,
                  height: 500,
                  onsubmit: function(e) {
                      // Insert content when the window form is submitted
                      editor.insertContent('Title: ' + e.data.title);
                  }
              });
          }
      });
  });  
});