<?php
namespace system\view;

class View {

  /**
   * @var \system\view\View
   */
  private static $instance;
  
  /**
   * @return \system\view\View
   */
  public static function getInstance() {
    if (\is_null(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Dynamically works out the view API class which implements the method
   * @param string $method Method name
   * @return callable Callback for the method or NULL in case it cannot be found
   */
  protected function getApi($method) {
    static $cache = array();
    static $viewClasses = null;
    if (!\array_key_exists($method, $cache)) {
      if (\is_null($viewClasses)) {
        $viewClasses = \system\Main::getViewClasses();
      }
      $cache[$method] = null;
      foreach ($viewClasses as $c) {
        if (\is_callable(array($c, $method))) {
          $cache[$method] = array($c, $method);
          break;
        }
      }
    }
    return $cache[$method];
  }
  
  public static function __callStatic($method, $args) {
    return self::getInstance()->__call($method, $args);
  }

  public function __call($method, $args) {
    $api = $this->getApi($method);
    if (!\is_null($api)) {
      return \call_user_func_array($api, $args);
    } else {
      throw new \system\exceptions\InternalError('Template API <em>@name</em> not found.', array('@name' => $method));
    }
  }
}

