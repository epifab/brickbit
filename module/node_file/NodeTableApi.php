<?php
namespace module\node_file;

use system\model2\RecordsetInterface;
use system\utils\File;
use module\node\NodeApi;

class NodeTableApi {
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
   * Returns files grouped by node index
   * @param RecordsetInterface $node
   * @return RecordsetInterface[][] List of node files grouped by node index
   */
  public static function getFiles(RecordsetInterface $node) {
    if ($node->getExtra('files', false) === false) {
      $files = array();
      foreach ($node->all_files as $nodeFile) {
        $files[$nodeFile->node_index][$nodeFile->file_id] = $nodeFile;
      }
      $node->setExtra('files', $files);
    }
    return $node->getExtra('files');
  }
}
