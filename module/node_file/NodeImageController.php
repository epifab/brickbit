<?php
namespace module\node_file;

use system\Component;
use system\Main;
use system\model2\Table;
use system\utils\File;
use system\exceptions\InputOutputError;

class NodeImageController extends Component {
  
  public function runGetVersion() {
    
    list($nodeId, $version, $nodeIndex, $virtualName, $extension) = $this->getUrlArgs();
    
    $nodeFileVersions = Main::moduleConfigArray('nodeFileVersions');
    if (!\array_key_exists($version, $nodeFileVersions)) {
      throw new InputOutputError('Invalid image version.');
    } else {
      $versionHandler = $nodeFileVersions[$version];
      if (!\is_callable($versionHandler)) {
        throw new InputOutputError('Invalid image version handler.');
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
      if (\file_exists(Main::getTempPath('images/' . $version . '/' . $rs->id . '.' . $extension))) {
        // read the cache
      } else {
        // create new version
        File::uploadImageFixedSize($inputName, $destinationPath, $fixedWidth, $fixedHeight);
      }
    }
  }
}
