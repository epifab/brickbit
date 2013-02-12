<?php
namespace module\core;

class Core extends \system\logic\Module {
	/**
	 * Define node types and variables allowed files / node children
	 * @return type 
	 */
	public static function nodeTypes() {
		return array(
			'#' => array(
				'page'
			),
			'page' => array(
				'label' => \t('Page'),
				'children' => array(
					'article',
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

	}
}
?>