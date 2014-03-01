<?php
namespace module\core\controller;

class NodeFile extends \system\Component {
  
  public function createFile($fileName) {
    \system\utils\Log::create(__CLASS__, 'File upload complete. Node id: <em>@id</em>, index: <em>@index</em>', array('@id' => $this->getUrlArg(0), '@index' => $this->getUrlArg(1)), \system\LOG_DEBUG);
    
    $nodeId = $this->getUrlArg(0);
    $nodeIndex = $this->getUrlArg(1);
    
    $dataAccess = \system\model\DataLayerCore::getInstance();
    
    $nodeIndexQuery = 'SELECT MAX(sort_index) + 1'
      . ' FROM node_file'
      . ' WHERE node_id = ' . $nodeId
      . ' AND node_index = ' . \system\metatypes\MetaString::stdProg2Db($nodeIndex);
    
    $virtualName = \system\utils\File::getSafeFilename($fileName);
    
    $name = \system\utils\File::stripExtension($virtualName);
    $ext = \system\utils\File::getExtension($virtualName);

    $virtualNamesQuery = 'SELECT virtual_name'
      . ' FROM node_file'
      . ' WHERE node_id = ' . $nodeId
      . ' AND node_index = ' . \system\metatypes\MetaString::stdProg2Db($nodeIndex)
      . ' AND virtual_name LIKE ' . \system\metatypes\MetaString::stdProg2Db($name . '%');
    
    $virtualNames = $dataAccess->executeQueryArray($virtualNamesQuery);
    
    if (\in_array($virtualName, $virtualNames)) {
      for ($i = 2; \in_array($name . $i . '.' . $ext, $virtualNames); $i++);
      $virtualName = $name . $i . '.' . $ext;
    }
    
    $rsb = new \system\model\RecordsetBuilder('node_file');
    $rsb->using('*', 'file.*');
    
    // init the recordset
    $rs = $rsb->newRecordset();

    $rs->file->dir_id = self::getDirId();
    $rs->file->name = $fileName;
    $rs->file->size = \filesize(self::getAbsDirPath() . $fileName);
    $rs->file->save();
    
    $rs->file_id = $rs->file->id;
    $rs->node_id = $nodeId;
    $rs->node_index = $nodeIndex;
    $rs->sort_index = 1 + \intval($dataAccess->executeScalar($nodeIndexQuery));
    $rs->virtual_name = $virtualName;
    $rs->save();
  }
  
  public static function getDirId() {
    $dirId = \system\Main::getVariable('core-nodefile-dir-id', null);
    if (!$dirId) {
      $rsb = new \system\model\RecordsetBuilder('dir');
      $rsb->using('*');
      
      $rs = $rsb->selectFirstBy(array('path' => self::getDirPath()));
      if (!$rs) {
        $rs = $rsb->newRecordset();
        $rs->path = self::getDirPath();
        $rs->save();
      }
      $dirId = $rs->id;
      \system\Main::setVariable('core-nodefile-dir-id', $rs->id);
    }
    return $dirId;
  }

  public static function getDirPath() {
    return 'data/nodes/';
  }
  
  public static function getAbsDirPath() {
    return \config\settings()->BASE_DIR_ABS . self::getDirPath();
  }
  
  public function runUpload() {
    new \module\core\lib\JQueryFileUploadHandler(
        array(
            'script_url' => $this->getUrl(),
            'upload_dir' => \system\Main::dataPath('nodes/'),
            'upload_url' => $this->getUrl(),
            'user_dirs' => false,
            'mkdir_mode' => 0755,
            'param_name' => 'files',
            // Set the following option to 'POST', if your server does not support
            // DELETE requests. This is a parameter sent to the client:
            'delete_type' => 'DELETE',
            'access_control_allow_origin' => '*',
            'access_control_allow_credentials' => false,
            'access_control_allow_methods' => array(
                'OPTIONS',
                'HEAD',
                'GET',
                'POST',
                'PUT',
                'PATCH',
                'DELETE'
            ),
            'access_control_allow_headers' => array(
                'Content-Type',
                'Content-Range',
                'Content-Disposition'
            ),
            // Enable to provide file downloads via GET requests to the PHP script:
            //     1. Set to 1 to download files via readfile method through PHP
            //     2. Set to 2 to send a X-Sendfile header for lighttpd/Apache
            //     3. Set to 3 to send a X-Accel-Redirect header for nginx
            // If set to 2 or 3, adjust the upload_url option to the base path of
            // the redirect parameter, e.g. '/files/'.
            'download_via_php' => false,
            // Read files in chunks to avoid memory limits when download_via_php
            // is enabled, set to 0 to disable chunked reading of files:
            'readfile_chunk_size' => 10 * 1024 * 1024, // 10 MiB
            // Defines which files can be displayed inline when downloaded:
            'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
            // Defines which files (based on their names) are accepted for upload:
            'accept_file_types' => '/.+$/i',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            // The maximum number of files for the upload directory:
            'max_number_of_files' => null,
            // Defines which files are handled as image files:
            'image_file_types' => '/\.(gif|jpe?g|png)$/i',
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to 0 to use the GD library to scale and orient images,
            // set to 1 to use imagick (if installed, falls back to GD),
            // set to 2 to use the ImageMagick convert binary directly:
            'image_library' => 1,
            // Uncomment the following to define an array of resource limits
            // for imagick:
            /*
            'imagick_resource_limits' => array(
                imagick::RESOURCETYPE_MAP => 32,
                imagick::RESOURCETYPE_MEMORY => 32
            ),
            */
            // Command or path to the ImageMagick convert binary:
            'convert_bin' => 'convert',
            // Uncomment the following to add parameters in front of each
            // ImageMagick convert call (the limit constraints seem only
            // to have an effect if put in front):
            /*
            'convert_params' => '-limit memory 32MiB -limit map 32MiB',
            */
            // Command or path for to the ImageMagick identify binary:
            'identify_bin' => 'identify',
            'image_versions' => array(
                // The empty image version key defines options for the original image:
                '' => array(
                    // Automatically rotate images based on EXIF meta data:
                    'auto_orient' => true
                ),
                // Uncomment the following to create medium sized images:
                /*
                'medium' => array(
                    'max_width' => 800,
                    'max_height' => 600
                ),
                */
                'thumbnail' => array(
                    // Uncomment the following to use a defined directory for the thumbnails
                    // instead of a subdirectory based on the version identifier.
                    // Make sure that this directory doesn't allow execution of files if you
                    // don't pose any restrictions on the type of uploaded files, e.g. by
                    // copying the .htaccess file from the files directory for Apache:
                    //'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/thumb/',
                    //'upload_url' => $this->get_full_url().'/thumb/',
                    // Uncomment the following to force the max
                    // dimensions and e.g. create square thumbnails:
                    //'crop' => true,
                    'max_width' => 80,
                    'max_height' => 80
                )
            )
        )
    );
  }
  
  public function __runUpload() {
    /**
    * upload.php
    *
    * Copyright 2009, Moxiecode Systems AB
    * Released under GPL License.
    *
    * License: http://www.plupload.com/license
    * Contributing: http://www.plupload.com/contributing
    */

    \system\utils\Log::create(__CLASS__, 'Uploading file', array(), \system\LOG_DEBUG);
    
    // HTTP headers for no cache etc
    \header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    \header("Last-Modified: " . \gmdate("D, d M Y H:i:s") . " GMT");
    \header("Cache-Control: no-store, no-cache, must-revalidate");
    \header("Cache-Control: post-check=0, pre-check=0", false);
    \header("Pragma: no-cache");

    // Settings
    $targetDir = self::getAbsDirPath();

    $cleanupTargetDir = true; // Remove old files
    $maxFileAge = 5 * 3600; // Temp file age in seconds

    // 5 minutes execution time
    @\set_time_limit(5 * 60);

    // Uncomment this one to fake upload time
    // usleep(5000);

    // Get parameters
    $chunk = isset($_REQUEST["chunk"]) ? \intval($_REQUEST["chunk"]) : 0;
    $chunks = isset($_REQUEST["chunks"]) ? \intval($_REQUEST["chunks"]) : 0;
    $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

    // Clean the fileName for security reasons
    $fileName = \preg_replace('/[^\w\._]+/', '-', $fileName);

    // Make sure the fileName is unique but only if chunking is disabled
    if ($chunks < 2 && \file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
      $ext = \strrpos($fileName, '.');
      $fileName_a =  \substr($fileName, 0, $ext);
      $fileName_b = \substr($fileName, $ext);

      $count = 1;
      while (\file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
        $count++;

      $fileName = $fileName_a . '_' . $count . $fileName_b;
    }

    $filePath = $targetDir . $fileName;

    // Create target dir
    if (!\file_exists($targetDir))
      @\mkdir($targetDir);

    // Remove old temp files  
    if ($cleanupTargetDir && \is_dir($targetDir) && ($dir = \opendir($targetDir))) {
      while (($file = \readdir($dir)) !== false) {
        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

        // Remove temp file if it is older than the max age and is not the current file
        if (\preg_match('/\.part$/', $file) && (\filemtime($tmpfilePath) < \time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
          @\unlink($tmpfilePath);
        }
      }

      closedir($dir);
    } else
      die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');


    // Look for the content type header
    if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
      $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

    if (isset($_SERVER["CONTENT_TYPE"]))
      $contentType = $_SERVER["CONTENT_TYPE"];

    // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
    if (empty($contentType) || \strpos($contentType, "multipart") !== false) {
      
      if (isset($_FILES['files']['tmp_name']) && \is_uploaded_file($_FILES['files']['tmp_name'][0])) {
        // Open temp file
        $out = \fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
        if ($out) {
          // Read binary input stream and append it to temp file
          $in = \fopen($_FILES['files']['tmp_name'][0], "rb");

          if ($in) {
            while ($buff = fread($in, 4096))
              \fwrite($out, $buff);
          } else {
            die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
          }
          \fclose($in);
          \fclose($out);
          @\unlink($_FILES['files']['tmp_name'][0]);
        } else
          die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
      } else
        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
    } else {
      // Open temp file
      $out = \fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
      if ($out) {
        // Read binary input stream and append it to temp file
        $in = \fopen("php://input", "rb");

        if ($in) {
          while ($buff = \fread($in, 4096))
            \fwrite($out, $buff);
        } else
          die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

        \fclose($in);
        \fclose($out);
      } else
        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    }

    // Check if file has been uploaded
    if (!$chunks || $chunk == $chunks - 1) {
      // Strip the temp .part suffix off 
      \rename("{$filePath}.part", $filePath);
      self::createFile($fileName);
    }

    // Return JSON-RPC response
    die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
  }
  
  public function runDownload() {
    $nodeId = $this->getUrlArg(0);
    $nodeIndex = $this->getUrlArg(1);
    $virtualName = $this->getUrlArg(2);
    
    $rsb = new \system\model\RecordsetBuilder('node_file');
    $rsb->using('*', 'file.path');
    
    $nodeFile = $rsb->selectFirstBy(array(
      'node_id' => $nodeId,
      'node_index' => $nodeIndex,
      'virtual_name' => $virtualName
    ));

    while (\ob_get_clean());
    
    \header("Cache-Control: no-cache, must-revalidate");
    \header("Content-Description: File Transfer"); 
    \header("Content-Disposition: attachment; filename=" . $content->getRead('download_file_name'));
    \header("Content-Type: application/octet-stream");
    \header("Content-Transfer-Encoding: binary"); 
    \header('Content-Length: ' . filesize($content->download_file->path));
    // Leggo il contenuto del file 
    \readfile($nodeFile->path);
    // NESSUN TEMPLATE
    return null;
  }
  
  public function runUpdate() {
    
  }
  
  public function runDelete() {
    
  }
}
