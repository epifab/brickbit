<?php
namespace module\node_file;

use system\Main;
use system\model2\RecordsetInterface;
use system\model2\Table;
use system\exceptions\InternalError;
use system\Utils\File;
use system\Utils\Handler;

class NodeFileApi {
  /**
   * Image version handlers
   * @return Handler[]
   */
  public static function imageVersionHandlers() {
    static $handlers = null;
    if (\is_null($handlers)) {
      $handlers = Main::invokeStaticMethodAllMerge('imageVersionHandlers', false);
      foreach ($handlers as $k => $handler) {
        $handlers[$k] = new Handler($handler);
      }
    }
    return $handlers;
  }
  
  /**
   * Image version handler
   * @param string $version Veersion
   * @return Handler
   */
  public static function imageVersionHandler($version) {
    $handlers = self::imageVersionHandlers();
    return isset($handlers[$version]) ? $handlers[$version] : null;
  }
  
  /**
   * Returns the path to the image version
   * @param string $version
   * @param RecordsetInterface $nodeFile Node file
   * @return string Image path
   * @throws InternalError
   */
  public static function imageVersionPath($version, RecordsetInterface $nodeFile) {
    $fileName = $nodeFile->file_id . '.' . $nodeFile->file->extension;
    
    $fileDir = self::getUploadDirectory($version);
    $filePath = $fileDir . $fileName;
    
    if (!\file_exists($filePath)) {
      $handler = self::imageVersionHandler($version);
      if (empty($handler)) {
        throw new InternalError('Unknown image version <em>@version</em>', array('@version' => $version));
      }
      $handler->run($version, $filePath, $nodeFile);
      if (!\file_exists($filePath)) {
        throw new InternalError('Unable to create a version <em>@version</em> for file <em>@file</em>', array('@version' => $version, '@file' => $nodeFile->file->path));
      }
      
      $table = Table::loadTable('file_version');
      $table->import('*');
      
      $fileVersion = $table->newRecordset();
      $fileVersion->file_id = $nodeFile->file_id;
      $fileVersion->version = $version;
      $fileVersion->directory = $fileDir;
      $fileVersion->name = $fileName;
      $fileVersion->size = \filesize($filePath);
      $fileVersion->type = File::getContentType($fileName);
      
      $fileVersion->save();
    }
    
    return $filePath;
  }
  
  public static function getUploadDirectory($version = null) {
    return empty($version)
      ? Main::dataPath('content/')
      : Main::dataPath('images/' . $version . '/');
  }
  
  public static function fileTypeIcons() {
    return Main::invokeStaticMethodAllMerge('fileTypeIcons');
  }
}