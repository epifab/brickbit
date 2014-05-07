<?php
namespace module\node;

use system\Main;

class NodeApi {
  /**
   * Usage example
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
    $nodeTypes = Main::invokeStaticMethodAllMerge('nodeTypes');
    foreach (Main::moduleImplements('nodeTypesAlter') as $callback) {
      \call_user_func_array($callback, array(&$nodeTypes));
    }
    return $nodeTypes;
  }
}