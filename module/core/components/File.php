<?php
namespace module\core\components;

class File extends \system\logic\Component {
	public static function access($action, $urlArgs, $request, \system\Login $user) {
		return $user && $user->logged;
	}
	
	public function onError($exception) {
		if ($exception instanceof \system\AuthorizationException) {
			\header('HTTP/1.1 403 Forbidden');
		} else {
			\header('HTTP/1.1 500 Internal Server Error');
		}
	}
	
	public function runHandler() {
		if (\array_key_exists('system', $_REQUEST)) {
			
		}
		new \module\core\lib\CBUploaderHandler(array(
			'upload_dir' => \config\settings()->BASE_DIR_ABS . 'temp' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR,
			'upload_url' => '/file/',
			'script_url' => '/file',
			'user_dirs' => true
		));
		return null; // no output
	}
	
	public function runThumbnail() {
		switch (\strtolower(\system\File::getExtension($this->getUrlArg(1)))) {
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
		\readfile(\config\settings()->BASE_DIR_ABS . 'temp/upload/thumbnail/' . $this->getUrlArg(0) . '.' . $this->getUrlArg(1));
		return null; // no output
	}
	
	public function runDownload() {
		print_r($this->getUrlArgs());
		die();
		$fileName = $this->getUrlArg(0) . '.' . $this->getUrlArg(1);
		$filePath = \config\settings()->BASE_DIR_ABS . 'temp/upload/' . $fileName;
//		if (is_file($filePath)) {
//			if (!preg_match($this->options['inline_file_types'], $fileName)) {
				\header('Content-Description: File Transfer');
				\header('Content-Type: application/octet-stream');
				\header('Content-Disposition: attachment; filename="' . $fileName . '"');
				\header('Content-Transfer-Encoding: binary');
//			} else {
				// Prevent Internet Explorer from MIME-sniffing the content-type:
//				$this->header('X-Content-Type-Options: nosniff');
//				$this->header('Content-Type: ' . $this->get_file_type($filePath));
//				$this->header('Content-Disposition: inline; filename="' . $fileName . '"');
//			}
//			\header('Content-Length: ' . $this->get_file_size($filePath));
//			\header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($filePath)));
			\readfile($filePath);
//		}
		return null;
	}
}
?>