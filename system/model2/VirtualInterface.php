<?php
namespace system\model2;

interface VirtualInterface extends TablePropertyInterface {
  /**
   * Runs the virtual handler
   * @param \system\model2\RecordsetInterface $recordset Recordset
   * @return mixed Virtual property value
   */
  public function runHandler(RecordsetInterface $recordset);
}