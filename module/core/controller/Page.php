<?php
namespace module\core\controller;
use \system\Main;

class Test {
  function x(Page $p1, \module\core\controller\Page $p2 = null, $asd = null) {
  }
}

class Page extends Node {
  /**
   * @throws \system\exceptions\PageNotFound
   */
  public function runNotFound() {
    throw new \system\exceptions\PageNotFound();
  }
  
  public function runHome() {
    $this->setMainTemplate('home');
    $this->setPageTitle('Home');

//    $x = new \system\utils\ArrayWrapper(array('foo' => array('bar' => array('baz' => 123))));
//    Main::pushMessage($x->search('foo.bar.baz'));
//    Main::pushMessage($x->search('foo.bar', array('default' => 'HELLO', 'prefix' => 'PRE', 'suffix' => 'SUF')));
//    
//    Main::pushMessage(Main::settings()->toArray());
    
    $node = \system\model2\Table::loadTable('node');
    $node->import(
      '*',
      'text.*',
      'record_mode.*',
      'texts.*'
    );
    
//    $nodeClass = new \ReflectionClass('module\core\controller\Test');
//    $m = $nodeClass->getMethod('x');
//    foreach ($m->getParamseters() as $p) {
//      Main::pushMessage($p->isOptional() ? 'Is optional' : 'Non optional');
//      Main::pushMessage($p->getClass());
//    }
    
    $node->addFilters(
      $node->filterGroup('OR')->addClauses(
        $node->filter('ldel', 10, '>')
      )
    );
    $n = $node->selectFirst();
    Main::pushMessage($n->toArray());
    $n->texts;
    Main::pushMessage($n->toArray());
    Main::pushMessage('Execution time: ' . Main::getExecutionTime(true) . ' sec.');
    
    $t = $n->text;
    $t->description = \rand(0, 123);
    $t->save();
    
    return \system\RESPONSE_TYPE_READ;
  }
}