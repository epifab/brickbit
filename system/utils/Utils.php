<?php
namespace system\utils;

use system\exceptions\InternalError;

class Utils {
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
  
  public static function lightVarDump($arg, $maxLevel = 5, $indent = '') {
    $msg = '';
    if (\is_array($arg)) {
      if ($maxLevel == 0) {
        $msg .= 'array(...)';
      }
      else {
        $msg .= 'array(';
        $first = true;
        foreach ($arg as $k => $v) {
          $first ? $first = false : $msg .= ",";
          $msg .= "\n" . $indent . '  ' . self::lightVarDump($k, 0, $indent . '  ') . " => " . self::lightVarDump($v, $maxLevel - 1, $indent . '  ');
        }
        $msg .= "\n" . $indent . ')';
      }
    }
    elseif (\is_object($arg)) {
      if ($maxLevel == 0) {
        $msg .= get_class($arg) . '(...)';
      }
      else {
        $msg .= get_class($arg) . '(';
        $first = true;
        foreach ($arg as $k => $v) {
          $first ? $first = false : $msg .= ",";
          $msg .= "\n" . $indent . '  ' . self::lightVarDump($k, 0, $indent . '  ') . " => " . self::lightVarDump($v, $maxLevel - 1, $indent . '  ');
        }
        $msg .= "\n" . $indent . ')';
      }
    }
    else if (\is_null($arg)) {
      $msg .= 'null';
    }
    else if (\is_string($arg)) {
      $arg = str_replace("\r", "\\r", $arg);
      $arg = str_replace("\n", "\\n", $arg);
      $arg = htmlentities($arg);
      $msg .= '"' . $arg . '"';
    }
    else if ($arg === false) {
      $msg .= 'false';
    }
    else if ($arg === true) {
      $msg .= 'true';
    }
    else {
      $msg .= $arg;
    }
    return $msg;
  }

  public static function varDump($arg) {
    return self::lightVarDump($arg);
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
  
  /**
   * Example usage:
   * $asd = array(1 => array(2 => 'x'));
   * $x = arrayItem($asd, '1|2', null); // x
   * $x = arrayItem($asd, '1'); // array(2 => 'x')
   * $x = arrayItem($asd, '1|3'); // null
   * $x = arrayItem($asd, '1|3', 'hello!'); // 'hello!'
   * Note, this doesn't raise any php error
   * @param array $haystack Haystack
   * @param string $needle Needles (multidimensional indexes separated by pipe 
   *  character)
   * @param mixed $default Default value returned for not found elements
   * @return mixed Value
   */
  public static function arrayElement(&$haystack, $needle, $default = null) {
    $h = &$haystack;
    foreach (explode('|', $needle) as $k) {
      if (\array_key_exists($k, $h)) {
        $h = &$h[$k];
      }
      else {
        return $default;
      }
    }
    return $h;
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
}
