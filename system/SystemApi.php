<?php
namespace system;

use system\model2\RecordsetInterface;
use system\model2\TableInterface;

class SystemApi {
  /**
   * Raises the whatchdog event.
   * This is used to log messages.
   * @param string $code Typically the module where the event happened
   * @param string $message Watchdog message
   * @param array $messageArgs Message arguments
   * @param int $level Log level
   */
  public static function watchdog($code, $message, array $messageArgs, $level = system\LOG_NOTICE) {
    Main::invokeMethodAll('watchdog', $code, $message, $messageArgs, $level);
  }
  
  /**
   * Returns the initial datamodel for every template
   * @return array Default datamodel
   */
  public static function initDatamodel() {
    return Main::invokeMethodAllMerge('initDatamodel');
  }

  /**
   * Raises the onRun event.
   * This is fired before the main component is processed.
   * Please note also that <em>preRun</em> and <em>onRun</em> methods are also 
   *  fired on the active theme respectively before and after this event.
   * @param Component $component
   */
  public static function onRun(Component $component) {
    Main::invokeMethodAll('onRun', $component);
  }
  
  /**
   * Raises the onInit event.
   * This is fired when bootstrap completed its initialization before running
   *  any component.
   */
  public static function onInit() {
    Main::invokeStaticMethodAll('onInit');
  }
  
  /**
   * Raises the dateFormat event.
   * @return array Array of date formats
   */
  public static function dateFormat() {
    return Main::invokeStaticMethodAllMerge('dateFormat', false);
  }
  
  /**
   * Raises the dateTimeFormat event.
   * @return array Array of date time formats
   */
  public static function dateTimeFormat() {
    return Main::invokeStaticMethodAllMerge('dateTimeFormat', false);
  }
  
  /**
   * Raises the timeFormat event.
   * @return array Array of time formats
   */
  public static function timeFormat() {
    return Main::invokeStaticMethodAllMerge('timeFormat', false);
  }
  
  /**
   * Raises the metaTypesMap event.
   * @return array Associative array 'metatype name' => 'metatype class'
   */
  public static function metaTypesMap() {
    return Main::invokeStaticMethodAllMerge('metaTypesMap');
  }
  
  /**
   * Raises the widgetsMap event.
   * @return array Associative array 'widget name' => 'widget class'
   */
  public static function widgetsMap() {
    return Main::invokeStaticMethodAllMerge('widgetsMap');
  }
  
  /**
   * Fired when a recordset gets deleted
   * @param RecordsetInterface $recordset Recordset which will be deleted
   */
  public static function onDelete(RecordsetInterface $recordset) {
    Main::invokeMethodAll('onDelete', $recordset);
  }
  
  /**
   * Fired when a recordset gets updated
   * @param RecordsetInterface $recordset Recordset which will be updated
   */
  public static function onUpdate(RecordsetInterface $recordset) {
    Main::invokeMethodAll('onUpdate', $recordset);
  }
  
  /**
   * Fired when a recordset gets created
   * @param RecordsetInterface $recordset Recordset which will be created
   */
  public static function onCreate(RecordsetInterface $recordset) {
    Main::invokeMethodAll('onCreate', $recordset);
  }
  
  /**
   * Fired when a new recordset table is initialized
   * @param TableInterface $table Table
   */
  public static function recordsetTableInit(TableInterface $table) {
    Main::invokeMethodAll('recordsetTableInit', $table);
  }
}