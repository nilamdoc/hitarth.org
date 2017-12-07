<?php
namespace app\models;
class SIIOrders extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_sii',
      'source'=>'orders'
    );
}
?>