<?php
namespace system\view;

use system\session\Session;

class Form {
  /**
   * @var \system\view\Form
   */
  private static $instance;
  /**
   * @var \system\view\Form
   */
  private static $submitted;
  
  private $name;
  private $input = array();
  private $errors = array();
  private $recordsets = array();
  private $data = array();
  private $timestamp;
  
  private function __construct($name) {
    $this->timestamp = \time();
    $this->name = $name;
  }
  
  public static function startForm($name) {
    if (!empty(self::$instance)) {
      throw new \system\exceptions\InternalError('Illegal nested form.');
    }
    $form = self::getForm($name);
    if (empty($form)) {
      $form = new self($name);
      Session::getInstance()->set('forms', $name, $form);
    }
    self::$instance = $form;
  }

  public static function closeForm() {
    self::$instance = null;
  }
  
  /**
   * Return the form (if exists)
   * @param string $name Form name
   * @return \system\view\Form
   */
  public static function getForm($name) {
    return Session::getInstance()->get('forms', $name);
  }
  
  /**
   * Return the form (if exists)
   * @return \system\view\Form
   */
  public static function getCurrent() {
    return self::$instance;
  }
  
  public function attach($name, $value) {
    $this->data[$name] = $value;
  }
  
  public function addRecordset($name, \system\model\RecordsetInterface $recordset) {
    $this->recordsets[$name] = array(
      'name' => $name,
      'table' => $recordset->getBuilder()->getTableName(),
      'key' => $recordset->getPrimaryKey(),
      'input' => array()
    );
  }
  
  public function addRecordsetInput($recordsetName, $name, $path) {
    if (isset($this->recordsets[$recordsetName])) {
      $rs =& $this->recordsets[$recordsetName];
      $rs['input'][$path] = $name;
    }
  }
  
  public function addInput($name, $widget, $defaultValue, array $input = array(), $metaType = null) {
    if (!isset($this->input[$name])) {
      $this->input[$name] = array(
        'name' => $name,
        'value' => $defaultValue,
        'widget' => $widget,
        'metaType' => $metaType
      ) + $input;
    }
    return $this->input[$name]['value'];
  }
  
  public function renderInput($name) {
    $input = @$this->input[$name];
    if ($input) {
      return \system\view\Widget::getWidget($input['widget'])->render($input);
    }
  }
  
  public static function getPostedFormId() {
    if (isset($_REQUEST['system']) && isset($_REQUEST['system']['formId'])) {
      return $_REQUEST['system']['formId'];
    }
    return null;
  }

  /**
   * Returns the posted form
   * @param string $formId [optional] If passed, the posted form id must match it
   * @return \system\view\Form Null if no form has been submitted or the submitted form id doesn't match with the formId argument
   */
  public static function getPostedForm($formId = null) {
    if (empty($formId) || $formId == self::getPostedFormId()) {
      if (!isset(self::$submitted)) {
        $name = self::getPostedFormId();
        if (!\is_null($name)) {
          $form = self::getForm($name);
          if ($form) {
            $form->fetchInputValues();
            $form->fetchRecordsets();
            self::$submitted = $form;
          }
        }
      }
      return self::$submitted;
    }
    return null;
  }
  
  /**
   * Checks whether a form has been submitted
   * @return boolean
   */
  public static function checkFormSubmission($formId = null) {
    return
      isset($_REQUEST['system'])
      && isset($_REQUEST['system']['formId'])
      && Session::getInstance()->exists('forms', $_REQUEST['system']['formId'])
      && (empty($formId) || $formId == $_REQUEST['system']['formId']);
  }
  
  private static function getInputPostedValue(array $input) {
    $haystack = $_REQUEST;
    
    $needles = \preg_split('/(\[|\])+/', $input['name'], 0, PREG_SPLIT_NO_EMPTY);
    if (count($needles)) {
      foreach ($needles as $needle) {
        if (\array_key_exists($needle, $haystack)) {
          $haystack = $haystack[$needle];
        } else {
          return null;
        }
      }
      return \system\view\Widget::getWidget($input['widget'])->fetch($haystack, $input);
    } else {
      return null;
    }
  }
  
  private function fetchInputValues() {
    foreach ($this->input as &$input) {
      $input['value'] = self::getInputPostedValue($input['name']);
      $input['error'] = null;
      
      $this->errors[$input['name']] = null;

      $mt = $input['metaType'];
      if ($mt) {
        try {
          $mt->validate($input['value']);
        } catch (\system\exceptions\ValidationError $ex) {
          $this->errors[$input['name']] = $ex->getMessage();
        }
      }
    }
  }
  
  private function fetchRecordsets() {
    foreach ($this->recordsets as $recordset) {
      $rsObj = $recordset['recordset'];
      foreach ($recordset['input'] as $path => $name) {
        $rsObj->setProg($path, $this->input[$name]['value']);
      }
    }
  }
  
  public function getRecordset($name) {
    static $rs = array();
    if (!isset($rs[$name])) {
      if (isset($this->recordsets[$name])) {
        $rsb = new \system\model\RecordsetBuilder($this->recordsets[$name]['table']);
        $rsb->usingAll();
        if (empty($this->recordsets[$name]['key'])) {
          $rs[$name] = $rsb->selectFirstBy($this->recordsets[$name]['key']);
        } else {
          $rs[$name] = $rsb->newRecordset();
        }
      }
    }
    return isset($rs[$name])
      ? $rs[$name]
      : null;
  }
  
  public function getName() {
    return $this->name;
  }
  
  public function getInput() {
    return $this->input;
  }
  
  public function getTimestamp() {
    return $this->timestamp;
  }
  
  public function getData() {
    return $this->data;
  }
  
  public function inputErrorCount() {
    return \count($this->errors);
  }
  
  public function inputErrors() {
    return $this->errors;
  }
}
