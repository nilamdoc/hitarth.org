<?php
namespace app\models;
use lithium\util\Validator;
use lithium\util\String;

class Users extends \lithium\data\Model {

	protected $_schema = array(
		'_id'	=>	array('type' => 'id'),
		'firstname'	=>	array('type' => 'string', 'null' => false),
		'lastname'	=>	array('type' => 'string', 'null' => false),
		'email'	=>	array('type' => 'string', 'null' => false),		
		'updated'	=>	array('type' => 'datetime', 'null' => false),
		'created'	=>	array('type' => 'datetime', 'null' => false),
		'ip'	=>	array('type' => 'string', 'null' => true),		
	);

	protected $_meta = array(
		'key' => '_id',
		'locked' => true
	);

	public $validates = array(
		'email' => array(
			array('uniqueEmail', 'message' => 'This Email is already used'),
			array('notEmpty', 'message' => 'Please enter your email address'),			
			array('email', 'message' => 'Not a valid email address'),						
		)
		);
}

	Validator::add('uniqueEmail', function($value, $rule, $options) {
		$conflicts = Users::count(array('email' => $value));
		if($conflicts) return false;
		return true;
	});

	
	Users::applyFilter('save', function($self, $params, $chain) {
		if ($params['data']) {
			$params['entity']->set($params['data']);
			$params['data'] = array();
		}
		if (!$params['entity']->exists()) {
			$params['entity']->created = new \MongoDate();
			$params['entity']->updated = new \MongoDate();
			$params['entity']->ip = $_SERVER['REMOTE_ADDR'];
		}
		return $chain->next($self, $params, $chain);
	});
?>