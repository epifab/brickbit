<?php
namespace module\node;

use \system\utils\Lang;

class NodeModule {
  public static function cron() {
    // Delete temp nodes
  }
  
  public static function nodeTypes() {
    return array(
      '#' => array(
        'page' // Only pages on the master level
      ),
      'page' => array(
        'label' => Lang::translate('Page'),
        'children' => array('article'),
        'files' => array() // no files allowed for pages?
      ),
      'article' => array(
        'label' => Lang::translate('Article'),
        'children' => array(),
        'files' => array('image', 'downloads')
      ),
    );
  }
}