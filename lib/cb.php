<?php
namespace cb;

function t($sentence, $args=null) {
  return \system\utils\Lang::translate($sentence, $args);
}

function set_log($key, $message, $args = array(), $type=\system\utils\Utils::LOG_INFO) {
  return \system\utils\Log::create($key, $message, $args, $type);
}

function type($value, $type) {
  switch ($type) {
    case 'array':
      if (empty($value)) {
        return array();
      } else {
        return (array)$value;
      }
      break;
    case 'int':
      return \intval($value);
      break;
    case 'float':
    case 'double':
      return \floatval($value);
      break;
    case 'bool':
      return (bool)$value;
      break;
    case 'string':
      return (string)$value;
      break;
    case 'plaintext':
      return \cb\plaintext($value);
      break;
    case 'time':
      if (\is_int($value)) {
        return $value;
      } else if (\is_string($value)) {
        $value = \strtotime($value);
        if ($value === false) {
          throw new \system\exceptions\ConversionError('Unable to convert the variable');
        } else {
          return $value;
        }
      } else {
        throw new \system\exceptions\ConversionError('Unable to convert the variable');
      }
      break;
    case 'object':
      if (!\is_object($value)) {
        throw new \system\exceptions\ConversionError('Unable to convert the variable');
      } else {
        return $value;
      }
    default:
      if (\class_exists($type)) {
        if (type($value, 'object') instanceof $type) {
          return $type;
        } else {
          throw new \system\exceptions\ConversionError('Unable to convert the variable');
        }
      } else {
        // unknown type
      }
  }
}

function array_item($needle, array $haystack, $options = array()) {
  if (!\array_key_exists($needle, $haystack)) {
    if (\array_key_exists('required', $options) && (bool)$options['required']) {
      throw new \system\exceptions\InternalError('Required arg @name.', array('@name' => $needle));
    } else if (\array_key_exists('default', $options)) {
      return $options['default'];
    } else {
      return null;
    }
  } 
  else {
    $value = $haystack[$needle];
    
    if (empty($value)) {
      if (!empty($options['!empty'])) {
        throw new \system\exceptions\InternalError('@name cannot be empty', array('@name' => $needle));
      }
    } else {
      if (!empty($options['type'])) {
        try {
          $value = type($value, $options['type']);
        } catch (\Exception $ex) {
          throw new \system\exceptions\InternalError('Invalid param @name', array('@name' => $needle), $ex);
        }
      }
    }
    
    if (\array_key_exists('options', $options) && \is_array($options['options'])) {
      if (!\in_array($value, $options['options'])) {
        throw new \system\exceptions\InternalError('Invalid param @name', array('@name' => $needle));
      }
    }
    
    if (!empty($options['regexp'])) {
      if (!\preg_match($options['regexp'], (string)$value)) {
        throw new \system\exceptions\InternalError('Invalid param @name', array('@name' => $needle));
      }
    }
    
    if (\is_null($value)) {
      return null;
    }
    else if (\is_string($value) || \array_key_exists('prefix', $options) || \array_key_exists('suffix', $options)) {
      return
        (\array_key_exists('prefix', $options) ? $options['prefix'] : '')
        . $value
        . (\array_key_exists('suffix', $options) ? $options['suffix'] : '');
    } else {
      return $haystack[$needle];
    }
  }
}

function xml_arguments(array $arguments, $required = array(), $allowed = null) {
  $allowAll = !\is_array($allowed);
  foreach ($required as $req) {
    array_item($req, $arguments, array('required' => true, '!empty' => true));
    if (!$allowAll && !in_array($req, $allowed)) {
      $allowed[] = $req;
    }
  }
  if (!$allowAll) {
    foreach ($arguments as $key => $value) {
      if (!\in_array($key, $allowed)) {
        unset($arguments[$key]);
      }
    }
  }
  $str = '';
  foreach ($arguments as $key => $value) {
    $str .= ' ' . $key . '="' . plaintext($value) . '"';
  }
  return $str;
}

function plaintext($text) {
  return \htmlspecialchars($text, \ENT_QUOTES, 'UTF-8');
}
