<?php
namespace app\models;
class WinCountries extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_win',
      'source'=>'countries'
    );
}
?>