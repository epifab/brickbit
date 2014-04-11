<?php
namespace module\ciderbit;

use system\Main;
use system\utils\Lang;
use system\utils\HTMLHelpers;
use system\utils\Login;

class CiderbitModule {
  /**
   * Implements controller event initDatamodel()
   */
  public static function initDatamodel() {
    $langsLabels = array();
    foreach (Main::settings()->languages as $lang) {
      $langsLabels[$lang] = Lang::translate('@lang', array('@lang' => $lang));
    }
    return array(
      'system' => array(
        'component' => self::initDatamodelComponentInfo(Main::getActiveComponent()),
        'mainComponent' => self::initDatamodelComponentInfo(Main::getActiveComponentMain()),
        // Default response type
        'responseType' => \system\RESPONSE_TYPE_READ,
        'ajax' => HTMLHelpers::isAjaxRequest(),
        'ipAddress' => HTMLHelpers::getIpAddress(),
        'lang' => Lang::getLang(),
        'langs' => Main::setting('languages'),
        'langsLabels' => $langsLabels,
        'theme' => Main::getTheme(),
        'themes' => Main::setting('themes'),
        'messages' => array()
      ),
      'user' => Login::getLoggedUser(),
      'website' => self::initDatamodelWebsiteInfo(),
      'page' => array(
        'title' => Main::setting('defaultPageTitle'),
        'url' => $_SERVER['REQUEST_URI'],
        'meta' => array(),
        'js' => array(),
        'css' => array(),
      )
    );
  }
  
  private static function initDatamodelComponentInfo(\system\Component $component) {
    static $info = array();
    
    if (!isset($info[$component->getRequestId()])) {
      $info[$component->getRequestId()] = array(
        'name' => $component->getName(),
        'module' => $component->getModule(),
        'action' => $component->getAction(),
        'url' => $component->getUrl(),
        'urlArgs' => $component->getUrlArgs(),
        'requestId' => $component->getRequestId(),
        'requestType' => $component->getRequestType(),
        'requestData' => $component->getRequestData(),
        'nested' => $component->isNested(),
        'alias' => $component->getAlias()
      );
    }
    return $info[$component->getRequestId()];
  }
  
  private static function initDatamodelWebsiteInfo() {
    static $settings = null;
    if (\is_null($settings)) {
      $settings = array(
        'title' => Main::setting('siteTitle'),
        'subtitle' => Main::setting('siteSubtitle'),
        'domain' => Main::getDomain(),
        'base' => Main::getBaseUrl(),
        'defaultLang' => Main::setting('defaultLang', 'en'),
      );
    }
    return $settings;
  }
  
  /**
   * Implements controller event preprocessTemplate()
   */
  public static function preprocessTemplate() {
    
  }

  /**
   * Implements controller event onRun()
   */
  public static function onRun(\system\Component $component) {
    $component->addTemplate('website-logo', 'header');
    $component->addTemplate('langs-control', 'header-sidebar');
    $component->addTemplate('footer', 'footer');
    //$component->addTemplate('sidebar', 'sidebar');
  }
}