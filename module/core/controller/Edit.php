<?php
namespace module\core\controller;

use system\Component as Component;
use system\view\Form as Form;
use system\view\FormRecordset as FormRecordset;

/**
 * Generic base recordset editing component
 */
abstract class Edit extends Component {
  /**
   * @return array List of valid actions (example array('Create', 'Update')
   */
  abstract protected function getEditActions();
  /**
   * Returns a list of editable recordsets keyed by a recordset name.
   * @return \system\model\RecordsetInterface[] Edit recordsets
   */
  abstract protected function getEditRecordsets();
  /**
   * Returns the form ID
   * @return string Form id
   */
  abstract protected function getFormId();
  /**
   * Returns the template which should be used to render the edit form.
   * @return string Form template
   */
  abstract protected function getFormTemplate();

  /**
   * @var \system\view\FormRecordset Form object
   */
  private $form = null;
  
  /**
   * Returns the form object
   * @return \system\view\FormRecordset Form object
   */
  public function getForm() {
    if (empty($this->form)) {
      // Initialize the form
      $this->form = \system\view\FormRecordset::initForm($this->getFormId());
      foreach ($this->getEditRecordsets() as $name => $recordset) {
        // Attach the recordsets to the form
        $this->form->addRecordset($name, $recordset);
      }
    }
    return $this->form;
  }
  
  /**
   * This is used to implements every action.
   * There's no need to implement any runAction handler as the entire control is
   *  implemented here
   * @return type
   */
  protected function defaultRunHandler() {
    $this->datamodel['website']['outlineLayoutTemplate'] = 'outline-layout-1col';
    
    // Set the form ID
    $this->datamodel['currentFormId'] = $this->getFormId();
    
    if (!\in_array($this->getAction(), $this->getEditActions())) {
      // Invalid action
      throw new \system\exceptions\PageNotFound();
    }
    
    $form = $this->getForm();
    
    $this->datamodel['form'] = $form;

    if (!$form->checkSubmission() || !$form->submission()) {
      // Form not submitted or validation errors
      $this->setMainTemplate($this->getFormTemplate());
      return Component::RESPONSE_TYPE_FORM;
    }
    else {
      // Submit handler
      if (\is_callable(array($this, 'submit' . $this->getAction()))) {
        \call_user_func(array($this, 'submit' . $this->getAction()), $form);
      }
      return Component::RESPONSE_TYPE_NOTIFY;
    }
  }
  
  protected function defaultSubmitHandler(\system\view\FormRecordset $form) {
    
    $rs->save();
  }
}