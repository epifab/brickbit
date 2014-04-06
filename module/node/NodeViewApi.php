<?php
namespace module\node;

use system\Main;
use system\view\View;

class NodeViewApi {
  public static function displayNode($node, $display = 'default') {
    $templates = array(
      'node-'. $display . '--' . $node->id,
      'node-'. $display . '-' . $node->type,
      'node-'. $display
    );
    if ($display != 'default') {
      $templates += array(
        'node-default--' . $node->id,
        'node-default-' . $node->type,
        'node-default'
      );
    }
    
    foreach ($templates as $t) {
      if (Main::templateExists($t)) {
        View::import($t, array('node' => $node));
        return;
      }
    }
  }
  
  public static function editNode($node, $display = 'default') {
    $templates = array(
      'edit-node-'. $display . '--' . $node->id,
      'edit-node-'. $display . '-' . $node->type,
      'edit-node-'. $display
    );
    if ($display != 'default') {
      $templates += array(
        'edit-node-default--' . $node->id,
        'edit-node-default-' . $node->type,
        'edit-node-default'
      );
    }
    
    foreach ($templates as $t) {
      if (Main::templateExists($t)) {
        View::import($t, array('node' => $node));
        return;
      }
    }
  }
}