<?php
namespace system\module\system10;

use system\Component;
use system\Main;
use system\model2\RecordsetInterface;
use system\model2\Table;
use system\utils\Lang;
use system\utils\HTMLHelpers;
use system\utils\Login;

class SystemModule {
  /**
   * Implements widgetsMap() controller event
   */
  public static function widgetsMap() {
    return array(
      'hidden' => '\system\view\WidgetHidden',
      'textbox' => '\system\view\WidgetTextbox',
      'textarea' => '\system\view\WidgetTextarea',
      'selectbox' => '\system\view\WidgetSelectbox',
      'radiobutton' => '\system\view\WidgetRadiobutton',
      'radiobuttons' => '\system\view\WidgetRadiobuttons',
      'checkbox' => '\system\view\WidgetCheckbox',
      'checkboxes' => '\system\view\WidgetCheckboxes',
      'password' => '\system\view\WidgetPassword',
    );
  }

  /**
   * Implements metaTypesMap() controller event
   */
  public static function metaTypesMap() {
    return array(
      'integer' => '\system\metatypes\MetaInteger',
      'decimal' => '\system\metatypes\MetaDecimal',
      'string' => '\system\metatypes\MetaString',
      'plaintext' => '\system\metatypes\MetaString',
      'html' => '\system\metatypes\MetaHTML',
      'blob' => '\system\metatypes\MetaBlob',
      'boolean' => '\system\metatypes\MetaBoolean',
      'date' => '\system\metatypes\MetaDate',
      'datetime' => '\system\metatypes\MetaDateTime',
      'virtual' => '\system\metatypes\MetaString',
      'password' => '\system\metatypes\MetaString'
    );
  }

  public static function onRun(Component $component) {
    $component->addJsData('settings', array(
      'baseDir' => Main::getPathVirtual('')
    ));
  }

  /**
   * Implements onDelete() controller event
   */
  public static function onDelete(RecordsetInterface $rs) {
    // Get a clean table
    $table = Table::loadTable($rs->getTable()->getName());

    $table->import('**');

    $relationsToDelete = array();

    foreach ($table->getRelations() as $relation) {
      if ($relation->deleteCascade()) {
        $relation->import('*');
        // Always load the parent
        $relation->setJoinType('LEFT');
        // We'll have to delete children
        $relation->setLazyLoading(false);
        $relationsToDelete[] = $relation;
      }
      else {
        // We don't need to load this relation
        $relation->setLazyLoading(true);
      }
    }

    if (!empty($relationsToDelete)) {
      // Gets a fresh recordset to be sure all the relations are loaded
      $primary = $rs->getPrimaryKey();
      foreach ($primary as $field => $value) {
        $table->addFilters($table->filter($field, $value));
      }
      $rs = $table->selectFirst();
      if (empty($rs)) {
        SystemApi::watchdog('recordset-delete', 'Unable to perform delete cascade. Recordset not found.', array(), \system\LOG_WARNING);
        return;
      }

      foreach ($relationsToDelete as $relation) {
        $children = $rs->{$relation->getName()};

        if (!empty($children)) {
          if (\is_array($children)) {
            // Has many relation
            foreach ($children as $child) {
              $child->delete();
            }
          }
          else {
            // Has one relation
            $children->delete();
          }
        }
      }
    }
  }

  /**
   * Implements controller event initDatamodel()
   */
  public static function initDatamodel() {
    $languages = array();
    foreach (Main::getLanguages() as $lang) {
      $languages[$lang] = Lang::langLabel($lang);
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
        'langs' => $languages,
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
        'js' => array(
          'script' => array(),
          'data' => array('test' => json_encode(array(
            'x' => array(1,2,'y' => 'whatever'),
            'y' => array(2,3,array())
          )))
        ),
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
}
