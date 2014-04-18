<?php
namespace module\node_file;

use system\Main;
use system\exceptions\InternalError;
use system\model2\RecordsetInterface;
use system\utils\File;

class NodeFileModule {
  /**
   * Implements controller event onDelete()
   */
  public static function onDelete(RecordsetInterface $recordset) {
    switch ($recordset->getTable()->getName()) {
      case 'file':
      case 'file_version':
        unlink($recordset->path);
        break;
    }
  }
  
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

      'teaser-large' => array(get_class(), 'imageVersion')
    );
    
    return $handlers;
  }
  
  public static function imageVersion($version, $targetFilePath, RecordsetInterface $nodeFile) {
    switch ($version) {
      case 'thumb':
        return self::imageVersionFixedSizes('60-60', $targetFilePath, $nodeFile);
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
      case 'teaser-large':
        return self::imageVersionFixedSizes('680-120', $targetFilePath, $nodeFile);
        break;
      default:
        throw new InternalError('Unknown image version <em>@version</em>', array('@version' => $version));
    }
  }
  
  public static function imageVersionFixedSizes($version, $targetFilePath, RecordsetInterface $nodeFile) {
    list($x, $y) = \explode('-', $version);
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
      'image' => Main::modulePathRel('node_file', 'img/icon/image-x-generic.png'),
      'audio' => Main::modulePathRel('node_file', 'img/icon/audio-x-generic.png'),
      'video' => Main::modulePathRel('node_file', 'img/icon/video-x-generic.png'),
      'document' => Main::modulePathRel('node_file', 'img/icon/application-octet-stream.png'),
      'unknown' => Main::modulePathRel('node_file', 'img/icon/application-octet-stream.png'),
    );
  }
}