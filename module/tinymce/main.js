ciderbit.setBehavior('tinymce', function() {
  profiles = ciderbit.data('tinymce');
  
  for (i in profiles) {
    tinymce.init(profiles[i]);
  }
});
