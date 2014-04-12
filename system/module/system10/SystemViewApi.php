<?php
namespace system\module\system10;

use system\SystemApi;
use system\Main;
use system\exceptions\InternalError;
use system\utils\Utils;
use system\utils\Lang;
use system\view\View;
use system\view\Template;

class SystemViewApi {
  private static $blocks = array();
  
  public static function open($callback, $args = array()) {
    $callback = 'block' . ucfirst($callback);
    View::__callStatic($callback, array(null, $args, true));
    \array_push(self::$blocks, array($callback, $args));
    \ob_start();
  }

  public static function close() {
    if (empty(self::$blocks)) {
      throw new InternalError('Syntax error. No block has been open.');
    }
    list($callback, $args) = \array_pop(self::$blocks);
    $content = \ob_get_clean();
    $x = View::__callStatic($callback, array($content, $args, false));
    if (!\is_null($x)) {
      echo $x;
    }
  }
  
  public static function comparePaths($url1, $url2) {
    return self::path($url1) == self::path($url2);
  }

  public static function path($url) {
    return Main::getPathRelative($url);
  }

  public static function vpath($url) {
    return Main::getPathVirtual($url);
  }

  public static function modulePath($module, $url) {
    return Main::modulePathRel($module, $url);
  }

  public static function themePath($url) {
    return Main::themePathRel($url);
  }

  public static function langPath($lang) {
    return Lang::langPath($lang);
  }
  
  public static function element($name, $args) {
    $out = '<' . $name . ' ';
    foreach ($args as $key => $val) {
      $out .= $key . '="' . \cb\plaintext($val) . '" ';
    }
    return $out . '/>';
  }
  
  public static function openElement($name, $args) {
    $out = '<' . $name . ' ';
    foreach ($args as $key => $val) {
      $out .= $key . '="' . \cb\plaintext($val) . '" ';
    }
    return $out . '>';
  }
  
  public static function closeElement($name) {
    return '</' . $name . '>';
  }
  
  private static function params2input($key, $val, &$input, $prefix='') {
    if (\is_array($val)) {
      foreach ($val as $k1 => $v1) {
        self::params2input($k1, $v1, $input, (empty($prefix) ? $key : $prefix . '[' . $key . ']'));
      }
    }
    else {
      $input .= 
        '<input'
        . ' type="hidden"'
        . ' name="' . (empty($prefix) ? $key : $prefix . '[' . $key . ']') . '"'
        . ' value="' . \htmlentities($val) . '"/>';
    }
  }
  
  public static function loadBlock($name, $url, $args=array()) {
    $url = Main::getPathVirtual($url);
    echo self::printBlock($name, $url, null, $args);
  }

  private static function printBlock($name, $url, $content, $args=array()) {
    static $ids = array();
    
    $blockId = $name;
    if (!\array_key_exists($name, $ids)) {
      $ids[$name] = 1;
    } else {
      $ids[$name]++;
      $blockId .= '-' . $ids[$name];
    }

    $vars = Template::current()->getVars();

    // system array is reserved
    // make sure it isn't overridden
    $args['system'] = array(
      'url' => $vars['system']['mainComponent']['url'],
      'requestType' => 'AJAX',
      'blockId' => $blockId
    );
    
    echo
      '<form'
      . ' action="' . $url . '"'
      . ' method="POST"' 
      . ' name="' . $blockId . '"'
      . ' class="system-block-form"'
      . ' id="system-block-form-' . $blockId . '">';
    
    foreach ($args as $key => $val) {
      self::params2input($key, $val, $out);
    }

    echo
      '</form>'
      . '<div class="system-block" id="' . $blockId . '">';
  
    if (\is_null($content)) {
      Main::run($url, $args);
    } else {
      echo $content;
    }
    
    echo '</div>';
  }
  
  public static function blockBlock($content, $params, $open) {
    if (!$open) {
      $name = \cb\array_item('name', $params, array('required' => true));
      $url = \cb\array_item('url', $params, array('required' => true));
      $args = \cb\array_item('args', $params, array('default' => array()));
      echo self::printBlock($name, $url, $content, $args);
    }
  }
  
  public static function import($name, $args = array()) {
    $a = $args + Template::current()->getVars();
    $tpl = new Template($name, $a);
    $tpl->render();
  }

  public static function region($region) {
    $vars = Template::current()->getVars();

    if (\array_key_exists($region, $vars['system']['templates']['regions'])) {
      \asort($vars['system']['templates']['regions'][$region]);
      foreach ($vars['system']['templates']['regions'][$region] as $templates) {
        foreach ($templates as $tpl) {
          View::import($tpl);
        }
      }
    }
  }

  public static function t($sentence, $args = null) {
    return Lang::translate($sentence, $args);
  }
  
 
  public static function blockLink($content, $params, $open) {
    if (!$open) {
      $params['url'] = \cb\array_item('url', $params, array('required' => true));

      $url = $params['url'];
      $ajax = \cb\array_item('ajax', $params, array('default' => true, 'options' => array(false, true)));
      $class = \cb\array_item('class', $params, array('default' => 'link'));
      $params['system'] = array(
        'requestType' => 'MAIN',
  //      'requestId' => null
      );
      $jsArgs = Utils::php2Js($params); //array_merge(array('url' => $url), \cb\array_item('args', $params, array('default' => array()))));

      if ($ajax) {
        $confirm = \cb\array_item('confirm', $params, array('default' => false, 'options' => array(false, true)));
        if ($confirm) {
          $confirmTitle = str_replace("'", "\\'", \cb\array_item('confirmTitle', $params, array('default' => '')));
          $confirmQuest = str_replace("'", "\\'", \cb\array_item('confirmQuest', $params, array('default' => '')));
          $action = "ciderbit.confirm('" . $confirmTitle . "', '" . $confirmQuest . "', " . $jsArgs . "); return false;";
        } else {
          $action = "ciderbit.request(" . $jsArgs . "); return false;";
        }
      }
      return 
        '<a href="' . $url . '"'
        . (empty($class) ? '' : ' class="' . $class . '"')
        . (empty($action) ? '' : ' onclick="' . $action . '"') . '>'
        . $content
        . '</a>';
    }
  }
  
  public static function access($url) {
    $url = Main::getPathVirtual($url);
    return Main::checkAccess($url);
  }
  
  public static function dateTimeFormat($time, $key = 'medium') {
    $formats = SystemApi::dateTimeFormat();
    $format = (isset($formats[$key]))
      ? $formats[$key]
      : 'Y/m/d H:i:s';
    return date($format, $time);
  }
  
  public static function dateFormat($time, $key = 'medium') {
    $formats = SystemApi::dateFormat();
    $format = (isset($formats[$key]))
      ? $formats[$key]
      : 'Y/m/d';
    return date($format, $time);
  }
  
  public static function timeFormat($time, $key = 'medium') {
    $formats = SystemApi::timeFormat();
    $format = (isset($formats[$key]))
      ? $formats[$key]
      : 'Y/m/d';
    return date($format, $time);
  }
  
  public static function getEditFormId() {
    $vars = Template::current()->getVars();
    return 'system-edit-form-' . $vars['system']['component']['requestId'];
  }
}
