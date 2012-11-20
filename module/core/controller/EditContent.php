<?php
namespace module\core\controller;

use system\logic\Component;
use system\logic\EditComponent;
use system\InternalErrorException;

/**
 * Component EditContent.
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class EditContent extends EditComponent {
	
	public static function checkPermission($args) {
		if (\array_key_exists("id", $args)) {
			// Modifica contenuto esistente
			$contentBuilder = new \module\core\model\XmcaContent();
			$contentBuilder->using("id");
			$contentBuilder->setFilter(new \system\model\FilterClause($contentBuilder->id, "=", $args["id"]));
			if ($contentBuilder->countRecords()) {
				$contentBuilder->addEditModeFilters();
				return $contentBuilder->countRecords() == 1;				
			}
		}
		else if (\array_key_exists("supercontent_id", $args)) {
			// Aggiunta subcontent - controllo permessi di scrittura sul contenuto padre
			$contentBuilder = new \module\core\model\XmcaContent();
			$contentBuilder->using("id");
			$contentBuilder->setFilter(new \system\model\FilterClause($contentBuilder->id, "=", $args["supercontent_id"]));
			if ($contentBuilder->countRecords()) {
				$contentBuilder->addEditModeFilters();
				return $contentBuilder->countRecords() == 1;				
			}
		}
		else if (\array_key_exists("page_id", $args)) {
			// Aggiunta content - controllo permessi di scrittura sulla pagina
			$pageBuilder = new \module\core\model\XmcaPage();
			$pageBuilder->using("id");
			$pageBuilder->setFilter(new \system\model\FilterClause($pageBuilder->id, "=", $args["page_id"]));
			if ($pageBuilder->countRecords()) {
				$pageBuilder->addEditModeFilters();
				return $pageBuilder->countRecords() == 1;				
			}
		}
		// Se il controllo arriva qui, non c'e' un problema di permessi
		// ma un passaggio errato di parametri
		return true;
	}
	
	private static function moveContent($recordset, $move) {
		if ($move > 0) {
			$up = false;
		} else if ($move < 0) {
			$up = true;
		} else {
			return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
		}

		$dl = \system\model\DataLayerCore::getInstance();

		$query = 
			"SELECT COUNT(*)"
			. " FROM xmca_content"
			. " WHERE page_id = " . $recordset->page_id
			. " AND parent_content_id " . ($recordset->parent_content_id ? "= " . $recordset->parent_content_id : "IS NULL")
			. " AND sort_index " . ($up ? "<=" : ">=") . " " . $recordset->sort_index;
		if ($dl->executeScalar($query, __FILE__, __LINE__) == 1) {
			// non c'è nessun item da spostare
			return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
		}

		$dl->beginTransaction();
		try {
			$newSortIndex = $recordset->sort_index + $move;

			$query = 
				"UPDATE xmca_content SET sort_index = sort_index " . ($up ? "+" : "-") . " 1"
				. " WHERE page_id = " . $recordset->page_id
				. " AND parent_content_id " . ($recordset->parent_content_id ? " = " . $recordset->parent_content_id : "IS NULL")
				. " AND sort_index " . ($up ? ">=" : "<=") . " " . $newSortIndex
				. " AND sort_index " . ($up ? "<" : ">") . " " . $recordset->sort_index;
			$dl->executeUpdate($query, __FILE__, __LINE__);

			$recordset->sort_index = $newSortIndex;

			$recordset->update();

			$dl->commitTransaction();

			return \system\logic\Component::RESPONSE_TYPE_NOTIFY;

		} catch (\Exception $ex) {
			$dl->rollbackTransaction();
			throw $ex;
		}
	}
	
	protected function getName() {
		return "EditContent";
	}
	
	protected function getTemplateForm() {
		return "EditContent";
	}
	
	protected function getTemplateNotify() {
		return "layout/Success";
	}
	
	public function onProcess() {
		$tmpBuilder = new \module\core\model\XmcaTag();
		$tmpBuilder->using(
			"id",
			"value",
			"size",
			"rate"
		);
		$tmpBuilder->setSort(new \system\model\SortClause($tmpBuilder->value, "ASC"));
		$this->datamodel["tags"] = $tmpBuilder->select();
		
		$tmpBuilder = new \module\core\model\XmcaContentStyle();
		$tmpBuilder->using(
			"code",
			"description"
		);
		$tmpBuilder->setSort(new \system\model\SortClause($tmpBuilder->code, "ASC"));
		$this->datamodel["contentStyles"] = $tmpBuilder->select();
		
		$tmpBuilder = new \module\core\model\XmcaGroup();
		$tmpBuilder->using(
			"id",
			"name"
		);
		$tmpBuilder->setSort(new \system\model\SortClause($tmpBuilder->name, "ASC"));
		$tmpBuilder->setFilter(new \system\model\FilterClause($tmpBuilder->id, "<>", \module\core\model\XmcaGroup::ADMINS_GROUP_ID));
		$this->datamodel["groups"] = $tmpBuilder->select();
		
		$builder = new \module\core\model\XmcaContent();
		$builder->useAllKeys();
		$builder->using(
			"id",
			"page_id",
			"url",
			"supercontent_id",
			"sort_index",
			  
			"style.code",
				  
			"title",

			"expandable",
			"social_networks",
			"comments",
				  
			"download_file_url",
			"audio_file_url",
			"video_file_url",
			"image1_url",
			"image2_url",
			"image3_url",
			"image4_url"
		);
		$builder->setHasManyRelationBuilder("texts", new \module\core\model\XmcaContentText());	
		$contentTagBuilder = new \module\core\model\XmcaContentTag();
		$contentTagBuilder->using(
			"tag.id",
			"tag.value",
			"tag.size",
			"tag.rate"
		);
		$contentTagBuilder->setSort(new \system\model\SortClause($contentTagBuilder->tag->value, "ASC"));
		$builder->setHasManyRelationBuilder("tags", $contentTagBuilder);
		
		foreach (\config\settings()->LANGUAGES as $lang) {
			$builder->using(
				"text_" . $lang . ".title",
				"text_" . $lang . ".subtitle",
				"text_" . $lang . ".body",
				"text_" . $lang . ".preview"
			);
		}
		
		if (\array_key_exists("id", $_REQUEST)) {
			$recordset = $builder->selectFirstBy("id", $_REQUEST["id"]);
			if (\is_null($recordset)) {
				throw new \system\ValidationException("Contenuto non trovato");
			}
			
			$this->setPageTitle("Modifica contenuto " . $recordset->getRead("title"));
			
			if (\array_key_exists("move", $_REQUEST)) {
				return $this->moveContent($recordset, (int)$_REQUEST["move"]);
			}
		}
		
		else if (\array_key_exists("supercontent_id", $_REQUEST)) {
			$superBuilder = new \module\core\model\XmcaContent();
			$superBuilder->using("id", "page_id", "title");
			$supercontentRs = $superBuilder->selectFirstBy("id", $_REQUEST["supercontent_id"]);
			if (\is_null($supercontentRs)) {
				throw new \system\ValidationException("Contenuto padre non trovato");
			}
			
			$recordset = $builder->newRecordset();
			$recordset->page_id = $supercontentRs->page_id;
			$recordset->supercontent_id = $supercontentRs->id;
			
			$query = 
				"SELECT MAX(sort_index) FROM xmca_content"
				. " WHERE page_id = " . $recordset->page_id
				. " AND supercontent_id = " . $recordset->supercontent_id;
			$recordset->sort_index = 1 + (int)\system\model\DataLayerCore::getInstance()->executeScalar($query, __FILE__, __LINE__);
			
			$this->setPageTitle("Nuovo contenuto " . $supercontentRs->getRead("title"));
		}
		
		else if (\array_key_exists("page_id", $_REQUEST)) {
			$pageBuilder = new \module\core\model\XmcaPage();
			$pageBuilder->using("id", "title");
			$pageRs = $pageBuilder->selectFirstBy("id", $_REQUEST["page_id"]);
			if (\is_null($pageRs)) {
				throw new \system\ValidationException("Pagina non trovata");
			}
			
			$recordset = $builder->newRecordset();
			$recordset->page_id = $pageRs->id;
			$recordset->supercontent_id = null;
			
			$query = 
				"SELECT MAX(sort_index) FROM xmca_content"
				. " WHERE page_id = " . $recordset->page_id
				. " AND supercontent_id IS NULL";
			$recordset->sort_index = 1 + (int)\system\model\DataLayerCore::getInstance()->executeScalar($query, __FILE__, __LINE__);
			
			$this->setPageTitle("Nuovo contenuto pagina " . $pageRs->getRead("title"));
		}
		
		else {
			throw new \system\ValidationException("Errore nella trasmissione dei parametri");
		}
		
		// Oggetto recordset inizializzato correttamente

		$errors = array();
		$posted = 
			$this->loadData($recordset, $errors, array(
				"url",
				"style_code",
				"expandable",
				"comments",
				"social_networks",
			)) && $this->checkKey($recordset, $errors, "url_key")
			&& $this->checkHasOneRelation($recordset, $errors, "style");
		
		foreach (\config\settings()->LANGUAGES as $lang) {
			
			$posted = $this->loadData($recordset, $errors, array(
				"text_" . $lang . ".lang_id",
				"text_" . $lang . ".title",
				"text_" . $lang . ".subtitle",
				"text_" . $lang . ".body",
				"text_" . $lang . ".preview",
			)) && $posted;
			
			// Controllo che non ci siano versioni senza titolo ma con descrizione
			if (!empty($recordset->__get("text_" . $lang)->title) 
				|| !empty($recordset->__get("text_" . $lang)->subtitle) 
				|| !empty($recordset->__get("text_" . $lang)->body)
				|| !empty($recordset->__get("text_" . $lang)->preview)) {
				// Testi lingua interamente vuoti
				$insertText[$lang] = true;
			} else {
				$insertText[$lang] = false;
			}
		}

		
		if (!$posted) {
			$this->datamodel["recordset"] = $recordset;
			$this->datamodel["errors"] = $errors;
			return Component::RESPONSE_TYPE_FORM;
		}
		
		$da = \system\model\DataLayerCore::getInstance();
		$da->beginTransaction();
		
		///<editor-fold defaultstate="collapse" desc="Gestione upload files">
		if ($posted) {
			try {
				if (\array_key_exists("download_0_tmpname", $_REQUEST)) {
					if (!\is_null($recordset->download_file_id)) {
						throw new InternalErrorException("Impossibile sovrascrivere il file");
					}
					
					$file = new \module\core\model\XmcaFile();
					$file->using("id", "dir_id", "name");
					$fileRs = $file->newRecordset();
					$fileRs->dir_id = \module\core\model\Dir::DOWNLOAD_DIR_ID;
					$fileRs->create();

					$fileRs->name = $fileRs->id . "." . \system\File::getExtension($_REQUEST["download_0_tmpname"]);
					
					$fileRs->update();
					
					if (!@\copy("temp/" . $_REQUEST["download_0_tmpname"], \module\core\model\Dir::DOWNLOAD_DIR_PATH . $fileRs->name)) {
						throw new InternalErrorException("Impossibile copiare il file caricato (" .  $_REQUEST["download_0_tmpname"] . ") " . \module\core\model\Dir::DOWNLOAD_DIR_PATH . $name);
					} else {
						@\unlink("temp/" . $_REQUEST["download_0_tmpname"]);
					}
					$recordset->download_file_id = $fileRs->id;
				}
			}
			
			catch (\system\ValidationException $ex) {
				$posted = false;
				$errors["download_file"] = $ex->getMessage();
				$da->rollbackTransaction();
			}
			
			catch (\Exception $ex) {
				$da->rollbackTransaction();
				throw $ex;
			}
		}
		
		if ($posted) {
			try {
				if (\array_key_exists("audio_0_tmpname", $_REQUEST)) {
					if (!\is_null($recordset->audio_file_id)) {
						throw new InternalErrorException("Impossibile sovrascrivere il file");
					}
					
					$file = new \module\core\model\XmcaFile();
					$file->using("id", "dir_id", "name");
					$fileRs = $file->newRecordset();
					$fileRs->dir_id = \module\core\model\Dir::AUDIO_DIR_ID;
					$fileRs->create();

					$fileRs->name = $fileRs->id . "." . \system\File::getExtension($_REQUEST["audio_0_tmpname"]);
					
					$fileRs->update();
					
					if (!@\copy("temp/" . $_REQUEST["audio_0_tmpname"], \module\core\model\Dir::AUDIO_DIR_PATH . $fileRs->name)) {
						throw new InternalErrorException("Impossibile copiare il file caricato (" .  $_REQUEST["audio_0_tmpname"] . ") " . \module\core\model\Dir::AUDIO_DIR_PATH . $name);
					} else {
						@\unlink("temp/" . $_REQUEST["audio_0_tmpname"]);
					}
					$recordset->audio_file_id = $fileRs->id;
				}
			}
			
			catch (\system\ValidationException $ex) {
				$posted = false;
				$errors["audio_file"] = $ex->getMessage();
				$da->rollbackTransaction();
			}
			
			catch (\Exception $ex) {
				$da->rollbackTransaction();
				throw $ex;
			}
		}
		
		if ($posted) {
			try {
				if (\array_key_exists("video_0_tmpname", $_REQUEST)) {
					if (!\is_null($recordset->video_file_id)) {
						throw new InternalErrorException("Impossibile sovrascrivere il file");
					}
					
					$file = new \module\core\model\XmcaFile();
					$file->using("id", "dir_id", "name");
					$fileRs = $file->newRecordset();
					$fileRs->dir_id = \module\core\model\Dir::VIDEO_DIR_ID;
					$fileRs->create();

					$fileRs->name = $fileRs->id . "." . \system\File::getExtension($_REQUEST["video_0_tmpname"]);
					
					$fileRs->update();
					
					if (!@\copy("temp/" . $_REQUEST["video_0_tmpname"], \module\core\model\Dir::VIDEO_DIR_PATH . $fileRs->name)) {
						throw new InternalErrorException("Impossibile copiare il file caricato (" .  $_REQUEST["video_0_tmpname"] . ") " . \module\core\model\Dir::VIDEO_DIR_PATH . $name);
					} else {
						@\unlink("temp/" . $_REQUEST["video_0_tmpname"]);
					}
					$recordset->video_file_id = $fileRs->id;
				}
			}
			
			catch (\system\ValidationException $ex) {
				$posted = false;
				$errors["video_file"] = $ex->getMessage();
				$da->rollbackTransaction();
			}
			
			catch (\Exception $ex) {
				$da->rollbackTransaction();
				throw $ex;
			}
		}
		
		if ($posted) {
			try {
				// Immagine
				if (\array_key_exists("image", $_FILES) && !empty($_FILES["image"]["name"])) {
					// NUOVA IMMAGINE
					
					// Contrassegno la vecchia immagine come da eliminare
					$image2Delete = \is_null($recordset->image_id) ? null : $recordset->image;
					
					// Creo il record image
					$imgRs = \module\core\model\XmcaImage::initializeRs("image");
					
					// Salvo le immagini su file system
					\system\File::uploadImage("image", $imgRs->path_file1, 540);
					\system\File::uploadImage("image", $imgRs->path_file2, 200);
					\system\File::uploadImage("image", $imgRs->path_file3, 100);
					\system\File::uploadImageFixedSize("image", $imgRs->path_file4, 100, 100);
					
					// Creo i record files e aggiorno image
					\module\core\model\XmcaImage::finalizeRs($imgRs);
					
					$recordset->image_id = $imgRs->id;

					if ($image2Delete) {
						$image2Delete->delete();
						@\unlink($image2Delete->path_file1);
						@\unlink($image2Delete->path_file2);
						@\unlink($image2Delete->path_file3);
						@\unlink($image2Delete->path_file4);
					}
				} else if (\array_key_exists("deleteimg", $_REQUEST) && !\is_null($recordset->image_id)) {
					$recordset->image->delete();
					@\unlink($recordset->image->path_file1);
					@\unlink($recordset->image->path_file2);
					@\unlink($recordset->image->path_file3);
					@\unlink($recordset->image->path_file4);
					$recordset->image_id = null;
				}
			} catch (\system\ValidationException $ex) {
				$posted = false;
				$errors["image"] = $ex->getMessage();
				$da->rollbackTransaction();
			} catch (\Exception $ex) {
				$da->rollbackTransaction();
				throw $ex;
			}
		}
		///</editor-fold>
		
		if (!$posted) {
			$this->datamodel["recordset"] = $recordset;
			$this->datamodel["errors"] = $errors;
			return Component::RESPONSE_TYPE_FORM;
		}
		
		else {
			if ($recordset->id) {
				foreach ($recordset->texts as $t) {
					// Cancello tutti i testi
					$t->delete();
				}
				foreach ($recordset->tags as $t) {
					// Cancello tutti i tag
					$t->delete();
					
					if ($t->tag->size == 1) {
						// Cancello il tag vero e proprio se non è più referenziato
						$t->tag->delete();
					}
				}
				
				if ($recordset->record_mode->owner_id == \system\Login::getLoggedUserId()) {
					// Permetto la modifica del record mode soltanto all'owner del contenuto
					$recordset->update(
						$_REQUEST["recordset"]["record_mode.read_mode"],
						$_REQUEST["recordset"]["record_mode.edit_mode"],
						$_REQUEST["recordset"]["record_mode.group_id"]
					);
				} else {
					$recordset->update();
				}
			} else {
				// Salvo il record mode
				$recordset->create(
					$_REQUEST["recordset"]["record_mode.read_mode"],
					$_REQUEST["recordset"]["record_mode.edit_mode"],
					$_REQUEST["recordset"]["record_mode.group_id"]
				);
			}

			foreach (\config\settings()->LANGUAGES as $lang) {
				// Inserisco tutti i nuovi testi
				$textRs = $recordset->__get('text_' . $lang);
				if ($textRs->getRead('title')) {
					$textRs->content_id = $recordset->id;
					$textRs->create();
				}
			}
			
			// Aggiorno i tags
			if (\array_key_exists("tags", $_REQUEST["recordset"])) {
				$tags = \explode(",", $_REQUEST["recordset"]["tags"]);
				foreach ($tags as $t) {
					$t = \trim($t);
					$tagBuilder = $contentTagBuilder->tag;
					$tag = $tagBuilder->selectFirstBy("value", $t);
					if (!$tag) {
						$tag = $tagBuilder->newRecordset();
						$tag->value = $t;
						$tag->create();
					}
					$contentTag = $contentTagBuilder->newRecordset();
					$contentTag->content_id = $recordset->id;
					$contentTag->tag_id = $tag->id;
					$contentTag->create();
				}
			}
			
			$da->commitTransaction();
			return Component::RESPONSE_TYPE_NOTIFY;
		}
	}
}
?>