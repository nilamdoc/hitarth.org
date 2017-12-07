<?php
namespace app\models;
class KYCQuestions extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_kyc',
      'source'=>'questions'
    );
}
?>