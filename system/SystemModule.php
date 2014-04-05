<?php
namespace system;

class SystemModule {
  public static function widgetsMap() {
    return array(
      'hidden' => '\system\view\WidgetHidden',
      'textbox' => '\system\view\WidgetTextBox',
      'textarea' => '\system\view\WidgetTextarea',
      'selectbox' => '\system\view\WidgetSelectbox',
      'radiobutton' => '\system\view\WidgetRadiobutton',
      'radiobuttons' => '\system\view\WidgetRadiobuttons',
      'checkbox' => '\system\view\WidgetCheckbox',
      'checkboxes' => '\system\view\WidgetCheckboxes',
      'password' => '\system\view\WidgetPassword',
    );
  }
  
  public static function metaTypesMap() {
    return array(
      'integer' => '\system\metatypes\MetaInteger',
      'decimal' => '\system\metatypes\MetaDecimal',
      'string' => '\system\metatypes\MetaString',
      'plaintext' => '\system\metatypes\MetaString',
      'html' => '\system\metatypes\MetaHTML',
      'blob' => '\system\metatypes\MetaBlob',
      'boolean' => '\system\metatypes\MetaBoolean',
      'date' => '\system\metatypes\MetaDate',
      'datetime' => '\system\metatypes\MetaDateTime',
      'virtual' => '\system\metatypes\MetaString',
      'password' => '\system\metatypes\MetaString'
    );
  }
}