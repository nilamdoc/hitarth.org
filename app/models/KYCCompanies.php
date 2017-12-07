<?php
namespace app\models;
class KYCCompanies extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_kyc',
      'source'=>'companies'
    );
}
?>