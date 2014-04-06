<?php
namespace module\node;

use system\Main;
use system\model2\Table;

class NodeApi {
  /**
   * Raises and caches nodeTypes event.
   * <pre>
   * array(
   *   'article' => array(
   *     'label' => Lang::translate('Article'),
   *     'children' => array('comment'), // allows comments on articles
   *     'files' => array('image', 'attachments')
   *   ),
   *   'comment' => array(...)
   * )
   * </pre>
   * @return array 
   */
  public static function nodeTypes() {
    return $nodeTypes = Main::invokeStaticMethodAllMerge('nodeTypes');
  }
  
  public static function nodeTableEntityAlter($table) {
    Main::invokeMethodAll('nodeTableEntityAlter', $table);
  }
  
  public static function nodeTableEntity() {
    $table = Table::loadTable('node');
    $table->import('*');
    // Allowing modules to import other table relations
    self::nodeTableEntityAlter($table);
  }
  
  public static function nodeCache($nodeId) {
    static $table = null;
    static $cache = array();
    
    if (empty($table)) {
    }
    
    if (!isset($cache[$nodeId])) {
      $cache[$nodeId] = $table->selectFirst($table->filter('id', $nodeId));
    }
  }
}