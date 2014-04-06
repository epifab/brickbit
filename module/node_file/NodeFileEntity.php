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
    $urn = 'content/' . $recordset->node_id . '/file/' . $recordset->node_index . '/' . $recordset->virtual_name;
    return Main::getPathRelative($urn);
  }
  
  /**
   * Node edit URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Edit URL
   */
  public static function getEditUrl(RecordsetInterface $recordset) {
    $urn = 'content/' . $recordset->node_id . '/file/' . $recordset->node_index . '/' . $recordset->file_id . '/update';
    return Main::getPathRelative($urn);
  }
  
  /**
   * Node delete URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Delete URL
   */
  public static function getDeleteUrl(RecordsetInterface $recordset) {
    $urn = 'content/' . $recordset->node_id . '/file/' . $recordset->node_index . '/' . $recordset->file_id . '/delete';
    return Main::getPathRelative($urn);
  }
  
  public static function getImages(RecordsetInterface $recordset) {
    $imgVersions = NodeFileApi::imageVersionHandlers();
    $versions = array();
    foreach ($imgVersions as $version => $handler) {
      $versions[$version] = Main::getPathRelative("content/{$recordset->node_id}/file/{$recordset->node_index}/{$version}/{$recordset->virtual_name}");
    }
    return $versions;
  }
}