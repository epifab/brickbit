<?php
namespace system\utils;

use system\exceptions\InternalError;

class Utils {
  const LOG_ERROR = 1;
  const LOG_WARNING = 2;
  const LOG_INFO = 3;
  const LOG_DEBUG = 4;
  
  public static function backtraceInfo($trace = null) {
    $traceDesc = '<ol>';
    if (empty($trace)) {
      $trace = \debug_backtrace();
    }
    $i = count($trace);
    foreach ($trace as $t) {
      $traceDesc .= '<li value="' . $i . '"><p><code>';

      $i--;

      if (\array_key_exists('class', $t) && !empty($t['class'])) {
        $traceDesc .= $t['class'] . '->';
      }
      $traceDesc .= '<b>' . $t['function'] . '</b>(';

      $first = true;

      if (\array_key_exists("args", $t)) {
        foreach ($t['args'] as $arg) {
          $first ? $first = false : $traceDesc .= ', ';
          $traceDesc .= self::lightVarDump($arg);
        }
      }
      $traceDesc .= '</code>)<br/> ' 
        . ((isset($t['file'], $t['line'])) ? $t['file'] . ' ' . $t['line'] . '</p></li>' : '');
    }
    $traceDesc .= '</ol>';
    
    return $traceDesc;
  }
  
//  public static function log($key, $message, $type=self::LOG_INFO) {
//    $logs = self::get('system-logs', array());
//    $logsByKey = self::get('system-logs-by-key', array());
//    $logsByType = self::get('system-logs-by-type', array());
//
//    // because of the reverse array order
//    // the first element key is the greatest one
//    $index = 1 + \key($logs);
//
//    $traceDesc = self::backtraceInfo();
//    
//    $logs[$index] = array(
//      'id' => $index,
//      'time' => \time(),
//      'key' => $key,
//      'message' => $message,
//      'type' => $type,
//      'trace' => $traceDesc
//    );
//    
//    $logsByKey[$key][] = $index;
//    $logsByType[$type][] = $index;
//    
//    \arsort($logs);
//    \arsort($logsByKey[$key]);
//    \arsort($logsByType[$type]);
//    
//    self::set('system-logs', $logs);
//    self::set('system-logs-by-key', $logsByKey);
//    self::set('system-logs-by-type', $logsByType);
//  }
  
  public static function lightVarDump($arg, $maxLevel=-1) {
    $msg = '';
    if (\is_array($arg)) {
      if ($maxLevel == 0) {
        $msg .= 'array';
      } else {
        $msg .= 'array(';
        $first = true;
        foreach ($arg as $k => $v) {
          $first ? $first = false : $msg .= ", ";
          $msg .= self::lightVarDump($k, 0) . " => " . self::lightVarDump($v, 0);
        }
        $msg .= ')';
      }
    } else if (\is_object($arg)) {
      $msg .= '[object ' . get_class($arg) . ']';
    } else if (\is_null($arg)) {
      $msg .= 'null';
    } else if (\is_string($arg)) {
      $msg .= '"' . $arg . '"';
    } else if ($arg === false) {
      $msg .= 'false';
    } else if ($arg === true) {
      $msg .= 'true';
    } else {
      $msg .= $arg;
    }
    return $msg;
  }

  public static function varDump($arg) {
    return self::lightVarDump($arg);
  }
  
  public static function getLogs($page=0, $size=10) {
    $logs = self::get('system-logs', array());
    if ($page < 0) {
      return $logs;
    } else {
      return \array_slice($logs, ($page * $size), $size);
    }
  }
  
  public static function getLogsByKey($key) {
    $logs = self::get('system-logs', array());
    $logsByKey = self::get('system-logs-by-key', array());
    
    $return = array();
    
    if (isset($logsByKey[$key])) {
      foreach ($logsByKey[$key] as $index) {
        $return[] = $logs[$index];
      }
    }
    return $return;
  }
  
  public static function getLogsByType($type) {
    $logs = self::get('system-logs', array());
    $logsByType = self::get('system-logs-by-type', array());
    
    $return = array();
    
    if (isset($logsByType[$type])) {
      foreach ($logsByType[$type] as $index) {
        $return[] = $logs[$index];
      }
    }
    return $return;
  }
  
  public static function resetLogs() {
    self::setVariable('system-logs', array());
    self::setVariable('system-logs-by-key', array());
    self::setVariable('system-logs-by-type', array());
  }

  /**
   * Get a variable. Variable are stored in the file system via method 
   *  setVariable
   * @param string $name Key
   * @param mixed $default Default value (returned in case the variable is not
   *  defined)
   * @return mixed Value
   */
  public static function getVariable($name, $default = null) {
    if (file_exists("config/vars/" . $name . ".var")) {
      $fp = fopen("config/vars/" . $name . ".var", "r");
      $content = "";
      while ($s = fread($fp, 4096)) {
        $content .= $s;
      }
      fclose($fp);
      return unserialize($content);
    } else {
      return $default;
    }
  }
  
  /**
   * Alias of getVariable.
   * @deprecated
   * @param string $name Key
   * @param mixed $default Default
   * @return mixed Value
   */
  public static function get($name, $default = null) {
    return self::getVariable($name, $default);
  }

  /**
   * Set a variable. Variables are stored in the file system and can be accessed
   *  via method getVariable
   * @param string $name Key
   * @param mixed $value Value
   */
  public static function setVariable($name, $value) {
    $content = serialize($value);

    $fp = fopen("config/vars/" . $name . ".var", "w");
    fwrite($fp, $content);
    fclose($fp);
  }
  
  /**
   * Alias of setVariable
   * @deprecated
   * @param string $name Key
   * @param mixed $value Value
   */
  public static function set($name, $value) {
    return self::setVariable($name, $value);
  }
  
  /**
   * Returns the ciderbit session.
   * Examples: 
   * <code>
   * // Returns the entire ciderbit session
   * session();
   * 
   * // Returns the entire 'core' module array
   * // If it hasn't been initialized yet, it will be set to a empty array
   * session('core');
   * 
   * // Returns the 'test' variable in the 'core' module array
   * // If it hasn't been initialized yet, it will be set to the $default 
   * //  parameter value
   * session('system', 'test');
   * 
   * NB.
   * This method always returns a reference. This means that the following code:
   * $x = &Utils::session('test', 'x');
   * $x = 'asd';
   * echo Utils::session('test', 'x');
   * Will print out 'asd'
   * </code>
   * @param string $module Module [optional, if not passed the whole ciderbit 
   *  session is returned]
   * @param string $key Key [optional, if not passed the whole module session
   *  is returned]
   * @param mixed $default Default key value
   * @return mixed Session
   */
  public static function &session($module = null, $key = null, $default = null) {
    if (!isset($_SESSION['ciderbit'])) {
      $_SESSION['ciderbit'] = array();
    }
    if (!empty($module)) {
      // Module has been transmitted
      if (!isset($_SESSION['ciderbit'][$module])) {
        // Initialize if does not exist
        $_SESSION['ciderbit'][$module] = array();
      }
      if (!empty($key)) {
        // Key has been transmitted
        if (!isset($_SESSION['ciderbit'][$module][$key])) {
          // Initialize if does not exist
          $_SESSION['ciderbit'][$module][$key]= $default;
        }
        // Return the key value
        return $_SESSION['ciderbit'][$module][$key];
      }
      else {
        // Return the module array
        return $_SESSION['ciderbit'][$module];
      }
    }
    else {
      // Return the whole ciderbit session
      return $_SESSION['ciderbit'];
    }
  }
  
  /**
   * Get a session variable.
   * @param string $module Module name
   * @param string $key Variable name
   * @param mixed $default Default value
   * @return mixed Variable value
   */
  public static function &getSession($module, $key, $default = null) {
    return self::session($module, $key, $default);
  }
  
  /**
   * Set a session variable
   * @param string $module Module name
   * @param string $key Variable name
   * @param mixed $value Value
   */
  public static function setSession($module, $key, $value) {
    $var = &self::session($module, $key);
    $var = $value;
  }
  
  /**
   * Delete a session variable
   * @param string $module Module name
   * @param string $key Variable name
   */
  public static function unsetSession($module, $key=null) {
    if (empty($key)) {
      $session = &self::session();
      unset($session[$module]);
    }
    else {
      $session = &self::session($module);
      unset($session[$key]);
    }
  }

  
  public static function php2Js($args) {
    if (empty($args)) {
      return '{}';
    }
    $jsVars = "{";
    foreach ($args as $k => $v) {
      !isset($first) ? $first = true : $jsVars .= ",";
      $jsVars .= "'" . $k . "': ";
      if (\is_array($v)) {
        $jsVars .= self::php2Js($v);
      } else {
        if (is_bool($v)) {
          $jsVars .= $v ? "true" : "false";
        } else if (is_integer($v)) {
          $jsVars .= $v;
        } else if (substr(trim($v),0,8) == "function") {
          $jsVars .= $v;
        } else {
          $jsVars .= "'" . addslashes($v) . "'";
        }
      }
    }
    return $jsVars . "}";
  }
  
  public static function addUrlArgs($url, $args) {
    $first = !\strpos($url, '?', true);
    foreach ($args as $k => $v) {
      if ($first) {
        $first = false;
        $url .= "?";
      } else {
        $url .= "&amp;";
      }
      $url .= urlencode($k) . '=' . urlencode($v);
    }
    return $url;
  }
  
  public static function escape($x, $char) {
    $str = "";
    $escape = false;
    for ($i = 0; $i < \strlen($i); $i++) {
      if ($x{$i} == $char) {
        $str .= (!$escape ? "\\" : "");
        $escape = false;
      } else if ($x{$i} == "\\") {
        $escape = !$escape;
      } else {
        $escape = false;
      }
    }
  }
  
  public static function arg2Input(&$results, $prefix, $value) {
    if (is_object($value)) {
      // ?
    } else if (!is_array($value)) {
      $results[$prefix] = $value;
    } else {
      foreach ($value as $k => $v) {
        self::arg2Input($results, $prefix . "[" . $k . "]", $v);
      }
    }
  }
  
  public static function getParam($needle, $haystack, $options=array()) {
    if (!\array_key_exists($needle, $haystack)) {
      if (\array_key_exists('required', $options) && (bool)$options['required']) {
        throw new InternalError('Required arg @name.', array('@name' => $needle));
      } else if (\array_key_exists('default', $options)) {
        return $options['default'];
      } else {
        return null;
      }
    } 
    else {
      if (\array_key_exists('options', $options) && \is_array($options['options'])) {
        if (!\in_array($haystack[$needle], $options['options'])) {
          throw new InternalError('Invalid param @name', array('@name' => $needle));
        }
      }
      if (\is_null($haystack[$needle])) {
        return null;
      }
      else if (\is_string($haystack[$needle]) || \array_key_exists('prefix', $options) || \array_key_exists('suffix', $options)) {
        return
          (\array_key_exists('prefix', $options) ? $options['prefix'] : '')
          . $haystack[$needle]
          . (\array_key_exists('suffix', $options) ? $options['suffix'] : '');
      } else {
        return $haystack[$needle];
      }
    }
  }
}
