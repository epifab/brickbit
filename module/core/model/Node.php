<?php
namespace module\core\model;

use system\model\RecordsetBuilder;
use system\model\RecordsetInterface;
use system\model\FilterClauseGroup;
use system\model\FilterClause;
use system\model\SortClause;

class Node {
  public static function children_groups(RecordsetInterface $node) {
    static $children = array();
    if (!isset($children[$node->id])) {
      $children[$node->id] = array();
      foreach ($node->children as $child) {
        $children[$node->id][$node->type][$child->id] = $child;
      }
    }
    return $children[$node->id];
  }
  
  public static function content(RecordsetInterface $node) {
    static $contents = array();
    if (!isset($contents[$node->id])) {
      try {
        $rsb = new RecordsetBuilder('content_' . $node->type);
        $rsb->using('*');
        $r = $rsb->selectFirstBy(array('node_id' => $node->id));
      } catch (\Exception $ex) {
        $r = null;
      }
      $contents[$node->id] = $r;
    }
    return $contents[$node->id];
  }
  
//  public static function node_text(\system\model\RecordsetInterface $node) {
//    static $texts = array();
//    if (!isset($texts[$node->id])) {
//      $rsb = new \system\model\RecordsetBuilder('text');
//      $rsb->using('*');
//      $rsb->setSort(new \system\model\SortClause($rsb->lang, 'DESC'));
//      $rsb->setFilter(
//        new \system\model\FilterClauseGroup(
//          new \system\model\FilterClause($rsb->lang, '=', \system\utils\Lang::getLang()),
//          'OR',
//          new \system\model\FilterClause($rsb->lang, 'IS_NULL')
//        )
//      );
//      $texts[$node->id] = $rsb->selectFirst();
//    }
//    return $texts[$node->id];
//  }
  
  public static function url(\system\model\RecordsetInterface $recordset) {
    if ($recordset->text->urn) {
      if ($recordset->type == 'page') {
        return $recordset->text->urn . '.html';
      } else {
        return 'content/' . $recordset->text->urn . '.html';
      }
    } else {
      return 'content/' . $recordset->id;
    }
  }
  
  public static function edit_url(\system\model\RecordsetInterface $recordset) {
    return 'content/' . $recordset->id . '/edit';
  }
  
  public static function delete_url(\system\model\RecordsetInterface $recordset) {
    return 'content/' . $recordset->id . '/delete';
  }
  
  public static function title(RecordsetInterface $node) {
    if ($node->text->title) {
      return $node->text->title;
    } else {
      return \t('Untitled @type', $node->type);
    }
  }
  
//  public static function text(RecordsetInterface $node) {
//    if (isset($node->texts[\system\utils\Lang::getLang()])) {
//      return $node->texts[\system\utils\Lang::getLang()];
//    } else if (isset($node->texts[null])) {
//      return $node->texts[null];
//    } else {
//      $first = \current($node->texts);
//      !empty($first) ? $first : null;
//    }
//  }
  
  public static function textFilter(RecordsetInterface $node, RecordsetBuilder $textBuilder) {
    $textBuilder->addFilter(new FilterClauseGroup(
      new FilterClause($textBuilder->lang, 'IS_NULL'),
      'OR',
      new FilterClause($textBuilder->lang, '=', \system\utils\Lang::getLang())
    ));
    $textBuilder->setSort(new SortClause($textBuilder->lang, 'DESC'));
    $textBuilder->setLimit(1);
  }
  
  public static function text_und(RecordsetInterface $node) {
    return $node->texts[null];
  }
  
//  public static function text_en(RecordsetInterface $node) {
//    return $node->texts['en'];
//  }
//  
//  public static function text_it(RecordsetInterface $node) {
//    return $node->texts['it'];
//  }
//  
//  public static function text_de(RecordsetInterface $node) {
//    return $node->texts['de'];
//  }
  
//  public static function textUndFilter(RecordsetInterface $node, RecordsetBuilder $textBuilder) {
//    $textBuilder->addFilter(new \system\model\FilterClause(
//      $textBuilder->lang, 'IS_NULL'
//    ));
//  }
  
//  public static function ancestors(\system\model\RecordsetInterface $child) {
//    static $ancestors = array();
//    if (!isset($ancestors[$child->id])) {
//      $builder = new \system\model\RecordsetBuilder('node');
//      $builder->using('*');
//      $builder->setFilter(new \system\model\FilterClauseGroup(
//        new FilterClause($builder->rdel, '>', $child->rdel),
//        'AND',
//        new FilterClause($builder->ldel, '<', $child->ldel)
//      ));
//      $builder->setSort(new SortClause(
//        $builder->ldel, 'DESC'
//      ));
//      $ancestors[$child->id] = $builder->select();
//    }
//    return $ancestors[$child->id];
//  }
//  
//  public static function descendants(\system\model\RecordsetInterface $child) {
//    static $descendants = array();
//    if (!isset($descendants[$child->id])) {
//      $builder = new \system\model\RecordsetBuilder('node');
//      $builder->using('*');
//      $builder->setFilter(new \system\model\FilterClauseGroup(
//        new FilterClause($builder->rdel, '<', $child->rdel),
//        'AND',
//        new FilterClause($builder->ldel, '>', $child->ldel)
//      ));
//      $builder->setSort(new SortClause(
//        $builder->rdel, 'DESC'
//      ));
//      $descendants[$child->id] = $builder->select();
//    }
//    return $descendants[$child->id];
//  }

}
?>