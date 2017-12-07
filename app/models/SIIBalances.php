<?php
namespace app\models;
class SIIBalances extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_sii',
      'source'=>'balances'
    );
}
?>