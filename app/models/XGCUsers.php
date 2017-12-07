<?php
namespace app\models;
class XGCUsers extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_xgc',
      'source'=>'users'
    );
}
?>