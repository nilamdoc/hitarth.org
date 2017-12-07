<?php
namespace app\models;
class SIITransactions extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_sii',
      'source'=>'transactions'
    );
}
?>