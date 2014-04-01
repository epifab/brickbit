<?php
namespace module\core\lib;

class UploadHandler extends JQueryFileUploadHandler {
  public function __construct($options) {
    parent::__construct($options, false); // Force to not initialize
  }
  
  public function post($print_response = false) {
    if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
      return $this->delete($print_response);
    }
    $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : null;
    // Parse the Content-Disposition header, if available:
    $file_name = $this->get_server_var('HTTP_CONTENT_DISPOSITION') ?
            rawurldecode(preg_replace(
                            '/(^[^"]+")|("$)/', '', $this->get_server_var('HTTP_CONTENT_DISPOSITION')
            )) : null;
    // Parse the Content-Range header, which has the following form:
    // Content-Range: bytes 0-524287/2000000
    $content_range = $this->get_server_var('HTTP_CONTENT_RANGE') ?
            preg_split('/[^0-9]+/', $this->get_server_var('HTTP_CONTENT_RANGE')) : null;
    $size = $content_range ? $content_range[3] : null;
    $files = array();
    if ($upload && is_array($upload['tmp_name'])) {
      // param_name is an array identifier like "files[]",
      // $_FILES is a multi-dimensional array:
      foreach ($upload['tmp_name'] as $index => $value) {
        $files[] = $this->handle_file_upload(
                $upload['tmp_name'][$index], $file_name ? $file_name : $upload['name'][$index], $size ? $size : $upload['size'][$index], $upload['type'][$index], $upload['error'][$index], $index, $content_range
        );
      }
    } else {
      // param_name is a single object identifier like "file",
      // $_FILES is a one-dimensional array:
      $files[] = $this->handle_file_upload(
              isset($upload['tmp_name']) ? $upload['tmp_name'] : null, $file_name ? $file_name : (isset($upload['name']) ?
                              $upload['name'] : null), $size ? $size : (isset($upload['size']) ?
                              $upload['size'] : $this->get_server_var('CONTENT_LENGTH')), isset($upload['type']) ?
                      $upload['type'] : $this->get_server_var('CONTENT_TYPE'), isset($upload['error']) ? $upload['error'] : null, null, $content_range
      );
    }
    return $files;
  }
  
  public function printResponse($content) {
    parent::generate_response($content);
  }
}