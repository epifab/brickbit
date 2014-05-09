<?php
namespace module\crud;

use system\session\Session;
use system\model2\RecordsetInterface;
use system\metatypes\MetaType;
use system\exceptions\InternalError;
use system\exceptions\ValidationError;
use system\view\Widget;

class Form {
  /**
   * @var Form
   */
  private static $instance;

  /**
   * Caches the form recordsets.
   * This has been made static in order not to serialize the entire recordsets
   *  with their builders into the session.
   * @var RecordsetInterface[][]
   */
  private static $formRecordsets = array();

  private $recordsetsInfo = array();

  protected $id;
  protected $inputInfo = array();
  protected $errors = array();
  protected $data = array();
  protected $timestamp;

  protected $settings = array();

  private function __construct($id) {
    $this->timestamp = \time();
    $this->id = $id;
  }

  /**
   * Initialize a form.
   * This is tipically called from the control layer before the form is
   *  rendered.
   * @param string $id Form id
   * @param string $class Form class name (must extend the Form
   *  class) [optional]
   * @return Form
   */
  public static function initForm($id, $delegateClass = '\module\crud\Form') {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
      // Unless we are posting data, we destroy the form
      self::destroyForm($id);
    }

    $form = self::getForm($id);
    if (empty($form)) {
      if (!\class_exists($delegateClass)) {
        throw new InternalError('Class @class not found.', array('@class' => $delegateClass));
      }
      $form = new $delegateClass($id);
      if (!($form instanceof self)) {
        throw new InternalError('Invalid form class @class', array('@class' => $delegateClass));
      }
      Session::getInstance()->set('forms', $id, $form);
    }
    // Make sure there are not previous validation errors
    $form->resetValidationErrors();
    return $form;
  }

  /**
   * Removes a form from the session.
   * @param string $id Form ID
   */
  public static function destroyForm($id) {
    Session::getInstance()->remove('forms', $id);
  }

  /**
   * Removes this form from the session
   */
  public function destroy() {
    self::destroyForm($this->getId());
  }

  /**
   * Starts a form.
   * This must be called from the view layer.
   * @param string $id Form id
   * @throws InternalError
   */
  public static function startForm($id) {
    if (!empty(self::$instance)) {
      throw new InternalError('Illegal nested form.');
    }
    $form = self::getForm($id);
    if (empty($form)) {
      throw new InternalError('Starting a uninitialized form.');
    }
    self::$instance = $form;
  }

  /**
   * Closes the current form (after this has been started)
   */
  public static function closeForm() {
    if (empty(self::$instance)) {
      throw new InternalError('No form has been open.');
    }
    self::$instance = null;
  }

  /**
   * Return the form (if exists)
   * @param string $id Form id
   * @return Form
   */
  public static function getForm($id) {
    return Session::getInstance()->get('forms', $id);
  }

  /**
   * Return the form (if exists)
   * @return Form
   */
  public static function getCurrent() {
    return self::$instance;
  }

  /**
   * Attach data to the form
   * @param string $key Key
   * @param mixed $value Value
   */
  public function attach($key, $value) {
    $this->data[$key] = $value;
  }

  /**
   * Add a form input
   * @param string $name Input name
   * @param string $widget Widget name to render the input
   * @param mixed $defaultValue Default input value
   * @param array $input Input parameters [optional]
   * @param MetaType $metaType Meta type associated with the
   *  input[optional]
   * @return mixed The current input value
   */
  public function addInput($name, $widgetName, $defaultValue, array $input = array(), $metaType = null) {
    if (!isset($this->inputInfo[$name])) {
      // Initialize the input
      $this->inputInfo[$name] = array();
    }

    $this->inputInfo[$name] = array(
      'name' => $name,
      'state' => isset($this->inputInfo[$name]['state']) ? $this->inputInfo[$name]['state'] : $defaultValue,
      'widget' => $widgetName,
      'metaType' => $metaType
    ) + $input + (!empty($metaType) ? $metaType->getAttributes() : array());

    return $this->inputInfo[$name]['state'];
  }

  /**
   * Removes an input
   * @param string $name Input name
   */
  public function removeInput($name) {
    unset($this->inputInfo[$name]);
  }

  /**
   * Renders a input
   * @param string $name Input name
   * @return string Rendered input (HTML code)
   */
  public function renderInput($name) {
    if (!empty($this->inputInfo[$name])) {
      $input = $this->inputInfo[$name];
      return Widget::getWidget($input['widget'])->render($input);
    }
  }

  /**
   * Checks whether or not the form has been submitted
   * @return boolean TRUE if the form has been submitted
   */
  public function checkSubmission() {
    return $this->getId() && self::getPostedFormId() == $this->getId();
  }

  /**
   * Fetch input values
   */
  public function submission() {
    $this->fetchInputValues();
    $this->fetchRecordsets();
    $this->onSubmission();
    return $this->countValidationErrors() == 0;
  }

  /**
   * Allows extending classes to do something on submission
   */
  public function onSubmission() {

  }

  /**
   * Returns the current posted form ID
   * @return string Form ID (if any)
   */
  public static function getPostedFormId() {
    return (isset($_REQUEST['system']) && isset($_REQUEST['system']['formId']))
      ? $_REQUEST['system']['formId']
      : null;
  }

  /**
   * Returns the input value
   * @param array $input Input info
   * @return mixed Input submitted value as returned by the widget fetch method
   */
  private static function getInputPostedValue(array $input) {
    $haystack = &$_REQUEST;

    // Handles with input name like foo[bar][foo] -> $_REQUEST[foo][bar][foo]
    $needles = \preg_split('/(\[|\])+/', $input['name'], 0, PREG_SPLIT_NO_EMPTY);
    if (count($needles)) {
      foreach ($needles as $needle) {
        if (\array_key_exists($needle, $haystack)) {
          $haystack = &$haystack[$needle];
        }
        else {
          // Not transmitted
          return null;
        }
      }
      return Widget::getWidget($input['widget'])->fetch($haystack, $input);
    } else {
      return null;
    }
  }

  /**
   * Fetch a input value
   * @param string $inputName Input name
   * @throws ValidationError
   */
  public function fetchInputValue($inputName) {
    $input = &$this->inputInfo[$inputName];

    $input['state'] = self::getInputPostedValue($input);

    if ($input['metaType']) {
      // Metatype validation
      $input['metaType']->validate($input['state']);
    }

    return $input['state'];
  }

  /**
   * Fetch every form input
   */
  private function fetchInputValues() {
    $this->errors = array(); // Reset errors

    foreach ($this->inputInfo as &$input) {
      $input['state'] = self::getInputPostedValue($input);

      $mt = $input['metaType'];
      if ($mt) {
        try {
          // Metatype validation
          $mt->validate($input['state']);
        }
        catch (ValidationError $ex) {
          $this->errors[$input['name']] = $ex->getMessage();
        }
      }
    }
  }

  /**
   * Get form ID
   * @return string Form ID
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Get form input array
   * @return array Form input
   */
  public function getInput() {
    return $this->inputInfo;
  }

  /**
   * Get input value
   * @param string $inputName Input name
   * @return mixed Input value (null if the input does not exist)
   */
  public function getInputValue($inputName) {
    return isset($this->inputInfo[$inputName])
      ? $this->inputInfo[$inputName]['state']
      : null;
  }

  /**
   * Get form timestamp
   * @return time Form timestamp
   */
  public function getTimestamp() {
    return $this->timestamp;
  }

  /**
   * Get form attached data
   * @return array Form data
   */
  public function getData() {
    return $this->data;
  }

  /**
   * Returns the validation error related to the input
   * @param string $inputName Input name
   * @return string Error message or false if no errors occurred
   */
  public function getValidationError($inputName) {
    return isset($this->errors[$inputName])
      ? $this->errors[$inputName]
      : false;
  }

  /**
   * Returns the validation error message list.
   * @return string[] Validation errors
   */
  public function getValidationErrors() {
    return $this->errors;
  }

  /**
   * Set a validation error. Allows components to set a validation error
   * @param string $inputName Input name
   * @param string $errorMessage Error message
   */
  public function setValidationError($inputName, $errorMessage) {
    $this->errors[$inputName] = $errorMessage;
  }

  /**
   * Unset a validation error.
   * @param string $inputName Input name
   */
  public function unsetValidationError($inputName) {
    unset($this->errors[$inputName]);
  }

  /**
   * Reset all validation errors
   */
  public function resetValidationErrors() {
    $this->errors = array();
  }

  /**
   * Count validation errors
   * @return int Number of errors
   */
  public function countValidationErrors() {
    return count($this->errors);
  }

  /**
   * Attach a recordset to the form
   * @param string $name Recordset name
   * @param RecordsetInterface $recordset
   */
  public function addRecordset($name, RecordsetInterface $recordset) {
    // Caching the recordset
    self::$formRecordsets[$this->getId()][$name] = $recordset;

    // Make sure the recordset info are not overridden if the form has been
    //  submitted (for example recordset input, which may have been already
    //  added by the form template)
    if (!isset($this->recordsetsInfo[$name])) {
      $this->recordsetsInfo[$name] = array(
        'name' => $name,
        // Saving the recordset table name and its primary key, we wil be able
        //  to re-build it. No need to save the entire recordset object
        'table' => $recordset->getTable()->getTableName(),
        'key' => $recordset->getPrimaryKey(),
        // All the form input which are related to the recordset
        // This array will contain an association path => name, where name
        //  represents the form input name ($this->inputInfo[$name])
        //  while path represents the recordset field path
        'input' => array()
      );
    }
  }

  /**
   * Removes recordset input.
   * @param string $recordsetName Recordset name
   * @param array $paths Field paths. If not specified, it removes every field
   */
  public function removeRecordsetInput($recordsetName, $paths = array()) {
    if (isset($this->recordsetsInfo[$recordsetName])) {
      if (empty($paths)) {
        // Remove every input
        foreach ($this->recordsetsInfo[$recordsetName]['input'] as $path => $inputName) {
          unset($this->inputInfo[$inputName]);
          unset($this->recordsetsInfo[$recordsetName]['input'][$path]);
        }
      }
      else {
        // Remove only input specified in path
        $paths = (array)$paths;
        foreach ($paths as $path) {
          if (isset($this->recordsetsInfo[$recordsetName]['input'][$path])) {
            unset($this->inputInfo[$this->recordsetsInfo[$recordsetName]['input'][$path]]);
            unset($this->recordsetsInfo[$recordsetName]['input'][$path]);
          }
        }
      }
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
      throw new InternalError('Unknown recordset @name', array('@name' => $recordsetName));
    }
    $this->recordsetsInfo[$recordsetName]['input'][$path] = $name;
  }

  /**
   * Returns recordsets attached to this form
   * @return RecordsetInterface[] Recordset list
   */
  public function getRecordsets() {
    return self::$formRecordsets[$this->getId()];
  }

  /**
   * Returns a recordset attached to this form
   * @param string $name Recordset name
   * @return RecordsetInterface Recordset
   */
  public function getRecordset($name) {
    return (isset(self::$formRecordsets[$this->getId()][$name]))
      ? self::$formRecordsets[$this->getId()][$name]
      : null;
  }

  /**
   * Returns the input name for a given recordset field
   * @param string $recordsetName Recordset name
   * @param string $path Field path
   * @return string Input name
   */
  public function getRecordsetInputName($recordsetName, $inputPath) {
    return (isset($this->recordsetsInfo[$recordsetName]) && isset($this->recordsetsInfo[$recordsetName]['input'][$inputPath]))
      ? $this->recordsetsInfo[$recordsetName]['input'][$inputPath]
      : null;
  }

  /**
   * Fetch recordset input values
   */
  public function fetchRecordsets() {
    foreach ($this->recordsetsInfo as $recordsetInfo) {
      $recordset = $this->getRecordset($recordsetInfo['name']);
      foreach ($recordsetInfo['input'] as $path => $name) {
        $recordset->set($path, $this->inputInfo[$name]['state']);
      }
    }
  }

  public function getSetting($name, $default = null) {
    return isset($this->settings[$name])
      ? $this->settings[$name]
      : $default;
  }

  public function setSetting($name, $value) {
    $this->settings[$name] = $value;
  }
}
