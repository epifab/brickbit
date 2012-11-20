<?php
namespace module\core\controller;

use system\logic\Component;
use system\logic\EditComponent;
use system\InternalErrorException;

class Content extends \system\logic\Component {
	private static $pages;
	
	private $template = "PageContent";
	
	public function getName() {
		return "Content";
	}
	
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
	
//	public static function checkPermission($args) {
//		// URL non trasmessa o inesistente...
//		return true;
//	}
	
	public function getTemplate() {
		return $this->template;
	}
	
	public function onProcess() {
		$this->datamodel["menuItems"] = self::getPages();
		
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
			"image4_url", "image.width4", "image.height4",
				  
			"page.url",
			"page.title"
//			"page.body",
//			"page.style.code",
//			"page.style.page_template",
//			"page.sort_index",
//			"page.content_sorting",
//			"page.content_paging",
//			"page.content_filters",
//			"page.record_mode.read_mode",
//			"page.record_mode.owner_id",
//			"page.record_mode.group_id"
		);
		$contentBuilder->addReadModeFilters();
		
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

		$contentBuilder->setHasManyRelationBuilder("contents", $subcontentBuilder);
		// RELAZIONE 1-N RICORSIVA!!
		$subcontentBuilder->setHasManyRelationBuilder("contents", $subcontentBuilder);


		if (\array_key_exists("url", $_REQUEST)) {
			// Visualizzazione contenuto
			$contentBuilder->addFilter(new \system\model\FilterClause($contentBuilder->url, "=", $_REQUEST["url"]));
			if ($contentBuilder->countRecords()) {
				// URL valida... controllo i permessi di lettura sulla pagina
				$validContent = $contentBuilder->countRecords();
			}
		}
		
		else if (\array_key_exists("search", $_REQUEST)) {
			$search = $dl->sqlRealEscapeStrings($_REQUEST["search"]);
			
			$dl = \system\model\DataLayerCore::getInstance();
			
			$query = 
				"SELECT DISTINCT xct.content_id, xc.supercontent_id"
				. " FROM xmca_content_text xct"
				. " INNER JOIN xmca_content xc AS xc.id = xct.content_id"
				. " WHERE title LIKE '%" . $search . "%'"
				. " OR subtitle LIKE '%" . $search . "%'"
				. " OR body LIKE '%" . $search . "%'"
				. " OR preview LIKE '%" . $search . "%'";
			$res = $dl->executeQuery($query, __FILE__, __LINE__);
			
			$contentIds = array();
			
			while ($arr = $dl->sqlFetchArray($res)) {
				$contentIds[] = $arr["supercontent_id"] ? $arr["supercontent_id"] : $arr["content_id"];
			}
			
			
		}
		
		if ($validContent) {
			$recordset = $contentBuilder->selectFirst();

			$this->datamodel["content"] = $recordset;
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