<?php
namespace module\node_file;

use system\Main;
use system\model2\RecordsetInterface;

class NodeFileEntity {
  /**
   * Node URL
   * @param RecordsetInterface $recordset Node file recordset
   * @return string URL
   */
  public static function getUrl(RecordsetInterface $recordset) {
    $urn = empty($recordset->node_id)
      ? 'content/file/' . $recordset->file_id . '.' . $recordset->file->extension
      : 'content/' . $recordset->node_id . '/file/' . $recordset->node_index . '/' . $recordset->virtual_name;
    return Main::getUrl($urn);
  }
  
  /**
   * Node edit URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Edit URL
   */
  public static function getEditUrl(RecordsetInterface $recordset) {
    return Main::getUrl('content/file/' . $recordset->file_id . '/edit');
  }
  
  /**
   * Node delete URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Delete URL
   */
  public static function getDeleteUrl(RecordsetInterface $recordset) {
    return Main::getUrl('content/file/' . $recordset->file_id . '/delete');
  }
  
  public static function getImages(RecordsetInterface $recordset) {
    $imgVersions = \array_keys(\system\Main::invokeStaticMethodAllMerge('imageVersionMakers'));
    $versions = array();
    foreach ($imgVersions as $version) {
      $versions[$version] = 'content/' . $recordset->node_id . '/img-' . $version . '/' . $recordset->node_index . '/' . $recordset->virtual_name;
    }
  }
}