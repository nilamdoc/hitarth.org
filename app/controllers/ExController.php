<?php
namespace app\controllers;

use app\extensions\action\Uuid;
use app\models\Apps;
use app\models\XGCDetails;
use app\models\XGCUsers;
use MongoID;  
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\Coingreen;

class ExController extends \lithium\action\Controller {
      
      public function index(){
         echo "Wrong";
         exit();
      }

      public function verifyemail($key = null){
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }
          
        if($this->request->data['email']==null || $this->request->data['email']==""){
            return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email missing!'
          )));
        }    
        
          $uuid = new Uuid();
          $kyc_id = $uuid->v4v();
          $email_code = substr($kyc_id,0,4);
        
          ////////////////////////////////////////Send Email
            // $emaildata = array(
            //  'kyc_id'=>$email_code,
            //  'email'=>$this->request->data['email']
            // );
            // $function = new Functions();
            // $compact = array('data'=>$emaildata);
            // $from = array(NOREPLY => "noreply@".COMPANY_URL);
            // $email = $this->request->data['email'];
            // $function->sendEmailTo($email,$compact,'process','walletEmailVarify',"Wallet - Email Code",$from,'','','',null);
          //////////////////////////////////////////////////////////////////////
               
         return $this->render(array('json' => array('success'=>1,
          'email_code'=>$email_code,
          'email' => $this->request->data['email']
         )));
      }

      public function sendmobilecode($key=null){
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));  
        }else{
            
            if($this->request->data['mobile']==null || $this->request->data['mobile']=="") {
              return $this->render(array('json' => array('success'=>0,
               'now'=>time(),
               'error'=>'Mobile number required!'
              )));
            }

            if($this->request->data['country_code']==null || $this->request->data['country_code']=="") {
              return $this->render(array('json' => array('success'=>0,
               'now'=>time(),
               'error'=>'Country code required!'
              )));
            }
            extract($this->request->data);
            $ga = new GoogleAuthenticator();
            $secret = $ga->createSecret(64);
            $signinCode = $ga->getCode($secret);  
            $function = new Functions();
            $phone = $this->request->data['mobile'];
            if(substr($phone,0,1)=='+'){
              $phone = str_replace("+","",$phone);
            }
          
            $msg = 'Please enter GreenCoinX mobile verification code: '.$signinCode.'.';
            $returnvalues = $function->twilio($phone,$msg,$signinCode);  // Testing if it works 
            
            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'phone_code'=>$signinCode,
              'phone'=>$phone,
              'country_code' => $country_code
            )));
        }
      }

      public function createwallet($key = null){
        extract($this->request->data); 
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Key missing!'
          )));
        }    

        if($this->request->data['name'] ==null || $this->request->data['name']==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Wallet name required!'
          )));
        }

        if($this->request->data['email'] ==null || $this->request->data['email']==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email required!'
          )));
        }  

        if($this->request->data['phone'] ==null || $this->request->data['phone']==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Phone number required!'
          )));
        }

        if($this->request->data['country_code'] ==null || $this->request->data['country_code']==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'country code required!'
          )));
        }    

        if($this->request->data['code'] ==null || $this->request->data['code'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Code required!'
          )));
        }  

        if($this->request->data['walletid'] ==null || $this->request->data['walletid'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Wallet id required!'
          )));
        }  

        if($this->request->data['password'] ==null || $this->request->data['password'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'password required!'
          )));
        }

        if(strlen($password) < 8){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'password length must be 8 charecter!'
          )));
        }

        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$%^&]).*$/";
        if (!preg_match($regex, $password)) {
           return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'password contains at least one char, numeric or special like @ $ % ^ &'
            )));
        }    

        if($this->request->data['kyc_id'] ==null || $this->request->data['kyc_id'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'kyc id required!'
          )));
        }      
        
          extract($this->request->data);   
          $password = password_hash($password, PASSWORD_BCRYPT);

          $record = Apps::find('first',array('conditions' => array('key'=>$key)))->to('array');    
          if(count($record) > 0){

            $checkId = Apps::find('first',array('conditions' => array('key'=>$key, 'wallets.walletid' => $walletid)));
            if(count($checkId)!=0){
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Wallet Already exits!'
                )));
            }


            $walletAry = count($record['wallets']) > 0 ? $record['wallets'] : array();
            $newWallet = array(
                'walletid' => $walletid,
                'walletName' => $name,
                'walletPassword' => $password,
                'walletCurrency' => 'XGC'
              );
            array_push($walletAry,$newWallet);

            $data['wallets'] = $walletAry;
            $conditions = array('key' => $key);
            Apps::update($data, $conditions);

            $uuid = new Uuid();     
            $xemail = $uuid->hashme($email);
            $xphone = $uuid->hashme($phone);
            $xcode = $uuid->hashme($code);
            $xwalletid = $uuid->hashme($walletid);
            $ga = new GoogleAuthenticator();
            $secret = $ga->createSecret(64);
            $data = array(
              'walletid'=>$walletid,
              'kyc'=>$kyc_id,
              'secret'=>$secret,
              'password.send.email'=>false
            );
          
            $Details = XGCDetails::create($data);
            $saved = $Details->save();
            
            $data = array(
              'walletid'=>$walletid,
              'password'=>$password,
              'email'=>$email,
              'xemail'=>$xemail,
              'phone'=>$phone,
              'xphone'=>$xphone,
              'code'=>$code,
              'xcode'=>$xcode,
            );
            $Users = XGCUsers::create($data);
            $saved = $Users->save();
          
            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Wallet created',
              'walletid'=>$walletid,
              'email'=>$email,
              'xemail'=>$xemail,
              'phone'=>$phone,
              'xphone'=>$xphone,
              'code'=>$code,
              'xcode'=>$xcode,
            )));   
          }    
      }

      public function setpublickey($key = null){        
          if($key==null || $key==""){
            return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Key missing!'
            )));
          }    

          if($this->request->data['publickey'] ==null || $this->request->data['publickey']==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'public key required!'
            )));
          }
      }
}
?>