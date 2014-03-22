  <?php
namespace module\core\model;

use system\model2\RelationInterface;
use system\model2\RecordsetInterface;

class Tree {
  public static function ancestorsFilter(RelationInterface $ancestors, RecordsetInterface $parent = null) {
    if (empty($parent)) {
      $parent = $ancestors->getParentTable();
    }
    return $ancestors->filterGroup('AND')->addClauses(
      $ancestors->filter('rdel', $parent->rdel, '>'),
      $ancestors->filter('ldel', $parent->rdel, '<')
    );
  }
  
  public static function descendantsFilter(RelationInterface $descendants, RecordsetInterface $parent = null) {
    if (empty($parent)) {
      $parent = $descendants->getParentTable();
    }
    return $descendants->filterGroup('AND')->addClauses(
      $descendants->filter('rdel', $parent->rdel, '<'),
      $descendants->filter('ldel', $parent->rdel, '>')
    );
  }
}
