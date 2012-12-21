<?php
class Blocks {
	private static $instance;
	
	private $blocks;
	private $level;
	
	private $filterName;
	private $filterClass;
	
	private function __construct($filterName=null, $filterClass=null) {
		$this->filterName = $filterName;
		$this->filterClass = $filterClass;
	}
	
	public static function getInstance($filterName=null, $filterClass=null) {
		if (!self::$instance) {
			self::$instance = new self($filterName, $filterClass);
		}
		return self::$instance;
	}
	
	public function isOpened() {
		return $this->level > 0;
	}
	
	public function openBlock() {
		$this->level++;
	}
	
	public function closeBlock($name, $classes, $content) {
		$this->level--;
		if (!$this->isOpened()) {
			$print = true;
			if ($this->filterName && $name != $this->filterName) {
				$print = false;
			}
			if ($print) {
				if ($this->filterClass) {
					$classes = is_array($classes) ? $classes : explode(" ", $classes);
					$print = in_array($this->filterClass, $classes);
					
					if ($print) {
						$this->addBlock($content);
					}
				}
			}
		}
	}
	
	public function addBlock($content) {
		$this->blocks[] = $content;
	}
	
	public function getBlocks() {
		return $this->blocks;
	}
}

function smarty_block_filter_blocks($params, $content, &$smarty, &$repeat) {
	if ($repeat) {
		Blocks::getInstance(
			system\Utils::getParam($params, 'name', array('default' => null)),
			system\Utils::getParam($params, 'class', array('default' => null))
		);
	} else {
		// erase content
		$content = '';
		
		$blocks = Blocks::getInstance()->getBlocks();
		
		foreach ($blocks as $block) {
			$content .= $block;
		}
		
		return $content;
	}
}
?>