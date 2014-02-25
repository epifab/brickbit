<?php
namespace system\view;

/**
 * This class extends the Form functionalities in order to simplify the
 *  Recordset editable
 */
class FormRecordset extends Form {
  /**
   * Caches the form recordsets.
   * This has been made static in order not to serialize the entire recordsets
   *  with their builders into the session.
   * @var \system\model\RecordsetInterface[][]
   */
  private static $formRecordsets = array();
  
  private $recordsetsInfo = array();
  
  /**
   * Initialize a FormRecordset object
   * @param string $id Form id
   * @param string $class Form class name
   * @return \system\view\FormRecordset Form object
   */
  public static function initForm($id, $class = '\\system\\view\\FormRecordset') {
    return Form::initForm($id, $class);
  }
  
  /**
   * Attach a recordset to the form
   * @param string $name Recordset name
   * @param \system\model\RecordsetInterface $recordset
   */
  public function addRecordset($name, \system\model\RecordsetInterface $recordset) {
    // Caching the recordset
    self::$formRecordsets[$this->getId()][$name] = $recordset;
    
    // Make sure the recordset info are not overridden if the form has been
    //  submitted (for example recordset input, which may have been already 
    //  added by the form template)
    if (!isset($this->recordsetsInfo[$name])) {
      $this->recordsetsInfo[$name] = array(
        'name' => $name,
        // Saving the recordset table name and its primary key, we wil be able to
        //  re-build it. No need to save the entire recordset object (and the 
        //  related recordset builder)
        'table' => $recordset->getBuilder()->getTableName(),
        'key' => $recordset->getPrimaryKey(),
        // All the form input which are related to the recordset
        // This array will contain an association path => name, where name
        //  represents the form input name ($this->input[$name])
        //  while path represents the recordset field path
        'input' => array()
      );
    }
  }
  
  /**
   * Attach a recordset input to the form
   * @param string $recordsetName Name of the recordset
   * @param string $name Input name
   * @param string $path Field path
   */
  public function addRecordsetInput($recordsetName, $name, $path) {
    if (!isset($this->recordsetsInfo[$recordsetName])) {
      throw new \system\exceptions\InternalError('Unknown recordset @name', array('@name' => $recordsetName));
    }
    $this->recordsetsInfo[$recordsetName]['input'][$path] = $name;
  }
  
  /**
   * Returns recordsets attached to this form
   * @return \system\model\RecordsetInterface[] Recordset list
   */
  public function getRecordsets() {
    return self::$formRecordsets[$this->getId()];
  }
  
  /**
   * Returns a recordset attached to this form
   * @param string $name Recordset name
   * @return \system\model\RecordsetInterface Recordset
   */
  public function getRecordset($name) {
    return (isset(self::$formRecordsets[$this->getId()][$name]))
      ? self::$formRecordsets[$this->getId()][$name]
      : null;
  }

  /**
   * Fetch recordset fields
   */
  public function onSubmission() {
    foreach ($this->recordsetsInfo as $recordsetInfo) {
      foreach ($recordsetInfo['input'] as $path => $name) {
        $this
          ->getRecordset($recordsetInfo['name'])
          ->setProg($path, $this->input[$name]['value']);
      }
    }
  }
}
