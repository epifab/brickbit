<?php
namespace system\model2;

interface PropertyInterface {
  /**
   * @return string Property name
   */
  public function getName();
  /**
   * @return string Property path
   */
  public function getPath();
  /**
   * @return string Property alias
   */
  public function getAlias();
  /**
   * @return array Property info
   */
  public function getInfo();
}
