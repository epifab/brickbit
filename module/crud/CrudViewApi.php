<?php
namespace module\crud;

use system\view\Widget;

class CrudViewApi {
  //////////////////////
  // BLOCKS
  //////////////////////
  public static function blockForm($content, $params, $open) {
    $vars = \system\view\Template::current()->getVars();
    
    $formId = \cb\array_item('id', $params, array('required' => true));

    $id = $formId . '-' . $vars['system']['component']['requestId'];

    if ($open) {
      Form::startForm($formId);
    }
    else {
      Form::closeForm();

      $form = "\n"
        . '<form id="' . $id . '" name="' . $formId . '" method="post" enctype="multipart/form-data" action="' . $vars['system']['component']['url'] . '">'
        // forzo le risposte ad avere gli stessi ID per il form e per i contenuti
        . '<input type="hidden" name="system[formId]" value="' . $formId . '"/>'
        . '<input type="hidden" name="system[requestId]" value="' . $vars['system']['component']['requestId'] . '"/>';
      
      return $form . $content . '</form>';
    }
  }

  private static function uniqueId($name) {
    static $uniqueIds = array();
    if (!isset($uniqueIds[$name])) {
      $uniqueIds[$name] = 1;
      return $name;
    }
    else {
      $uniqueIds[$name]++;
      return $name . '-' . $uniqueIds[$name];
    }
  }
  
  /**
   * Renders a input and adds it to the form
   * @param array $params Parameters
   * <ul>
   *  <li><b>recordset</b>: recordset name</li>
   *  <li><b>path</b>: field path (relative to the recordset) [required if recordset has been specified]</li>
   *  <li><b>name</b>: input name [required if recordset is missing]</li>
   *  <li><b>state</b>: default input state (please refer to the widget to use this consistently) [required if recordset is missing]</li>
   *  <li><b>widget</b>: widget to render the input (e.g. selectbox) [required if recordset is missing]</li>
   *  <li><b>id</b>: input id [default to input name]</li>
   *  <li><b>metatype</b>: metatype associated with the input (e.g. string) - if a recordset is specified, the field metatype is used by default</li>
   *  <li><b>label</b>: input label - if a recordset is specified, the field label is used by default</li>
   *  <li><b>info</b>: input additional info and tips</li>
   *  <li><b>attributes</b>: additional attributes to be used by the widget</li>
   * </ul>
   * @return string HTML code to renders the widget
   * @throws \system\exceptions\InternalError
   */
  public static function input($params) {
    $form = Form::getCurrent();
    if (empty($form)) {
      throw new \system\exceptions\InternalError('Form not found');
    }
    
    $recordset = null;
    
    if (isset($params['recordset'])) {
      $recordset = $form->getRecordset($params['recordset']);
      
      $path = \cb\array_item('path', $params, array('required' => true, 'type' => 'string'));
      
      $field = $recordset->getTable()->importField($path);
      
      $defaultName = 'recordset--' . $params['recordset'] . '--' . str_replace('.', '--', $path);
      $defaultMetatype = $field->getMetaType();
      $defaultWidget = $defaultMetatype->getEditWidget();
      $defaultLabel = $defaultMetatype->getLabel();
      
      if (!isset($params['name'])) {
        $params['name'] = $defaultName;
      }
      $params['state'] = $recordset->{$path};
      $params['metatype'] = $defaultMetatype;
      if (!isset($params['widget'])) {
        // Widget can be overriden
        $params['widget'] = $defaultWidget;
      }
      if (!isset($params['label'])) {
        // Label can be overriden
        $params['label'] = $defaultLabel;
      }
    }
    
    // Two types of input:
    // - Normal input
    // - Input from a recordset
    // Fetches and validates parameters
    $inputName        = \cb\array_item('name', $params, array('required' => true, 'regexp' => '/^[a-zA-Z_-][a-zA-Z_0-9-]*$/'));
    $inputId          = self::uniqueId(\cb\array_item('id', $params, array('default' => $inputName, 'regexp' => '/^[a-zA-Z_-][a-zA-Z_0-9-]*$/')));
    $inputMetaType    = \cb\array_item('metatype', $params, array('default' => null));
    $inputWidget      = \cb\array_item('widget', $params, array('required' => true, 'type' => 'string'));
    $inputAttributes  = \cb\array_item('attributes', $params, array('default' => array(), 'type' => 'array'));
    $inputLabel       = \cb\array_item('label', $params, array('default' => $inputName, 'type' => 'string'));
    $inputInfo        = \cb\array_item('info', $params, array('default' => null, 'type' => 'string'));
    
    // Initial input state
    // This is the input 'value' parameter for most of the input element, but 
    //  depending on the widget which will render the input it may be something
    //  different. 
    //  e.g. WidgetSelectbox uses this value to add a 'selected' parameter for
    //  selected options.
    $inputState = \cb\array_item('state', $params, array('required' => true));

    $inputError = $form->getValidationError($inputName);
    
    $widgetObject = Widget::getWidget($inputWidget);
    
    // Checkboxes and radio buttons widgets displays the label
    $inlineLabel = 
      $widgetObject instanceof \system\view\WidgetCheckboxes
      || $widgetObject instanceof \system\view\WidgetRadiobuttons;
    
    // A single checkbox or a single radiobutton does not have a label displayed
    $inlineLabelStrict = 
      $widgetObject instanceof \system\view\WidgetCheckbox
      || $widgetObject instanceof \system\view\WidgetRadiobutton;
    
    // Every other widget will have an outline label
    $outlineLabel = !$inlineLabel && !$inlineLabelStrict;

    $inputAttributes['class'] = 
      (isset($inputAttributes['class']) ? $inputAttributes['class'] . ' ' : '')
      . 'widget-' . $inputWidget . ($outlineLabel ? ' form-control' : '');

    ////////////////////////////////////////////////////////////////////////////
    //
    // Adds the input to the form
    // 
    ////////////////////////////////////////////////////////////////////////////
    $form->addInput($inputName, $inputWidget, $inputState, array('id' => $inputId, 'attributes' => $inputAttributes) + $params, $inputMetaType);
    
    if (!empty($recordset)) {
      //////////////////////////////////////////////////////////////////////////
      //
      // Links the recordset field with the input
      // 
      //////////////////////////////////////////////////////////////////////////
      $form->addRecordsetInput($params['recordset'], $inputName, $params['path']);
    }

    if (\cb\array_item('columns', $params, array('default' => false))) {
      // Displays a columned structure with the input label
      if (!isset($params['layout'])) {
        $params['layout'] = array();
      }
      $params['layout'] = $params['layout'];
      
      $layoutDefaults = array(
        //////////////label input
        'xs' => array(12,   12),
        'sm' => array(3,    9),
        'md' => array(2,    10),
        //'lg' => array(2,    10),
      );
      
      // Initialising the layout
      foreach ($layoutDefaults as $size => $layoutDefault) {
        if (isset($params['layout'][$size])) {
          // Stops at the first size definition (mobile first approach)
          break;
        }
        else {
          // Sets a default
          $params['layout'][$size] = $layoutDefault;
        }
      }
      
      $labelWrapperClasses = 'de-label-wrapper';
      $inputWrapperClasses = 'de-input-wrapper' . ($inputError ? ' has-error' : '');
      foreach ($params['layout'] as $size => $layout) {
        $labelWrapperClasses .= ' col-' . $size . '-' . $layout[0];
        $inputWrapperClasses .= ' col-' . $size . '-' . $layout[1];
      }
      
      return
          '<div class="' . $labelWrapperClasses . '">'
        . ($outlineLabel ? '<label class="de-label" for="' . $inputId . '">' . $inputLabel . '</label>' : '')
        . ($inlineLabel ? '<span class="de-label">' . $inputLabel . '</span>' : '')
        . '</div>'
        . '<div class="' . $inputWrapperClasses . '">'
        . $form->renderInput($inputName)
        . ($inputInfo ? '<div class="de-input-info alert alert-info">' . $inputInfo . '</div>' : '')
        . ($inputError ? '<div class="de-input-error alert alert-danger">' . $inputError . '</div>' : '')
        . '</div>';
    }
    else {
      return $form->renderInput($inputName);
    }
  }
}
