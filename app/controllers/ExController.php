<?php
namespace app\controllers;

use app\extensions\action\Uuid;
use app\models\Apps;
use app\models\KYCDocuments;
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
         extract($this->request->data);
         $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));   
         if(count($record) > 0){

                $emailchk = XGCUsers::find('all', [
                  'conditions' => array(
                      'hash' => $record['hash'],
                      'email' => $email
                  )
                ]);
                
                if(count($emailchk) > 0){
                    return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>'Wallet email already exits!'
                    )));
                } 

              $uuid = new Uuid();
              $kyc_id = $uuid->v4v();
              $email_code = '1111'; //substr($kyc_id,0,4);
            
              ////////////////////////////////////////Send Email
                // $emaildata = array(
                //  'kyc_id'=>$email_code,
                //  'email'=> strtolower($this->request->data['email'])
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
         }else{
            return $this->render(array('json' => array('success'=>2,
              'now'=>time(),
              'error'=>'Invalid Key!'
            )));
         }    
      }

      public function sendmobilecode($key=null){
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));  
        }
            
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
            $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));   
            if(count($record) > 0){
                
                // $wallet = XGCUsers::find('all', [
                //   'conditions' => array(
                //       'hash' => $record['hash'],
                //       'phone' => $mobile,
                //   )
                // ]);

                // if(count($wallet) > 0){
                //     return $this->render(array('json' => array('success'=>0,
                //       'now'=>time(),
                //       'error'=>'Wallet mobile already exits!'
                //     )));
                // }

                $ga = new GoogleAuthenticator();
                $secret = $ga->createSecret(64);
                $signinCode = '111111'; //$ga->getCode($secret);  
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
            }else{
               return $this->render(array('json' => array('success'=>2,
                  'now'=>time(),
                  'error'=>'Invalid Key!'
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

        $regex = "/^[A-Za-z0-9 ]{2,12}$/";
        if (!preg_match($regex, $name)) {
           return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'please enter proper name'
            )));
        }



        if($this->request->data['email'] ==null || $this->request->data['email']==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email required!'
          )));
        }

        if(Validator::rule('email',$this->request->data['email'])==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Email not correct!'       
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

        // if(strlen($password) < 8){
        //   return $this->render(array('json' => array('success'=>0,
        //     'now'=>time(),
        //     'error'=>'password length must be 8 charecter!'
        //   )));
        // }

        // $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$%^&]).*$/";
        // 'error'=>'password contains at least one char, numeric or special like @ $ % ^ &'
        
        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$%^&])[A-Za-z0-9@$%^&]{8,}$/";
        if (!preg_match($regex, $password)) {
           return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'password contains  minimum 8 characters at least 1 Uppercase Alphabet, 1 Lowercase Alphabet, 1 Number and 1 Special Character @ $ % ^ &'
            )));
        }    

        if($this->request->data['kyc_id'] ==null || $this->request->data['kyc_id'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'kyc id required!'
          )));
        }

        if($this->request->data['ip'] ==null || $this->request->data['ip'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Ip required!'
          )));
        }      
        
          extract($this->request->data);   
          $pass = password_hash($password, PASSWORD_BCRYPT);

          $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));   
          if(count($record) > 0){

            $wallet = XGCUsers::find('all', [
              'conditions' => array(
                  'hash' => $record['hash'],
                  'or' => array(
                      array('walletid' => $walletid),
                      array('email' => $email),
                      // array('phone' => $phone)
                  ),
              )
            ]);

            if(count($wallet)!=0){
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Wallet Already exits!'
                )));
            }

            $record = $record->to('array');

            $walletAry = count($record['wallets']) > 0 ? $record['wallets'] : array();
            $number    = count($record['wallets']); // == 0 ? 0 : count($record['wallets']);
      
            $newWallet = array(
                'walletid' => $walletid,
                'walletName' => $name,
                'walletPassword' => $pass,
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
              'hash' => $record['hash'],
              'secret'=>$secret,
              'password.send.email'=>false
            );
          
            $Details = XGCDetails::create($data);
            $saved = $Details->save();
            
            $data = array(
              'walletid'=>$walletid,
              'xwalletid'=>$xwalletid,
              'hash' => $record['hash'],
             // 'password'=>$password,
              'email'=>$email,
              'xemail'=>$xemail,
              'phone'=>$phone,
              'country_code'=>$country_code,
              'xphone'=>$xphone,
              'code'=>$code,
              'xcode'=>$xcode,
              'ip'=>$ip,
            );
            $Users = XGCUsers::create($data);
            $saved = $Users->save();
            $id = $Users->_id;
           
            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Wallet created',
              'record' =>$number,
              'recordid'=> (string) $id,
              'walletid'=>$walletid,
              'xwalletid'=>$xwalletid,
              'secondpassword' => $record['secondpassword'],
              'email'=>$email,
              'xemail'=>$xemail,
              'phone'=>$phone,
              'country_code'=>$country_code,
              'xphone'=>$xphone,
              'code'=>$code,
              'xcode'=>$xcode,
              'ip'=>$ip,
            )));   
          }else{
            return $this->render(array('json' => array('success'=>2,
              'now'=>time(),
              'error'=>'Invalid Key!'
            ))); 
          }    
      }

      public function setgreencoinaddress($key = null){
          if($key==null || $key==""){
            return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Key missing!'
            )));
          }    

          if($this->request->data['walletid'] ==null || $this->request->data['walletid']==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'wallet id required!'
            )));
          }

          if($this->request->data['pubkey'] ==null || $this->request->data['pubkey']==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'pub key required!'
            )));
          }

          if($this->request->data['privkey'] ==null || $this->request->data['privkey']==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'privkey key required!'
            )));
          }

          extract($this->request->data);

          $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));    
          if(count($record) > 0){
              $XGCUsers = XGCUsers::find('first',array('conditions' => array('walletid'=>$walletid)));    
              if(count($XGCUsers) > 0){
                  $data['greencoinAddress'] = (object) array('0' => $pubkey);
                  $data['greencoinPrivate'] = (object) array('0' => $privkey);
                  $data['DateTime'] = new \MongoDate();
                  $conditions = array('walletid' => $walletid);
                  XGCUsers::update($data,$conditions);

                  return $this->render(array('json' => array('success'=>1,
                    'now'=>time(),
                    'result'=>'Greencoin address set success',
                    'walletid'=>$walletid,
                  )));
              }else{
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Invalid Wallet Id!'
                ))); 
              }  
          
          }else{
            return $this->render(array('json' => array('success'=>2,
              'now'=>time(),
              'error'=>'Invalid Key!'
            ))); 
          }      
      }

      public function checkwalletpassword($key = null){
        extract($this->request->data);
        if ($key==null || $key == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }

        if ($this->request->data['walletid']==null || $this->request->data['walletid'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Wallet id missing!'
          )));
        }

        if ($this->request->data['password']==null || $this->request->data['password'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Password missing!'
          )));
        }

        // if(strlen($this->request->data['password']) < 8){
        //   return $this->render(array('json' => array('success'=>0,
        //     'now'=>time(),
        //     'error'=>'Password length must be 8 charecter!'
        //   )));
        // }

        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$%^&])[A-Za-z0-9@$%^&]{8,}$/";
        if (!preg_match($regex, $password)) {
           return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'password contains  minimum 8 characters at least 1 Uppercase Alphabet, 1 Lowercase Alphabet, 1 Number and 1 Special Character @ $ % ^ &'
            )));
        }

        $record = Apps::find('first',array(
            'conditions' => array(
                'key'=>$key,
                'isdelete' =>'0',
                'wallets.walletid'=>$walletid,
            ),
            'fields' => array(
               'wallets.walletid',
               'wallets.walletPassword'
            )
          )
        );
        
        if(count($record) > 0){
            foreach ($record['wallets'] as $k => $v) {
              if($v['walletid'] == $walletid){
                if(password_verify($password, $v['walletPassword'])) { 
                      return $this->render(array('json' => array('success'=>1,
                        'now'=>time(),
                        'result'=>'password is valid',
                      ))); 
                }else{
                    return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>'password wrong!'
                    )));
                }
              }
            }            
        }else{
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Wallet not found!',
          )));
        }
      }

      public function checksecondpassword($key = null){
        
        if($key == null || $key == ""){
            return $this->render(array('json' => array('success' => 0,
              'now' => time(),
              'error' => 'Key missing!'
            )));
        }

        $record = Apps::find('first',array(
            'conditions' => array(
                'key'=>$key,
                'isdelete' => '0',
                'secondpassword' => [
                  '$exists' => true
                ]
            )
          )
        );

        if(count($record) > 0){
            $issecondpassword = 1;          
        }else{
            $issecondpassword = 0;
        }

        return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'result'=>'success',
            'issecondpassword' => $issecondpassword
          )));
      }
      
      public function changesecondrypass($key = null){
        extract($this->request->data);
        if ($key==null || $key == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }

        if ($this->request->data['oldpassword']==null || $this->request->data['oldpassword'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Old password missing!'
          )));
        }

        if ($this->request->data['newpassword']==null || $this->request->data['newpassword'] == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'New password missing!'
          )));
        }

        if ($this->request->data['oldpassword'] == $this->request->data['newpassword']){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'New password same as old password'
          )));
        }

        // if(strlen($newpassword) < 8){
        //   return $this->render(array('json' => array('success'=>0,
        //     'now'=>time(),
        //     'error'=>'New password length must be 8 charecter!'
        //   )));
        // }

        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$%^&])[A-Za-z0-9@$%^&]{8,}$/";
        if (!preg_match($regex, $newpassword)) {
           return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'newpassword contains  minimum 8 characters at least 1 Uppercase Alphabet, 1 Lowercase Alphabet, 1 Number and 1 Special Character @ $ % ^ &'
            )));
        }

        $record = Apps::find('first',array(
            'conditions' => array(
                'key'=>$key,
                'isdelete' => '0'
            )
          )
        );

        if(count($record) > 0){
            if(password_verify($oldpassword, $record['secondpassword'])) { 
                // check New Password Regular exprestion
                $data['secondpassword'] = password_hash($newpassword, PASSWORD_BCRYPT);;
                $conditions = array('key' => $key);
                Apps::update($data,$conditions);

                return $this->render(array('json' => array('success'=>1,
                  'now'=>time(),
                  'result'=>'Secondary password updated.',
                ))); 
            }else{
              return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'old password wrong!'
              )));
            }           
        }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Invalid Key',
          )));
        }
      }

      public function getWallet($key = null){
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }else{
             extract($this->request->data);
             $conditions = array('key' => $key,'isdelete' =>'0');
             $record = Apps::find('first',array('conditions'=>$conditions));
            if(count($record)!=0){

                $conditions = array('hash' => $record['hash'],'email' => $record['email'],'phone' => $record['phone']);
                $document = KYCDocuments::find('first',array('conditions'=>$conditions));
                
                if(count($document)!=0){            
                  
                  $wallets = [];
                  foreach ($record['wallets'] as $k => $v) { 
                    $XGCWallet = XGCUsers::find('first',array(
                        'conditions'=>['walletid' => $v['walletid']]
                      ))->to('array');

                       $wallets[$k] = array(
                        'walletid' => $record['wallets'][$k]['walletid'],
                        'name' => $record['wallets'][$k]['walletName'],
                        'kyc_id'=> $document['kyc_id'],
                        'greencoinAddress' => $XGCWallet['greencoinAddress'][0], 
                        'email'=> $XGCWallet['email'],
                        'phone'=> $XGCWallet['phone'],
                        'country_code' => $XGCWallet['country_code'],
                        'default_currency'=> 'XGC',
                        'currency'=> $record['wallets'][$k]['walletCurrency'],

                        );
                  }  
                   return $this->render(array('json' => array('success'=>1,
                    'now'=>time(),
                    'result' =>'Wallet list',
                    'wallets' => $wallets

                   )));

                }else{
                   return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>'Kyc id wrong!'
                  )));
                }
                
            }else{
                return $this->render(array('json' => array('success'=>2,
                'now'=>time(),
                'error'=>'Invalid Key!'
                )));    
            }
        }   
      }

      public function testing($key = null){

          if($key==null || $key==""){
            return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Key missing!'
            )));
          }

          $data = ['wallets' =>['a' => 'test12526354']];
          $conditions = array('key' => $key);
          $option = array('Server');
          Apps::update($data,$conditions,$option);

          print_r(Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')))->data());
      }
}
?>