<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


namespace app\controllers;
use app\models\Wallets;
use app\models\Apps;
use app\models\KYCDocuments;
use app\models\KYCFiles;

use app\models\XGCUsers;
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
    
      // echo "<pre>";
      // print_r($this->request->data);
      // exit();


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
    		'Refer'=>md5($_SERVER["REMOTE_ADDR"]),
        'isdelete' => '1'
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
   
    public function changePin($key = null){
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
          }else{ 
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
          'key'=>$key,'isdelete' =>'0'
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
        return $this->render(array('json' => array('success'=>2,
          'now'=>time(),
          'error'=>'Key does not match',
        )));
      }
    }

    public function updateCurrencyUnit($key = null){
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
          'key'=>$key,'isdelete' =>'0'
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
        return $this->render(array('json' => array('success'=>2,
          'now'=>time(),
          'error'=>'Key Invalid!',
        )));
      }
    }
    
    public function checkEmailNotification($key = null){
      if ($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
        )));
      }
      $record = Apps::find('first',array(
        'conditions' => array(
          'key'=>$key,'isdelete' =>'0'
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
        return $this->render(array('json' => array('success'=>2,
          'now'=>time(),
          'error'=>'Key Invalid!',
        )));
      }         
    }
      
    public function updateEmailNotification($key = null){
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
          'key'=>$key,'isdelete' =>'0'
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
        return $this->render(array('json' => array('success'=>2,
          'now'=>time(),
          'error'=>'Key Invalid!',
        )));
      }
    }
    
    public function checkSmsNotification($key = null){
      if ($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
        )));
      }
      $record = Apps::find('first',array(
        'conditions' => array(
          'key'=>$key,'isdelete' =>'0'
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
        return $this->render(array('json' => array('success'=>2,
          'now'=>time(),
          'error'=>'Key Invalid!',
        )));
      }  
    }
      
    public function updateSmsNotification($key = null){
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
          'key'=>$key,'isdelete' =>'0'
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
        return $this->render(array('json' => array('success'=>2,
          'now'=>time(),
          'error'=>'Key Invalid!',
        )));
      }
    }

    public function changeWalletName($key = null){
        if ($key==null || $key == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }

        if ($this->request->data['walletid']==null || $this->request->data['walletid'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'wallet id missing!'
          )));
        }

        if ($this->request->data['walletName']==null || $this->request->data['walletName'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'wallet name missing!'
          )));
        }

        $record = Apps::find('first',array(
            'conditions' => array(
                'key'=>$key,'isdelete' =>'0'
            )
          )
        );

        if(count($record) > 0){
            $walletid = $this->request->data['walletid'];
            $walletName = $this->request->data['walletName'];
            
             $changeWalletName = false;
             $update = array(); 
             foreach ($record['wallets'] as $wkey => $wallet) {        
                if($wallet['walletid'] == $walletid){
                  $changeWalletName = true;
                  
                  // get first all filed after update
                  foreach ($wallet as $k => $v) {
                      $update[$wkey][$k] = $v;
                  }
                  $update[$wkey]['walletName'] = $walletName;
                }else{
                  // Remaing Record set field
                    foreach ($wallet as $k => $v) {
                      $update[$wkey][$k] = $v;
                    }
                }             
             } 

             // echo "<pre>";
             // print_r($update);
             // exit();
          if($changeWalletName){   
             $data = array('wallets' => $update);
             $conditions = array('key' => $key);
             Apps::update($data, $conditions);

             return $this->render(array('json' => array('success'=>1,
               'now'=>time(),
               'result'=>'Wallet name updated',
               'walletid'=> $walletid
            )));
          }else{
             return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Wallet id does not match',
            )));
          }   


        }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Key Invalid!',
          )));
        }
    }

    public function changeWalletPassword($key = null){
        if ($key==null || $key == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }

        if ($this->request->data['walletid']==null || $this->request->data['walletid'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'wallet id missing!'
          )));
        }

        $record = Apps::find('first',array(
            'conditions' => array(
                'key'=>$key,'isdelete' =>'0'
            )
          )
        );

        if(count($record) > 0){
            $walletid  = $this->request->data['walletid'];
            //$walletName  = $this->request->data['walletName'];
            $password  = $this->request->data['password'];
            $new_password  = $this->request->data['new_password'];
            
             $update = array(); 
             $changeWalletPassword = false;
             // check all wallets record 
             foreach ($record['wallets'] as $wkey => $wallet) {        
                // check wallet given id exits or not
                if($wallet['walletid'] == $walletid){
                     $changeWalletPassword = true;                 
                      // get first all filed after update
                      foreach ($wallet as $k => $v) {
                          $update[$wkey][$k] = $v;
                      }

                      if(!empty($wallet['walletPassword'])){

                          if ($this->request->data['password']==null || $this->request->data['password'] == ""){
                            return $this->render(array('json' => array('success'=>0,
                              'now'=>time(),
                              'error'=>'old password id missing!'
                            )));
                          }

                          if ($this->request->data['new_password']==null || $this->request->data['new_password'] == ""){
                            return $this->render(array('json' => array('success'=>0,
                              'now'=>time(),
                              'error'=>'new password id missing!'
                            )));
                          }

                          // check New and old password not same
                          if ($this->request->data['password'] == $this->request->data['new_password']){
                            return $this->render(array('json' => array('success'=>0,
                              'now'=>time(),
                              'error'=>'New password same as old password!'
                            )));
                          }
                          // check password old 
                          if(password_verify($password, $wallet['walletPassword'])) { 
                              // check New Password Regular exprestion 
                              //$string = "abcD@123";

                              $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$%^&])[A-Za-z0-9@$%^&]{8,}$/";
                              if (preg_match($regex, $new_password)) {
                                   // create Password
                                   $update[$wkey]['walletPassword'] = password_hash($new_password, PASSWORD_BCRYPT);
                              }else{
                                 return $this->render(array('json' => array('success'=>0,
                                    'now'=>time(),
                                    'error'=>'password contains  minimum 8 characters at least 1 Uppercase Alphabet, 1 Lowercase Alphabet, 1 Number and 1 Special Character @ $ % ^ &'
                                  )));
                              }
                          }else{
                            return $this->render(array('json' => array('success'=>0,
                              'now'=>time(),
                              'error'=>'old password wrong!'
                            )));
                          }

                      }else{
                        $update[$wkey]['walletPassword'] = password_hash($new_password, PASSWORD_BCRYPT);
                      }
                }
                else
                {
                    // Remaing Record set field
                    foreach ($wallet as $k => $v) {
                      $update[$wkey][$k] = $v;
                    }
                }  
             } 

            if($changeWalletPassword){
              $data = array('wallets' => $update);
              $conditions = array('key' => $key);
              Apps::update($data, $conditions);

              return $this->render(array('json' => array('success'=>1,
                'now'=>time(),
                'result'=>'Wallet Password updated',
                'walletid'=> $walletid
              )));
            }else{
               return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Wallet id does not match',
              )));
            }  


        }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Key Invalid!',
          )));
        }
    }

    public function checkWalletCurrency($key = null){
      if ($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
        )));
      }

      if ($this->request->data['walletid']==null || $this->request->data['walletid'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'wallet id missing!'
          )));
        }

      $record = Apps::find('first',array(
        'conditions' => array(
          'key'=>$key,'isdelete' =>'0'
          )
        )
      );
    
      $walletCurrency = '';
      if(count($record) > 0){
          $update = array();
          $walletid  = $this->request->data['walletid'];

          foreach ($record['wallets'] as $wkey => $wallet) {        
            // get first all filed after update
            foreach ($wallet as $k => $v) {
                $update[$wkey][$k] = $v;
            }  

            if($wallet['walletid'] == $walletid){
                if($wallet['walletCurrency'] == null){
                    $walletCurrency = 'XGC';
                    $update[$wkey]['walletCurrency'] = $walletCurrency; 
                }else{
                  $walletCurrency = $wallet['walletCurrency'];
                } 
            }  
          }

          if(!empty($walletCurrency))
          {
             $data = array('wallets' => $update);
             $conditions = array('key' => $key);
             Apps::update($data, $conditions); 

             return $this->render(array('json' => array('success'=>1,
                'now'=>time(),
                'result'=>'Wallet currency',
                'walletid '=> $walletid,
                'walletCurrency'=> $walletCurrency
              )));
          }else{
               return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Wallet id does not match',
              )));
          } 


      }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Key Invalid!',
          )));
      }
    }

    public function updateWalletCurrency($key = null){
      if ($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
        )));
      }

      if ($this->request->data['walletid']==null || $this->request->data['walletid'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'wallet id missing!'
          )));
      }

      if ($this->request->data['walletCurrency']==null || $this->request->data['walletCurrency'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'wallet currency missing!'
          )));
      }  

      $record = Apps::find('first',array(
        'conditions' => array(
          'key'=>$key,'isdelete' =>'0'
          )
        )
      );
    
      $updateCurrency = false;
      if(count($record) > 0){
          $update = array();
          $walletid  = $this->request->data['walletid'];
          $walletCurrency  = $this->request->data['walletCurrency'];

          foreach ($record['wallets'] as $wkey => $wallet) {        
            // get first all filed after update
            foreach ($wallet as $k => $v) {
                $update[$wkey][$k] = $v;
            }  

            if($wallet['walletid'] == $walletid){
                $updateCurrency = true;
                $update[$wkey]['walletCurrency'] = $walletCurrency;  
            }  
          }

          if($updateCurrency)
          {
             $data = array('wallets' => $update);
             $conditions = array('key' => $key);
             Apps::update($data, $conditions); 

             return $this->render(array('json' => array('success'=>1,
                'now'=>time(),
                'result'=>'Wallet update currency',
                'walletid'=> $walletid,
                'walletCurrency'=> $walletCurrency
              )));
          }else{
               return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Wallet id does not match',
              )));
          } 


      }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Key Invalid!',
          )));
      }
    }

    public function getprofile($key = null){
      extract($this->request->data);
      if($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
        )));
      }

      if($kyc_id==null || $kyc_id == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Kyc id missing!'
        )));
      }

      $record = Apps::find('first',array(
        'conditions' => array(
          'key'=>$key,'isdelete' =>'0'
          )
        )
      );
    
      if(count($record) > 0){
          $document = KYCDocuments::find('first',array(
            'conditions' => array(
              'hash'=>$record['hash'],
              'kyc_id'=>$kyc_id
              )
            )
          );
        
          if(count($document) > 0){

              $setting = [];
              $setting['unit'] = $record['unit'];
              $setting['sms_notification'] = $record['sms_notification'];
              $setting['email_notification'] = $record['email_notification'];



             $path = $this->getImage($record['hash'],'profile_img'); 
            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Profile getting',
              'kyc_id' => $document['kyc_id'],
              'email' => $record['email'],
              'name' => $document['details']['Name'],
              'phone' => $record['phone'],
              'country_code' => $record['country_code'],
              'address' => $document['details']['Address'],
              'profile' => $path,
              'setting' => $setting,
            ))); 
          }else{
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Invalid kyc id!',
            )));
          }  
      }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Key Invalid!',
          )));
      }
    }

    public function logout($key = null){
      if ($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
        )));
      }

      $record = Apps::find('first',array(
        'conditions' => array(
          'key'=>$key,'isdelete' =>'0'
          )
        )
      );
    
      if(count($record) > 0){
        return $this->render(array('json' => array('success'=>1,
          'now'=>time(),
          'result'=>'Logout success'
        ))); 
      }else{
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Key Invalid!',
        )));
      }
    }

    /* Common Function */
    private function getImage($id=null,$type=null){
      $document = KYCDocuments::find('first',array(
          'conditions'=>array(
              'hash'=>$id,
              )
      ));  

       $image_name = KYCFiles::find('first',array(
          'conditions'=>array('details_'.$type.'_id'=>(string)$document['_id'])
      ));


      if($image_name['filename']!=""){
          $image_name_name = $image_name['_id'].'_'.$image_name['filename'];
          $path = LITHIUM_APP_PATH . '/webroot/documents/'.$image_name_name;
         // $return_path = 'http://hitarth:8888/documents/'.$image_name_name;
          $return_path = 'http://192.168.10.131:8888/hitarth.org/documents/'.$image_name_name;
          file_put_contents($path, $image_name->file->getBytes());
          return $return_path;
      }
    }

    /* Friend Address List */

    public function insertaddress($key = null){
      extract($this->request->data); 
      if ($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
       )));
      }

      if ($email==null || $email == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'email missing!'
       )));
      }

      if ($walletname==null || $walletname == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Wallet name missing!'
       )));
      }

      $record = Apps::find('first',array(
          'conditions' => array(
              'key'=>$key,
              'isdelete' => '0',
          )
        )
      );
       
      if(count($record) > 0){

        $XGCUsers = XGCUsers::find('first',array(
            'conditions' => array(
                'email'=>$email,
            ),
            'order' => array(
                '_id' => 'DESC'
            )    
          )
        );  

        if(count($XGCUsers)){
            if($XGCUsers['hash'] != $record['hash']){
                
                foreach ($record['contacts'] as $contact) {
                  if($contact['walletid'] == $XGCUsers['walletid']){
                      return $this->render(array('json' => array('success'=>1,
                        'now'=>time(),
                        'error'=>'Address already exists!'
                      )));
                  }
                }  

                $record = $record->to('array');
                $contactsAry = count($record['contacts']) > 0 ? $record['contacts'] : array();
                $newContact = array(
                    'walletname' => $walletname,
                    'walletid' => $XGCUsers['walletid']
                  );
                array_push($contactsAry,$newContact);

                $data['contacts'] = $contactsAry;
                $conditions = array('key' => $key);
                Apps::update($data, $conditions); 
            
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Add Address successfully'
                )));

            }else{
              return $this->render(array('json' => array('success'=>1,
                'now'=>time(),
                'error'=>'Wallet not found!'
              )));  
            }
        }else{
          return $this->render(array('json' => array('success'=>1,
            'now'=>time(),
            'error'=>'Wallet not found!'
          ))); 
        }
      }else{
        return $this->render(array('json' => array('success'=>2,
          'now'=>time(),
          'error'=>'Invalid Key!'
        )));     
      }
    }

    public function addresslist($key = null){
      extract($this->request->data); 
      if ($key==null || $key == ""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
       )));
      }

      $record = Apps::find('first',array(
          'conditions' => array(
              'key'=>$key,
              'isdelete' => '0',
          )
        )
      );
       
      if(count($record) > 0){
        $cont = [];
        $record = $record->to('array');  
        foreach ($record['contacts'] as $k => $contact) {  
          
          $XGCUsers = XGCUsers::find('first',array(
            'conditions' => array(
                'walletid'=>$contact['walletid'],
              )    
            )
          );

          if(count($XGCUsers) > 0){

            $friend = Apps::find('first',array(
                'conditions' => array(
                    'hash'=>$XGCUsers['hash'],
                    'isdelete' => '0',
                )
              )
            );
            $path = $this->getImage($friend['hash'],'profile_img');  
            
            $cont[$k]['name'] = $contact['walletname'];
            $cont[$k]['greencoinAddress'] = $XGCUsers['greencoinAddress'][0];
            $cont[$k]['email'] = $XGCUsers['email']; 
            $cont[$k]['phone'] = $XGCUsers['phone']; 
            $cont[$k]['country_code'] = $XGCUsers['country_code'];
            $cont[$k]['profile'] = $path; 
          }
        }  
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'success',
          'contacts' => $cont
        )));
      }else{
        return $this->render(array('json' => array('success'=>2,
          'now'=>time(),
          'error'=>'Invalid Key!'
        )));     
      }
    }
    public function uploadProfileThumb($walletID){
     $directory = substr($walletID,0,1)."/".substr($walletID,1,1)."/".substr($walletID,2,1)."/".substr($walletID,3,1);
     $file = $walletID .".jpg";
     
     $ftp_host = FTP_HOST; /* host */
     $ftp_user_name = FTP_USER; /* username */
     $ftp_user_pass = FTP_PASS; /* password */

     $local_file = LITHIUM_APP_PATH.   "/webroot". FTP_LOCAL_PATH. $file;     
     $remote_file = "/profiles/" . $directory ."/".$file; 
     
     $connect_it = ftp_connect( $ftp_host );
// print_r($local_file);
// print_r($remote_file);
 
/* Login to FTP */
     $login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
 
/* Send $local_file to FTP */
     if ( ftp_put( $connect_it, $remote_file, $local_file, FTP_BINARY ) ) {
      return $this->render(array('json' => array('success'=>1,
          'now'=>time(),
          'directory'=>$directory."/".$file
        )));
     } else {
      return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'directory'=>$directory."/".$file
        )));
     }

     
     
     
     
     
    }
}
?>