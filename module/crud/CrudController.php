<?php
namespace module\crud;

use system\model2\RecordsetInterface;
use system\Component;

/**
 * Generic base recordset editing component
 */
abstract class CrudController extends Component {
  /**
   * @var Form Form object
   */
  private $form = null;

  /**
   * @return array List of valid actions (example array('Create', 'Update')
   */
  abstract protected function getEditActions();
  /**
   * Returns a list of editable recordsets keyed by a recordset name.
   * @return RecordsetInterface[] Edit recordsets
   */
  abstract protected function getEditRecordsets();
  /**
   * Returns the form ID
   * @return string Form id
   */
  abstract protected function getFormId();
  /**
   * Returns the form object
   * @return Form Form object
   */
  public function getForm() {
    if (empty($this->form)) {
      // Initialize the form
      $this->form = Form::initForm($this->getFormId());
      foreach ($this->getEditRecordsets() as $name => $recordset) {
        // Attach the recordsets to the form
        $this->form->addRecordset($name, $recordset);
      }
    }
    return $this->form;
  }

  /**
   * Perform form submission and validation
   * @return boolean TRUE if there isn't any validation error
   */
  protected function formSubmission() {
    return $this->getForm()->submission();
  }

  /**
   * This is used to implements every action.
   * There's no need to implement any runAction handler as the entire control is
   *  implemented here, unless you need other actions outside the list returned
   *  by getEditActions() method.
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

    if (!$form->checkSubmission() || !$this->formSubmission()) {
      // Form not submitted or validation errors
      if (\is_callable(array($this, 'form' . $this->getAction()))) {
        \call_user_func(array($this, 'form' . $this->getAction()));
      }
      return \system\RESPONSE_TYPE_FORM;
    }
    else {
      try {
        // Submit handler
        if (\is_callable(array($this, 'submit' . $this->getAction()))) {
          \call_user_func(array($this, 'submit' . $this->getAction()));
        }
        $form->destroy();
        return \system\RESPONSE_TYPE_NOTIFY;
      }
      catch (\system\exceptions\ValidationError $ex) {
        // Form not submitted or validation errors
        if (\is_callable(array($this, 'form' . $this->getAction()))) {
          \call_user_func(array($this, 'form' . $this->getAction()));
        }
        return \system\RESPONSE_TYPE_FORM;
      }
    }
  }
}