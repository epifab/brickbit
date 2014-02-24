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
    $this->datamodel['website']['outlineLayoutTemplate'] = 'outline-layout-1col';
    
    $this->datamodel['currentFormId'] = $this->getFormId();
    
    if (\in_array($this->getAction(), $this->getEditActions())) {
      $rs = $this->getEditRecordset();
      if (empty($rs)) {
        throw new \system\exceptions\PageNotFound();
      }
      
      $form = Form::getPostedForm($this->getFormId());
      
      $this->datamodel['recordset'] = $rs;
      
      if (empty($form) || $form->inputErrorCount()) {
        // Form not initialised or input errors
        $this->setMainTemplate($this->getFormTemplate($rs));
        return Component::RESPONSE_TYPE_FORM;
      }
      else {
        // Submit handler
        if (\is_callable(array($this, 'submit' . $this->getAction()))) {
          \call_user_func(array($this, 'submit' . $this->getAction()), $form, $rs);
        }
        return Component::RESPONSE_TYPE_NOTIFY;
      }
    }
    return parent::defaultRunHandler();
  }
  
  protected function defaultSubmitHandler(Form $form, \system\model\RecordsetInterface $rs) {
    $rs->save();
  }
}