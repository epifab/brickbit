<?php
namespace module\core\lib;

class CBUploaderHandler extends UploadHandler {
  protected function get_user_dir() {
    return \system\utils\Login::getLoggedUserId();
  }
//  protected function set_file_delete_properties($file) {
//    parent::set_file_delete_properties($file);
//    $file->delete_url = $this->options['script_url'] . 'delete/' . rawurlencode($file->name);
//  }
//  
//  protected function get_download_url($file_name, $version = null) {
//    if ($this->options['download_via_php']) {
//      $url = $this->options['script_url']
//            . $this->get_query_separator($this->options['script_url'])
//            . 'file=' . rawurlencode($file_name);
//      if ($version) {
//        $url .= '&version=' . rawurlencode($version);
//      }
//      return $url . '&download=1';
//    }
//    $version_path = empty($version) ? '' : rawurlencode($version) . '/';
//    return $this->options['upload_url'] . $this->get_user_path()
//          . $version_path . rawurlencode($file_name);
//  }
}