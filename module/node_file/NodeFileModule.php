<?php
namespace module\node_file;

use system\Main;
use system\Module;
use system\model2\RecordsetInterface;
use system\utils\File;

class NodeFileModule {
  
  /**
   * Implements controller event imageVersionHandlers()
   */
  public static function imageVersionHandlers() {
    $handlers = array(
      'thumb' => array(get_class(), 'imageVersion'),
      'xsmall' => array(get_class(), 'imageVersion'),
      'small' => array(get_class(), 'imageVersion'),
      'medium' => array(get_class(), 'imageVersion'),
      'large' => array(get_class(), 'imageVersion'),
      'xlarge' => array(get_class(), 'imageVersion'),
    );
    
    return $handlers;
  }
  
  public static function imageVersion($version, $targetFilePath, RecordsetInterface $nodeFile) {
    switch ($version) {
      case 'thumb':
        return self::imageVersionFixedSizes('60x60', $targetFilePath, $nodeFile);
        break;
      case 'xsmall':
        return self::imageVersionFixedWidth('60-Y', $targetFilePath, $nodeFile);
        break;
      case 'small':
        return self::imageVersionFixedWidth('120-Y', $targetFilePath, $nodeFile);
        break;
      case 'medium':
        return self::imageVersionFixedWidth('240-Y', $targetFilePath, $nodeFile);
        break;
      case 'large':
        return self::imageVersionFixedWidth('480-Y', $targetFilePath, $nodeFile);
        break;
      case 'xlarge':
        return self::imageVersionFixedWidth('960-Y', $targetFilePath, $nodeFile);
        break;
    }
  }
  
  public static function imageVersionFixedSizes($version, $targetFilePath, RecordsetInterface $nodeFile) {
    list($x, $y) = \explode('x', $version);
    File::saveImageFixedSize($nodeFile->file->path, $targetFilePath, $x, $y);
  }
  
  public static function imageVersionFixedWidth($version, $targetFilePath, RecordsetInterface $nodeFile) {
    list($x, ) = \explode('-', $version);
    File::saveImage($nodeFile->file->path, $targetFilePath, $x);
  }
  
  public static function imageVersionFixedHeight($version, $targetFilePath, RecordsetInterface $nodeFile) {
    list(, $y) = \explode('-', $version);
    File::saveImage($nodeFile->file->path, $targetFilePath, 0, $y);
  }
  
  public static function fileTypeIcons() {
    return array(
      'image' => Module::getPath('node_file', 'img/icon/image-x-generic.png'),
      'audio' => Module::getPath('node_file', 'img/icon/audio-x-generic.png'),
      'video' => Module::getPath('node_file', 'img/icon/video-x-generic.png'),
      'document' => Module::getPath('node_file', 'img/icon/application-octet-stream.png'),
      'unknown' => Module::getPath('node_file', 'img/icon/application-octet-stream.png'),
    );
  }
}