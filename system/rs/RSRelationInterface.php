<?php
namespace system\rs;

interface RSRelationInterface extends RSTableInterface {
  public function getType();
	public function getJoinType();
	public function getOnDelete();

	public function isHasOne();
	public function isHasMany();
	
	public function getRootTable();
	public function getParentTable();
}
