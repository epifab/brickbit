<?php
namespace module\core\components;

class NodeFile extends \system\logic\Component {
	
	public function createFile($fileName) {
		\system\Utils::log(__CLASS__, 'File upload complete - node id: ' . $this->getUrlArg(0) . ' index: ' . $this->getUrlArg(1));
		
		$nodeId = $this->getUrlArg(0);
		$nodeIndex = $this->getUrlArg(1);
		
		$dataAccess = \system\model\DataLayerCore::getInstance();
		
		$nodeIndexQuery = 'SELECT MAX(sort_index) + 1'
			. ' FROM node_file'
			. ' WHERE node_id = ' . $nodeId
			. ' AND node_index = ' . \system\metatypes\MetaString::stdProg2Db($nodeIndex);
		
		$virtualName = \system\File::getSafeFilename($originalFileName);
		
		$name = \system\File::stripExtension($virtualName);
		$ext = \system\File::getExtension($virtualName);

		$virtualNamesQuery = 'SELECT virtual_name'
			. ' FROM node_file'
			. ' WHERE node_id = ' . $nodeId
			. ' AND node_index = ' . \system\metatypes\MetaString::stdProg2Db($nodeIndex)
			. ' AND virtual_name LIKE ' . \system\metatypes\MetaString::stdProg2Db($name . '%');
		
		$virtualNames = $dataAccess->executeQueryArray($virtualNamesQuery, __FILE__, __LINE__);
		
		if (\in_array($virtualName, $virtualNames)) {
			for ($i = 2; \in_array($name . $i . '.' . $ext, $virtualNames); $i++);
			$virtualName = $name . $i . '.' . $ext;
		}
		\system\Utils::log(__CLASS__, 'Safe filename: ' . $virtualName);
		
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
		$rs->sort_index = 1 + \intval($dataAccess->executeScalar($nodeIndexQuery, __FILE__, __LINE__));
		$rs->virtual_name = $virtualName;
		$rs->save();
	}
	
	public static function getDirId() {
		$dirId = \system\Utils::get('core-nodefile-dir-id', null);
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
			\system\Utils::set('upload-dir-id', $rs->id);
		}
		return $dirId;
	}

	public static function getDirPath() {
		return 'nodes/';
	}
	
	public static function getAbsDirPath() {
		return \config\settings()->BASE_DIR_ABS . self::getDirPath();
	}
	
	public function runUpload() {
		/**
		* upload.php
		*
		* Copyright 2009, Moxiecode Systems AB
		* Released under GPL License.
		*
		* License: http://www.plupload.com/license
		* Contributing: http://www.plupload.com/contributing
		*/

		\system\Utils::log(__CLASS__, 'File upload', \system\Utils::LOG_DEBUG);
		
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
			$fileName_a =	\substr($fileName, 0, $ext);
			$fileName_b = \substr($fileName, $ext);

			$count = 1;
			while (\file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
				$count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

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
		if (\strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = \fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = \fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096))
							\fwrite($out, $buff);
					} else {
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					}
					\fclose($in);
					\fclose($out);
					@\unlink($_FILES['file']['tmp_name']);
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
?>