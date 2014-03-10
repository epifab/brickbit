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
      throw new \system\exceptions\InternalError('Invalid type paremeter');
  }
}

/**
 * Extract a parameter from an array.
 * <p>List of valid options:</p>
 * <ul>
 *  <li><b>required</b>: Requires the parameter exists</li>
 *  <li><b>default</b>: Specifies a default value if the parameter does not 
 *   exist</li>
 *  <li><b>type</b>: Requires the parameter to be of a certain type. Please
 *   refer to the 'type' function for a list of possible values</li>
 *  <li><b>options</b>: Defines a range of valid options for the parameter
 *   ('type' is assumed to be 'string')</li>
 *  <li><b>regexp</b>: Defines a regular expression to validate the parameter 
 *   against ('type' is assumed to be 'string')</li>
 *  <li><b>prefix</b>: Prefix for non-empty string parameter ('type' is assumed
 *   to be 'string')</li>
 *  <li><b>suffix</b>: Suffix for non-empty string parameter ('type' is assumed
 *   to be 'string')</li>
 * </ul>
 * @param string $needle Parameter key
 * @param array $haystack Parameters array
 * @param array $options Options
 * @return mixed Parameter value
 * @throws \system\exceptions\InternalError
 */
function array_item($needle, array $haystack, $options = array()) {
  if (!\array_key_exists($needle, $haystack)) {
    // The parameter does not exist
    if (!empty($options['required'])) {
      // Required parameter
      throw new \system\exceptions\InternalError('Required arg @name.', array('@name' => $needle));
    }
    else {
      // Non-required parameter: returns a default value
      return \array_key_exists('default', $options)
         ? $options['default']
         : null;
    }
  }
  else {
    // The parameter exists
    $value = $haystack[$needle];
    
    if (isset($options['regexp']) || isset($options['prefix']) || isset($options['suffix']) || isset($options['options'])) {
      // This options can be applied to string only
      if (isset($options['type']) && $options['type'] != 'string') {
        throw new \system\exceptions\InternalError('The following parameters: "regexp", "options", "prefix", "suffix" cannot be applied to "' . $options['type'] . '" types.');
      }
      // Foces it to be a string
      $options['type'] = 'string';
    }
    
    // Checks parameter type
    if (!empty($options['type'])) {
      try {
        // Parameter cast
        $value = type($value, $options['type']);
        
        if ($options['type'] == 'string') {
          if (!empty($options['regexp'])) {
            // Parameter value restricted to a certain regular expression
            if (!\preg_match($options['regexp'], (string)$value)) {
              throw new \system\exceptions\InternalError('Invalid param @name', array('@name' => $needle));
            }
          }
          
          if (isset($options['options']) && \is_array($options['options'])) {
            // Parameter value restricted to a range of options
            if (!\in_array($value, $options['options'])) {
              // Invalid option
              throw new \system\exceptions\InternalError('Invalid param @name', array('@name' => $needle));
            }
          }
          
          // Prefix and suffix for string parameters
          return
            (\array_key_exists('prefix', $options) ? $options['prefix'] : '')
            . $value
            . (\array_key_exists('suffix', $options) ? $options['suffix'] : '');
        }
      }
      catch (\Exception $ex) {
        // Unable to convert the parameter: Invalid type
        throw new \system\exceptions\InternalError('Invalid param <em>@name</em>', array('@name' => $needle), $ex);
      }
    }
    
    return $value;
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
