<?php
namespace module\core\model;

use system\Main;
use system\model2\RelationInterface;
use system\model2\RecordsetInterface;
use system\model2\Table;
use system\model2\clauses\FilterClauseInterface;

class Node {
  /**
   * Returns the filters for the 'text' relationship
   * @param RelationInterface $nodeText
   * @param RecordsetInterface $node
   * @return FilterClauseInterface Filter clause
   */
  public static function textFilter(RelationInterface $nodeText, RecordsetInterface $node = null) {
    return $nodeText->filter('lang', \system\utils\Lang::getLang());
  }

  /**
   * Selects node children borrowing the table interface from the parent node.
   * @param RecordsetInterface $node
   * @return RecordsetInterface[] Node children
   */
  public static function getChildrenRecursive(RecordsetInterface $node) {
    if ($node->getExtra('children_recursive', false) === false) {
      $table = $node->getTable();
      $node->setExtra('children_recursive', $table->select($table->filter('parent_id', $node->id)));
    }
    return $node->getExtra('children_recursive', array());
  }
  
  /**
   * Returns an array of valid children types (according to the node type)-
   * @param RecordsetInterface $node
   * @return array List of node types
   */
  public static function getValidChildrenTypes(RecordsetInterface $node) {
    $nodeTypes = Main::invokeStaticMethodAllMerge('nodeTypes');
    return $nodeTypes[$node->type]['children'];
  }
  
  /**
   * Returns an array of valid children types (according to the node type)-
   * @param RecordsetInterface $node
   * @return array List of node file types
   */
  public static function getValidFileKeys(RecordsetInterface $node) {
    $nodeTypes = Main::invokeStaticMethodAllMerge('nodeTypes');
    return $nodeTypes[$node->type]['files'];
  }
  
  /**
   * Groups node children by their type
   * @param RecordsetInterface $node
   * @return array Array like: ([type1] => array(...), [type2] => array(...))
   */
  public static function getChildrenGroupedByType(RecordsetInterface $node) {
    if ($node->getExtra('children_grouped_by_type', false) === false) {
      $children = array();
      foreach ($node->children as $child) {
        $children[$child->type][$child->id] = $child;
      }
      $node->setExtra('children_grouped_by_type', $children);
    }
    return $node->getExtra('children_grouped_by_type');
  }
  
  /**
   * Node content record.
   * @param RecordsetInterface $node
   * @return RecordsetInterface
   */
  public static function getContent(RecordsetInterface $node) {
    if ($node->getExtra('content', false) === false) {
      $content = null;
      try {
        $table = Table::loadTable('content_' . $node->type);
        $table->import('*');
        $content = $table->selectFirst($table->filter('node_id', $node->id));
      }
      catch (\Exception $ex) {
        // Content table not found (likely)
      }
      $node->setExtra('content', $content);
    }
    return $node->getExtra('content');
  }
  
  /**
   * Node URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string URL
   */
  public static function getUrl(RecordsetInterface $recordset) {
    $urn = '';
    if ($recordset->text->urn) {
      if ($recordset->type == 'page') {
        $urn = $recordset->text->urn . '.html';
      } else {
        $urn = 'content/' . $recordset->text->urn . '.html';
      }
    } else {
      $urn = 'content/' . $recordset->id;
    }
    return Main::getUrl($urn);
  }
  
  /**
   * Node edit URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Edit URL
   */
  public static function getEditUrl(RecordsetInterface $recordset) {
    return Main::getUrl('content/' . $recordset->id . '/edit');
  }
  
  /**
   * Node delete URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Delete URL
   */
  public static function getDeleteUrl(RecordsetInterface $recordset) {
    return Main::getUrl('content/' . $recordset->id . '/delete');
  }
  
  /**
   * Node title
   * @param RecordsetInterface $node Node recordset
   * @return string Title
   */
  public static function getTitle(RecordsetInterface $node) {
    if ($node->text->title) {
      return $node->text->title;
    } else {
      return \t('Untitled @type', $node->type);
    }
  }
}
