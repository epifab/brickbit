<?php
namespace module\core\controller;

use system\logic\Component;
use system\logic\EditComponent;
use system\InternalErrorException;
use module\core\model\XmcaRecordMode;

/**
 * Component Image.
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class Image extends Component {
	public static function checkPermission($args) {
		return true;
	}
	
	protected function getName() {
		return "Image";
	}
	
	protected function getTemplate() {
		return null;
	}
	
	public function onProcess() {
		//////////////////////////////////////////////////////
		//
		// Recupero il contenuto del quale è richiesto il download
		// 
		//////////////////////////////////////////////////////
		
		$contentBuilder = new \module\core\model\XmcaContent();
		$contentBuilder->using(
			"url",
			"image.path_file1",
			"image.path_file2",
			"image.path_file3",
			"image.path_file4"
		);

		if (\array_key_exists("n", $_REQUEST) && (int)$_REQUEST["n"] >= 1 && (int)$_REQUEST["n"] <= 4) {
			$n = (int)$_REQUEST["n"];
		} else {
			$n = 1;
		}
		
		$content = null;
		if (\array_key_exists("url", $_REQUEST)) {
			$content = $contentBuilder->selectFirstBy("url", $_REQUEST["url"]);
		}
		
		if (!$content) {
			$imgPath = "img/layout/noimg" . $n . ".jpg";
		} else {
			$imgPath = $content->image->getRead("path_file" . $n);
		}
		
		switch (\system\File::getExtension($imgPath)) {
			case "jpg":
			case "jpeg":
				$contentType = "jpeg";
				break;
			case "gif":
				$contentType = "gif";
				break;
			default:
				$contentType = "png";
				break;
		}
		
		\ob_clean();
		// Invio il file
		\header("Content-Type: image/" . $contentType);
		// Leggo il contenuto del file 
		\readfile($imgPath);
		// NESSUN TEMPLATE
		return null;
	}
}
?>