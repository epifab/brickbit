<?php 
//echo $recordset->id . ' - ' . $recordset->type;
//echo '<code>' . print_r($_SESSION['system']['forms']['edit-node']) . '</code>';
/*
<form class="dataedit" id="fileupload" action="<{path url="file/upload"}>" method="POST" enctype="multipart/form-data">
  <fieldset>
    <legend>Attachement</legend>
    <div class="de-row">
      <div class="de-cell">
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
          <div class="span7">
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="btn btn-success fileinput-button">
              <i class="icon-plus icon-white"></i>
              <span>Add files...</span>
              <input type="file" name="files[]" multiple>
            </span>
            <button type="submit" class="btn btn-primary start">
              <i class="icon-upload icon-white"></i>
              <span>Start upload</span>
            </button>
            <button type="reset" class="btn btn-warning cancel">
              <i class="icon-ban-circle icon-white"></i>
              <span>Cancel upload</span>
            </button>
            <button type="button" class="btn btn-danger delete">
              <i class="icon-trash icon-white"></i>
              <span>Delete</span>
            </button>
            <input type="checkbox" class="toggle">
          </div>
          <!-- The global progress information -->
          <div class="span5 fileupload-progress fade">
            <!-- The global progress bar -->
            <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
              <div class="bar" style="width:0%;"></div>
            </div>
            <!-- The extended global progress information -->
            <div class="progress-extended">&nbsp;</div>
          </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div classs="fileupload-loading"></div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
      </div>
    </div>
  </fieldset>
</form>
*/ ?>

<script type="text/javascript">
$(function() {
  brickbit.setBehavior('plupload', function() {
    $('#<?php echo $system['component']['requestId']; ?>-plupload-image').each(function() {
      var id = $(this).attr('id');
      var title = $(this).attr('title');

      $('#' + id).pluploadQueue({
        // General settings
        runtimes : 'html5,flash',
        url : '/content/<?php echo $form->getRecordset('node')->id; ?>/file/' + title + '/upload',
        max_file_size : '50mb',
        chunk_size : '1mb',
        unique_names : true,

        // Resize images on clientside if we can
        //resize : {width : 320, height : 240, quality : 90},

        // Specify what files to browse for
    //    filters : [
    //      {title : "Image files", extensions : "jpg,gif,png"},
    //      {title : "Pdf files", extensions : "pdf"},
    //      {title : "Audio files", extensions : "mp3"},
    //      {title : "Video files", extensions : "avi,wmv"},
    //      {title : "Zip files", extensions : "zip"}
    //    ],
  //      init : {
  //        FilesAdded: function(up, files) {
  //        },
  //        FilesRemoved: function(up, files) {
  //        }
  //      },
    //    multi_selection: false,
        // Flash settings
        flash_swf_url : '<?php echo $this->api->modulePath('core', 'js/plupload/js/plupload.flash.swf'); ?>',

        // Silverlight settings
        silverlight_xap_url : '<?php echo $this->api->modulePath('core', 'js/plupload/js/plupload.silverlight.xap'); ?>'
      })
    });
  });
});
</script>

<div id="<?php echo $system['component']['requestId']; ?>-plupload-image" class="plupload" title="image"></div>

<?php /*
<form class="dataedit" id="fileupload" action="<?php echo $this->api->vpath("file/upload"); ?>" method="POST" enctype="multipart/form-data">
  <fieldset>
    <legend><?php echo $this->api->t("Image"); ?></legend>
    <div class="de-row">
      <div class="de-label-wrapper">
        <span class="de-label"><?php echo $this->api->t("Image"); ?></span>
      </div>
      <div class="de-input-wrapper">
        <input type="hidden" name="system[requestId]" value="<?php echo $system['component']['requestId']; ?>"/>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
          <div class="span7">
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="btn btn-success fileinput-button">
              <i class="icon-plus icon-white"></i>
              <span>Add file...</span>
              <input type="file" name="files[]" multiple>
            </span>
            <button type="submit" class="btn btn-primary start">
              <i class="icon-upload icon-white"></i>
              <span>Start upload</span>
            </button>
            <button type="reset" class="btn btn-warning cancel">
              <i class="icon-ban-circle icon-white"></i>
              <span>Cancel upload</span>
            </button>
          </div>
          <!-- The global progress information -->
          <div class="span5 fileupload-progress fade">
            <!-- The global progress bar -->
            <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
              <div class="bar" style="width:0%;"></div>
            </div>
            <!-- The extended global progress information -->
            <div class="progress-extended">&nbsp;</div>
          </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div classs="fileupload-loading"></div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
      </div>
    </div>
  </fieldset>
</form>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
  <td class="preview"><span class="fade"></span></td>
  <td class="name"><span>{%=file.name%}</span></td>
  <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
  {% if (file.error) { %}
      <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
  {% } else if (o.files.valid && !i) { %}
      <td>
    <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
      </td>
      <td class="start">{% if (!o.options.autoUpload) { %}
    <button class="btn btn-primary">
        <i class="icon-upload icon-white"></i>
        <span>Start</span>
    </button>
      {% } %}</td>
  {% } else { %}
      <td colspan="2"></td>
  {% } %}
  <td class="cancel">{% if (!i) { %}
      <button class="btn btn-warning">
    <i class="icon-ban-circle icon-white"></i>
    <span>Cancel</span>
      </button>
  {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
  {% if (file.error) { %}
      <td></td>
      <td class="name"><span>{%=file.name%}</span></td>
      <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
      <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
  {% } else { %}
      <td class="preview">{% if (file.thumbnail_url) { %}
    <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"/></a>
      {% } %}</td>
      <td class="name">
    <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
      </td>
      <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
      <td colspan="2"></td>
  {% } %}
  <td class="delete">
      <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
    <i class="icon-trash icon-white"></i>
    <span>Delete</span>
      </button>
  </td>
    </tr>
{% } %}
</script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="http://blueimp.github.com/JavaScript-Templates/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js"></script>
<!-- jQuery Image Gallery -->
<script src="http://blueimp.github.com/jQuery-Image-Gallery/js/jquery.image-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?php echo $this->api->path("js/jquery-file-upload/js/jquery.iframe-transport.js"); ?>"></script>
<!-- The basic File Upload plugin -->
<script src="<?php echo $this->api->path("js/jquery-file-upload/js/jquery.fileupload.js"); ?>"></script>
<!-- The File Upload file processing plugin -->
<script src="<?php echo $this->api->path("js/jquery-file-upload/js/jquery.fileupload-fp.js"); ?>"></script>
<!-- The File Upload user interface plugin -->
<script src="<?php echo $this->api->path("js/jquery-file-upload/js/jquery.fileupload-ui.js"); ?>"></script>
<!-- The File Upload jQuery UI plugin -->
<script src="<?php echo $this->api->path("js/jquery-file-upload/js/jquery.fileupload-jui.js"); ?>"></script>
*/ ?>
<?php $this->api->open('form', array('form' => $form)); ?>
  <div class="dataedit">
    <fieldset>
      <legend>
        <div class="legend">
          <div>Languages</div>
          <div class="">
            <?php foreach ($system['langs'] as $lang): ?>
            <a href="#" id="node-lang-<?php print $lang; ?>" class="node-lang-control show-hide-class<?php if ($lang == $website['defaultLang']): ?> expanded<?php endif; ?>">
              <img src="<?php print $this->api->themePath('img/lang/40/' . $lang . '.jpg'); ?>"/>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </legend>
      <?php foreach ($system['langs'] as $lang): ?>
      <div class="node-lang node-lang-<?php print $lang; ?>">
        <div class="de-row">
          <div class="de-label-wrapper">
            
          </div>
          <div class="de-input-wrapper">
            <?php echo $this->api->formInput(array(
              'recordset' => 'node',
              'widget' => 'checkbox',
              'name' => 'text_' . $lang . '.enable',
              'id' => 'edit-node-text_' . $lang . '-enable',
              'label' => $this->api->t('Enable for this lang'),
              'value' => isset($form->getRecordset('node')->texts[$lang])
            )); ?>
            <label for="edit-node-text_<?php print $lang; ?>-enable"><?php print $this->api->t("Content available for this language."); ?></label>
          </div>
        </div>
        <div id="node-lang-<?php print $lang; ?>-fields" class="edit-node-text_<?php print $lang; ?>-enable">
          <div class="de-row">
            <div class="de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'urn',
                'label' => $this->api->t('URN'),
              )); ?>
            </div>
            <div class="de-input-wrapper">
              <?php echo $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'urn',
                'widget' => 'textbox', 
                'attributes' => array('class' => 'xl')
              )); ?>
              <div class="de-info">
                <p>
                  <?php print $this->api->t("Once you choose a URN you shouldn't change it anymore."); ?><br/>
                  <?php print $this->api->t("In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself."); ?>
                  <?php print $this->api->t("Each word should be separeted by the dash characted."); ?>
                </p>
                <p>
                  <?php print $this->api->t("Please note also that two different contents, translated in @lang, must have two different URNs.", array('@lang' => $this->api->t($lang))); ?>
                </p>
              </div>
              <?php print $this->api->formInputError('text_' . $lang . '.urn'); ?>
            </div>
          </div>
          <div class="de-row">
            <div class="de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'description',
                'label' => $this->api->t('Description'),
              )); ?>
            </div>
            <div class="de-input-wrapper">
              <?php print $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'description',
                'attributes' => array('class' => 'xxl')
              )); ?>
              <div class="de-info">
                <p>
                  <?php print $this->api->t("The description is not directly shown to the user but it's used as a meta-data for search engines purposes."); ?>
                </p>
              </div>
              <?php print $this->api->formInputError('text_' . $lang . '.description'); ?>
            </div>
          </div>
          <div class="de-row">
            <div class="de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'title',
                'label' => $this->api->t('Title')
              )); ?>
            </div>
            <div class="de-input-wrapper">
              <?php print $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'title',
                'attributes' => array('class' => 'l')
              )); ?>
              <?php print $this->api->formInputError('text_' . $lang . '.title'); ?>
            </div>
          </div>
          <div class="de-row">
            <div class="de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'subtitle',
                'label' => $this->api->t('Subtitle')
              )); ?>
            </div>
            <div class="de-input-wrapper">
              <?php print $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'subtitle',
                'attributes' => array('class' => 'xl')
              )); ?>
              <?php print $this->api->formInputError('text_' . $lang . '.subtitle'); ?>
            </div>
          </div>
          <div class="de-row">
            <div class="de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'body',
                'label' => $this->api->t('Body')
              )); ?>
            </div>
            <div class="de-input-wrapper">
              <?php print $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'body',
                'attributes' => array('class' => 'xxl richtext wysiwyg')
              )); ?>
              <?php echo $this->api->formInputError('text_' . $lang . '.body'); ?>
            </div>
          </div>
          <div class="de-row">
            <div class="de-label-wrapper">
              <?php echo $this->api->formRSInputLabel(array(
                'recordset' => 'node_' . $lang,
                'path' => 'preview',
                'label' => $this->api->t('Preview')
              )); ?>
            </div>
            <div class="de-input-wrapper">
              <?php echo $this->api->formRSInput(array(
                'recordset' => 'node_' . $lang,
                'path' => 'preview',
                'attributes' => array('class' => 'xxl richtext wysiwyg')
              )); ?>
              <?php echo $this->api->formInputError('text_' . $lang . '.preview'); ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </fieldset>

    <fieldset>
      <legend><div class="legend"><?php echo $this->api->t('Tags'); ?></div></legend>
      <div class="de-row">
        <div class="de-label-wrapper">
          <label class="de-label" for="edit-node-terms"/>
            <?php echo $this->api->t('Tags'); ?>
          </label>
        </div>
        <div class="de-input-wrapper">
          <input type="text" class="de-input xl" name="node[tags]"/>
        </div>
      </div>
    </fieldset>
    <fieldset class="de-fieldset">
      <legend><div class="legend"><?php echo $this->api->t('Content access'); ?></div></legend>
      <div class="de-row">
        <div class="de-label-wrapper">
          <label class="de-label" for="edit-node-record_mode-users"><?php echo $this->api->t('Content administrators'); ?></label>
        </div>
        <div class="de-input-wrapper">
          <input class="de-input xl" type="text" name="node[record_mode.users]" id="edit-node-record_mode-users" value=""/>
          <?php 
//          echo $this->api->input(array(
//            'widget' => 'autocomplete',
//            'value' => array(),
//            'url' => 'autocomplete/user',
//            'item' => '<div><img src="@[image.url]"/>@[name]</div>',
//            'name' => 'users'
//          )); ?>
          <?php echo $this->api->formInputError("record_mode.users"); ?>
        </div>
      </div>
      <div class="de-row">
        <div class="de-label-wrapper">
          <label class="de-label" for="edit-node-record_mode-read_mode"><?php echo $this->api->t('Read access'); ?></label>
        </div>
        <div class="de-input-wrapper">
          <?php echo $this->api->formRSInput(array(
            'recordset' => 'node',
            'path' => 'record_mode.read_mode',
            'widget' => 'selectbox',
            'attributes' => array('class' => 'l')
          )); ?>
          <?php echo $this->api->formInputError("record_mode.read_mode"); ?>
        </div>
      </div>
      <div class="de-row">
        <div class="de-label-wrapper">
          <label class="de-label" for="edit-node-record_mode-edit_mode"><?php echo $this->api->t('Edit access'); ?></label>
        </div>
        <div class="de-input-wrapper">
          <?php echo $this->api->formRSInput(array(
            'recordset' => 'node',
            'path' => 'record_mode.edit_mode',
            'widget' => 'selectbox',
            'attributes' => array('class' => 'l', 'rows')
          )); ?>
          <?php echo $this->api->formInputError("record_mode.edit_mode"); ?>
        </div>
      </div>
      <div class="de-row">
        <div class="de-label-wrapper">
          <label class="de-label" for="edit-node-record_mode-delete_mode"><?php echo $this->api->t('Delete access'); ?></label>
        </div>
        <div class="de-input-wrapper">
          <?php echo $this->api->formRSInput(array(
            'recordset' => 'node',
            'path' => 'record_mode.delete_mode',
            'widget' => 'selectbox',
            'attributes' => array('class' => 'l')
          )); ?>
          <?php echo $this->api->formInputError("record_mode.delete_mode"); ?>
        </div>
      </div>
    </fieldset>
    <?php echo $this->api->formSubmitControl($this->api->t('Save')); ?>
  </div>
<?php $this->api->close(); ?>