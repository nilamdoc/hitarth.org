<?php
namespace app\models;
class KYCCountries extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_kyc',
      'source'=>'countries'
    );
}
?>