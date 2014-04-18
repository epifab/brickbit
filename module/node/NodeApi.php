<?php
namespace module\node;

use system\Main;

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
}