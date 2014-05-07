<?php
namespace module\node;

use system\model2\RecordsetInterface;

interface NodeCrudInterface {
  /**
   * @return RecordsetInterface Node
   */
  function getNode();
}