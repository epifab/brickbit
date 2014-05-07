<?php
/**
 * @param string $uploaderId ID (fileKey)
 * @param string $uploaderFileListUrl $this->api->vpath('content/' . $form->getRecordset('node')->id . '/file/' . $uploaderId))
 * @param string $uploaderUrl URL $this->api->vpath('content/' . $form->getRecordset('node')->id . '/file/' . $uploaderId . '/upload')
 */
?>

<div class="dataedit fileupload"
  id="fileupload-<?php echo $uploaderId; ?>"
  data-filekey="<?php echo $uploaderId; ?>"
  data-filekeyurl="<?php echo $uploaderFileListUrl; ?>"
  style="border: 10px solid red">
  <fieldset>
    <legend><?php echo $uploaderId; ?></legend>
    <div class="row de-row">
      <div class="de-input-wrapper" style="background-color: #fff">
        <input type="hidden" name="system[requestId]" value="<?php echo $system['component']['requestId']; ?>"/>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel upload</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle">
                <!-- The global file processing state -->
                <span class="fileupload-process"></span>
            </div>
            <!-- The global progress state -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress state -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
      </div>
    </div>
  </fieldset>
</div>
