<?php
namespace system\utils;

class ArrayWrapper extends \ArrayObject {
  /**
   * Extracts a parameter from an array.
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
   * @param string $needle Parameter key (e.g. foo.bar.baz will search for 
   *  data[foo][bar][baz])
   * @param array $options Options
   * @param string $needleSep Separator character for $needle
   * @return mixed Parameter value
   * @throws \system\exceptions\InternalError
   */
  public function search($needle, array $options = array(), $needleSep = '.') {
    $h = $this;
    
    foreach (\explode($needleSep, $needle) as $k) {
      if (\array_key_exists($k, $h)) {
        $h = &$h[$k];
      }
      else {
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
    }
    
    // The parameter exists
    $value = $h;
    
    if (isset($options['regexp']) || isset($options['prefix']) || isset($options['suffix']) || isset($options['options'])) {
      // This options can be applied to string only
      if (isset($options['type']) && $options['type'] != 'string') {
        throw new \system\exceptions\InternalError('The following parameters: "regexp", "options", "prefix", "suffix" cannot be applied to "' . $options['type'] . '" types.');
      }
      // Forces it to be a string
      $options['type'] = 'string';
    }
    
    // Checks parameter type
    if (!empty($options['type'])) {
      try {
        // Parameter cast
        $value = \cb\type($value, $options['type']);
        
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