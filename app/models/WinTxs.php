<?php
namespace app\models;
class WinTxs extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_win',
      'source'=>'txs'
    );
}
?>