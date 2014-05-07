<?php
namespace system;

class Settings {
  private static $instance = null;

  private $settings = array();

  private function __construct($host, $context) {
    $this->settings = self::getDomainSettings($host, $context);
  }

  private static function getDomainSettings($host, $context) {
    foreach (require 'appdata/domains.php' as $d => $directory) {
      $slashPos = strpos($d, '/');
      if ($slashPos) {
        $dHost = substr($d, 0, $slashPos);
        $dContext = substr($d, $slashPos + 1);
      }
      else {
        $dHost = $d;
        $dContext = '';
      }
      if ($host == $dHost && (empty($dContext) || strpos($context, $dContext) === 0)) {
        return require 'appdata/' . $directory . '/config.php';
      }
    }
    if (!file_exists('appdata/default/config.php')) {
      header('HTTP/1.0 404 Not Found');
      echo '<h1>Website not found</h1>';
      echo '<p>Website not found or not properly installed</p>';
      die();
    }
    return require 'appdata/default/config.php';
  }

  /**
   * Gets the settings instance.
   * Implements singleton design pattern.
   * @return Settings Settings
   */
  public static function getInstance() {
    if (\is_null(self::$instance)) {
      self::$instance = new self(Main::getDomain(), Main::getRequestUri());
    }
    return self::$instance;
  }

  /**
   * Gets a configuration variable.
   * @param string $name Setting name
   * @return mixed Setting value
   */
  public function get($name, $default = null) {
    return (isset($this->settings[$name]))
      ? $this->settings[$name]
      : $default;
  }

  /**
   * Gets a configuration variable.
   * @param string $name Setting name
   * @return mixed Setting value
   */
  public function __get($name) {
    return $this->get($name, null);
  }

  /**
   * Sets a configuration variable.
   * @param string $name Var name
   * @param mixed $value Value
   */
  public function set($name, $value) {
    $this->settings[$name] = $value;
  }

  /**
   * Sets a configuration variable.
   * @param string $name Var name
   * @param mixed $value Value
   */
  public function __set($name, $value) {
    $this->set($name, $value);
  }

  /**
   * Returns the settings array
   * @return array Settings
   */
  public function toArray() {
    return $this->settings;
  }
}