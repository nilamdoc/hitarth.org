<?php
namespace app\models;
class XGCMarkets extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_xgc',
      'source'=>'markets'
    );
}
?>