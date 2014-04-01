<?php
namespace module\node_file;

use system\Module;
use system\model2\RecordsetInterface;
use system\utils\File;

class NodeFileModule {
  /**
   * Implements controller event imageVersion()
   */
  public static function imageVersion($version, $fileName, RecordsetInterface $nodeFile) {
    switch ($version) {
      case 'thumb':
        return self::imageVersionFixedSizes('60x60', $fileName, $nodeFile);
        break;
      case 's':
        return self::imageVersionFixedW('120-Y', $fileName, $nodeFile);
        break;
      case 'm':
        return self::imageVersionFixedW('240-Y', $fileName, $nodeFile);
        break;
      case 'l':
        return self::imageVersionFixedW('480-Y', $fileName, $nodeFile);
        break;
      case 'xl':
        return self::imageVersionFixedW('960-Y', $fileName, $nodeFile);
        break;
    }
  }
  
  public static function imageVersionFixedSizes($version, $fileName, RecordsetInterface $nodeFile) {
    list($x, $y) = \explode('x', $version);
    File::saveImageFixedSize($nodeFile->file->path, $fileName, $x, $y);
  }
  
  public static function imageVersionFixedWidth($version, $fileName, RecordsetInterface $nodeFile) {
    list($x, ) = \explode('-', $version);
    File::saveImage($nodeFile->file->path, $fileName, $x);
  }
  
  public static function imageVersionFixedHeight($version, $fileName, RecordsetInterface $nodeFile) {
    list(, $y) = \explode('-', $version);
    File::saveImage($nodeFile->file->path, $fileName, 0, $y);
  }
  
  /**
   * Implements controller event imageVersionMakers()
   */
  public static function imageVersionMakers() {
    $makers = array(
      'thumb' => array(Module::getNamespace('core') . 'Core', 'imageVersion'),
      's' => array(Module::getNamespace('core') . 'Core', 'imageVersion'),
      'm' => array(Module::getNamespace('core') . 'Core', 'imageVersion'),
      'l' => array(Module::getNamespace('core') . 'Core', 'imageVersion'),
      'xl' => array(Module::getNamespace('core') . 'Core', 'imageVersion'),
    );
    
//    $makers = array();
//    $sizes = array(
//      '50x50',
//      '100x100',
//      '200x200',
//      '300x300',
//      '150x50',
//      '300x100',
//      '600x200',
//      '900x300',
//    );
//    foreach ($sizes as $s) {
//      $makers[$s] = array(Module::getNamespace('core') . 'Core', 'imageVersionFixedSizes');
//    }
//    $a = array(50, 100, 200, 300, 600, 900);
//    foreach ($a as $x) {
//      $makers[$x . '-Y'] = array(Module::getNamespace('core') . 'Core', 'imageVersionFixedWidth');
//    }
//    foreach ($a as $y) {
//      $makers['X-' . $y] = array(Module::getNamespace('core') . 'Core', 'imageVersionFixedHeight');
//    }
    
    return $makers;
  }
}