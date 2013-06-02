<?php
namespace module\core\components;

class NodeImage extends \system\logic\Component {
  
	public function runGetVersion() {
    
		list($nodeId, $version, $nodeIndex, $virtualName, $extension) = $this->getUrlArgs();
    
    $nodeFileVersions = \system\Main::moduleConfigArray('nodeFileVersions');
    if (!\array_key_exists($version, $nodeFileVersions)) {
      throw new \system\error\InputOutputError('Invalid image version.');
    } else {
      $versionHandler = $nodeFileVersions[$version];
      if (!\is_callable($versionHandler)) {
        throw new \system\error\InputOutputError('Invalid image version handler.');
      }
    }
    
    $rsb = new \system\model\RecordsetBuilder('node_file');
    $rsb->using('*');
    $rsb->addFilter(new \system\model\FilterClause($rb->node_id, '=', $nodeId));
    $rsb->addFilter(new \system\model\FilterClause($rb->node_index, '=', $nodeIndex));
    $rsb->addFilter(new \system\model\FilterClause($rb->virtual_name, '=', $virtualName));
    
    $rs = $rsb->selectFirst();
    
    if (!$rs) {
      // return default image?
    }
    else {
      if (\file_exists(\config\settings()->BASE_DIR . 'temp/images/' . $version . '/' . $rs->id . '.' . $extension)) {
        // read the cache
      } else {
        // create new version
        \system\File::uploadImageFixedSize($inputName, $destinationPath, $fixedWidth, $fixedHeight);
      }
    }
	}
}
?>