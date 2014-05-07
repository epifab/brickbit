<?php
namespace module\node_file;

use system\Main;
use system\model2\RecordsetInterface;
use system\utils\File;

class NodeFileTableApi {
  /**
   * Node URL
   * @param RecordsetInterface $recordset Node file recordset
   * @return string URL
   */
  public static function getUrl(RecordsetInterface $recordset) {
    $urn = 'content/' . $recordset->node_id . '/file/' . $recordset->node_index . '/' . $recordset->virtual_name;
    return Main::getPathVirtual($urn);
  }

  /**
   * Node edit URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Edit URL
   */
  public static function getEditUrl(RecordsetInterface $recordset) {
    $urn = 'content/' . $recordset->node_id . '/file/' . $recordset->node_index . '/' . $recordset->file_id . '/update';
    return Main::getPathVirtual($urn);
  }

  /**
   * Node delete URL
   * @param RecordsetInterface $recordset Node recordset
   * @return string Delete URL
   */
  public static function getDeleteUrl(RecordsetInterface $recordset) {
    $urn = 'content/' . $recordset->node_id . '/file/' . $recordset->node_index . '/' . $recordset->file_id . '/delete';
    return Main::getPathVirtual($urn);
  }

  public static function isImage(RecordsetInterface $nodeFile) {
    return File::isImage($nodeFile->virtual_name);
  }

  public static function getImageUrls(RecordsetInterface $recordset) {
    if (File::isImage($recordset->virtual_name)) {
      $imgVersions = NodeFileApi::imageVersionHandlers();
      $versions = array();
      foreach ($imgVersions as $version => $handler) {
        $versions[$version] = Main::getPathVirtual("content/{$recordset->node_id}/file/{$recordset->node_index}/{$version}/{$recordset->virtual_name}");
      }
      return $versions;
    }
    else {
      return array();
    }
  }
}