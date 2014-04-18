<?php
namespace module\crud;

use system\model2\RecordsetInterface;
use system\model2\Table;
use system\model2\TableInterface;

class RecordMode {
  const MODE_NOBODY = 0;
  const MODE_SU = 1;
  const MODE_SU_OWNER = 2;
  const MODE_SU_OWNER_ADMINS = 3;
  const MODE_REGISTERED = 4;
  const MODE_ANYONE = 5;
  
  /**
   * Checks read access to the record mode
   * @param RecordsetInterface $recordMode Record mode
   * @param RecordsetInterface $user User
   * @return bool TRUE if the user has access to the recordset
   */
  public static function checkReadAccess(RecordsetInterface $recordMode, RecordsetInterface $user = null) {
    return self::checkAccess('read', $recordMode, $user);
  }
  
  /**
   * Checks edit access to the record mode
   * @param RecordsetInterface $recordMode Record mode
   * @param RecordsetInterface $user User
   * @return bool TRUE if the user has access to the recordset
   */
  public static function checkEditAccess(RecordsetInterface $recordMode, RecordsetInterface $user = null) {
    return self::checkAccess('edit', $recordMode, $user);
  }
  
  /**
   * Checks delete access to the record mode
   * @param RecordsetInterface $recordMode Record mode
   * @param RecordsetInterface $user User
   * @return bool TRUE if the user has access to the recordset
   */
  public static function checkDeleteAccess(RecordsetInterface $recordMode, RecordsetInterface $user = null) {
    return self::checkAccess('delete', $recordMode, $user);
  }
  
  private static function checkAccess($modeType, RecordsetInterface $recordMode, RecordsetInterface $user = null) {
    $mode = "{$modeType}_mode";

    if (empty($user) || $user->anonymous) {
      // NOT LOGGED -> not logged user can access the recordset only when 
      //  record mode is = MODE_ANYONE
      return $recordMode->{$mode} >= self::MODE_ANYONE;
    }

    else if ($user->superuser) {
      // SUPERUSER -> just make sure the record mode is >= than MODE_SU
      return $recordMode->{$mode} >= self::MODE_SU;
    }

    else {
      // GENERIC LOGGED USER
      // 
      //  logged users can access the recordset when
      //   1) the user owns the recordset 
      //      and the record mode is >= than MODE_SU_OWNER
      //   2) the user is an admininstrators 
      //      and the record mode is >= than MODE_SU_OWNER_ADMINS 
      //   3) the record mode is >= than MODE_REGISTERED 
      
      if ($recordMode->{$mode} >= self::MODE_REGISTERED) {
        return true;
      }
      
      elseif ($recordMode->{$mode} >= self::MODE_SU_OWNER_ADMINS) {
        if (\array_key_exists($user->id, $recordMode->users)) {
          return true;
        }
        else {
          foreach ($user->roles as $role) {
            if (\array_key_exists($role->id, $recordMode->roles)) {
              return true;
            }
          }
        }
      }
    }
    
    return false;
  }
  
  /**
   * Adds filter to a record moded table to implement read permission.
   * @param \system\model2\TableInterface $table Record moded table
   * @param \system\model2\RecordsetInterface $user User
   */
  public static function addReadModeFilters(TableInterface $table, RecordsetInterface $user = null) {
    self::addRecordModeFilters('read', $table, $user);
  }
  
  /**
   * Adds filter to a record moded table to implement edit permission.
   * @param \system\model2\TableInterface $table Record moded table
   * @param \system\model2\RecordsetInterface $user User
   */
  public static function addEditModeFilters(TableInterface $table, RecordsetInterface $user = null) {
    self::addRecordModeFilters('edit', $table, $user);
  }
  
  /**
   * Adds filter to a record moded table to implement delete permission.
   * @param \system\model2\TableInterface $table Record moded table
   * @param \system\model2\RecordsetInterface $user User
   */
  public static function addDeleteModeFilters(TableInterface $table, RecordsetInterface $user = null) {
    self::addRecordModeFilters('delete', $table, $user);
  }
  
  private static function addRecordModeFilters($modeType, TableInterface $table, RecordsetInterface $user = null) {
    $table->addFilters(self::getRecordModeFilters($modeType, $table->record_mode, $user));
  }
  
  private static function getRecordModeFilters($modeType, TableInterface $recordMode, RecordsetInterface $user = null) {
    $mode = "{$modeType}_mode";

    if (empty($user) || $user->anonymous) {
      // NOT LOGGED -> not logged user can access the recordset only when 
      //  record mode is = MODE_ANYONE
      return $recordMode->filter($mode, self::MODE_ANYONE, '>=');
    }

    else if ($user->superuser) {
      // SUPERUSER -> just make sure the record mode is >= than MODE_SU
      return $recordMode->filter($mode, self::MODE_SU, '>=');
    }

    else {
      // GENERIC LOGGED USER
      // 
      //  logged users can access the recordset when
      //   1) the user owns the recordset 
      //      and the record mode is >= than MODE_SU_OWNER
      //   2) the user is an admininstrators 
      //      and the record mode is >= than MODE_SU_OWNER_ADMINS 
      //   3) the record mode is >= than MODE_REGISTERED 
      
      return $recordMode->filterGroup('OR')->addClauses(
        // Registered
        $recordMode->filter($mode, self::MODE_REGISTERED, '>='),

        // User is an administrator
        $recordMode->filterGroup('AND')->addClauses(
          $recordMode->filter($mode, self::MODE_SU_OWNER_ADMINS, '>='),
          $recordMode->filterGroup('OR')->addClauses(
            $recordMode->filterCustom(
              "@uid IN ("
              . "SELECT user_id"
              . " FROM record_mode_user rmu"
              . " WHERE rmu.record_mode_id = {$recordMode->getField('id')->getSelectExpression()}"
              . ")", array('@uid' => $user->id)
            ),
            $table->filterCustom(
              "@uid IN ("
              . "SELECT ur.user_id"
              . " FROM record_mode_role rmr"
              . " INNER JOIN user_role ur ON ur.role_id = rmr.role_id"
              . " WHERE rmr.record_mode_id = {$recordMode->getField('id')->getSelectExpression()}"
              . ")", array('@uid' => $user->id)
            )
          )
        ),

        // User is the owner
        $recordMode->filterGroup('AND')->addClauses(
          $recordMode->filter($mode, self::MODE_SU_OWNER, '>='),
          $recordMode->filter("owner_id", $user->id)
        )
      );
    }
  }
  
  /**
   * Saves a record mode
   * @param RecordsetInterface $recordset Record moded recordset
   * @param int $readMode Read mode (default to anyone)
   * @param int $editMode Edit mode (default to record administrators)
   * @param int $deleteMode Delete mode (default to record administrators)
   */
  public static function saveRecordMode(
      RecordsetInterface $recordset,
      $readMode = null,
      $editMode = null,
      $deleteMode = null) {
    
    if ($recordset->record_mode->isStored()) {
      self::updateRecordMode($recordset, $readMode, $editMode, $deleteMode);
    }
    else {
      self::createRecordMode($recordset, $readMode, $editMode, $deleteMode);
    }
  }

  /**
   * Creates a record mode
   * @param RecordsetInterface $recordset Record moded recordset
   * @param int $readMode Read mode
   * @param int $editMode Edit mode
   * @param int $deleteMode Delete mode
   */
  private static function createRecordMode(
      RecordsetInterface $recordset,
      $readMode = null,
      $editMode = null,
      $deleteMode = null) {
    
    $recordMode = $recordset->record_mode;
    
    switch ($readMode) {
      //case self::MODE_NOBODY:
      //case self::MODE_SU:
      case self::MODE_SU_OWNER:
      case self::MODE_SU_OWNER_ADMINS:
      case self::MODE_REGISTERED:
      case self::MODE_ANYONE:
        $recordMode->read_mode = $readMode;
        break;
      
      default:
        $recordMode->read_mode = self::MODE_ANYONE;
        break;
    }
    
    switch ($editMode) {
      //case self::MODE_NOBODY:
      case self::MODE_SU:
      case self::MODE_SU_OWNER:
      case self::MODE_SU_OWNER_ADMINS:
      case self::MODE_REGISTERED:
      //case self::MODE_ANYONE:
        $recordMode->edit_mode = $editMode;
        break;
      
      default:
        $recordMode->edit_mode = self::MODE_SU_OWNER_ADMINS;
        break;
    }
    
    switch ($deleteMode) {
      //case self::MODE_NOBODY:
      case self::MODE_SU:
      case self::MODE_SU_OWNER:
      case self::MODE_SU_OWNER_ADMINS:
      case self::MODE_REGISTERED:
      //case self::MODE_ANYONE:
        $recordMode->delete_mode = $deleteMode;
        break;
      
      default:
        $recordMode->delete_mode = self::MODE_SU_OWNER_ADMINS;
        break;
    }
    
    $recordMode->owner_id = \system\utils\Login::getLoggedUserId();
    $recordMode->ins_date_time = \time();
    $recordMode->last_upd_date_time = \time();
    $recordMode->last_modifier_id = \system\utils\Login::getLoggedUserId();
    
    $recordMode->create();
    
    if (\system\Main::setting('recordModeLogs')) {
      self::createRecordModeLog($recordMode);
    }
    
    $recordset->record_mode_id = $recordMode->id;
  }
  
  /**
   * Creates a record mode
   * @param RecordsetInterface $recordset Record moded recordset
   * @param int $readMode Read mode
   * @param int $editMode Edit mode
   * @param int $deleteMode Delete mode
   */
  private static function updateRecordMode(
      RecordsetInterface $recordset,
      $readMode = null,
      $editMode = null,
      $deleteMode = null) {
    
    $recordMode = $recordset->record_mode;
    
    switch ($readMode) {
      //case self::MODE_NOBODY:
      //case self::MODE_SU:
      case self::MODE_SU_OWNER:
      case self::MODE_SU_OWNER_ADMINS:
      case self::MODE_REGISTERED:
      case self::MODE_ANYONE:
        $recordMode->read_mode = $readMode;
        break;

      default:
        // Leave it as it is
        break;
    }
    
    switch ($editMode) {
      //case self::MODE_NOBODY:
      case self::MODE_SU:
      case self::MODE_SU_OWNER:
      case self::MODE_SU_OWNER_ADMINS:
      case self::MODE_REGISTERED:
      //case self::MODE_ANYONE:
        $recordMode->edit_mode = $editMode;
        break;

      default:
        // Leave it as it is
        break;
    }
    
    switch ($deleteMode) {
      //case self::MODE_NOBODY:
      case self::MODE_SU:
      case self::MODE_SU_OWNER:
      case self::MODE_SU_OWNER_ADMINS:
      case self::MODE_REGISTERED:
      //case self::MODE_ANYONE:
        $recordMode->delete_mode = $deleteMode;
        break;

      default:
        // Leave it as it is
        break;
    }

    $recordMode->last_modifier_id = \system\utils\Login::getLoggedUserId();
    $recordMode->last_upd_date_time = \time();
    $recordMode->update();
    
    if (\system\Main::setting('recordModeLogs')) {
      self::createRecordModeLog($recordMode);
    }
  }
  
  /**
   * Creates the record mode log entry
   * @param RecordsetInterface $recordMode Record mode
   */
  private static function createRecordModeLog(RecordsetInterface $recordMode) {
    $recordModeLog = Table::loadTable('record_mode_log');
    $recordModeLog->import('*');

    $rs = $recordModeLog->newRecordset();

    $rs->record_mode_id = $recordMode->id;
    $rs->user_id = $recordMode->last_modifier_id;
    $rs->upd_date_time = $recordMode->last_upd_date_time;
    
    $rs->create();
  }
}