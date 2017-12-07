<?php
namespace app\models;
class KYCDetails extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_kyc',
      'sources'=>'details'
    );
}
?>