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
    if($this->request->data['vendor_uuid']==null || $this->request->data['vendor_uuid']==""){
     return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Vendor uuid missing!'
     )));
    }    
    $key = md5(time());
    $data = array(
    'device_type'=>$this->request->data['device_type'],
    'os_version'=>$this->request->data['os_version'],
    'device_name'=>$this->request->data['device_name'],
    'vendor_uuid'=>$this->request->data['vendor_uuid'],
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
 
 public function changePin($key = null)
 {
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
      if($this->request->data['new_pin']==null || $this->request->data['new_pin']==""){
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'New Pin missing!'
      )));
      }

      if(strlen($this->request->data['new_pin']) != 4){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'New pin must be 4 number!'
        )));
        }

      if($this->request->data['pin'] == $this->request->data['new_pin']){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'New Pin same as current pin'
        )));
        }
      
        $record = Apps::find('first',array(
          'conditions' => array(
            'key'=>$key,
            'pin'=>$this->request->data['pin'] )
        )
      );
      if(count($record) > 0){
        $data = array('pin' => $this->request->data['new_pin']);
        $conditions =  array(
          'key'=>$key,
          'pin'=>$this->request->data['pin'] 
        );
        Apps::update($data, $conditions);
        return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'result'=>'New Pin updated',
        'pin'=> $this->request->data['new_pin']
      )));
    }
    else{ 
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Old Pin does not match',
      )));
    }
    }
  }

  public function checkCurrencyUnit($key = null){

    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
      )));
    }
    $record = Apps::find('first',array(
      'conditions' => array(
        'key'=>$key
        )
      )
    );
    if(count($record) > 0){
      if($record['unit'] == null){
        $unit = 'One';
        $data = array('unit' => 'One');
        $conditions = array('key' => $key);
        Apps::update($data, $conditions);
      }else{
        $unit = $record['unit'];        
      }
      return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'result'=>'Current unit',
        'unit'=> $unit
      )));
    }else{
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key does not match',
      )));
    }
  }
  public function updateCurrencyUnit($key = null)
  {
    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
      )));
    }

    if ($this->request->data['unit']==null || $this->request->data['unit'] == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'unit missing!'
      )));
    }


    $record = Apps::find('first',array(
      'conditions' => array(
        'key'=>$key
        )
      )
    );
    if(count($record) > 0){
        $unit = $this->request->data['unit'];
        $data = array('unit' => $unit);
        $conditions = array('key' => $key);
        Apps::update($data, $conditions);
      return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'result'=>'Update unit',
        'unit'=> $unit
      )));
    }else{
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key does not match',
      )));
    }
  }
  
  public function checkEmailNotification($key = null)
  {
    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
      )));
    }
    $record = Apps::find('first',array(
      'conditions' => array(
        'key'=>$key
        )
      )
    );
    if(count($record) > 0){
      if($record['email_notification'] == null){
        $email_notification = 0;
        $data = array('email_notification' => 0);
        $conditions = array('key' => $key);
        Apps::update($data, $conditions);
      }else{
        $email_notification = $record['email_notification'];        
      }
      return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'result'=>'Email Notification',
        'email_notification'=> $email_notification
      )));
    }else{
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key does not match',
      )));
    }         
  }
    
  public function updateEmailNotification($key = null)
  {
    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
      )));
    }

    if ($this->request->data['email_notification']==null || $this->request->data['email_notification'] == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'email notification status missing!'
      )));
    }

    $record = Apps::find('first',array(
      'conditions' => array(
        'key'=>$key
        )
      )
    );
    if(count($record) > 0){
        $email_notification = $this->request->data['email_notification'];
        $data = array('email_notification' => $email_notification);
        $conditions = array('key' => $key);
        Apps::update($data, $conditions);
      
      return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'result'=>'Email notification Updated',
        'email_notification'=> $email_notification
      )));
    }else{
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key does not match',
      )));
    }
  }
  
  public function checkSmsNotification($key = null)
  {
    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
      )));
    }
    $record = Apps::find('first',array(
      'conditions' => array(
        'key'=>$key
        )
      )
    );
    if(count($record) > 0){
      if($record['sms_notification'] == null){
        $sms_notification = 0;
        $data = array('sms_notification' => 0);
        $conditions = array('key' => $key);
        Apps::update($data, $conditions);
      }else{
        $sms_notification = $record['sms_notification'];        
      }
      return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'result'=>'Sms notification',
        'sms_notification'=> $sms_notification
      )));
    }else{
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key does not match',
      )));
    }  
  }
    
  public function updateSmsNotification($key = null)
  {
    if ($key==null || $key == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'Key missing!'
      )));
    }

    if ($this->request->data['sms_notification']==null || $this->request->data['sms_notification'] == ""){
      return $this->render(array('json' => array('success'=>0,
      'now'=>time(),
      'error'=>'sms notification status missing!'
      )));
    }

    $record = Apps::find('first',array(
      'conditions' => array(
        'key'=>$key
        )
      )
    );
    if(count($record) > 0){
        $sms_notification = $this->request->data['sms_notification'];
        $data = array('sms_notification' => $sms_notification);
        $conditions = array('key' => $key);
        Apps::update($data, $conditions);
      
      return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'result'=>'Sms notification Updated',
        'sms_notification'=> $sms_notification
      )));
    }else{
      return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key does not match',
      )));
    }
  }


}
?>