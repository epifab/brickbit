brickbit.setBehavior('tinymce', function() {
  profiles = brickbit.data('tinymce');
  
  for (i in profiles) {
    tinymce.init(profiles[i]);
  }
});
