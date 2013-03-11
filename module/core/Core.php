<?php
namespace module\core;

class Core extends \system\logic\Module {
	public static function getNodeUrn(\system\model\RecordsetInterface $recordset) {
		if ($recordset->text->urn) {
			if ($recordset->type == 'page') {
				return $recordset->text->urn . '.html';
			} else {
				return 'content/' . $recordset->text->urn . '.html';
			}
		} else {
			return \config\settings()->BASE_DIR . 'content/' . $recordset->id;
		}
	}
	
	public static function getEditNodeUrn(\system\model\RecordsetInterface $recordset) {
		return 'content/' . $recordset->id . '/edit';
	}
	
	public static function getDeleteNodeUrn(\system\model\RecordsetInterface $recordset) {
		return 'content/' . $recordset->id . '/delete';
	}
	
	/**
	 * Define node types and variables allowed files / node children
	 * @return type 
	 */
	public static function nodeTypes() {
		return array(
			'#' => array(
				'page'
			),
			'profile' => array(
				'label' => \t('User profile'),
				'file' => array('avatar'),
				'children' => array() // no children
			),
			'page' => array(
				'label' => \t('Page'),
				'children' => array(
					'article'
				),
				'files' => array() // no files allowed for pages
			),
			'article' => array(
				'label' => \t('Article'),
				'children' => array(
					'gallery',
					'audioplayer',
					'videoplayer',
					'comment'
				),
				'files' => array(
					'image',
					'downloads' // has many relation
				)
			),
			'photogallery' => array(
				'label' => \t('Photo gallery'),
				'children' => array(
					'photo',
					'comment'
				),
				'files' => array()
			),
			'photo' => array(
				'label' => \t('Photo'),
				'children' => array(
					'comment'
				),
				'files' => array(
					'image'
				)
			),
			'audioplayer' => array(
				'label' => \t('Audio player'),
				'children' => array(
					'audiotrack',
					'comment'
				),
				'files' => array(
					'image'
				)
			),
			'audiotrack' => array(
				'label' => \t('Audio track'),
				'children' => array(
					'comment'
				),
				'files' => array(
					'image',
					'track',
				)
			),
			'videoplayer' => array(
				'label' => \t('Video player'),
				'children' => array(
					'comment'
				),
				'files' => array(
					'image',
					'video'
				)
			),
			'comment' => array(
				'label' => \t('Comment'),
				'children' => array(
					'comment'
				),
				'files' => array()
			)
		);
	}

	
	public static function cron() {

	}
	
	public static function onRun(\system\logic\Component $component) {
		$component->addTemplate('website-logo', 'header');
		$component->addTemplate('langs-control', 'header-sidebar');
		$component->addTemplate('footer', 'footer');
//		$component->addTemplate('sidebar', 'sidebar');
		
		$component->addJs(\system\logic\Module::getAbsPath('core', 'js') . 'core.js');
		$component->addCss(\system\Theme::getThemePath() . 'css/upload-jquery/jquery.fileupload-ui.css');
	}
}
?>