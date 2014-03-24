<?php
namespace module\core\controller;

use system\Component;
use system\Main;
use system\exceptions\InputOutputError;
use system\exceptions\PageNotFound;
use system\model2\Table;
use system\model2\RecordsetInterface;
use system\utils\File;

class Image extends Component {
  
  public static function runVersion() {
    list($version, $nodeId, $nodeIndex, $virtualName, $ext) = $this->getUrlArgs();
    if (!\array_key_exists($version, Main::invokeStaticMethodAllMerge('imageVersionMakers'))) {
      throw new PageNotFound();
    }
    $table = Table::loadTable('node_file');
    $table->import('*');
    $nodeFile = $table->selectFirst($table->filterGroup('AND')->addClauses(
      $table->filter('node_id', $nodeId),
      $table->filter('node_index', $nodeIndex),
      $table->filter('virtual_name', $virtualName)
    ));
    if (!$nodeFile) {
      throw new PageNotFound();
    }
    if (!\in_array($nodeFile->file->extension, array('gif', 'jpg', 'jpeg', 'png'))) {
      throw new PageNotFound();
    }
    // controlla che non esista una versione dell'immagine
    $dir = Main::dataPath() . 'img/' . $version;
    if (!\file_exists($dir)) {
      @\mkdir($dir);
    }
    $fileName = $dir . $virtualName . '.' . $ext;
    
    if (!\file_exists($fileName) || \filetime($fileName) < $nodeFile->file->last_update) {
      // file version doesn't exist or outdated
      $handler = Main::invokeStaticMethodAllMerge('imageVersionMakers');
      if (!isset($handler[$version])) {
        throw new PageNotFound();
      } else {
        \call_user_func($handler[$version], $fileName, $nodeFile);
      }
    }
    if (\file_exists($fileName)) {
      switch (\strtolower($ext)) {
        case 'gif':
          \header('Content-Type:image/gif');
          break;
        case 'jpg':
        case 'jpeg':
          \header('Content-Type:image/jpeg');
          break;
        case 'png':
          \header('Content-Type:image/png');
          break;
          break;
        default:
          throw new InputOutputError('Invalid image extension <em>@ext</em>', array('@ext' => $ext));
      }
      \header('Content-Length:' . \filesize($fileName));
      \readfile($fileName);
    }
    else {
      throw new InputOutputError('File <em>@name</em> not foud', array('@name' => $fileName));
    }
  }
  
  public static function imageVersionMaker50x50($fileName, RecordsetInterface $nodeFile) {
    File::createImageFixedSize($nodeFile->file->path, $fileName, 100, 100);
  }
  
  public static function imageVersionMaker100x100($fileName, RecordsetInterface $nodeFile) {
    File::createImageFixedSize($nodeFile->file->path, $fileName, 100, 100);
  }
  
  public static function imageVersionMaker300x300($fileName, RecordsetInterface $nodeFile) {
    File::createImageFixedSize($nodeFile->file->path, $fileName, 300, 300);
  }
  
  public static function imageVersionMaker500x500($fileName, RecordsetInterface $nodeFile) {
    File::createImageFixedSize($nodeFile->file->path, $fileName, 500, 500);
  }
}
