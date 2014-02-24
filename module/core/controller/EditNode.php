<?php
namespace module\core\controller;

class EditNode extends Edit {
  private $rs;
  
  public function getEditActions() {
    return array('Add', 'Add2Node', 'Edit');
  }
  
  /**
   * Creates a new (temporary) node recordset
   * @param string $type Recordset type
   * @param int $parentId Parent node id
   * @return \system\model\RecordsetInterface
   * @throws \system\exceptions\InputOutputError
   */
  private function getTmpRecordset($type, $parentId=null) {
    $parentNode = null;
    if ($parentId) {
      $prsb = new \system\model\RecordsetBuilder('node');
      $prsb->using("*");
      $parentNode = $prsb->selectFirstBy(array('id' => $parentId));
      if (!$parentNode) {
        throw new \system\exceptions\InputOutputError('The node you were looking for was not found.');
      }
    }
    
    $rsb = new \system\model\RecordsetBuilder('node');
    $rsb->usingAll();
    
    $recordset = null;

    // always handle with a temporary node
    $node_id = \system\utils\Utils::getSession('core', 'temp_node_id', null);
    if ($node_id) {
      $recordset = $rsb->selectFirstBy(array('id' => $node_id));
      if (!$recordset->temp) {
        $recordset = null;
      } else if ($recordset->record_mode->owner_id != \system\utils\Login::getLoggedUserId()) {
        $recordset = null;
      } else if ($recordset->type != $type) {
        // delete the previous temp content
        $recordset->delete();
        $recordset = null;
      }
    }

    if (!$recordset) {
      $da = \system\model\DataLayerCore::getInstance();
      $da->beginTransaction();
      
      try {
        $recordset = $rsb->newRecordset();
        $recordset->temp = true;
        $recordset->type = $type;

        if (!$parentNode) {
          $recordset->parent_id = null;
          $recordset->ldel = 1 + $da->executeScalar("SELECT MAX(rdel) FROM node");
        } else {
          $recordset->parent_id = $parentNode->id;
          $recordset->ldel = $parentNode->rdel;
        }
        $recordset->rdel = $recordset->ldel + 1;

        if ($parentNode) {
          $offset = $recordset->rdel - $recordset->ldel + 1;
          $da->executeUpdate("UPDATE node SET ldel = ldel + " . $offset . " WHERE ldel > " . $recordset->rdel);
          $da->executeUpdate("UPDATE node SET rdel = rdel + " . $offset . " WHERE rdel >= " . $recordset->ldel);
        }

        $recordset->sort_index = 1 + $da->executeScalar(
          "SELECT MAX(sort_index) FROM node"
          . " WHERE " . ($parentNode ? "parent_id = " . $parentNode->id : "parent_id IS NULL")
            );

        $recordset->save(
          // default record mode options
          \system\model\RecordMode::MODE_SU_OWNER,
          \system\model\RecordMode::MODE_SU_OWNER,
          \system\model\RecordMode::MODE_SU_OWNER
        );
        
        $da->commitTransaction();
        
        \system\utils\Utils::setSession('core', 'temp_node_id', $recordset->id);
      }
      
      catch (\Exception $ex) {
        $da->rollbackTransaction();
        throw $ex;
      }
    }
    return $recordset;
  }
  
  /**
   * Returns the node recordset to edit
   * @return \system\model\RecordsetInterface
   * @throws \system\exceptions\InputOutputError
   */
  protected function getEditRecordset() {
    if (empty($this->rs)) {
      switch ($this->getAction()) {
        case 'Add':
          $this->rs = $this->getTmpRecordset($this->getUrlArg(0));
          break;
        
        case 'Add2Node':
          $this->rs = $this->getTmpRecordset($this->getUrlArg(1), $this->getUrlArg(0));
          break;
        
        case 'Edit':
          $rsb = new \system\model\RecordsetBuilder('node');
          $rsb->usingAll();
          $this->rs = $rsb->selectFirstBy(array('id' => $this->getUrlArg(0)));
          break;
      }
    }
    return $this->rs;
  }
  
  public function getFormId() {
    switch ($this->getAction()) {
      case 'Add':
      case 'Add2Node':
        return 'node-create-form';
        break;
      case 'Edit':
        return 'node-update-form';
        break;
    }
  }
  
  public function getFormTemplate(\system\model\RecordsetInterface $rs) {
    $templates = array(
      'edit-node--' . $rs->id,
      'edit-node-' . $rs->type,
      'edit-node'
    );
    foreach ($templates as $t) {
      if (\system\Main::templateExists($t)) {
        return $t;
      }
    }
    throw new \system\exceptions\InputOutputError(
      'No suitable editing template found for this node.'
      . '<p>Possible template suggestions are:</p>'
      . '<ul>'
      . '<li>edit-node--@nid</li>'
      . '<li>edit-node-@type</li>'
      . '<li>edit-node</li>'
      . '</ul>', array('@nid' => $rs->id, '@type' => $rs->type)
    );
  }
  
  public function submitAdd($form, $rs) {
    $rs->save();
    $this->setMainTemplate('node-create-submit');
    return \system\Component::RESPONSE_TYPE_NOTIFY;
  }
  
  public function submitAdd2Node($form, $rs) {
    $rs->save();
    $this->setMainTemplate('node-create-submit');
    return \system\Component::RESPONSE_TYPE_NOTIFY;
  }
  
  public function submitEdit($form, $rs) {
    $rs->save();
//    $this->addMessage('Node @id has been updated', array('@id' => $rs->id), 'notice');
    $this->setMainTemplate('notify');
    return \system\Component::RESPONSE_TYPE_NOTIFY;
  }
  
  public function runDelete() {
    // @todo delete logic here
  }
}