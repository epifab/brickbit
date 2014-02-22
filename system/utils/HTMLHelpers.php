<?php
namespace system\utils;

class HTMLHelpers {
  public static function getTagUrl($x) {
    return \str_replace(' ', '_', $x);
  }
  
  public static function getIpAddress() {
    static $ipAddress = null;
    if (is_null($ipAddress)) {
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
      } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
      }
    }
    return $ipAddress;
  }

  /**
   * Stampa le intestazioni html
   * @param out
   * @param title Titolo della pagina
   */
  public static function printPageHeader($title) {
    echo '<?xml version="1.0" encoding="ISO-8859-1"' .
       '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' .
       '<html xmlns="http://www.w3.org/1999/xhtml">' .
       '<head><title>' . $title . '</title></head><body>';
  }

  /**
   * Chiude i tag body e html
   * @param out Print writer
   */
  public static function printPageFooter() {
    echo '</body></html>';
  }

  public static function makeLoginErrorPage($templateManager, $datamodel, $message=null) {
    self::makeErrorPage($templateManager, $datamodel, $message);
  }

  public static function isAjaxRequest() {
    return (isset($_REQUEST['system']) && !empty($_REQUEST['system']['ajax'])) ||
         (\array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || \strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'));
  }

  /**
   * Genera una pagina HTML di errore, con un messaggio specificato dall'utente
   * @param tpl Template manager
   * @param message Messaggio di errore
   * @param out Print writer
   */
  public static function makeErrorPage(\system\view\TemplateManager $templateManager, array $datamodel, \system\exceptions\Error $mainError, $executionTime=0) {
    $msg = "";
    $exception = $mainError;

    while ($exception != null) {
      $msg .=
        '<h3>' . $exception->getMessage() . '</h3>'
        . '<h4>' . \system\utils\Lang::translate('Exception details') . '</h4>'
        . '<p>' . $exception->getFile() . ' ' . $exception->getLine() . '</p>';
      
      if ($exception instanceof \system\exceptions\Error) {
        $msg .= $exception->getDetails();
      }

      $trace = $exception->getTrace();

      if (count($trace) > 0) {
        $msg .= '<ol>';

        $i = count($trace);
        foreach ($trace as $t) {
          $msg .= '<li value="' . $i . '"><p><code>';

          $i--;

          if (array_key_exists('class', $t) && !empty($t['class'])) {
            $msg .= $t['class'] . '->';
          }
          $msg .= '<b>' . $t['function'] . '</b>(';

          $first = true;

          if (\array_key_exists("args", $t)) {
            foreach ($t['args'] as $arg) {
              $first ? $first = false : $msg .= ', ';
              $msg .= \system\utils\Utils::varDump($arg);
            }
          }
          $msg .= ')</code><br/> ' . @$t['file'] . ' ' . @$t['line'] . '</p></li>';
        }
        $msg .= '</ol>';
      }
      $exception = $exception->getPrevious();
    }
    
    if ($executionTime > 0) {
      $msg .= '<p>' . Lang::translate('Execution time: @time', array(
        '@time' => ($executionTime < 1)
          ? (round($executionTime * 1000, 0) . ' ms.')
          : ($executionTime . ' sec.')
      )) . '</p>';
    }
    
    if ($mainError instanceof \system\exceptions\AuthorizationError) {
      $title = \system\utils\Lang::translate('Forbidden');
      $msg = \system\utils\Lang::translate("You don't have sufficient permission to access this resource.");
    } else {
      $title = \system\utils\Lang::translate('Fatal error');
      $msg .= \system\utils\Log::getDebug();
    }
    
    $datamodel['page']['title'] = $title;
    $datamodel['message'] = array(
      'title' => $title,
      'body' => $msg
    );
    $datamodel['system']['responseType'] = 'ERROR';
    
    \system\utils\Log::create('system', $msg, array(), \system\LOG_ERROR);

    try {
      
      $templateManager->setMainTemplate('notify');
      
      $templateManager->process($datamodel);
      
    } catch (TemplateManagerError $ex) {
      
      // Non e' stato trovato il template di errore
      
      if (HTMLHelpers::isAjaxRequest()) {
        echo '<div class="fatal_error"><h2>' . $title . '</h2>'. $msg .'</div>';
      }
      else {
        HTMLHelpers::printPageHeader("Errore");
        echo '<div class="fatal_error"><h2>' . $title . '</h2>'. $msg .'</div>';
        HTMLHelpers::printPageFooter();
      }
    }
  }
//  
//  public static function makeValidationErrorPage(TemplateManager $templateManager, $datamodel) {
//    
//    $datamodel["url"] = "Error";
//
//    try {
//      
//      $templateManager->process("layout/Forbidden", $datamodel);
//      
//    } catch (TemplateManagerError $ex) {
//      
//      // Non e' stato trovato il template di errore
//      
//      if (HTMLHelpers::isAjaxRequest()) {
//
//        echo '<div class="fatal_error"><h2>' . $title . '</h2>'. $msg .'</div>';
//
//      } else {
//
//        HTMLHelpers::printPageHeader("Errore");
//        echo '<div class="fatal_error"><h2>' . $title . '</h2>'. $msg .'</div>';
//        HTMLHelpers::printPageFooter();
//
//      }
//    }
//  }
}
