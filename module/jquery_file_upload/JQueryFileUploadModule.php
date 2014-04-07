<?php
namespace module\jquery_file_upload;

use system\Component;
use system\Main;

class JQueryFileUploadModule {
  public static function onRun(Component $component) {
    // css
    $component->addCss(Main::modulePathRel('jquery_file_upload', '9.5.6/css/jquery.fileupload.css'));
    $component->addCss(Main::modulePathRel('jquery_file_upload', '9.5.6/css/jquery.fileupload-ui.css'));
  }
}

