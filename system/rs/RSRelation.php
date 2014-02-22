<?php
namespace system\rs;

class RSRelation extends RSTable implements RSRelationInterface {
  /**
   * @var RSTableInterface Parent table
   */
  private $parent;
  
  private $joinType;
  
  private $relationInfo;
  
  public function __construct(\system\rs\RSTableInterface $parent, array $info) {
    parent::__construct($info['table']);
    
    $this->parent = $parent;
    $this->relationInfo = $info;
    
    foreach ($info['clauses'] as $c) {
      $parentField = \key($c);
      $childField = \current($c);
      $this->parent->import($parentField);
      $this->import($childField);
    }
    
    if (isset($info['join'])) {
      switch (\strtoupper($info['join'])) {
        case 'INNER':
        case 'LEFT':
        case 'RIGHT':
          $this->joinType = $info['join'];
          break;
      }
    }
    else {
      $this->joinType = 'LEFT';
    }
  }
  
  public function getJoinType() {
    return $this->joinType;
  }
  
  public function getParentTable() {
    return $this->parent;
  }
  
  public function getRootTable() {
    if ($this->parent instanceof RSRelation) {
      return $this->parent->getRootTable();
    }
    else {
      return $this->parent;
    }
  }
  
  public function isHasMany() {
    return $this->relationInfo['type'] == '1-N';
  }
  
  public function isHasOne() {
    return !$this->isHasMany();
  }

  public function getOnDelete() {
    return $this->relationInfo['onDelete'];
  }

  public function getType() {
    
  }
}