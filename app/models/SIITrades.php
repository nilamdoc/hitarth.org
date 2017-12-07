<?php
namespace app\models;
class SIITrades extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_sii',
      'source'=>'trades'
    );
}
?>