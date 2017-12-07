<?php
namespace app\models;
class KYCFiles extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_kyc',
      'source'=>'fs.files'
    );
}
?>