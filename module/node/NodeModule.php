<?php
namespace module\node;

use system\model2\TableInterface;
use system\utils\Lang;

class NodeModule {
  /**
   * Implements recordsetTableInit() event
   */
  public static function recordsetTableInit(TableInterface $table) {
    switch ($table->getName()) {
      case 'node':
        $table->import(
          '*',
          'record_mode.*',
          'record_mode.users.user_id',
          'record_mode.roles.role_id',
          'text.*',
          'texts.*'
        );
        break;
    }
  }
  
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