<?php
namespace app\models;
class SIICountries extends \lithium\data\Model {
    protected $_meta = array(
      'connection' => 'default_sii',
      'source'=>'countries'
    );
}
?>