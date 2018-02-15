<?php
namespace app\controllers;

use app\extensions\action\Uuid;
use app\models\Apps;
use app\models\KYCDocuments;
use app\models\KYCFiles;
use app\models\XGCDetails;
use app\models\XGCUsers;
use MongoID;  
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\Coingreen;
use li3_qrcode\extensions\action\QRcode;
use app\models\Wallets;
use app\models\Countries;
use app\models\Ipv6s;
use \lithium\template\View;

class ExController extends \lithium\action\Controller {
      
      public function index(){
         echo "Wrong";
         exit();
      }

      public function checkwalletname($key = null){
        extract($this->request->data);
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }
          
        if($walletname==null || $walletname==""){
            return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email missing!'
          )));
        }

        $regex = "/^[A-Za-z0-9 ]{2,12}$/";
        if (!preg_match($regex, $walletname)) {
           return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'please enter proper name'
            )));
        }

        if($kyc_id==null || $kyc_id==""){
            return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'kyc id missing!'
          )));
        }  

        if($ip==null || $ip==""){
            return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Ip missing!'
          )));
        }

         $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));   
         if(count($record) > 0){
            /* check Kyc is valid*/
            $kyc = KYCDocuments::find('first',array('conditions' => array('hash'=>$record['hash'],'kyc_id' =>$kyc_id)));
            if(count($kyc) <= 0){
               return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Something went wrong!'
              ))); 
            }            


                $chkname = Wallets::find('all', [
                  'conditions' => array(
                      'name' => $walletname,
                      'hash' => $record['hash'],
                      'secret'=>array('$ne'=>null)
                  )
                ]);
                
                if(count($chkname) > 0){
                    return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>'Wallet name already exits!'
                    )));
                } 

                $ga = new GoogleAuthenticator();
                $codekey = $ga->createSecret(64);
                if($ip==null ){
                  $ip = $_SERVER['REMOTE_ADDR'];
                }
                $pos = strpos($ip, ":");

                if($pos===false){
                      $response = file_get_contents("http://ipinfo.io/{$ip}");
                      $IPResponse = json_decode($response);
                      $country = Countries::find('first',array(
                        'conditions' => array('ISO'=>$IPResponse->country)
                      ));
                      $data = array(
                      'key'=>$codekey,
                      'name' => $walletname,
                      'kyc_id'=>$kyc_id,
                      'DateTime' => new \MongoDate(),
                      'IPinfo' => $IPResponse,
                      'ISDPhone'=>$country['Phone'],
                      );
                      $wallets = Wallets::create()->save($data);
                    
                    return $this->render(array('json' => array('success'=>1,
                        'now'=>time(),
                        'codekey'=>$codekey,
                      )));
                }else{
                      $ipcountry = Ipv6s::find('first',array(
                        'conditions' => array('from'=>array('$lt'=>$ip),'to'=>array('$gte'=>$ip)),
                      ));
                        $country = Countries::find('first',array(
                        'conditions' => array('ISO'=>$ipcountry['ISO'])
                      ));
                        $data = array(
                        'key'=>$codekey,
                        'name' => $walletname,
                        'kyc_id'=>$kyc_id,
                        'DateTime' => new \MongoDate(),
                        'IPinfo.ip' => $ip,
                        'ISDPhone'=>$country['Phone'],
                        'IPinfo.country'=>$ipcountry['ISO'],
                      );
                      $wallets = Wallets::create()->save($data);
                      return $this->render(array('json' => array('success'=>1,
                        'now'=>time(),
                        'codekey'=>$codekey,
                    )));
                }
         }else{
            return $this->render(array('json' => array('success'=>2,
              'now'=>time(),
              'error'=>'Invalid Key!'
            )));
         }  
      }

      public function sendemailcode($key = null){
        extract($this->request->data);
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }
        
        if($codekey==null || $codekey==""){
            return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Something went wrong!'
          )));
        }

        if($email==null || $email==""){
            return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email missing!'
          )));
        }  

        if(Validator::rule('email',$email)==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email not correct!'       
          )));
        }

        
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

                $wallet = Wallets::find('first',array(
                    'conditions'=>array('key'=>$codekey)
                  ));
                if(count($wallet)==0){
                  return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>'Code incorrect!'        
                  )));
                }

               $ga = new GoogleAuthenticator(); 
              if($wallet['oneCodeused']=='Yes' || $wallet['oneCodeused']==""){
                $oneCode = $ga->getCode($ga->createSecret(64)); 
              }else{
                $oneCode = $wallet['oneCode'];
              }
              $data = array(
                  'oneCode' => $oneCode,
                  'oneCodeused' => 'No',
                  'email'=>$email
                );
              $conditions = array("key"=>$codekey);

              $wallet = Wallets::update($data,$conditions);  

              /////////////////////////////////Email//////////////////////////////////////////////////
              
              $function = new Functions();
              $compact = array('data'=>$data);
              // sendEmailTo($email,$compact,$controller,$template,$subject,$from,$mail1,$mail2,$mail3)
              $from = array(NOREPLY => "noreply@".COMPANY_URL);
              $email = $email;
              $attach = null;
              $function->sendEmailTo($email,$compact,'code','code',"Validation code ",$from,'','','',$attach);
            
            /////////////////////////////////Email//////////////////////////////////////////////////
                   
             return $this->render(array('json' => array('success'=>1,
              'email_code'=>$oneCode,
              'email' => $email,
              'codekey' => $codekey,
              'result' => 'Please check your email to get the code'
             )));

         }else{
            return $this->render(array('json' => array('success'=>2,
              'now'=>time(),
              'error'=>'Invalid Key!'
            )));
         }    
      }

      public function verifyemail($key = null){
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }
        
        if($this->request->data['codekey']==null || $this->request->data['codekey']==""){
            return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Something went wrong!'
          )));
        }

        if($this->request->data['email']==null || $this->request->data['email']==""){
            return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email missing!'
          )));
        }  

        if(Validator::rule('email',$this->request->data['email'])==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email not correct!'       
          )));
        }

        if($this->request->data['code']==null || $this->request->data['code']==""){
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Email code missing!'
          )));
        }

         extract($this->request->data);
         $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));   
         if(count($record) > 0){
              $wallet = Wallets::find('first',array(
                'conditions'=> array(
                  'email'=>$email,
                  'key'=>$codekey,
                  'oneCode'=>$code,
                )
              ));
              
              if(count($wallet)==0){
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Something is incorrect!'        
                )));
              }else{
                  $conditions = array(
                    'key'=>$codekey,
                    'email'=>$email,
                    'oneCode'=>$code
                  );
                  $data = array(
                    'oneCodeused' => 'Yes'
                  );
                  Wallets::update($data,$conditions);
                  return $this->render(array('json' => array('success'=>1,
                    'now'=>time(),
                    'result'=>'Email is verified!'
                  )));
              }             
         }else{
            return $this->render(array('json' => array('success'=>2,
              'now'=>time(),
              'error'=>'Invalid Key!'
            )));
         }    
      }

      public function sendmobilecode($key=null){
        extract($this->request->data);
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));  
        }

        if($codekey==null || $codekey==""){
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Something went wrong!'
          )));
        }
            
        if($phone==null || $phone=="") {
          return $this->render(array('json' => array('success'=>0,
           'now'=>time(),
           'error'=>'Phone missing!'
          )));
        }

        // if(Validator::rule('intphone',$phone)==""){
        //   return $this->render(array('json' => array('success'=>0,
        //     'now'=>time(),
        //     'error'=>'Phone not correct!'       
        //   )));
        // }

        // $pattern = "^(999|998|997|996|995|994|993|992|991|990|979|978|977|976|975|974|973|972|971|970|969|968|967|966|965|964|963|962|961|960|899|898|897|896|895|894|893|892|891|890|889|888|887|886|885|884|883|882|881|880|879|878|877|876|875|874|873|872|871|870|859|858|857|856|855|854|853|852|851|850|839|838|837|836|835|834|833|832|831|830|809|808|807|806|805|804|803|802|801|800|699|698|697|696|695|694|693|692|691|690|689|688|687|686|685|684|683|682|681|680|679|678|677|676|675|674|673|672|671|670|599|598|597|596|595|594|593|592|591|590|509|508|507|506|505|504|503|502|501|500|429|428|427|426|425|424|423|422|421|420|389|388|387|386|385|384|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|296|295|294|293|292|291|290|289|288|287|286|285|284|283|282|281|280|269|268|267|266|265|264|263|262|261|260|259|258|257|256|255|254|253|252|251|250|249|248|247|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|219|218|217|216|215|214|213|212|211|210|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1)[0-9]{0,14}$";
                  
        // if(!preg_match($pattern, $phone)) {
        //       return $this->render(array('json' => array('success'=>0,
        //         'now'=>time(),
        //         'error'=>'Phone not correct!'
        //       )));
        // }
        
            
            $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));   
            if(count($record) > 0){
               
               $wallet = Wallets::find('first',array(
                 'conditions'=>array('key'=>$codekey )
               ));

                if(count($wallet)==0){
                  return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>'Code incorrect!'        
                  )));
                }

                $ga = new GoogleAuthenticator();
                if($wallet['twoCodeused']=='Yes' || $wallet['twoCodeused']==""){
                  $twoCode = $ga->getCode($ga->createSecret(64)); 
                }else{
                  $twoCode = $wallet['twoCode'];
                }
                $data = array(
                    'twoCode' => $twoCode,
                    'twoCodeused' => 'No',
                    'phone'=>$phone
                  );
                $conditions = array("key"=>$codekey);
                $wallet = Wallets::update($data,$conditions);

            
                $function = new Functions();
                if(substr($phone,0,3)=='+91'){
                  $msg = 'Please enter GreenCoin verification code: '.$twoCode.' on your GreenCoinX client.';
                  $phone = str_replace("+","",$phone);
                    $returnvalues = $function->SMS($phone,$msg);
                    $returnvalues = $function->twilio($phone,$msg,$twoCode);   // Testing if it works      
                }else{
                  $msg = 'Please enter GreenCoinX mobile phone verification code: '.$twoCode;
                  $phone = str_replace("+","",$phone);
                  $returnvalues = $function->send_sms_infobip($phone,$msg);         
                  $returnvalues = $function->twilio($phone,$msg,$twoCode);   // Testing if it works 
               }

              return $this->render(array('json' => array('success'=>1,
                'now'=>time(),
                'phone_code' => $twoCode,
                'result'=>'Code sent to phone!',
                'codekey' => $codekey
              ))); 
            }else{
               return $this->render(array('json' => array('success'=>2,
                  'now'=>time(),
                  'error'=>'Invalid Key!'
                ))); 
            }      
      }

      public function verifyphonecode($key=null){
        extract($this->request->data);
        
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));  
        }

        if($codekey==null || $codekey==""){
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Something went wrong!'
          )));
        }
            
        if($phone==null || $phone=="") {
          return $this->render(array('json' => array('success'=>0,
           'now'=>time(),
           'error'=>'Phone missing!'
          )));
        }

        if($phonecode==null || $phonecode==""){
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Phone code missing!'
          )));
        }

            $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));   
            if(count($record) > 0){
                $wallet = Wallets::find('first',array(
                  'conditions'=> array(
                    'phone'=>$phone,
                    'key'=>$codekey,
                    'twoCode'=>$phonecode,
                  )
                ));

                if(count($wallet)==0){
                  return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>'Something is incorrect!'        
                  )));
                }else{
                    $conditions = array(
                      'key'=>$codekey,
                      'phone'=>$phone,
                      'twoCode'=>$phonecode
                    );
                    $data = array(
                      'twoCodeused' => 'Yes'
                    );
                    Wallets::update($data,$conditions);
                    
                    return $this->render(array('json' => array('success'=>1,
                      'now'=>time(),
                      'result'=>'Phone is verified!'
                    )));
                }   
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

        if($codekey ==null || $codekey==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Something went wrong!'
          )));
        }    

        if($email ==null || $email==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Email missing!'
          )));
        }

        if(Validator::rule('email',$email)==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Email not correct!'       
            )));
        }  

        if($phone==null || $phone==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Phone missing!'
          )));
        }

        if($walletid ==null || $walletid == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Wallet missing!'
          )));
        }  

        if($password ==null || $password == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'password missing!'
          )));
        }

    
        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$%^&])[A-Za-z0-9@$%^&]{8,}$/";
        if (!preg_match($regex, $password)) {
           return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'password contains  minimum 8 characters at least 1 Uppercase Alphabet, 1 Lowercase Alphabet, 1 Number and 1 Special Character @ $ % ^ &'
            )));
        }    

        if($kyc_id ==null || $kyc_id == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'kyc missing!'
          )));
        }

        if($ip ==null || $ip == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Ip missing!'
          )));
        }      
      
          $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));   
          if(count($record) > 0){

            $wallet = Wallets::find('first',array(
              'conditions'=> array(
                'key'=>$codekey,
                'email' => $email,
                'phone'=>$phone,
                'oneCodeused' => "Yes",
                'twoCodeused'=>"Yes",
              )
            ));
            if(count($wallet)==0){
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Something went wrong!'
                )));
            }

            $walletXGC = XGCUsers::find('all', [
              'conditions' => array(
                  'hash' => $record['hash'],
                  'or' => array(
                      array('walletid' => $walletid),
                      array('email' => $email),
                  ),
              )
            ]);

            if(count($walletXGC)!=0){
                // return $this->render(array('json' => array('success'=>0,
                //   'now'=>time(),
                //   'error'=>'Wallet Already exits!'
                // )));
            }

            $record = $record->to('array');

            /* Profile Picture Move FtpCDN Server  */
            $path  =  $this->getImage($record['hash'],'profile_img');
            $movepath = LITHIUM_APP_PATH. '/webroot/documents/'.$walletid.'.jpg';
            $this->smart_resize_image($path, null,300 , 0 , true , $movepath , false , false ,100 );  
            $profile = $this->uploadProfileThumb($walletid);
            $basename = pathinfo($path,PATHINFO_BASENAME);
            unlink(LITHIUM_APP_PATH. '/webroot/documents/'.$basename);
            unlink($movepath);
            
            $data['profile'] = $profile;
            $conditions = array('key' => $key);
            Apps::update($data, $conditions);

            /* */ 

            $uuid = new Uuid();     
            $xemail = $uuid->hashme($email);
            $xphone = $uuid->hashme($phone);
            $xcode = $uuid->hashme($codekey);
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
              'name' => $wallet['name'],
              'hash' => $record['hash'],
              'password'=> password_hash($password, PASSWORD_BCRYPT),
              'email'=>$email,
              'xemail'=>$xemail,
              'phone'=>$phone,
              'xphone'=>$xphone,
              'code'=>$codekey,
              'xcode'=>$xcode,
              'ip'=>$ip,
            );
            $Users = XGCUsers::create($data);
            $saved = $Users->save();
            $id = $Users->_id;

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Wallet created',
              'record'=>0,
              'recordid'=> (string) $id,
              'walletid'=>$walletid,
              'xwalletid'=>$xwalletid,
              'password' => $password,
              'email'=>$email,
              'xemail'=>$xemail,
              'phone'=>$phone,
              'xphone'=>$xphone,
              'code'=>$codekey,
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
          ini_set('max_execution_time', 0);
          extract($this->request->data);
          if($key==null || $key==""){
            return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Key missing!'
            )));
          }

          if($codekey ==null || $codekey==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Something went wrong!'
            )));
          }    

          if($email ==null || $email==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Email missing!'
            )));
          }

          if(Validator::rule('email',$email)==""){
              return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Email not correct!'       
              )));
          }  

          if($phone==null || $phone==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Phone missing!'
            )));
          }    

          if($walletid ==null || $walletid==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'wallet id required!'
            )));
          }

          if($pubkey ==null || $pubkey==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'pub key required!'
            )));
          }

          if($privkey ==null || $privkey==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'privkey key required!'
            )));
          }

          if($passphrase ==null || $passphrase==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Passphrase required!'
            )));
          }

          if($ip ==null || $ip==""){
            return $this->render(array('json' => array('success'=>0,
              'now'=>time(),
              'error'=>'Ip required!'
            )));
          }

          

          $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));    
          if(count($record) > 0){

            $XGCUsers = XGCUsers::find('first',array('conditions' => array('code'=> $codekey,'walletid'=>$walletid)));    
            if(count($XGCUsers) > 0){

              /* public key */
              $data = array('greencoinAddress.0' => $pubkey,
                            'greencoinPriv.0' => $privkey,
                            'passphrase' => $passphrase,
                            'ip' => $ip,
                            'DateTime' => new \MongoDate()
                      );
              $conditions = array('walletid' => $walletid);
              XGCUsers::update($data,$conditions);

              /* Assign Wallet in GreenCoinx */ 
              
              $COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);

             // $createWallet = $COINGREEN->importprivkey($privkey,$walletid,false);

              $filename = "Wallet_".gmdate('Y-m-d_H-i-s',time()).".txt";
              $dumpwallet = $COINGREEN->dumpwallet($filename);
              
                  $printdata = array(
                    'email' => $email,
                    'phone' => $phone,
                    'walletid' => $walletid,
                    'passphrase' => $passphrase,
                    'code' => $codekey,
                    'privkey' => $privkey,
                    'greencoinAddress' => $pubkey,
                    'ip' => $ip,
                    'DateTime' => new \MongoDate(),
                  );

                      //create all QR Codes
              $qrcode = new QRcode();   
              $qrcode->png($passphrase, QR_OUTPUT_DIR."XGCWallet-".$walletid."-passphrase.png", 'H', 7, 2);   
              $qrcode->png($walletid, QR_OUTPUT_DIR."XGCWallet-".$walletid."-walletid.png", 'H', 7, 2);   
              $qrcode->png($codekey, QR_OUTPUT_DIR."XGCWallet-".$walletid."-code.png", 'H', 7, 2);   
              $qrcode->png($privkey, QR_OUTPUT_DIR."XGCWallet-".$walletid."-privkey.png", 'H', 7, 2);   
              $qrcode->png($pubkey, QR_OUTPUT_DIR."XGCWallet-".$walletid."-greencoinAddress.png", 'H', 7, 2);   
                    
                    // send email for password and QRCode print --------- start
              
                    
              $view  = new View(array(
                'paths' => array(
                  'template' => '{:library}/views/{:controller}/{:template}.{:type}.php',
                  'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php',
                )
              ));
              $page =  $view->render(
                'all',
                compact('printdata'),
                array(
                  'controller' => 'print',
                  'template'=>'walletsetup',
                  'type' => 'pdf',
                  'layout' =>'print'
                )
              );  
              
                  // sending email to the users 
                /////////////////////////////////Email//////////////////////////////////////////////////
                $function = new Functions();
                $compact = array('data'=>$printdata);
                $from = array(NOREPLY => "noreply@".COMPANY_URL);
                $function->sendEmailTo($email,$compact,'ex','setupwd',"XGCWallet - important document",$from,'','','',null);
                $attach = QR_OUTPUT_DIR.'XGCWallet-'.$printdata['walletid']."-Wallet".".pdf";
                $function->sendEmailTo($email,$compact,'ex','setup',"XGCWallet - important document",$from,'','','',$attach);

                  /////////////////////////////////Email//////////////////////////////////////////////////

                //delete all QR code files from the server      

                if ($handle = opendir(QR_OUTPUT_DIR)) {
                    while (false !== ($entry = readdir($handle))) {
                      if ($entry != "." && $entry != "..") {
                          if(strpos($entry,$printdata['walletid'])){
                          unlink(QR_OUTPUT_DIR.$entry);
                        }
                      }
                    }
                   closedir($handle);
                }
                
                exec("cd /home/hitarth/public_html/hitarth.org &&  libraries/lithium/console/li3 import-priv-key ".$pubkey);
                
                  return $this->render(array('json' => array('success'=>1,
                    'now'=>time(),
                    'result'=>'Greencoin address set success',
                    'walletid'=>$walletid,
                    'Server'=>$_SERVER["SERVER_ADDR"],
                    'Refer'=>$_SERVER["REMOTE_ADDR"]
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

      public function verified($key=null){
        extract($this->request->data);

        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Key missing!'
          )));
        }

        if($codekey==null || $codekey==""){
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Something went wrong!'
          )));
        }

        if($emailcode==null || $emailcode==""){
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Email code missing!'
          )));
        }
        if($phonecode==null || $phonecode==""){
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Phone code missing!'
          )));
        }
        if($pubkey==null || $pubkey==""){
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Pubkey missing!'
          )));
        }

          $record = Apps::find('first',array('conditions' => array('key'=>$key,'isdelete' =>'0')));   
          if(count($record) > 0){
              $coingreen = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
                  
              $validateAddress = $coingreen->validateaddress($pubkey);

              if($validateAddress['isvalid']==0 || $validateAddress==null || $validateAddress==''){
                exec("service greencoind start");
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Greencoinx address incorrect try after sometime!'
                )));
              }
                
              $wallet = Wallets::find('first',array(
                'conditions'=> array(
                  'key'=>$codekey,
                  'oneCode'=>$emailcode,
                  'twoCode'=>$phonecode,
                  'oneCodeused'=>'Yes',
                  'twoCodeused'=>'Yes',
                )));
              
                if($wallet['secret']){
                  return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>'Already verified!',
                    'email'=>$wallet['email'],
                    'phone'=>$wallet['phone'],
                  )));
                }
              
                if(count($wallet)==0){
                  return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>'Key incorrect!'
                  )));
                }

              $ga = new GoogleAuthenticator();
              $secret = $ga->createSecret(40);
              
                $conditions = array(
                  'key'=>$codekey,
                  'oneCode'=>$emailcode,
                  'twoCode'=>$phonecode
                );

              $data = array(
                  'addresses.0' => $pubkey, 
                  'amount' => (float)VERIFY_AMOUNT,
                  'secret' => $secret,
                  'extra'  => 'XGC_Wallet'
                );
              
              $txid = $coingreen->sendopreturn($pubkey,$secret);
              
              if(strlen($txid)==64){
                Wallets::update($data,$conditions);

                $wallet = Wallets::find('first',array(
                  'conditions'=> array(
                    'key'=>$codekey,
                    'oneCode'=>$emailcode,
                    'twoCode'=>$phonecode,
                    'oneCodeused'=>'Yes',
                    'twoCodeused'=>'Yes',
                )));

                  return $this->render(array('json' => array('success'=>1,
                    'now'=>time(),
                    'result'=>'Client verified',
                    'secret'=>$secret,
                    'email'=>$wallet['email'],
                    'phone'=>$wallet['phone'],
                    'txid'=>$txid,
                  )));
                  
              }else{
                  return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Not valid txid!'
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

        if ($walletid==null || $walletid == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Wallet id missing!'
          )));
        }

        if ($password==null || $password == ""){
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
                'isdelete' =>'0'
            )
          )
        );
        
        if(count($record) > 0){
            $walletXGC = XGCUsers::find('first', [
              'conditions' => array(
                  'hash' => $record['hash'],
                  'walletid' => $walletid
              ),
              'fields' => array(
                 'password'
              )
            ]);

            if(count($walletXGC) <= 0){
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Wallet not found!'
                )));
            } 

            if(password_verify($password, $walletXGC['password'])) { 
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
        }else{
          return $this->render(array('json' => array('success'=>2,
            'now'=>time(),
            'error'=>'Invalid key!',
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

                $conditions = array('hash' => $record['hash'],'email' => $record['email'],
                                    'details.Mobile' => $record['country_code'].$record['phone']);
                $document = KYCDocuments::find('first',array('conditions'=>$conditions));
                
                if(count($document)!=0){   

                  $walletXGC = XGCUsers::find('all', [
                    'conditions' => array(
                        'hash' => $record['hash'],
                        'greencoinAddress'=>array('$ne'=>null)
                    )
                  ]);         
                  
                  $wallets = [];
                  foreach ($walletXGC as $k => $wallet) { 
                      
                      $walletXGC = wallets::find('first', [
                        'conditions' => array(
                            'key' => $wallet['code'],
                            'oneCodeused' => 'Yes',
                            'twoCodeused' => 'Yes',
                            'secret'=>array('$ne'=>null)
                        )
                      ]);

                      if(count($walletXGC) == 0){
                            continue;
                      }    

                      $COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);  
                      $balance = $COINGREEN->getbalance($wallet['walletid']);

                       $wallets[] = array(
                          'walletid' => $wallet['walletid'],
                          'name' => $wallet['name'],
                          'kyc_id'=> $document['kyc_id'],
                          'greencoinAddress' => $wallet['greencoinAddress'][0], 
                          'email'=> $wallet['email'],
                          'phone'=> $wallet['phone'],
                          'country_code' => $wallet['country_code'],
                          'default_currency'=> 'XGC',
                          'currency'=> $wallet['currency'],
                          'balance' => $balance
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

      public function scanwalletqrcode($key = null){
         extract($this->request->data);
         if($key==null || $key==""){
            return $this->render(array('json' => array('success' => '0',
              'now' => time(),
              'error' => 'Key missing!'
            )));
         }

         if($walletkey == null || $walletkey == ""){
           return $this->render(array('json' => array('success' => '0',
              'now' => time(),
              'error' => 'Walletkey missing!'
            )));
         }

          $conditions = array('key' => $key,'isdelete' =>'0');
          $record = Apps::find('first',array('conditions'=>$conditions));
          if(count($record)!=0){
             $XGCUsers = XGCUsers::find('first',array('conditions' => array('greencoinAddress.0'=>$walletkey)));    
              if(count($XGCUsers) > 0){

                $walletXGC = wallets::find('first', [
                  'conditions' => array(
                      'key' => $XGCUsers['code'],
                      'oneCodeused' => 'Yes',
                      'twoCodeused' => 'Yes',
                      'secret'=>array('$ne'=>null)
                  )
                ]);

                if(count($walletXGC) == 0){
                   return $this->render(array('json' => array('success' => '0',
                    'now' => time(),
                    'error' => 'Scan wallet wrong!'
                  )));  
                }


                $Users = Apps::find('first',array('conditions'=>array('hash' => $XGCUsers['hash'],'isdelete' =>'0')));


                $wallet = [
                  'name'             => $Users['name'],
                  'greencoinAddress' => $XGCUsers['greencoinAddress'][0],
                  'email'            => $XGCUsers['email'],
                  'phone'            => $XGCUsers['phone'],
                  'country_code'     => $XGCUsers['country_code'],  
                ];
                return $this->render(array('json' => array('success'=>1,
                  'now'=>time(),
                  'result'=>'Wallet info',
                  'wallet' => $wallet
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

      public function sendviaaddress($key = null){
         extract($this->request->data);
         if($key==null || $key==""){
            return $this->render(array('json' => array('success' => '0',
              'now' => time(),
              'error' => 'Key missing!'
            )));
         }

         if($email == null || $email == ""){
           return $this->render(array('json' => array('success' => '0',
              'now' => time(),
              'error' => 'Email missing!'
            )));
         }

          $conditions = array('key' => $key,'isdelete' =>'0');
          $record = Apps::find('first',array('conditions'=>$conditions));
          if(count($record)!=0){
             
             $XGCUsers = XGCUsers::find('first',array('conditions' => array('email'=>$email)));    
              if(count($XGCUsers) > 0){ 

                $walletXGC = wallets::find('first', [
                  'conditions' => array(
                      'key' => $XGCUsers['code'],
                      'oneCodeused' => 'Yes',
                      'twoCodeused' => 'Yes',
                      'secret'=>array('$ne'=>null)
                  )
                ]);

                if(count($walletXGC) == 0){
                   return $this->render(array('json' => array('success' => '0',
                    'now' => time(),
                    'error' => 'Something went wrong!'
                  )));  
                }


                $Users = Apps::find('first',array('conditions'=>array('hash' => $XGCUsers['hash'],'isdelete' =>'0')));


                $wallet = [
                  'name'             => $Users['name'],
                  'greencoinAddress' => $XGCUsers['greencoinAddress'][0],
                  'email'            => $XGCUsers['email'],
                  'phone'            => $XGCUsers['phone'], 
                ];
                return $this->render(array('json' => array('success'=>1,
                  'now'=>time(),
                  'result'=>'Wallet info',
                  'wallet' => $wallet
                )));  
                
              }else{
                return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>'Invalid Email!'
                ))); 
              }  
          }else{
            return $this->render(array('json' => array('success'=>2,
              'now'=>time(),
              'error'=>'Invalid Key!'
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
            $return_path = 'http://hitarth/documents/'.$image_name_name;
           // $return_path = 'http://192.168.10.131:8888/hitarth.org/documents/'.$image_name_name;
            file_put_contents($path, $image_name->file->getBytes());
            return $return_path;
        }
      }

      private function uploadProfileThumb($walletID){
         $directory = substr($walletID,0,1)."/".substr($walletID,1,1)."/".substr($walletID,2,1)."/".substr($walletID,3,1);
         
         $file = $walletID.".jpg";   
       
         $ftp_host = FTP_HOST; /* host */
         $ftp_user_name = FTP_USER; /* username */
         $ftp_user_pass = FTP_PASS; /* password */
         $local_file = LITHIUM_APP_PATH.   "/webroot". FTP_LOCAL_PATH. $file;     
         $remote_file = "/profiles/" . $directory ."/".$file; 
   
         $connect_it = ftp_connect( $ftp_host );   
         /* Login to FTP */
         $login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
     
         /* Send $local_file to FTP */
         if(ftp_put( $connect_it, $remote_file, $local_file, FTP_BINARY)){
            return $directory."/".$file;
         }else{
            return $directory."/".$file;
         }
      } 

      /* Common Function */
      private function smart_resize_image($file,$string= null,$width= 0,$height= 0,$proportional= false,$output= 'file',$delete_original= true,$use_linux_commands = false,$quality = 100) {
          if ( $height <= 0 && $width <= 0 ) return false;
          if ( $file === null && $string === null ) return false;
              # Setting defaults and meta
          $info   = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
          $image  = '';
          $final_width = 0;
          $final_height = 0;
          // print_r($info);
          if($info==null){
            return false;
          }
            
          list($width_old, $height_old) = $info;
          $cropHeight = $cropWidth = 0;

            # Calculating proportionality
            if ($proportional) {
              if      ($width  == 0)  $factor = $height/$height_old;
              elseif  ($height == 0)  $factor = $width/$width_old;
              else                    $factor = min( $width / $width_old, $height / $height_old );

              $final_width  = round( $width_old * $factor );
              $final_height = round( $height_old * $factor );
            }
            else {
              $final_width = ( $width <= 0 ) ? $width_old : $width;
              $final_height = ( $height <= 0 ) ? $height_old : $height;
              $widthX = $width_old / $width;
              $heightX = $height_old / $height;
              
              $x = min($widthX, $heightX);
              $cropWidth = ($width_old - $width * $x) / 2;
              $cropHeight = ($height_old - $height * $x) / 2;
            }
            # Loading image to memory according to type
            switch ( $info[2] ) {
              case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
              case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
              case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
              default: return false;
            }
                
            
            # This is the resizing/resampling/transparency-preserving magic
            $image_resized = imagecreatetruecolor( $final_width, $final_height );
            if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
              $transparency = imagecolortransparent($image);
              $palletsize = imagecolorstotal($image);

              if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color  = imagecolorsforindex($image, $transparency);
                $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
              }
              elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
              }
            }
            imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);
            
            
            # Taking care of original, if needed
            if ( $delete_original ) {
              if ( $use_linux_commands ) exec('rm '.$file);
              else @unlink($file);
            }

            # Preparing a method of providing result
            switch ( strtolower($output) ) {
              case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
              break;
              case 'file':
                $output = $file;
              break;
              case 'return':
                return $image_resized;
              break;
              default:
              break;
            }

            # Writing image according to type to the output destination and image quality
            switch ( $info[2] ) {
              case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
              case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
              case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9*$quality)/10.0);
                imagepng($image_resized, $output, $quality);
                break;
              default: return false;
            }

            return true;
      }
}    
?>