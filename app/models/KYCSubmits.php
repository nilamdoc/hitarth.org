<?php
namespace app\models;
class KYCSubmits extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_kyc',
      'source'=>'submits'
    );
}
?>