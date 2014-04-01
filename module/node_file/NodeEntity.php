<?php
namespace module\node_file;

use system\model2\RecordsetInterface;
use module\node\NodeEvents;

class NodeEntity {
  /**
   * Returns an array of valid children types (according to the node type)-
   * @param RecordsetInterface $node
   * @return array List of node file types
   */
  public static function getValidFileKeys(RecordsetInterface $node) {
    $nodeTypes = NodeEvents::nodeTypes();
    return $nodeTypes[$node->type]['files'];
  }
}
