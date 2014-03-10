<?php
namespace system;

class Settings {
  private static $instance = null;
  
  private $settings = array();
  
  private function __construct($host) {
    $this->settings = self::getDomainSettings($host) + self::getDefaultSettings();
  }

  private static function getDefaultSettings() {
    return require 'config/default.php';
  }
  
  private static function getDomainSettings($host) {
    $domains = require 'config/domains.php';
    
    return (isset($domains[$host]))
      ? require 'config/sites/' . $domains[$host] . '.php'
      : array();
  }
  
  /**
   * Gets the settings instance.
   * Implements singleton design pattern.
   * @return \system\Settings Settings
   */
  public static function getInstance() {
    if (\is_null(self::$instance)) {
      self::$instance = new self($_SERVER['HTTP_HOST']);
    }
    return self::$instance;
  }
  
  /**
   * Gets a configuration variable.
   * @param string $name Setting name
   * @return mixed Setting value
   */
  public function __get($name) {
    return (isset($this->settings[$name]))
      ? $this->settings[$name]
      : null;
  }
  
  /**
   * Sets a configuration variable.
   * @param string $name Var name
   * @param mixed $value Value
   */
  public function __set($name, $value) {
    $this->settings[$name] = $value;
  }
  
  /**
   * Returns the settings array
   * @return array Settings
   */
  public function toArray() {
    return $this->settings;
  }
}