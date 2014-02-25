<?php
namespace system\view;

use system\session\Session;

class Form {
  /**
   * @var \system\view\Form
   */
  private static $instance;
  
  protected $id;
  protected $input = array();
  protected $errors = array();
  protected $errorsNo = 0;
  protected $data = array();
  protected $timestamp;
  
  private function __construct($id) {
    $this->timestamp = \time();
    $this->id = $id;
  }
  
  /**
   * Initialize a form.
   * This is tipically called from the control layer before the form is 
   *  rendered.
   * @param string $id Form id
   * @param string $class Form class name (must extend the \system\view\Form 
   *  class) [optional]
   * @return \system\view\Form
   */
  public static function initForm($id, $delegateClass = '\\system\\view\\Form') {
    $form = self::getForm($id);
    if (empty($form)) {
      if (!\class_exists($delegateClass)) {
        throw new \system\exceptions\InternalError('Class @class not found.', array('@class' => $delegateClass));
      }
      $form = new $delegateClass($id);
      if (!($form instanceof self)) {
        throw new \system\exceptions\InternalError('Invalid form class @class', array('@class' => $delegateClass));
      }
      Session::getInstance()->set('forms', $id, $form);
    }
    return $form;
  }
  
  /**
   * Starts a form.
   * This must be called from the view layer.
   * @param string $id Form id
   * @throws \system\exceptions\InternalError
   */
  public static function startForm($id) {
    if (!empty(self::$instance)) {
      throw new \system\exceptions\InternalError('Illegal nested form.');
    }
    $form = self::getForm($id);
    if (empty($form)) {
      throw new \system\exceptions\InternalError('Starting a uninitialized form.');
    }
    self::$instance = $form;
  }

  /**
   * Closes the current form (after this has been started)
   */
  public static function closeForm() {
    if (empty(self::$instance)) {
      throw new \system\exceptions\InternalError('No form has been open.');
    }
    self::$instance = null;
  }
  
  /**
   * Return the form (if exists)
   * @param string $id Form id
   * @return \system\view\Form
   */
  public static function getForm($id) {
    return Session::getInstance()->get('forms', $id);
  }
  
  /**
   * Return the form (if exists)
   * @return \system\view\Form
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
   * @param \system\metatypes\MetaType $metaType Meta type associated with the 
   *  input[optional]
   * @return mixed The current input value
   */
  public function addInput($name, $widgetName, $defaultValue, array $input = array(), $metaType = null) {
//    if (!isset($this->input[$name])) {
      $this->input[$name] = array(
        'name' => $name,
        'value' => $defaultValue,
        'widget' => $widgetName,
        'metaType' => $metaType
      ) + $input;
//    }
    $this->input[$name]['value'] = $defaultValue;
    return $this->input[$name]['value'];
  }
  
  /**
   * Renders a input
   * @param string $name Input name
   * @return string Rendered input (HTML code)
   */
  public function renderInput($name) {
    if (!empty($this->input[$name])) {
      $input = $this->input[$name];
      return \system\view\Widget::getWidget($input['widget'])->render($input);
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
    $this->onSubmission();
    return $this->inputErrorCount() == 0;
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
    $haystack = $_REQUEST;
    
    // Handles with input name like foo[bar][foo] -> $_REQUEST[foo][bar][foo]
    $needles = \preg_split('/(\[|\])+/', $input['name'], 0, PREG_SPLIT_NO_EMPTY);
    if (count($needles)) {
      foreach ($needles as $needle) {
        if (\array_key_exists($needle, $haystack)) {
          $haystack = $haystack[$needle];
        }
        else {
          // Not transmitted
          return null;
        }
      }
      return \system\view\Widget::getWidget($input['widget'])->fetch($haystack, $input);
    } else {
      return null;
    }
  }
  
  /**
   * Fetch every form input
   */
  private function fetchInputValues() {
    $this->errorsNo = 0;
    
    foreach ($this->input as &$input) {
      $input['value'] = self::getInputPostedValue($input);
      $input['error'] = null;
      
      $this->errors[$input['name']] = null;

      $mt = $input['metaType'];
      if ($mt) {
        try {
          $mt->validate($input['value']);
        } catch (\system\exceptions\ValidationError $ex) {
          $this->errors[$input['name']] = $ex->getMessage();
          $this->errorsNo++;
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
   * Get form input
   * @return array Form input
   */
  public function getInput() {
    return $this->input;
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
   * Count validation errors
   * @return int Number of errors
   */
  public function inputErrorCount() {
    return $this->errorsNo;
  }
  
  /**
   * Get validation errors
   * @return array Validation errors
   */
  public function inputErrors() {
    return $this->errors;
  }
}
