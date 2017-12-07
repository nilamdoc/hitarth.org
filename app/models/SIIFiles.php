<?php
namespace app\models;
class SIIFiles extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_sii',
      'source'=>'fs.files'
    );
}
?>