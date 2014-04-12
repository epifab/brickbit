<?php
namespace module\node;

use system\Main;
use system\model2\RelationInterface;
use system\model2\RecordsetInterface;
use system\model2\Table;
use system\model2\clauses\FilterClauseInterface;
use system\utils\Lang;

class NodeEntity {
  /**
   * Returns the filters for the 'text' relationship
   * @param RelationInterface $nodeText
   * @param RecordsetInterface $node
   * @return FilterClauseInterface Filter clause
   */
  public static function textFilter(RelationInterface $nodeText, RecordsetInterface $node = null) {
    return $nodeText->filter('lang', Lang::getLang());
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
    $nodeTypes = NodeApi::nodeTypes();
    return $nodeTypes[$node->type]['children'];
  }
  
  /**
   * Returns an array of valid children types (according to the node type)-
   * @param RecordsetInterface $node
   * @return array List of node file types
   */
  public static function getValidFileKeys(RecordsetInterface $node) {
    $nodeTypes = NodeApi::nodeTypes();
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
      try {
        $table = Table::loadTable('content_' . $node->type);
        $table->import('*');
        $node->setExtra('content', $table->selectFirst($table->filter('node_id', $node->id)));
      }
      catch (\Exception $ex) {
        // Content table not found (likely)
        $node->setExtra('content', null);
      }
    }
    return $node->getExtra('content');
  }
  
  /**
   * Gets the url for a given node text
   * @param RecordsetInterface $node Node
   * @param RecordsetInterface $nodeText Node text
   * @return string URL
   */
  public static function getUrl(RecordsetInterface $node, RecordsetInterface $nodeText = null) {
    if (empty($nodeText)) {
      $nodeText = $node->text;
    }
    $urn = '';
    if (!empty($nodeText->urn)) {
      if ($node->type == 'page') {
        $urn = $nodeText->urn . '.html';
      } else {
        $urn = 'content/' . $nodeText->urn . '.html';
      }
    } else {
      $urn = 'content/' . $node->id;
    }
    return Main::getPathVirtual($urn, $nodeText->lang);
  }
  
  /**
   * Gets a list of node URL
   * @param RecordsetInterface $node Node
   * @return array URLS
   */
  public static function getUrls(RecordsetInterface $node) {
    $urls = array();
    foreach ($node->texts as $lang => $nodeText) {
      $urls[$lang] = self::getUrl($node, $nodeText);
    }
    return $urls;
  }
  
  /**
   * Node edit URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Edit URL
   */
  public static function getEditUrl(RecordsetInterface $recordset) {
    return Main::getPathVirtual('content/' . $recordset->id . '/edit');
  }
  
  /**
   * Node delete URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Delete URL
   */
  public static function getDeleteUrl(RecordsetInterface $recordset) {
    return Main::getPathVirtual('content/' . $recordset->id . '/delete');
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
      return Lang::translate('Untitled @type', array('@type' => $node->type));
    }
  }
  
  public static function ancestorsFilter(RelationInterface $ancestors, RecordsetInterface $parent = null) {
    if (empty($parent)) {
      $parent = $ancestors->getParentTable();
    }
    return $ancestors->filterGroup('AND')->addClauses(
      $ancestors->filter('rdel', $parent->rdel, '>'),
      $ancestors->filter('ldel', $parent->rdel, '<')
    );
  }
  
  public static function descendantsFilter(RelationInterface $descendants, RecordsetInterface $parent = null) {
    if (empty($parent)) {
      $parent = $descendants->getParentTable();
    }
    return $descendants->filterGroup('AND')->addClauses(
      $descendants->filter('rdel', $parent->rdel, '<'),
      $descendants->filter('ldel', $parent->rdel, '>')
    );
  }

}
