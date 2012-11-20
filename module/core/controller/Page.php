<?php
namespace module\core\controller;

use system\logic\Component;
use system\logic\EditComponent;
use system\InternalErrorException;

class Page extends \system\logic\Component {
	private static $pages;
	
	private $template = "Page";
	
	public static function getPages() {
		if (\is_null(self::$pages)) {
			$tmpBuilder = new \module\core\model\XmcaPage();
			$tmpBuilder->using("url", "title", "sort_index");
			$tmpBuilder->addReadModeFilters();
			$tmpBuilder->setSort(new \system\model\SortClause($tmpBuilder->sort_index, "ASC"));
			self::$pages = $tmpBuilder->select();
		}
		return self::$pages;
	}
	
	public function getName() {
		return "Page";
	}
	
//	public static function checkPermission($args) {
//		// URL non trasmessa o inesistente...
//		return true;
//	}
	
	public function getTemplate() {
		return $this->template;
	}
	
	public function onProcess() {
		$this->datamodel["menuItems"] = self::getPages();
		
		$pageBuilder = new \module\core\model\XmcaPage();
		$pageBuilder->using(
			"url",
			"title",
			"body",
			"style.code",
			"style.page_template",
			"sort_index",
			"content_sorting",
			"content_paging",
			"content_filters",
			"record_mode.read_mode",
			"record_mode.owner_id",
			"record_mode.group_id"
		);
		$pageTagBuilder = new \module\core\model\XmcaPageTag();
		$pageTagBuilder->using(
			"tag.id",
			"tag.value",
			"tag.size",
			"tag.rate"
		);
		$pageTagBuilder->setSort(new \system\model\SortClause($pageTagBuilder->tag->value, "ASC"));
		$pageBuilder->setHasManyRelationBuilder("tags", $pageTagBuilder);

		$validPage = false;
		
		if (\array_key_exists("url", $_REQUEST)) {
			// Visualizzazione pagina
			$pageBuilder->addFilter(new \system\model\FilterClause($pageBuilder->url, "=", $_REQUEST["url"]));
			if ($pageBuilder->countRecords()) {
				// URL valida... controllo i permessi di lettura sulla pagina
				$pageBuilder->addReadModeFilters();
				$validPage = $pageBuilder->countRecords();
			}
		}
		
		if ($validPage) {
			$contentBuilder = new \module\core\model\XmcaContent();
			$contentBuilder->using(
				"page_id",
				"supercontent_id",
				"url",
				"sort_index",

				// testi
				"title",
				"subtitle",
				"body",
				"preview",

				"expandable",
				"social_networks",
				"comments",

				"style.code",
				"style.content_template",
				"style.preview_template",

				"download_file_url",
				"audio_file_url",
				"video_file_url",

				"image1_url", "image.width1", "image.height1",
				"image2_url", "image.width2", "image.height2",
				"image3_url", "image.width3", "image.height3",
				"image4_url", "image.width4", "image.height4"
			);
			$contentBuilder->addReadModeFilters();

			$contentTagBuilder = new \module\core\model\XmcaContentTag();
			$contentTagBuilder->using(
				"tag.id",
				"tag.value",
				"tag.size",
				"tag.rate"
			);
			$contentTagBuilder->setSort(new \system\model\SortClause($contentTagBuilder->tag->value, "ASC"));
			$contentBuilder->setHasManyRelationBuilder("tags", $contentTagBuilder);

			
			$subcontentBuilder = new \module\core\model\XmcaContent();
			$subcontentBuilder->using(
				"page_id",
				"supercontent_id",
				"url",
				"sort_index",

				// testi
				"title",
				"subtitle",
				"body",
				"preview",

				"expandable",
				"social_networks",
				"comments",

				"style.code",
				"style.content_template",
				"style.preview_template",

				"download_file_url",
				"audio_file_url",
				"video_file_url",

				"image1_url", "image.width1", "image.height1",
				"image2_url", "image.width2", "image.height2",
				"image3_url", "image.width3", "image.height3",
				"image4_url", "image.width4", "image.height4"
			);
			$subcontentBuilder->addReadModeFilters();

			$subcontentTagBuilder = new \module\core\model\XmcaContentTag();
			$subcontentTagBuilder->using(
				"tag.id",
				"tag.value",
				"tag.size",
				"tag.rate"
			);
			$subcontentTagBuilder->setSort(new \system\model\SortClause($subcontentTagBuilder->tag->value, "ASC"));
			$subcontentBuilder->setHasManyRelationBuilder("tags", $subcontentTagBuilder);

			$pageBuilder->setHasManyRelationBuilder("contents", $contentBuilder);

			$contentBuilder->setHasManyRelationBuilder("contents", $subcontentBuilder);
			// RELAZIONE 1-N RICORSIVA!!
			$subcontentBuilder->setHasManyRelationBuilder("contents", $subcontentBuilder);

			$recordset = $pageBuilder->selectFirst();

			
			switch ($recordset->content_sorting) {
				case "date_asc":
					$contentBuilder->setSort(new \system\model\SortClause($contentBuilder->public_date, "ASC"));
					break;
				case "date_desc":
					$contentBuilder->setSort(new \system\model\SortClause($contentBuilder->public_date, "DESC"));
					break;
				default:
					$contentBuilder->setSort(new \system\model\SortClause($contentBuilder->sort_index, "ASC"));
					break;
			}

			if ($recordset->content_paging) {
				$this->loadStdLimits($pageBuilder, $recordset->content_paging, "contents_");
			}

			$this->datamodel["page"] = $recordset;
			$this->datamodel["url"] = $recordset->url;

			$this->setPageTitle(\ucwords(\strtolower(\htmlspecialchars($recordset->title))), true);
		}
		
		else {
			$this->datamodel["url"] = "";
			$this->template = "PageNotFound";
		}
		
		return Component::RESPONSE_TYPE_READ;
	}
}
?>