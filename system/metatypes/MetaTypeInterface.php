<?php
namespace system\metatypes;

interface MetaTypeInterface {
  public function getName();
  public function getLabel();
  public function getType();
  public function getEditWidget();
  public function getDefaultValue();
  public function getAttr($key, $options = array());
  public function getAttributes();
  public function attrExists($key);
  public function toProg($x);
  public function db2Prog($x);
  public function prog2Db($x);
  public function edit2Prog($x);
  public function prog2Edit($x);
  public function validate($x);
}