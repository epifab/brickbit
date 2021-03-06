<?php
namespace system\metatypes;

class MetaInteger extends MetaType {
  
  public function prog2Db($x) {
    if ($this->getAttr('multiple', array('default' => false))) {
      $x = $this->toArray($x);
      return MetaString::stdProg2Db(\serialize($x));
    }
    else if (empty($x)) {
      if ($this->getAttr('nullable', array('default' => true)) && \is_null($x)) {
        return "NULL";
      } else {
        return "0";
      }
    }
    else {
      return $x;
    }
  }
  
  public function db2Prog($x) {
    if ($this->getAttr('multiple', array('default' => false))) {
      $x = \unserialize($x);
      $x = $this->toArray($x);
      foreach ($x as $k => $v) {
        $x[$k] = $this->toProg($v);
      }
    } else {
      $x = $this->toProg($x);
    }
    return $x;
  }
  
  public function edit2Prog($x) {
    if ($this->getAttr('multiple', array('default' => false))) {
      $x = $this->toArray($x);
      foreach ($x as $k => $v) {
        $x[$k] = $this->toProg($v);
      }
    } else {
      $x = $this->toProg($x);
    }
    $this->validate($x);
    return $x;
  }
  
  public function validate($x) {
    if ($this->getAttr('multiple', array('default' => false))) {
      $x = $this->toArray($x);
      $minOccurrence = $this->getAttr('minOccurrence', array('default' => 0));
      $maxOccurrence = $this->getAttr('maxOccurrence', array('default' => 0));
      if (\count($x) < $minOccurrence) {
        throw new \system\exceptions\ValidationError('Invalid <em>@name</em>. At least <em>@n</em> values expected.', array(
          '@n' => $minOccurrence,
          '@name' => $this->getAttr('label', array('default' => $this->getName()))
        ));
      }
      if ($maxOccurrence > 0 && \count($x) > $maxOccurrence) {
        throw new \system\exceptions\ValidationError('Invalid <em>@name</em>. No more than <em>@n</em> values allowed.', array(
          '@n' => $minOccurrence,
          '@name' => $this->getAttr('label', array('default' => $this->getName()))
        ));
      }
      foreach ($x as $v) {
        $this->validateSingle($v);
      }
    } else {
      $this->validateSingle($x);
    }
  }
  
  protected function validateSingle($x) {
    $options = $this->getAttr('options');
    if ($options) {
      if (!\array_key_exists($x, $options)) {
        throw new \system\exceptions\ValidationError('Invalid <em>@name</em>.', array(
          '@name' => $this->getAttr('label', array('default' => $this->getName()))
        ));
      }
    }
    $minvalue = $this->getAttr('minvalue', array('default' => null));
    if (!\is_null($minvalue)) {
      if ($x > $minvalue) {
        throw new \system\exceptions\ValidationError('Invalid <em>@name</em>. Number too small.', array(
          '@name' => $this->getAttr('label', array('default' => $this->getName()))
        ));
      }
    }
    $maxvalue = $this->getAttr('maxvalue', array('default' => null));
    if (!\is_null($maxvalue)) {
      if ($x > $maxvalue) {
        throw new \system\exceptions\ValidationError('Invalid <em>@name</em>. Number too big.', array(
          '@name' => $this->getAttr('label', array('default' => $this->getName()))
        ));
      }
    }
  }

  public function getEditWidgetDefault() {
    if ($this->attrExists('options')) {
      if ($this->attrExists('multiple')) {
        return 'checkboxes';
      } else {
        return 'selectbox';      
      }
    } else {
      return 'textbox';
    }
  }
  
  public function toProg($x) {
    if (\is_null($x)) {
      if ($this->getAttr('nullable', array('default' => true))) {
        return null;
      } else {
        return 0;
      }
    } else {
      if (!\is_numeric($x)) {
        throw new \system\exceptions\ValidationError('Invalid number');
      }
      return \intval($x);
    }
  }
}
