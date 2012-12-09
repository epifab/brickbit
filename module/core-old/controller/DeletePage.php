<?php
namespace module\core\controller;

use system\logic\Component;
use system\logic\EditComponent;
use system\InternalErrorException;

/**
 * Component DeletePage.
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class DeletePage extends Component {
	public static function checkPermission($args) {
		if (!\array_key_exists("id", $args)) {
			// Creazione nuova pagina
			return true;
		}
		else {
			// Modifica pagina esistente
			$pageBuilder = new \module\core\model\XmcaPage();
			$pageBuilder->using("id");
			$pageBuilder->setFilter(new \system\model\FilterClause($pageBuilder->id, "=", $args["id"]));
			if ($pageBuilder->countRecords()) {
				$pageBuilder->addEditModeFilters();
				return $pageBuilder->countRecords() == 1;				
			}
			return true;
		}
	}
	
	protected function getName() {
		return "layout/Success";
	}
	
	protected function getTemplate() {
		return "layout/Success";
	}
	
	private function deleteFiles($recordset) {
		foreach ($recordset->contents as $content) {
			$this->deleteFiles($content);
		}
		if ($recordset->image_id) {
			@\unlink($recordset->image->file1->path);
			@\unlink($recordset->image->file2->path);
			@\unlink($recordset->image->file3->path);
			@\unlink($recordset->image->file4->path);
		}
		if ($recordset->download_file_id) {
			@\unlink($recordset->download_file->path);
		}
		if ($recordset->audio_file_id) {
			@\unlink($recordset->audio_file->path);
		}
		if ($recordset->video_file_id) {
			@\unlink($recordset->video_file->path);
		}
	}
	
	public function onProcess() {
		$page = new \module\core\model\XmcaPage();
		$page->using(
			"id",
			"url",
			"title"
		);
		
		$pageText = new \module\core\model\XmcaPageText();
		$page->setHasManyRelationBuilder("texts", $pageText);
		
		$pageTag = new \module\core\model\XmcaPageTag();
		$page->setHasManyRelationBuilder("tags", $pageTag);

		
		$content = new \module\core\model\XmcaContent();
		$content->using(
			"download_file.path",
			"video_file.path",
			"audio_file.path",
			"image.file1.path",
			"image.file2.path",
			"image.file3.path",
			"image.file4.path"
		);
		$contentText = new \module\core\model\XmcaContentText();
		$content->setHasManyRelationBuilder("texts", $contentText);

		$contentTag = new \module\core\model\XmcaContentTag();
		$content->setHasManyRelationBuilder("tags", $contentTag);

		
		$subcontent = new \module\core\model\XmcaContent();
		$subcontent->using(
			"download_file.path",
			"video_file.path",
			"audio_file.path",
			"image.file1.path",
			"image.file2.path",
			"image.file3.path",
			"image.file4.path"
		);
		$subcontentText = new \module\core\model\XmcaContentText();
		$subcontent->setHasManyRelationBuilder("texts", $subcontentText);
		
		$subcontentTag = new \module\core\model\XmcaContentTag();
		$subcontent->setHasManyRelationBuilder("tags", $subcontentTag);

		$page->setHasManyRelationBuilder("contents", $content);
		$content->setHasManyRelationBuilder("contents", $subcontent);
		$subcontent->setHasManyRelationBuilder("contents", $subcontent);
		
		if (!\array_key_exists("id", $_REQUEST)) {
			throw new InternalErrorException("Id non trasmesso");
		}
		$recordset = $page->selectFirstBy("id", $_REQUEST["id"]);
		if (empty($recordset)) {
			throw new InternalErrorException("Id non valido");
		}
		
		$dl = \system\model\DataLayerCore::getInstance();
		
		try {
			$dl->beginTransaction();
			
			// Cancello il contenuto e tutte le relazioni implicate
			$recordset->delete();
			foreach ($recordset->contents as $content) {
				// Cancello i files
				$this->deleteFiles($content);
			}
			
			$dl->commitTransaction();
			
		} catch (\Exception $ex) {
			$dl->rollbackTransaction();
			throw $ex;
		}
		
		return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
	}
}
?>