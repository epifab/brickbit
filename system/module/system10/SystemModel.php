<?php
namespace system\module\system10;

use system\SystemApi;
use system\model2\RecordsetInterface;
use system\model2\Table;

class SystemModel {
  
  /**
   * Implements onDelete() model event
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
}