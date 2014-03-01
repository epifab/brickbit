<?php
namespace module\pages\components;

use system\model\RecordsetBuilder;
use system\model\FilterClause;
use system\model\FilterClauseGroup;

class Menu extends \system\Component {
  private static function getPage($url) {
    static $pages = array();
    if (!isset($pages[$url])) {
      $page = new RecordsetBuilder('menu');
      $page->using('*');
      $page->setFilter(new \system\model\FilterClause(
        $page->url, '=', $url
      ));
      $p = $page->selectFirst();
      $pages[$url] = $p;
    }
    return $pages[$url];
  }
  
  public static function accessMain($urlArgs, $user) {
    $page = self::getPage(current($urlArgs));
    if (!$page) {
      throw new \system\exceptions\PageNotFound();
    }
    return $user && $user->superuser;
  }
  
  public function runMain() {
    
  }
}