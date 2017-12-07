<?php
namespace app\models;
class KYCDocuments extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_kyc',
      'source'=>'documents'
    );
}
?>