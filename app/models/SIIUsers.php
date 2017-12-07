<?php
namespace app\models;
class SIIUsers extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_sii',
      'source'=>'users'
    );
}
?>