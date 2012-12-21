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

class Page extends Component {

	public static function access($action, $urlArgs, $args, $userId) {
		if (empty($urlArgs)) {
			return true;
		}
		
		list($pageUrn) = $urlArgs;
		
		$rs = new RecordsetBuilder('content');
		$rs->using('urn', 'type');
		$rs->setFilter(new FilterClauseGroup(
			new FilterClause($rs->urn, '=', $pageUrn),
			'AND',
			new FilterClause($rs->type, '=', $page)
		));
		
		if ($rs->countRecords() > 0) {
			switch ($action) {
				case "Edit":
					$rs->addEditModeFilters();
					break;
				case "Delete":
					$rs->addDeleteModeFilters();
					break;
				default:
					$rs->addReadModeFilters();
					break;
			}
			return $rs->countRecords() > 0;
		}
		return true;
	}
	
	public static function accessCreate($action, $urlArgs, $args, $userId) {
		
	}

	public function onRead() {
		$this->setOutlineTemplate('Page');
		
		list($pageUrn) = $this->getUrlArgs();

		$rs = new RecordsetBuilder('node');
		$rs->using(
			'*',
			'text.*',
			'image.*',
			'image.file1.*',
			'image.file2.*',
			'image.file3.*',
			'image.file4.*',
			'tags.*',
			'columns.*',
			'columns.contents.*'
		);
		$this->datamodel['page']['rs'] = $rs->selectFirstBy($pageUrn);
		
		return Component::RESPONSE_TYPE_READ;
	}
}
?>