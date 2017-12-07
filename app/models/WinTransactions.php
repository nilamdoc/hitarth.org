<?php
namespace app\models;
class WinTransactions extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_win',
      'source'=>'transactions'
    );
}
?>