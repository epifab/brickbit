<?php
namespace module\node;

use module\crud\CrudController;
use system\Main;
use system\model2\RecordsetInterface;

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

  /**
   * Allows other modules to do special fixings before rendering the form
   * @param RecordsetInterface $node Node
   * @param CrudController $component Edit component
   */
  public static function onEditNode(RecordsetInterface $node, CrudController $component) {
    Main::invokeMethodAll('onEditNode', $node, $component);
  }
}