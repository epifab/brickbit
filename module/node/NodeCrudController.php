<?php
namespace module\node;

use system\Main;
use system\exceptions\InternalError;
use system\exceptions\InputOutputError;
use system\model2\DataLayerCore;
use system\model2\RecordsetInterface;
use system\model2\Table;
use system\session\Session;
use system\utils\Lang;
use system\utils\Login;
use module\crud\RecordMode;
use module\crud\CrudController;

class NodeCrudController extends CrudController {
  ///<editor-fold defaultstate="collapsed" desc="Access methods">
  
  /**
   * Check whether the user has access to the node identified by the $id
   *  parameter according to the $action parameter
   * @param string $action Action (READ, EDIT, DELETE)
   * @param int $id Node id
   * @param object $user User
   * @return boolean True if the user has access to the node
   */
  private static function accessRED($action, $id, $user) {
    $table = Table::loadTable('node');
    
    $table->addFilters($table->filter('id', $id));
    
    if ($table->countRecords() > 0) {
      switch ($action) {
        case "READ":
          RecordMode::addReadModeFilters($table, $user);
          break;
        case "EDIT":
          RecordMode::addEditModeFilters($table, $user);
          break;
        case "DELETE":
          RecordMode::addDeleteModeFilters($table, $user);
          break;
        default:
          throw new InternalError('Invalid @name parameter', array('@name' => 'action'));
      }
      return $table->countRecords() > 0;
    }
    return true;
  }

  /**
   * Determines access to creation of root nodes
   * @param array $urlArgs URL arguments
   * @param object $user User
   * @return boolean TRUE if the user is able to create a root node
   * @throws InputOutputError
   */
  public static function accessAdd($urlArgs, $user) {
    $nodeTypes = NodeApi::nodeTypes();
    
    if (!isset($nodeTypes[$urlArgs[0]])) {
      throw new InputOutputError('Invalid node type <em>@type</em>.', array('@type' => $urlArgs[0]));
    }
    if (!\in_array($urlArgs[0], $nodeTypes['#'])) {
      return false;
    }
    
    // only superuser is able to add nodes to the root
    return $user && $user->superuser;
  }
  
  /**
   * Determines access to creation of a node as a child of a specific node
   * @param array $urlArgs URL arguments
   * @param object $user User
   * @return boolean TRUE if the user is able to create the node
   * @throws InputOutputError
   */
  public static function accessAdd2Node($urlArgs, $user) {
    $nodeTypes = NodeApi::nodeTypes();
    
    if (!isset($nodeTypes[$urlArgs[1]])) {
      throw new InputOutputError('Invalid node type.');
    }
    
    // get the parent node
    $table = Table::loadTable('node');
    $table->import('type');
    $table->addFilters($table->filter('id', $urlArgs[0]));
    // Check if the logged user has sufficient permissions 
    //  to edit the parent node
    RecordMode::addEditModeFilters($table, $user);
    $parentNode = $table->selectFirst();
    
    if (!$parentNode) {
      return false;
    }
    // edit permissions ok
    
    // just need to check whether is allowed to add the node
    if (!\in_array($urlArgs[1], $nodeTypes[$parentNode->type]['children'])) {
      return false;
    }
    return true;
  }
  
  /**
   * Read access
   * @param array $urlArgs URL arguments
   * @param object $user User
   * @return boolean TRUE if the user is able to access the node
   */
  public static function accessRead($urlArgs, $user) {
    return self::accessRED("READ", $urlArgs[0], $user);
  }

  /**
   * Read access
   * @param array $urlArgs URL arguments
   * @param object $user User
   * @return boolean TRUE if the user is able to access the node
   */
  public static function accessReadByUrn($urlArgs, $user) {
    list($urn) = $urlArgs;
    
    $table = Table::loadTable('node');
    $table->import('text.urn');
    $table->addFilters($table->filter('text.urn', $urn));
    
    if ($table->countRecords() > 0) {
      RecordMode::addReadModeFilters($table, $user);
      return $table->countRecords() > 0;
    }
    return true;
  }

  /**
   * Determines access to node editing
   * @param array $urlArgs URL arguments
   * @param object $user User
   * @return boolean TRUE if the user should be able to edit this node
   */
  public static function accessEdit($urlArgs, $user) {
    return self::accessRED("EDIT", $urlArgs[0], $user);
  }

  /**
   * Determines access to node deletion
   * @param array $urlArgs URL arguments
   * @param object $user User
   * @return boolean TRUE if the user should be able to delete this node
   */
  public static function accessDelete($urlArgs, $user) {
    return self::accessRED("DELETE", $urlArgs[0], $user);
  }
  ///</editor-fold>
  
  /**
   * Returns the node builder
   * @return Table Node builder
   */
  protected function getNodeTable() {
    $table = Table::loadTable('node');
    $table->import(
      '*',
      'record_mode.*',
      'text.*',
      'texts.*'
    );
    return $table;
  }
  
  /**
   * Creates a new (temporary) node recordset
   * @param string $type Recordset type
   * @param int $parentId Parent node id
   * @return RecordsetInterface
   * @throws InputOutputError
   */
  private function getTmpRecordset($type, $parentId=null) {
    $parentNode = null;
    if ($parentId) {
      // Initialize parent node (if any)
      $ptable = Table::loadTable('node');
      $ptable->import('*');
      $parentNode = $ptable->selectFirst($ptable->filter('id', $parentId));
      if (empty($parentNode)) {
        // Parent node not found
        throw new InputOutputError('The node you were looking for was not found.');
      }
    }
    
    // Initialize node builder
    $table = $this->getNodeTable();
    
    $node = null;

    // Always handles with a temporary node
    // Get the temp node id from the session
    $nodeId = Session::getInstance()->get('core::EditNode', 'temp_node_id');
    // If the node ID exists..
    if ($nodeId) {
      // Load the node
      $node = $table->selectFirst($table->filter('id', $nodeId));
      if (!$node || !$node->temp) {
        // Node not found or "temp" field set to 0
        $node = null;
      }
      else if ($node->record_mode->owner_id != Login::getLoggedUserId()) {
        // The node owner must be the current logged in user
        $node->delete(); // This should never happen!
        $node = null;
      }
      else if ($node->type != $type) {
        // The node type must match the current node type
        $node->delete();
        $node = null;
      }
    }

    if (empty($node)) {
      // If the temp node is invalid or does not exist we need to create a new 
      //  one
      $da = DataLayerCore::getInstance();
      $da->beginTransaction();
      
      try {
        $node = $table->newRecordset();
        
        $node->temp = true;
        $node->type = $type;

        if (empty($parentNode)) {
          // No parent node
          $node->parent_id = null;
          $node->ldel = 1 + $da->executeScalar("SELECT MAX(rdel) FROM node");
          $node->rdel = $node->ldel + 1;
          // Node sort index
          $node->sort_index = 1 + $da->executeScalar("SELECT MAX(sort_index) FROM node WHERE parent_id IS NULL");
        }
        else {
          // Parent node
          $node->parent_id = $parentNode->id;
          // We set the left delimiter to the parent node right delimiter
          $node->ldel = $parentNode->rdel;
          $node->rdel = $node->ldel + 1;
          // We need to adjust the parent node right delimiter 
          //  and delimiters for every node to preserve the tree structure
          $da->executeUpdate("UPDATE node SET ldel = ldel + 2 WHERE ldel > " . $node->rdel);
          $da->executeUpdate("UPDATE node SET rdel = rdel + 2 WHERE rdel >= " . $node->ldel);
          // Node sort index
          $node->sort_index = 1 + $da->executeScalar("SELECT MAX(sort_index) FROM node WHERE parent_id = " . $parentNode->id);
        }

        RecordMode::saveRecordMode($node);
        $node->save();
        
        \system\session\Session::getInstance()->set('core::EditNode', 'temp_node_id', $node->id);
        
        $da->commitTransaction();
      }
      
      catch (\Exception $ex) {
        $da->rollbackTransaction();
        throw $ex;
      }
    }
    return $node;
  }
  
  ///<editor-fold defaultstate="collapsed" desc="Editing stuff">
  
  /**
   * Edit actions (add, add to a node, edit.
   * @return array List of available actions
   */
  public function getEditActions() {
    return array('Add', 'Add2Node', 'Edit');
  }
  
  /**
   * Perform some custom validation
   * @return boolean TRUE if the submission is valid
   */
  protected function formSubmission() {
    $form = $this->getForm();
    // Ignore disabled languages
    foreach (Main::getLanguages() as $lang) {
      if (!$form->fetchInputValue('node_' . $lang . '_enable')) {
        // Text disabled: we can ignore every input related to that translation
        $form->removeRecordsetInput('node_' . $lang);
      }
    }
    // Default form submission
    return parent::formSubmission();
  }
  
  /**
   * Returns editable recordsets
   * @return RecordsetInterface
   * @throws \system\exceptions\InputOutputError
   */
  protected function getEditRecordsets() {
    $node = null;
    switch ($this->getAction()) {
      case 'Add':
        $node = $this->getTmpRecordset($this->getUrlArg(0));
        $this->setPageTitle(\cb\t('Add a new @title', array('@title' => $node->type)));
        break;

      case 'Add2Node':
        $node = $this->getTmpRecordset($this->getUrlArg(1), $this->getUrlArg(0));
        $this->setPageTitle(\cb\t('Add a new @title', array('@title' => $node->type)));
        break;

      case 'Edit':
        $table = $this->getNodeTable();
        $node = $table->selectFirst($table->filter('id', $this->getUrlArg(0)));
        $this->setPageTitle(\cb\t('Edit @title', array('@title' => $node->title)));
        break;
    }
    
    $recordsets = array(
      'node' => $node,
      'node_record_mode' => $node->record_mode // Record mode
    );
    
    // This is used for text which aren't stored in the DB
    $nodeTextTable = Table::loadTable('node_text');
    $nodeTextTable->import('*');
    
    foreach (Main::getLanguages() as $lang) {
      if (isset($node->texts[$lang])) {
        // Translation already exists
        $nodeText = $node->texts[$lang];
      }
      else {
        // Translation does not exist... Need to add a new recordset to the form
        $nodeText = $nodeTextTable->newRecordset();
        // Initialize primary key
        $nodeText->lang = $lang;
        $nodeText->node_id = $node->id;
      }
      $recordsets['node_' . $lang] = $nodeText;
    }
    
    return $recordsets;
  }
  
  /**
   * Form ID
   * @return string Form ID
   */
  public function getFormId() {
    switch ($this->getAction()) {
      case 'Add':
      case 'Add2Node':
        return 'node-create-form';
        break;
      case 'Edit':
      default:
        return 'node-update-form';
        break;
    }
  }
  
  /**
   * Form template
   * @return string Form template
   */
  public function getFormTemplate() {
    $node = $this->getForm()->getRecordset('node');
    
    // Template name suggestions
    $templates = array(
      'edit-node--' . $node->id,
      'edit-node-' . $node->type,
      'edit-node'
    );
    foreach ($templates as $t) {
      if (Main::templateExists($t)) {
        return $t;
      }
    }
    throw new InternalError(
      'No suitable editing template found for this node.'
      . '<p>Possible template suggestions are:</p>'
      . '<ul>'
      . '<li>edit-node--@nid</li>'
      . '<li>edit-node-@type</li>'
      . '<li>edit-node</li>'
      . '</ul>', array('@nid' => $rs->id, '@type' => $rs->type)
    );
  }
  
  protected function saveRecordsets() {
    $form = $this->getForm();
    
    $node = $form->getRecordset('node');
    
    Main::pushMessage($node->toArray());
    
    $da = DataLayerCore::getInstance();
    
    try {
      foreach (Main::getLanguages() as $lang) {
        $text = $form->getRecordset('node_' . $lang);
        
        if ($form->getInputValue('node_' . $lang . '_enable')) {
          if (!$text->checkKey('urn_key')) {
            $form->setValidationError(
              $form->getRecordsetInputName('node_' . $lang, 'urn'),
              \cb\t('The URN you entered is already in use.')
            );
            throw new \system\exceptions\ValidationError('Duplicate URN key');
          }
          $text->save();
        }
        else {
          if ($text->isStored()) {
            $text->delete();
          }
        }
      }
      
      RecordMode::saveRecordMode($node);
      $node->save();
      
      $da->commitTransaction();
    }
    catch (\Exception $ex) {
      $da->rollbackTransaction();
      throw $ex;
    }
  }
  
  public function submitAdd() {
    $form = $this->getForm();
    $form->getRecordset('node')->temp = false;
    $this->saveRecordsets();
  }
  
  public function submitAdd2Node() {
    $form = $this->getForm();
    $form->getRecordset('node')->temp = false;
    $this->saveRecordsets();
  }
  
  public function submitEdit() {
    $this->saveRecordsets();
  }
  ///</editor-fold>
  
  protected function read($node) {
    $this->datamodel['node'] = $node;
    $this->setMainTemplate('node-default');
    if ($node->text->title) {
      $this->setPageTitle($node->text->title);
    }
    return \system\RESPONSE_TYPE_READ;
  }

  public function runRead() {
    $table = $this->getNodeTable();
    RecordMode::addReadModeFilters($table, Login::getLoggedUser());
    $node = $table->selectFirst($table->filter('id', $this->getUrlArg(0)));
    if (!$node) {
      throw new \system\exceptions\PageNotFound();
    }
    return $this->read($node);
  }
  
  public function runReadByUrn() {
    $table = $this->getNodeTable();
    RecordMode::addReadModeFilters($table, Login::getLoggedUser());
    $node = $table->selectFirst($table->filter('text.urn', $this->getUrlArg(0)));
    if (!$node) {
      throw new \system\exceptions\PageNotFound();
    }
    return $this->read($node);
  }
  
  public function runDelete() {
    $dl = DataLayerCore::getInstance();
    $dl->beginTransaction();
    try {
      $table = $this->getNodeTable();
      $table->import('*', 'text.title');
      RecordMode::addDeleteModeFilters($table, Login::getLoggedUser());
      
      $node = $table->selectFirst($table->filter('id', $this->getUrlArg(0)));
      $node->delete();

      $this->setMainTemplate('notify');
      $this->datamodel['message'] = array(
        'title' => 'Content deleted',
        'body' => Lang::translate('<em>@title</em> has been deleted', array('@title' => $node->text->title)),
      );
      
      $dl->commitTransaction();
      
      return \system\RESPONSE_TYPE_NOTIFY;
    }
    catch (\Exception $ex) {
      $dl->rollbackTransaction();
      throw $ex;
    }
  }
}