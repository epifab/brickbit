<?php
namespace module\core\controller;

use system\Component as Component;
use system\view\Form as Form;

/**
 * Generic base recordset editing component
 */
abstract class Edit extends Component {
  /**
   * @return array List of valid actions (example array('Create', 'Update')
   */
  abstract protected function getEditActions();
  /**
   * @return \system\model\RecordsetInterface Recordset
   */
  abstract protected function getEditRecordset();
  /**
   * @return string Form template
   */
  abstract protected function getFormTemplate(\system\model\RecordsetInterface $recordset);
  /**
   * @return string Form id
   */
  abstract protected function getFormId();
  
  protected function defaultRunHandler() {
    if (\in_array($this->getAction(), $this->getEditActions())) {
      $rs = $this->getEditRecordset();
      if (empty($rs)) {
        throw new \system\exceptions\PageNotFound();
      }
      $form = Form::getPostedForm($this->getFormId());
      if (empty($form) || $form->inputErrorCount()) {
        // Form not initialised or input errors
        $this->setMainTemplate($this->getFormTemplate($rs));
        return Component::RESPONSE_TYPE_FORM;
      }
      else {
        // Submit handler
        if (\is_callable(array($this, 'submit' . $this->getAction()))) {
          \call_user_func(array($this, 'submit' . $this->getAction()), $form, $rs  );
        }
        else {
          $this->defaultSubmitHandler($form, $rs);
        }
      }
    }
    return parent::defaultRunHandler();
  }
  
  protected function defaultSubmitHandler(Form $form, \system\model\RecordsetInterface $rs) {
    $rs->save();
  }
}