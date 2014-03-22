<?php
namespace module\core\controller;

use system\model2\Table;

class NodeImage extends \system\Component {
  
  public function runGetVersion() {
    
    list($nodeId, $version, $nodeIndex, $virtualName, $extension) = $this->getUrlArgs();
    
    $nodeFileVersions = \system\Main::moduleConfigArray('nodeFileVersions');
    if (!\array_key_exists($version, $nodeFileVersions)) {
      throw new \system\exceptions\InputOutputError('Invalid image version.');
    } else {
      $versionHandler = $nodeFileVersions[$version];
      if (!\is_callable($versionHandler)) {
        throw new \system\exceptions\InputOutputError('Invalid image version handler.');
      }
    }
    
    $rsb = Table::loadTable('node_file');
    $rsb->import('*');
    $rsb->addFilters(
      $rsb->filter('node_id', $nodeId),
      $rsb->filter('node_index', $nodeIndex),
      $rsb->filter('virtual_name', $virtualName)
    );
    $rs = $rsb->selectFirst();
    
    if (!$rs) {
      // return default image?
    }
    else {
      if (\file_exists(\system\Main::getBaseDir() . 'temp/images/' . $version . '/' . $rs->id . '.' . $extension)) {
        // read the cache
      } else {
        // create new version
        \system\utils\File::uploadImageFixedSize($inputName, $destinationPath, $fixedWidth, $fixedHeight);
      }
    }
  }
}
