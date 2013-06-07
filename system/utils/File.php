<?php
namespace system\utils;

use system\error\InternalError;
use system\error\ValidationError;

class File {
	public static function getExtension($fileName, $tolowercase=true) {
		if (($posDot = \strrpos($fileName, ".")) === false ) {
			return "";
		}
		$ext = \substr($fileName, $posDot+1);
		if ($tolowercase) {
			return \strtolower($ext);
		} else {
			return $ext;
		}
	}

	public static function stripExtension($fileName) {
		if (($posDot = \strrpos($fileName, ".")) === false ) {
			return $fileName;
		}
		return \substr($fileName, 0, $posDot);
	}
	
	public static function getSafeFilename($x) {
		$ext = self::getExtension($x);
		$name = self::stripExtension($x);
		$replace = "-";
		$pattern = "/([[:alnum:]_-]*)/";
		$name = \str_replace(\str_split(\preg_replace($pattern,$replace,$name)),$replace,$name);
		return "$name.$ext";
	}

	/**
	 * Controlla che il file postato (input name -> $postedFieldName) 
	 * sia stato uploadato correttamente,
	 * che le sue dimensioni non superino $maxFileSize (in byte)
	 * che la sua estensione sia nell'array $exts (es. {"jpg", "png", "gif"})
	 * che il tipo del file sia nell'array $types (es. {"image/jpg", "image/gif"})
	 * @param string $inputName Nome dell'input file
	 * @param int $maxFileSize Massima dimensione del file in Byte
	 * @param array $exts Estensioni consentite
	 */
	public static function validateFile($inputName, $maxFileSize=0, $exts=null) {

		if (!\array_key_exists($inputName, $_FILES) || empty($_FILES[$inputName]['name'])) {
			throw new ValidationError('File not uploaded');
		}

		if (empty($_FILES[$inputName]['size'])) {
			// File 0 Byte!
			throw new ValidationError('File not uploaded');
		}
		
		if ($maxFileSize > 0) {
			if ($_FILES[$inputName]['size'] > $maxFileSize) {
				$size = $maxFileSize;
				$unit = 'B';
				if ($maxFileSize > 1024*1024) {
					$size = round($maxFileSize/(1024*1024));
					$unit = 'MB';
				} else if ($maxFileSize > 1024) {
					$size = round($maxFileSize/(1024));
					$unit = 'KB';
				}
				throw new ValidationError('File too large. Max file isze: @size @unit', array(
					'@size' => $size,
					'@unit' => $unit
				));
			}
		}

		$fileNoExt = self::stripExtension($_FILES[$inputName]["name"]);
		// $fileNoExt contiene il nome del file senza estensione (es. image1.jpg = image1)

		$fileExt = self::getExtension($_FILES[$inputName]["name"]);
		// $ext contiene l'estensione del file (es. image1.jpg = jpg)
		if (\count($exts) > 0) {
			$ok = false;
			foreach ($exts as $k => $e) {
				if (strtolower($e) == $fileExt) {
					$ok = true; // Estensione riconosciuta
					break;
				}
			}
			if (!$ok) { // il file ha un estensione non riconosciuta
				throw new ValidationError('Invalid file extension');
			}
		}
	}

	/**
	 * Ridimensiona e salva un immagine
	 * @param string $inputName Nome dell'input file
	 * @param string $destinationPath Percorso nel file system per il nuovo file
	 * @param int $maxWidth Massima larghezza dell'immagine
	 * @param int $maxHeight Massima altezza dell'immagine
	 */
	public static function uploadImage($inputName, $destinationPath, $maxWidth=0, $maxHeight=0) {
		if (!\array_key_exists($inputName, $_FILES) || empty($_FILES[$inputName]['name'])) {
			throw new ValidationError('File not uploaded');
		}
    self::saveImage($_FILES[$inputName]['tmp_name'], $destinationPath. $maxWidth, $maxHeight);
  }
  
  
  public static function saveImage($sourcePath, $destinationPath, $maxWidth=0, $maxHeight=0) {

		$maxWidth = (int)$maxWidth;
		if ($maxWidth < 0) {
			throw new InternalError('Invalid parameter @name', array('@name' => 'maxWidth'));
		}
		$maxHeight = (int)$maxHeight;
		if ($maxHeight < 0) {
			throw new InternalError('Invalid parameter @name', array('@name' => 'maxHeight'));
		}
		
		if (!\copy($sourcePath, $destinationPath)) {
			throw new InternalError('Unable to create the file');
		}
		\chmod($destinationPath, octdec('0666'));

		list($width, $height) = \getimagesize($destinationPath);

		if (($maxWidth > 0 && $width > $maxWidth) || ($maxHeight > 0 && $height > $maxHeight)) {
			if ($maxWidth > 0 && $width > $maxWidth) {
				$height = ($maxWidth/$width)*$height;
				$width = $maxWidth;
			}

			// l'immagine va ridimensionata ulteriormente perche' eccede l'altezza
			if ($maxHeight > 0 && $height > $maxHeight) {
				$width = ($maxHeight/$height)*$width;
				$height = $maxHeight;
			}

			if (!($destImg = imagecreatetruecolor($width, $height))) {
				throw new InternalError('Unable to create a new image');
			}

			$ext = self::getExtension($destinationPath);
			switch ($ext) {
				case "jpg":
				case "jpeg":
					$newImg = \imagecreatefromjpeg($destinationPath);
					break;
				
				case "gif":
					$newImg = \imagecreatefromgif($destinationPath);
					break;
				
				case "png":
					$newImg = \imagecreatefrompng($destinationPath);
					break;
				
				default:
					throw new ValidationError('Invalid file estension');
			}

			if (!$newImg) {
				throw new InternalError('Unable to create the image');
			}

			if (\function_exists('imagecopyresampled')) {
				if (!\imagecopyresampled($destImg, $newImg, 0, 0, 0, 0, $width, $height, \imagesx($newImg), \imagesy($newImg))) {
					throw new InternalError('Unable to resize the image');
				}
			}
			else {
				if (!\imagecopyresized($destImg, $newImg, 0, 0, 0, 0, $width, $height, \imagesx($newImg), \imagesy($newImg))) {
					throw new InternalError('Unable to resize the image');
				}
			}

			switch ($ext) {
				case "jpg":
				case "jpeg":
					$result = \imagejpeg($destImg,$destinationPath,100);
					break;
				
				case "gif":
					$result = \imagegif($destImg,$destinationPath);
					break;
				
				case "png":
					$result = \imagepng($destImg,$destinationPath,9);
					break;
			}

			if (!$result) {
				throw new InternalError('Unable to save the image');
			}

			\imagedestroy($newImg);
			\imagedestroy($destImg);
		}
	}

	/**
	 * Ridimensiona e salva un immagine quadrata
	 * @param string $inputName Nome dell'input file
	 * @param string $destinationPath Percorso nel file system per il nuovo file
	 * @param int $fixedWidth Larghezza
	 * @param int $fixedHeight Altezza
	 */
	public static function uploadImageFixedSize($inputName, $destinationPath, $fixedWidth, $fixedHeight) {

		if (!\array_key_exists($inputName, $_FILES) || empty($_FILES[$inputName]['name'])) {
			throw new InternalError("File not uploaded");
		}

		self::saveImageFixedSize($_FILES[$inputName]['tmp_name'], $destinationPath, $fixedWidth, $fixedHeight);
  }
  
  
  public static function saveImageFixedSize($sourcePath, $destinationPath, $fixedWidth, $fixedHeight) {

		if (!\copy($sourcePath, $destinationPath)) {
			throw new InternalError("Unable to copy the file");
		}
		\chmod($destinationPath, octdec('0666'));

		list($originalWidth, $originalHeight) = \getimagesize($destinationPath);

		$widthRatio = $originalWidth / $fixedWidth;
		$heightRatio = $originalHeight / $fixedHeight;
		
		/**
		 * Per prima cosa confronto 
		 * le proporzioni tra le larghezze con le proporzioni tra le altezze
		 * 
		 * - se le proporzioni delle larghezze sono maggiori, 
		 * allora vuol dire che possiamo tagliare l'immagine in larghezza
		 * 
		 * - viceversa se le proporzioni delle altezze sono maggiori,
		 * allora vuol dire che possiamo tagliare l'immagine in altezza
		 * 
		 * - se sono identiche, questo significa che l'immagine è già delle giuste proporzioni
		 * 
		 * Opero un primo ridimensionamento dell'immagine,
		 * facendo in modo che almeno uno dei due lati sia della dimensione giusta.
		 * Il ridimensionamento sarà fatto in modo tale che l'altro lato invece
		 * sia di dimensioni maggiori (o uguali)
		 */
		if ($widthRatio > $heightRatio) {
			$newHeight = $fixedHeight;
			$newWidth = $newHeight*($originalWidth/$originalHeight);
		}
		else if ($heightRatio > $widthRatio) {
			$newWidth = $fixedWidth;
			$newHeight = $newWidth*($originalHeight/$originalWidth);
		}
		else {
			// Le proporzione sono già esatte
			$newWidth = $fixedWidth;
			$newHeight = $fixedHeight;
		}
		
		$newWidth = round($newWidth);
		$newHeight = round($newHeight);
		
		$ext = self::getExtension($destinationPath);
		switch ($ext) {
			case "jpg":
			case "jpeg":
				$originalImg = \imagecreatefromjpeg($destinationPath);
				break;

			case "gif":
				$originalImg = \imagecreatefromgif($destinationPath);
				break;

			case "png":
				$originalImg = \imagecreatefrompng($destinationPath);
				break;

			default:
				throw new ValidationError('Invalid file extension');
		}

		if (!$originalImg) {
			throw new InternalError('File not uploaded');
		}

		/**
		 * Creo la prima immagine ridimensionata
		 */
		$smallerImg = \imagecreatetruecolor($newWidth, $newHeight);
		\imagecopyresampled($smallerImg, $originalImg, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

		$targetImg = \imagecreatetruecolor($fixedWidth, $fixedHeight);

		if ($newWidth > $fixedWidth) {
			$difference = $newWidth-$fixedWidth;
			$differenceAvg =  round($difference/2);
			\imagecopyresampled($targetImg, $smallerImg, 0-$differenceAvg+1, 0, 0, 0, $fixedWidth+$difference, $fixedHeight, $newWidth, $newHeight);
		}
		else if ($newHeight > $fixedHeight) {
			$difference = $newHeight-$fixedHeight;
			$differenceAvg =  round($difference/2);
			\imagecopyresampled($targetImg, $smallerImg, 0, 0-$differenceAvg+1, 0, 0, $fixedWidth, $fixedHeight+$difference, $newWidth, $newHeight);
		}
		if ($newHeight == $newWidth) {
			\imagecopyresampled($targetImg, $smallerImg, 0, 0, 0, 0, $fixedWidth, $fixedHeight, $newWidth, $newHeight);
		}


		switch ($ext) {
			case "jpg":
			case "jpeg":
				$result = \imagejpeg($targetImg,$destinationPath,100);
				break;

			case "gif":
				$result = \imagegif($targetImg,$destinationPath);
				break;

			case "png":
				$result = \imagepng($targetImg,$destinationPath,9);
				break;
		}

		if (!$result) {
			throw new InternalError('Unable to save the image');
		}

		\imagedestroy($originalImg);
		\imagedestroy($smallerImg);
		\imagedestroy($targetImg);
	}

//	public static function resizeAndSaveFixedSizeImg($inputName, $destinationPath, $maxWidth, $maxHeight) {
//
//		$maxWidth = (int)$maxWidth;
//		if ($maxWidth <= 0) {
//			throw new InternalError("Parametro maxWidth non valido");
//		}
//		$maxHeight = (int)$maxHeight;
//		if ($maxHeight <= 0) {
//			throw new InternalError("Parametro maxHeight non valido");
//		}
//
//		if (!\array_key_exists($inputName, $_FILES) || empty($_FILES[$inputName]['name'])) {
//			throw new InternalError("Nessun file caricato");
//		}
//		
//		$name = $_FILES[$inputName]['name'];
//		$tmp = $_FILES[$inputName]['tmp_name'];
//		$size = $_FILES[$inputName]['size'];
//		$type = $_FILES[$inputName]['type'];
//
//		if (!\copy($tmp, $destinationPath)) {
//			throw new InternalError("Impossibile copiare il file caricato");
//		}
//		\chmod($destinationPath, octdec('0666'));
//
//		list($originalWidth, $originalHeight) = \getimagesize($destinationPath);
//
//		if ($originalWidth > $originalHeight) {
//			$newHeight = $squareSize;
//			$newWidth = $newHeight*($originalWidth/$originalHeight);
//		}
//		if ($originalHeight > $originalWidth) {
//			$newWidth = $squareSize;
//			$newHeight = $newWidth*($originalHeight/$originalWidth);
//		}
//		if ($originalHeight == $originalWidth) {
//			$newWidth = $squareSize;
//			$newHeight = $squareSize;
//		}
//
//		$newWidth = round($newWidth);
//		$newHeight = round($newHeight);
//		
//		$ext = self::getExtension($destinationPath);
//		switch ($ext) {
//			case "jpg":
//			case "jpeg":
//				$originalImg = \imagecreatefromjpeg($destinationPath);
//				break;
//
//			case "gif":
//				$originalImg = \imagecreatefromgif($destinationPath);
//				break;
//
//			case "png":
//				$originalImg = \imagecreatefrompng($destinationPath);
//				break;
//
//			default:
//				throw new InternalError("Estensione immagine non riconosciuta");				
//		}
//
//		if (!$originalImg) {
//			throw new InternalError("Errore durante il caricamento dell'immagine");
//		}
//		
//		$smallerImg = \imagecreatetruecolor($newWidth, $newHeight);
//		$squareImg = \imagecreatetruecolor($squareSize, $squareSize);
//
//		\imagecopyresampled($smallerImg, $originalImg, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
//
//		if ($newWidth > $newHeight) {
//			$difference = $newWidth-$newHeight;
//			$halfDifference =  round($difference/2);
//			\imagecopyresampled($squareImg, $smallerImg, 0-$halfDifference+1, 0, 0, 0, $squareSize+$difference, $squareSize, $newWidth, $newHeight);
//		}
//		if ($newHeight > $newWidth) {
//			$difference = $newHeight-$newWidth;
//			$halfDifference =  round($difference/2);
//			\imagecopyresampled($squareImg, $smallerImg, 0, 0-$halfDifference+1, 0, 0, $squareSize, $squareSize+$difference, $newWidth, $newHeight);
//		}
//		if ($newHeight == $newWidth) {
//			\imagecopyresampled($squareImg, $smallerImg, 0, 0, 0, 0, $squareSize, $squareSize, $newWidth, $newHeight);
//		}
//
//
//		switch ($ext) {
//			case "jpg":
//			case "jpeg":
//				$result = \imagejpeg($squareImg,$destinationPath,100);
//				break;
//
//			case "gif":
//				$result = \imagegif($squareImg,$destinationPath);
//				break;
//
//			case "png":
//				$result = \imagepng($squareImg,$destinationPath,9);
//				break;
//		}
//
//		if (!$result) {
//			throw new InternalError("Impossibile salvare l'immagine caricata");
//		}
//
//		\imagedestroy($originalImg);
//		\imagedestroy($smallerImg);
//		\imagedestroy($squareImg);
//	}
}
