<?php
namespace app\models;
class WinUsers extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_win',
      'source'=>'users'
    );
}
?>