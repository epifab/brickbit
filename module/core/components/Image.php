<?php
namespace module\core\components;

use \system\logic\Component;
use \system\model\Recordset;
use \system\model\RecordsetBuilder;
use \system\model\FilterClause;
use \system\model\FilterClauseGroup;
use \system\model\LimitClause;
use \system\model\SortClause;
use \system\model\SortClauseGroup;	

class Image extends Component {
	public static function runVersion() {
		list($version, $nodeId, $nodeIndex, $virtualName, $ext) = $this->getUrlArgs();
		if (!\array_key_exists($version, \system\Cache::imageVersionMakers())) {
			throw new \system\PageNotFoundException();
		}
		$rsb = new RecordsetBuilder('node_file');
		$rsb->usingAll();
		$nodeFile = $rsb->selectFirstBy(array('node_id(' => $nodeId, 'node_index' => $nodeIndex, 'virtual_name' => $virtualName));
		if (!$nodeFile) {
			throw new \system\PageNotFoundException();
		}
		if (!\in_array($nodeFile->file->extension, array('gif', 'jpg', 'jpeg', 'png'))) {
			throw new \system\PageNotFoundException();
		}
		// controlla che non esista una versione dell'immagine
		$dir = \system\Main::dataPath() . 'img/' . $version;
		if (!\file_exists($dir)) {
			@\mkdir($dir);
		}
		$fileName = $dir . $virtualName . '.' . $ext;
		
		if (!\file_exists($fileName) || \filetime($fileName) < $nodeFile->file->last_update) {
			// file version doesn't exist or outdated
			$handler = \system\Cache::imageVersionMakers();
			if (!isset($handler[$version])) {
				throw new \system\PageNotFoundException();
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
			}
			\header('Content-Length:' . \filesize($fileName));
			\readfile($fileName);
		}
		else {
			throw new \system\PageNotFoundException();
		}
	}
	
	public static function imageVersionMaker50x50($fileName, \system\model\RecordsetInterface $nodeFile) {
		\system\File::createImageFixedSize($nodeFile->file->path, $fileName, 100, 100);
	}
	
	public static function imageVersionMaker100x100($fileName, \system\model\RecordsetInterface $nodeFile) {
		\system\File::createImageFixedSize($nodeFile->file->path, $fileName, 100, 100);
	}
	
	public static function imageVersionMaker300x300($fileName, \system\model\RecordsetInterface $nodeFile) {
		\system\File::createImageFixedSize($nodeFile->file->path, $fileName, 300, 300);
	}
	
	public static function imageVersionMaker500x500($fileName, \system\model\RecordsetInterface $nodeFile) {
		\system\File::createImageFixedSize($nodeFile->file->path, $fileName, 500, 500);
	}
}
?>