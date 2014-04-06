<?php
namespace system\module\system10;

class SystemController {
  /**
   * Implements widgetsMap() controller event
   */
  public static function widgetsMap() {
    return array(
      'hidden' => '\system\view\WidgetHidden',
      'textbox' => '\system\view\WidgetTextbox',
      'textarea' => '\system\view\WidgetTextarea',
      'selectbox' => '\system\view\WidgetSelectbox',
      'radiobutton' => '\system\view\WidgetRadiobutton',
      'radiobuttons' => '\system\view\WidgetRadiobuttons',
      'checkbox' => '\system\view\WidgetCheckbox',
      'checkboxes' => '\system\view\WidgetCheckboxes',
      'password' => '\system\view\WidgetPassword',
    );
  }

  /**
   * Implements metaTypesMap() controller event
   */
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
