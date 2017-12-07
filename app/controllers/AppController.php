<?php
namespace app\controllers;
use app\models\Wallets;
use app\models\Apps;

use app\models\Countries;
use app\models\Templates;
use MongoID;	
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\Coingreen;

class AppController extends \lithium\action\Controller {
	public function index($ip = null){
  if($ip == null || $ip == ""){
   return $this->render(array('json' => array('success'=>0,
   'now'=>time(),
   'error'=>'IP missing!'
		)));
  }
	}
	public function initialize($ip = null,$device_type=null,$os_version=null,$device_name=null){
  
   if($this->request->data){
    if($this->request->data['ip']==null || $this->request->data['ip']==""){
     return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'IP missing!'
     )));
    }
    if($this->request->data['device_type']==null || $this->request->data['device_type']==""){
     return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Device Type missing!'
     )));
    }
    if($this->request->data['device_name']==null || $this->request->data['device_name']==""){
     return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Device Name missing!'
     )));
    }
    if($this->request->data['os_version']==null || $this->request->data['os_version']==""){
     return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'OS Version missing!'
     )));
    }    
    $key = md5(time());
    $data = array(
    'device_type'=>$this->request->data['device_type'],
    'os_version'=>$this->request->data['os_version'],
    'device_name'=>$this->request->data['device_name'],
    'DateTime' => new \MongoDate(),
    'IP' => $this->request->data['ip'],
    'key' => $key,
				'Server'=>md5($_SERVER["SERVER_ADDR"]),
				'Refer'=>md5($_SERVER["REMOTE_ADDR"])
    );
    $App = Apps::create()->save($data);
    return $this->render(array('json' => array('success'=>1,
     'now'=>time(),
     'key'=>$key,
     'ip'=>$this->request->data['ip'],
     'Server'=>md5($_SERVER["SERVER_ADDR"]),
     'Refer'=>md5($_SERVER["REMOTE_ADDR"])
    )));
   }else{
    return $this->render(array('json' => array('success'=>0,
    'now'=>time(),
    'error'=>'IP missing!'
   )));
  }
 }
 
 public function setpin($key=null){
  if ($key==null || $key == ""){
    return $this->render(array('json' => array('success'=>0,
    'now'=>time(),
    'error'=>'Key missing!'
   )));
  }
  
  if($this->request->data){
    if($this->request->data['pin']==null || $this->request->data['pin']==""){
     return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Pin missing!'
     )));
    }
   
   $data = array(
     'pin' => $this->request->data['pin']
     );
   $conditions = array(
     'key' => $key 
   );
   
   Apps::update($data,$conditions);
    return $this->render(array('json' => array('success'=>1,
     'now'=>time(),
     'key'=>$key,
     'Server'=>md5($_SERVER["SERVER_ADDR"]),
     'Refer'=>md5($_SERVER["REMOTE_ADDR"])
    )));
  }else{
    return $this->render(array('json' => array('success'=>0,
    'now'=>time(),
    'error'=>'Pin missing!'
   )));
  }
 }
 
 public function checkpin($key=null){
  if ($key==null || $key == ""){
    return $this->render(array('json' => array('success'=>0,
    'now'=>time(),
    'error'=>'Key missing!'
   )));
  }
   $conditions = array(
     'key' => $key 
   );
   
   $record = Apps::find('first',array(
    'conditions'=>$conditions
   ));
   if(count($record)!=0){
    return $this->render(array('json' => array('success'=>1,
     'now'=>time(),
     'key'=>$key,
     'pin'=>$record['pin'],
     'Server'=>md5($_SERVER["SERVER_ADDR"]),
     'Refer'=>md5($_SERVER["REMOTE_ADDR"])
    )));
   }else{
    return $this->render(array('json' => array('success'=>0,
    'now'=>time(),
    'error'=>'Invalid Key!'
   )));    
   }
 }
 
}
?>