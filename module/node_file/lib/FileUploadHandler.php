<?php
namespace module\node_file\lib;

use system\Main;
use stdClass;

class FileUploadHandler {
  private $jqueryFileUploadHandler;
  
  public function __construct($fileName, $options = null) {
    $this->jqueryFileUploadHandler = new _JQueryFileUploadHandler($fileName, $options);
  }
  
  /**
   * Post file
   */
  public function post() {
    return $this->jqueryFileUploadHandler->post(false);
  }
}

class _JQueryFileUploadHandler extends JQueryFileUploadHandler {
  private $file_name;
  
  public function __construct($file_name, $options) {
    parent::__construct($options, false); // Force to not initialize
    $this->file_name = $file_name;
  }

  public function post($print_response = false) {
    $upload = isset($_FILES[$this->options['param_name']])
      ? $_FILES[$this->options['param_name']] : null;
    
    $file_name = $this->file_name;
    
    // Parse the Content-Range header, which has the following form:
    // Content-Range: bytes 0-524287/2000000
    $content_range = $this->get_server_var('HTTP_CONTENT_RANGE')
      ? preg_split('/[^0-9]+/', $this->get_server_var('HTTP_CONTENT_RANGE'))
      : null;
    
    $size = $content_range ? $content_range[3] : null;
    $files = array();
    if ($upload && is_array($upload['tmp_name'])) {
      // param_name is an array identifier like "files[]",
      // $_FILES is a multi-dimensional array:
      foreach ($upload['tmp_name'] as $index => $value) {
        $files[] = $this->handle_file_upload(
          $upload['tmp_name'][$index],
          $file_name ? $file_name : $upload['name'][$index],
          $size ? $size : $upload['size'][$index],
          $upload['type'][$index],
          $upload['error'][$index],
          $index,
          $content_range
        );
      }
    }
    else {
      // param_name is a single object identifier like "file",
      // $_FILES is a one-dimensional array:
      $files[] = $this->handle_file_upload(
        isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
        $file_name ? $file_name : (isset($upload['name']) ? $upload['name'] : null),
        $size ? $size : (isset($upload['size']) ? $upload['size'] : $this->get_server_var('CONTENT_LENGTH')),
        isset($upload['type']) ? $upload['type'] : $this->get_server_var('CONTENT_TYPE'),
        isset($upload['error']) ? $upload['error'] : null,
        null,
        $content_range
      );
    }
    return $files;
  }
  
  protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {
    $file = new stdClass();
    $file->name = $this->get_file_name($uploaded_file, $name, $size, $type, $error, $index, $content_range);
    $file->size = $this->fix_integer_overflow(intval($size));
    $file->type = $type;
    if ($this->validate($uploaded_file, $file, $error, $index)) {
      $this->handle_form_data($file, $index);
      $upload_dir = $this->get_upload_path();
      if (!is_dir($upload_dir)) {
        mkdir($upload_dir, $this->options['mkdir_mode'], true);
      }
      $file_path = $this->get_upload_path($file->name);
      $append_file = $content_range && is_file($file_path) &&
              $file->size > $this->get_file_size($file_path);
      if ($uploaded_file && is_uploaded_file($uploaded_file)) {
        // multipart/formdata uploads (POST method uploads)
        if ($append_file) {
          file_put_contents(
                  $file_path, fopen($uploaded_file, 'r'), FILE_APPEND
          );
        } else {
          move_uploaded_file($uploaded_file, $file_path);
        }
      } else {
        // Non-multipart uploads (PUT method support)
        file_put_contents(
                $file_path, fopen('php://input', 'r'), $append_file ? FILE_APPEND : 0
        );
      }
      $file_size = $this->get_file_size($file_path, $append_file);
      if ($file_size === $file->size) {
        $file->url = $this->get_download_url($file->name);
      } else {
        $file->incomplete = true;
        $file->size = $file_size;
        if (!$content_range && $this->options['discard_aborted_uploads']) {
          unlink($file_path);
          $file->error = $this->get_error_message('abort');
        }
      }
    }
    return $file;
  }
}
