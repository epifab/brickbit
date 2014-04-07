<?php
namespace module\node_file;

use system\Component;
use system\Main;
use system\exceptions\PageNotFound;
use system\metatypes\MetaString;
use system\model2\DataLayerCore;
use system\model2\RecordsetInterface;
use system\model2\Table;
use system\model2\TableInterface;
use system\utils\File;
use module\node\NodeCrudController;
use module\jquery_file_upload\FileUploadHandler;

class NodeFileController extends Component {
  ///<editor-fold defaultstate="collapsed" desc="Access methods">
  public static function accessUpload($urlArgs, RecordsetInterface $user) {
    // Checking the user has permissions to edit the node
    return NodeCrudController::accessEdit(array($urlArgs[0]), $user);
  }
  
  public static function accessList($urlArgs, RecordsetInterface $user) {
    return NodeCrudController::accessEdit(array($urlArgs[0]), $user);
  }
  
  public static function accessDownload($urlArgs, RecordsetInterface $user) {
    return NodeCrudController::accessEdit(array($urlArgs[0]), $user);
  }
  
  public static function accessDownloadImage($urlArgs, RecordsetInterface $user) {
    return NodeCrudController::accessRead(array($urlArgs[0]), $user);
  }
  ///</editor-fold>
  
  protected function createFile($nodeId, $nodeIndex, $virtualName, $directory, $fileName) {
    $dataAccess = DataLayerCore::getInstance();
    
    $name = File::stripExtension($virtualName);
    $ext = File::getExtension($virtualName);

    $table = Table::loadTable('node_file');
    $table->addFilters(
      $table->filter('node_id', $nodeId),
      $table->filter('node_index', $nodeIndex),
      $table->filter('virtual_name', $name, 'STARTS')
    );
    $table->setSelectKey($table->importField('virtual_name'));
    $duplicates = $table->select();
    
    if (isset($duplicates[$virtualName])) {
      for ($i = 2; array_key_exists($name . $i . '.' . $ext, $duplicates); $i++);
      $virtualName = $name . $i . '.' . $ext;
    }
    
    $table->import('file.*');
    
    // init the recordset
    $rs = $table->newRecordset();

    $rs->file->directory = $directory;
    $rs->file->name = $fileName;
    $rs->file->size = \filesize($directory . $fileName);
    $rs->file->type = File::getContentType($fileName);
    $rs->file->save();
    
    $nodeIndexQuery = 
      'SELECT MAX(sort_index)'
      . ' FROM node_file'
      . ' WHERE node_id = ' . $nodeId
      . ' AND node_index = ' . MetaString::stdProg2Db($nodeIndex);
    
    $rs->file_id = $rs->file->id;
    $rs->node_id = $nodeId;
    $rs->node_index = $nodeIndex;
    $rs->sort_index = 1 + \intval($dataAccess->executeScalar($nodeIndexQuery));
    $rs->virtual_name = $virtualName;
    $rs->save();
    
    return $rs;
  }
  
  protected function initFileObject(RecordsetInterface $nodeFile) {
    $file = (object)array(
      'name' => $nodeFile->virtual_name,
      'size' => $nodeFile->file->size,
      'url' => $nodeFile->url,
      'deleteUrl' => $nodeFile->delete_url,
    );
    if (File::isImage($file->name)) {
      $file->thumbnailUrl = $nodeFile->images['thumb'];
    }
    else {
      $icons = NodeFileApi::fileTypeIcons();
      switch (File::getExtension($nodeFile->virtual_name)) {
        case 'mp3':
          $file->thumbnailUrl = $icons['audio'];
          break;
        case 'avi':
        case 'mp4':
          $file->thumbnailUrl = $icons['video'];
          break;
        case 'pdf':
        default:
          $file->thumbnailUrl = $icons['document'];
          break;
      }
    }
    
    return $file;
  }
  
  public function runList() {
    $nodeId = $this->getUrlArg(0);
    $nodeIndex = $this->getUrlArg(1);
    
    $table = Table::loadTable('node_file');
    $table->import('*', 'file.*');
    $table->addFilters($table->filter('node_id', $nodeId));
    if (!empty($nodeIndex)) {
      $table->addFilters($table->filter('node_index', $nodeIndex));
    }
    
    $nodeFiles = $table->select();
    
    $files = array();
    foreach ($nodeFiles as $nodeFile) {
      $files[] = $this->initFileObject($nodeFile);
    }
    
    echo json_encode(array('files' => $files));
  }
  
  public function runUpload() {
    list($nodeId, $nodeIndex) = $this->getUrlArgs();
    
    $data = $this->getRequestData();
    
    $virtualName = File::getSafeFilename($data['name']);
    if (strlen($virtualName) > 50) {
      $virtualName = 
        \substr(File::stripExtension($virtualName), 0, 50 - strlen($virtualName)) 
        . '.' . File::getExtension($virtualName);
    }
    
    $upload = new FileUploadHandler($virtualName, array(
      'script_url' => Main::getActiveComponent()->getUrl(),
      'upload_dir' => NodeFileApi::getUploadDirectory(),
      'upload_url' => Main::getActiveComponent()->getUrl(),
      // Defines which files (based on their names) are accepted for upload:
      'accept_file_types' => '/.+$/i',
      // The php.ini settings upload_max_filesize and post_max_size
      // take precedence over the following max_file_size setting:
      'max_file_size' => null,
      'min_file_size' => 1,
      // Defines which files are handled as image files:
      'image_file_types' => '/\.(gif|jpe?g|png)$/i',
    ));
    
    $files = $upload->post();
    
    foreach ($files as $k => $file) {
      if (!isset($file->error) && empty($file->incomplete)) {
        $nodeFile = $this->createFile(
          $nodeId,
          $nodeIndex,
          $virtualName,
          NodeFileApi::getUploadDirectory(),
          $file->name
        );
        $files[$k] = $this->initFileObject($nodeFile);
      }
    }
    
    echo json_encode(array('files' => $files));
    
    return null;
  }
  
  /**
   * @return TableInterface Table
   */
  private function nodeFileTable() {
    $table = Table::loadTable('node_file');
    $table->import('*', 'file.path');
    return $table;
  }
  
  private function download($nodeId, $nodeIndex, $virtualName, $contentType = 'application/octet-stream', $version = null) {
    $table = $this->nodeFileTable();
    $nodeFile = $table->selectFirst($table->filterGroup('AND')->addClauses(
      $table->filter('node_id', $nodeId),
      $table->filter('node_index', $nodeIndex),
      $table->filter('virtual_name', $virtualName)
    ));
    
    if (empty($nodeFile)) {
      throw new PageNotFound();
    }

    $path = ($version)
      ? NodeFileApi::imageVersionPath($version, $nodeFile)
      : $nodeFile->file->path;

    while (\ob_get_clean());
    
    \header("Cache-Control: no-cache, must-revalidate");
    \header("Content-Description: File Transfer"); 
    if (empty($version)) {
      \header("Content-Disposition: attachment; filename=" . $virtualName);
      \header("Content-Type: application/octet-stream");
    }
    else {
      \header("Content-Type: $contentType");
    }
    \header("Content-Transfer-Encoding: binary"); 
    \header('Content-Length: ' . \filesize($path));
    // Leggo il contenuto del file 
    \readfile($path);
    // NESSUN TEMPLATE
    return null;
  }
  
  public function runDownload() {
    list($nodeId, $nodeIndex, $virtualName) = $this->getUrlArgs();
    return $this->download($nodeId, $nodeIndex, $virtualName);
  }
  
  public function runDownloadImage() {
    list($nodeId, $nodeIndex, $version, $virtualName) = $this->getUrlArgs();
    return $this->download($nodeId, $nodeIndex, $virtualName, File::getContentType($virtualName), $version);
  }
}
