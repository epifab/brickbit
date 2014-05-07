<?php
namespace module\user;

use system\model2\TableInterface;

class UserModule {
  /**
   * Implements recordsetTableInit() event
   */
  public static function recordsetTableInit(TableInterface $table) {
    switch ($table->getName()) {
      case 'user':
        $table->import(
          '*',
          'roles.*',
          'roles.role.*',
          'roles.role.permissions.*'
        );
        break;
    }
  }
}