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

  public static function traceString($trace = null) {
    if (empty($trace)) {
      $trace = debug_backtrace();
    }
    if (count($trace) > 0) {
      $traceString = '<ol>';

      $i = count($trace);
      foreach ($trace as $t) {
        $traceString .= '<li value="' . $i . '"><p><code>';

        $i--;

        if (!empty($t['class'])) {
          $traceString .= $t['class'] . '->';
        }
        $traceString .= '<b>' . $t['function'] . '</b>(';

        $first = true;

        if (\array_key_exists("args", $t)) {
          foreach ($t['args'] as $arg) {
            $first ? $first = false : $traceString .= ', ';
            $traceString .= \system\utils\Utils::varDump($arg);
          }
        }
        $traceString .= ')</code><br/> ' . (isset($t['file']) ? $t['file'] : '') . ' ' . (isset($t['line']) ? $t['line'] : '') .  '</p></li>';
      }
      $traceString .= '</ol>';
    }
    return $traceString;
  }
  
  /**
   * Genera una pagina HTML di errore, con un messaggio specificato dall'utente
   * @param tpl Template manager
   * @param message Messaggio di errore
   * @param out Print writer
   */
  public static function makeErrorPage(\system\view\TemplateManager $templateManager, array $datamodel, \system\exceptions\Error $exception, $executionTime=0) {
    $traceString = self::traceString($exception->getTrace());

    $executionTimeString = ($executionTime > 0)
      ? Lang::translate('<p>Execution time: @time</p>', array(
        '@time' => ($executionTime < 1)
          ? (round($executionTime * 1000, 0) . ' ms.')
          : ($executionTime . ' sec.')
      )) : '';
      
    if ($exception instanceof \system\exceptions\AuthorizationError) {
      $template = '403';
      $title = \system\utils\Lang::translate('403 Forbidden');
      $msg = \system\utils\Lang::translate("You don't have sufficient permission to access this resource.");
    }
    
    elseif ($exception instanceof \system\exceptions\PageNotFound) {
      $template = '404';
      $title = \system\utils\Lang::translate('404 Page not found');
      $msg = \system\utils\Lang::translate("Page not found.");
    }
    
    else {
      $template = '500';
      $title = \system\utils\Lang::translate('500 Internal error');
      $msg = 
        '<div>'
        . '<h3>' . $exception->getMessage() . '</h3>'
        . '<div class="trace">' . $traceString . '</div>'
        . '<div class="execution-time">' . $executionTimeString . '</div>'
        . '</div>';
    }
    
    $datamodel['page']['title'] = $title;
    $datamodel['error'] = array(
      'title' => $title,
      'body' => $msg,
      'exception' => $exception,
      'executionTime' => $executionTimeString,
      'trace' => $traceString,
      'debug' => \system\utils\Log::getDebug()
    );
    $datamodel['system']['responseType'] = 'ERROR';
    
    \system\utils\Log::create('system', $exception->getMessage(), array(), \system\LOG_ERROR);

    try {
      
      $templateManager->setMainTemplate($template);
      
      $templateManager->process($datamodel);
      
    } catch (TemplateManagerError $ex) {
      
      // Non e' stato trovato il template di errore
      
      if (HTMLHelpers::isAjaxRequest()) {
        echo '<div class="alert alert-danger"><h2>' . $title . '</h2>' . $msg . '</div>';
      }
      else {
        HTMLHelpers::printPageHeader("Fatal error");
        echo '<div class="alert alert-danger"><h2>' . $title . '</h2>' . $msg . '</div>';
        HTMLHelpers::printPageFooter();
      }
    }
  }
}
