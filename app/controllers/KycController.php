<?php
namespace app\controllers;

use app\models\Wallets;
use app\models\Countries;
use app\models\Templates;
use app\models\Apps;
use app\extensions\action\Uuid;


use app\models\KYCDetails;
use app\models\KYCCompanies;
use app\models\KYCDocuments;
use app\models\KYCCountries;
use app\models\KYCFiles;
use app\models\KYCQuestions;
use app\models\KYCSubmits;

use app\models\SIIBalances;
use app\models\SIIBanks;
use app\models\SIICountries;
use app\models\SIIDetails;
use app\models\SIIFiles;
use app\models\SIILogins;
use app\models\SIINotifies;
use app\models\SIIOrders;
use app\models\SIIPages;
use app\models\SIIParameters;
use app\models\SIIReasons;
use app\models\SIIRequests;
use app\models\SIISettings;
use app\models\SIITrades;
use app\models\SIITransactions;
use app\models\SIIUsers;

use app\models\XGCAddresses;
use app\models\XGCDetails;
use app\models\XGCMarkets;
use app\models\XGCParameters;
use app\models\XGCRichlists;
use app\models\XGCStats;
use app\models\XGCTxes;
use app\models\XGCUsers;

use app\models\WinApps;
use app\models\WinCountries;
use app\models\WinDetails;
use app\models\WinIpv6s;
use app\models\WinPages;
use app\models\WinParameters;
use app\models\WinTemplates;
use app\models\WinTransactions;
use app\models\WinTxs;
use app\models\WinUsers;
use app\models\WinWallets;

use MongoID;	
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\Coingreen;

ini_set('memory_limit', '-1');

class KycController extends \lithium\action\Controller {
	 
    public function index(){
      //  return $this->render(array('json' => array('success'=>0,		'now'=>time())));
      
      $kycCompanies = KYCCompanies::count();
      $kycCountries = KYCCountries::count();
      $kycDocuments = KYCDocuments::count();
      $kycFiles = KYCFiles::count();
      $kycQuestions = KYCQuestions::count();
      $kycSubmits = KYCSubmits::count();
      
      $siiBalances = SIIBalances::count();
      $siiBanks = SIIBanks::count();
      $siiCountries = SIICountries::count();
      $siiDetails = SIIDetails::count();
      $siiFiles = SIIFiles::count();
      $siiLogins = SIILogins::count();
      $siiNotifies = SIINotifies::count();
      $siiOrders = SIIOrders::count();
      $siiPages = SIIPages::count();
      $siiParameters = SIIParameters::count();
      $siiReasons = SIIReasons::count();
      $siiRequests = SIIRequests::count();
      $siiSettings = SIISettings::count();
      $siiTrades = SIITrades::count();
      $siiTransactions = SIITransactions::count();
      $siiUsers = SIIUsers::count();
      
      $xgcAddresses = XGCAddresses::count();
      $xgcDetails = XGCDetails::count();
      $xgcMarkets = XGCMarkets::count();
      $xgcParameters = XGCParameters::count();
      $xgcRichlists = XGCRichlists::count();
      $xgcStats = XGCStats::count();
      $xgcTxes = XGCTxes::count();
      $xgcUsers = XGCUsers::count();
      
      $winApps = WinApps::count();
      $winCountries = WinCountries::count();
      $winDetails = WinDetails::count();
      $winIpv6s = WinIpv6s::count();
      $winPages = WinPages::count();
      $winParameters = WinParameters::count();
      $winTemplates = WinTemplates::count();
      $winTransactions = WinTransactions::count();
      $winTxs = WinTxs::count();
      $winUsers = WinUsers::count();
      $winWallets = WinWallets::count();
      
      
        
    		return $this->render(array('json' => array('success'=>1,
    		'now'=>time(),
    		'kyc.submits'=>$kycSubmits,
      'kyc.questions'=>$kycQuestions,
      'kyc.documents'=>$kycDocuments,
      'kyc.companies'=>$kycCompanies,
      'kyc.countries'=>$kycCountries,
      'kyc.files'=>$kycFiles,
      
      'sii.balances'=>$siiBalances,
      'sii.banks'=>$siiBanks,
      'sii.countries'=>$siiCountries,
      'sii.details'=>$siiDetails,
      'sii.files'=>$siiFiles,
      'sii.logins'=>$siiLogins,
      'sii.notifies'=>$siiNotifies,
      'sii.orders'=>$siiOrders,
      'sii.pages'=>$siiPages,
      'sii.parameters'=>$siiParameters,
      'sii.reasons'=>$siiReasons,
      'sii.requests'=>$siiRequests,
      'sii.settings'=>$siiSettings,
      'sii.trades'=>$siiTrades,
      'sii.transactions'=>$siiTransactions,
      'sii.users'=>$siiUsers,
      
      'xgc.addresses'=>$xgcAddresses,
      'xgc.details'=>$xgcDetails,
      'xgc.markets'=>$xgcMarkets,
      'xgc.parameters'=>$xgcParameters,
      'xgc.richlist'=>$xgcRichlists,
      'xgc.stats'=>$xgcStats,
      'xgc.txes'=>$xgcTxes,
      'xgc.users'=>$xgcUsers,
      
      'win.apps'=>$winApps,
      'win.countries'=>$winCountries,
      'win.details'=>$winDetails,
      'win.ipv6s'=>$winIpv6s,
      'win.pages'=>$winPages,
      'win.parameters'=>$winParameters,
      'win.templates'=>$winTemplates,
      'win.transactions'=>$winTransactions,
      'win.txs'=>$winTxs,
      'win.users'=>$winUsers,
      'win.wallets'=>$winWallets,
    		'Server'=>$_SERVER["SERVER_ADDR"],
    		'Refer'=>$_SERVER["REMOTE_ADDR"]

      
      
    		)));
	  }

    public function verifyemail($key = null){
      if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
       )));
      }else{
       $conditions = array(
        'key' => $key 
       );
       $record = Apps::find('first',array(
        'conditions'=>$conditions
       ));
       
       if(count($record)!=0){
        $data = array(
         'process'=>'email verify'
        );
        Apps::update($data,$conditions);
        if($this->request->data){
         if($this->request->data['email']){
          $email = strtolower($this->request->data['email']);
          $uuid = new Uuid();
          $emails = kycDocuments::find('first',array(
           'conditions'=>array('email'=>$email)
          ));
          $data = array(
           'email' => strtolower($this->request->data['email']),
           'hash'=>md5(strtolower($this->request->data['email']))
          );
          $conditions = array(
           'key' => $key 
          );
          Apps::update($data,$conditions);

         if(count($emails)===0){
          $kyc_id = $uuid->v4v();
          $email_code = substr($kyc_id,0,4);
          $data = array(
           'kyc_id'=>$kyc_id,
           'email_code'=>$email_code,
           'email'=>$email,
           'hash'=>md5($email),
           // 'details' => [],
           // 'address' => [],
           // 'driving' => [],
           // 'passport' => [],
           // 'passport_face' =>[],
           'IP'=>$_SERVER['REMOTE_ADDR']
          );
          $Documents = kycDocuments::create($data);
          $saved = $Documents->save();
         }else{
          $emails = kycDocuments::find('first',array(
    				   'conditions'=>array('email'=>$email)
    			   ));
    			   $kyc_id = $emails['kyc_id'];
          $email_code = $emails['email_code'];
          if($emails['Verify']['Score']>=80){
            return $this->render(array('json' => array(
             'success'=>0,
             'reason'=>'Aleredy KYC complete'
            )));	
           }
         }

         ////////////////////////////////////////Send Email
    				$emaildata = array(
    					'kyc_id'=>$email_code,
    					'email'=>$email
    				);
    				$function = new Functions();
    				$compact = array('data'=>$emaildata);
    				$from = array(NOREPLY => "noreply@".COMPANY_URL);
    				$email = $email;
    				$function->sendEmailTo($email,$compact,'process','sendKYC',"KYCGlobal - Email Code",$from,'','','',null);
         //////////////////////////////////////////////////////////////////////
         
         
         
           return $this->render(array('json' => array(
            'success'=>1,
            'email_code'=>$email_code,
        				'Server'=>md5($_SERVER["SERVER_ADDR"]),
            'Refer'=>md5($_SERVER["REMOTE_ADDR"])
           )));
         }
        }else{
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'No Email Specified!'
        )));         
        }
       }else{
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Invalid Key!'
        )));    
       }
      }
    }

    public function checkemailcode($key = null){
      if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
       )));
      }else{
        if($this->request->data){
         if($this->request->data['code']){
          $conditions = array('hash'=>$key,'email_code'=>$this->request->data['code']);
          $find = kycDocuments::find('first',array(
           'conditions' => $conditions
          ));
          if(count($find)===0){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Code Invalid',
        				'Server'=>md5($_SERVER["SERVER_ADDR"]),
            'Refer'=>md5($_SERVER["REMOTE_ADDR"])
            )));
          }else{
           return $this->render(array('json' => array('success'=>1,
            'now'=>time(),
            'error'=>'Code Valid',
        				'Server'=>md5($_SERVER["SERVER_ADDR"]),
            'Refer'=>md5($_SERVER["REMOTE_ADDR"])
           )));
          }
         }else{
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Code Invalid',
        				'Server'=>md5($_SERVER["SERVER_ADDR"]),
            'Refer'=>md5($_SERVER["REMOTE_ADDR"])
            )));
                
         }
        }
       
      }
    }

    public function sendmobilecode($key=null){
      if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
       )));
      }else{
       $conditions = array(
        'key' => $key 
       );
      
       $record = Apps::find('first',array(
        'conditions'=>$conditions
       ));
       
       if(count($record)!=0){
        $data = array(
         'process'=>'send mobile code'
        );
        Apps::update($data,$conditions);   
       }else{
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Invalid Key!'
        ))); 
       }
       if($this->request->data){
        if($this->request->data['mobile']==null || $this->request->data['mobile']=="") {
         return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Mobile number required!'
         )));
        }
      		$ga = new GoogleAuthenticator();
        $secret = $ga->createSecret(64);
        $signinCode = $ga->getCode($secret);	
        $function = new Functions();
        $phone = $this->request->data['mobile'];
        if(substr($phone,0,1)=='+'){
        $phone = str_replace("+","",$phone);
        }
        $data = array(
         'phone'=>$phone,
         'phone_code'=>$signinCode,
         
        );
        $conditions = array(
         'key' => $key 
        );
        Apps::update($data,$conditions);   
        $msg = 'Please enter GreenCoinX mobile verification code: '.$signinCode.'.';
        $returnvalues = $function->twilio($phone,$msg,$signinCode);	 // Testing if it works 
        return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'phone_code'=>$signinCode,
        'phone'=>$phone,
    				'Server'=>md5($_SERVER["SERVER_ADDR"]),
        'Refer'=>md5($_SERVER["REMOTE_ADDR"])
       )));
       }
      }
    }

    public function saveaadhar($key=null){
    
      if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Key missing!'
       )));
      }else{
       $conditions = array(
        'key' => $key 
       );
      
       $record = Apps::find('first',array(
        'conditions'=>$conditions
       ));
       
       if(count($record)!=0){
        $data = array(
         'process'=>'save aadhar'
        );
        Apps::update($data,$conditions);   
       }else{
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'Invalid Key!'
        ))); 
       }
       
       if($this->request->data){
        if($this->request->data['aadhar_fname']==null || $this->request->data['aadhar_fname']=="") {
         return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'First Name Required!'
         )));
        }
        if($this->request->data['aadhar_lname']==null || $this->request->data['aadhar_lname']=="") {
         return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Last Name Required!'
         )));
        }     
        
        
        return $this->render(array('json' => array('success'=>1,
        'now'=>time(),
        'error'=>'Data saved!',
        ))); 
        
       }else{
        
        return $this->render(array('json' => array('success'=>0,
        'now'=>time(),
        'error'=>'No Post!'
        ))); 
       }
      }
    }
    
    /* get All Step Kyc info*/
    public function getKycDocument($key=null){

    if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Key missing!'
        )));
    }else{
       $conditions = array(
        'key' => $key 
       );
    
       $record = Apps::find('first',array(
        'conditions'=>$conditions
       ));
     
       if(count($record)!=0){
            $conditions = array(
              'hash' => $key 
             );
          
             $kyc = kycDocuments::find('first',array(
              'conditions'=>$conditions
             ));
           
             if(count($kyc)!=0){
                 return $this->render(array('json' => array('success'=>1,
                    'now'=>time(),
                    'result'=>'Kyc Documents',
                    'kyc'=> $kyc,
                  )));
             }else{
                return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'not found kyc documents!'
                ))); 
             }
       }else{
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Invalid Key!'
          ))); 
       }
    } 
    }

    // General Function Calling App Site
    public function saveKycDocument($key=null){
    
    if($key==null || $key==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Key missing!'
        )));
    }
    else if($this->request->data['step']==null || $this->request->data['step']==""){
        return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'step name missing!'
        )));    
    }
    else{
      $record = Apps::find('first',array(
          'conditions'=>array('key' => $key)
        ));
       
      if(count($record)!=0){

         $kyc = KYCDocuments::find('first',array(
          'conditions'=>array('hash' => $key)
         ));

         $step = $this->request->data['step'];
         switch ($step) {
             case 'basic':
               $this->kycBasicInfo($key,$kyc);
               break;
             case 'address':
               $this->kycAddressInfo($key,$kyc);
               break;
             case 'passport':
               $this->kycPassportInfo($key,$kyc);
               break;
             case 'aadhar':
               $this->kycAadharInfo($key,$kyc);
               break;
             case 'texation':
               $this->kycTexationInfo($key,$kyc);
               break;
             case 'drivinglicence':
               $this->kycDrivingLicenceInfo($key,$kyc);
               break;  
             default:
               echo "No Any Step";
               break;
           }      
       }else{
          return $this->render(array('json' => array('success'=>0,
          'now'=>time(),
          'error'=>'Invalid Key!'
          ))); 
       }
    } 
    }

    //Step1
    private function kycBasicInfo($key,$kyc){
        
        if($this->request->data['firstname']==null || $this->request->data['firstname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'First Name Required!'
           )));
        } 

        if($this->request->data['middlename']==null || $this->request->data['middlename']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Middle Name Required!'
           )));
        } 

        if($this->request->data['lastname']==null || $this->request->data['lastname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Last Name Required!'
           )));
        } 

        if($this->request->data['dob']==null || $this->request->data['dob']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Date Of Birth Required!'
           )));
        }

        extract($this->request->data);

        if(isset($this->request->data['file'])){
             
            foreach ($this->request->data['file'] as $img_key => $value) { 
            
                $status = $this->upload($key,$img_key,$type[$img_key]);

                if($status['upload'] == 0){
                   return $this->render(array('json' => array('success'=>0,
                    'now'=>time(),
                    'error'=>$status['msg']
                   ))); 
                }
            }
        }

            
            $basic = array(
                'first' => $firstname,
                'middle' =>$middlename,
                'last' =>$lastname
              );

            $birth = array(
                'date' => $dob
              );
            
            $data = array(
                'details.Name' => $basic,
                'details.Birth' => $birth
            );
            
            $conditions = array('hash' => $key);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Basic Information save success',
              'first' => $firstname,
              'middle' =>$middlename,
              'last' =>$lastname,
              'dob' => $dob,
              'hash' => $key
            ))); 
    }

    //Step2
    private function kycAddressInfo($key,$kyc){
        
        if($this->request->data['address']==null || $this->request->data['address']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Required!'
           )));
        } 

        if($this->request->data['street']==null || $this->request->data['street']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Street Required!'
           )));
        } 

        if($this->request->data['city']==null || $this->request->data['city']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'City Required!'
           )));
        } 

        if($this->request->data['zip']==null || $this->request->data['zip']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Zip Required!'
           )));
        }

        if($this->request->data['state']==null || $this->request->data['state']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'State Required!'
           )));
        }

        if($this->request->data['country']==null || $this->request->data['country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Country Required!'
           )));
        }
    
            extract($this->request->data);

            if(isset($this->request->data['file'])){
             
              foreach ($this->request->data['file'] as $img_key => $value) { 
              
                  $status = $this->upload($key,$img_key,$type[$img_key]);

                  if($status['upload'] == 0){
                     return $this->render(array('json' => array('success'=>0,
                      'now'=>time(),
                      'error'=>$status['msg']
                     ))); 
                  }
              }
          }



            $address_info = array(
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country
              );

           
            
            $data = array(
                'details.Address' => $address_info
            );
            
            $conditions = array('hash' => $key);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Address Information save success',
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'hash' => $key
            ))); 
    }

    //Step3
    private function kycPassportInfo($key,$kyc){
          
        if($this->request->data['firstname']==null || $this->request->data['firstname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport First Name Required!'
           )));
        } 

        if($this->request->data['lastname']==null || $this->request->data['lastname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Last Name Required!'
           )));
        } 

        if($this->request->data['middlename']==null || $this->request->data['middlename']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Middel Name Required!'
           )));
        }

        if($this->request->data['dob']==null || $this->request->data['dob']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Date of Birth Required!'
           )));
        }

        if($this->request->data['address']==null || $this->request->data['address']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Required!'
           )));
        } 

        if($this->request->data['street']==null || $this->request->data['street']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Street Required!'
           )));
        } 

        if($this->request->data['city']==null || $this->request->data['city']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'City Required!'
           )));
        } 

        if($this->request->data['zip']==null || $this->request->data['zip']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Zip Required!'
           )));
        }

        if($this->request->data['state']==null || $this->request->data['state']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'State Required!'
           )));
        }

        if($this->request->data['country']==null || $this->request->data['country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Country Required!'
           )));
        }

        if($this->request->data['no']==null || $this->request->data['no']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Number Required!'
           )));
        }

        if($this->request->data['expiry']==null || $this->request->data['expiry']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Expiry Required!'
           )));
        }

        if($this->request->data['pass_country']==null || $this->request->data['pass_country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Passport Country Required!'
           )));
        }

    
            extract($this->request->data);

            if(isset($this->request->data['file'])){
             
              foreach ($this->request->data['file'] as $img_key => $value) { 
                  if($this->request->data['file'][$img_key]['name'] != ''){
                    $status = $this->upload($key,$img_key,$type[$img_key]);
                    if($status['upload'] == 0){
                       return $this->render(array('json' => array('success'=>0,
                        'now'=>time(),
                        'error'=>$status['msg']
                       ))); 
                    }
                  }  
              }
          }



            $passport_info = array(
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'dob' => $dob,
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'no' =>$no,
                'expiry' =>$expiry,
                'pass_country' =>$pass_country
              );

           
            
            $data = array(
                'details.Passport' => $passport_info
            );
            
            $conditions = array('hash' => $key);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Passport Information save success',
                'firstname' => $address,
                'middlename' => $address,
                'lastname' => $address,
                'dob' => $dob,
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'no' =>$no,
                'expiry' =>$expiry,
                'pass_country' =>$pass_country,
                'hash' => $key
            ))); 
    }

    //Step4
    private function kycAadharInfo($key,$kyc){
        if($this->request->data['firstName']==null || $this->request->data['firstName']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Aadhar First Name Required!'
           )));
        } 

        if($this->request->data['lastName']==null || $this->request->data['lastName']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Aadhar Last Name Required!'
           )));
        } 

        if($this->request->data['no']==null || $this->request->data['no']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Aadhar Number Required!'
           )));
        }

        if(!is_numeric($this->request->data['no'])) {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Aadhar Number Must Be Numeric!'
           )));
        }
          
        if(strlen($this->request->data['no']) != 16) {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Aadhar Number Length Must Be 16!'
           )));
        }
      

        extract($this->request->data);  
        $middleName = ($middleName == '') ? '' : $middleName;

            if(isset($this->request->data['file'])){
             
              foreach ($this->request->data['file'] as $img_key => $value) { 
                  if($this->request->data['file'][$img_key]['name'] != ''){
                    $status = $this->upload($key,$img_key,$type[$img_key]);
                    if($status['upload'] == 0){
                       return $this->render(array('json' => array('success'=>0,
                        'now'=>time(),
                        'error'=>$status['msg']
                       ))); 
                    }
                  }  
              }
            }



            $aadhar_info = array(
                'firstName' => $firstName,
                'middleName' => $middleName,
                'lastName' => $lastName,
                'no' => $no,
            );

           
            
            $data = array(
                'details.Aadhar' => $aadhar_info
            );
            
            $conditions = array('hash' => $key);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Aadhar Information save success',
                'firstName' => $firstName,
                'middleName' => $middleName,
                'lastName' => $lastName,
                'no' => $no,
                'hash' => $key
            ))); 
    }

    //Step5
    private function kycTexationInfo($key,$kyc){
        
        if($this->request->data['firstName']==null || $this->request->data['firstName']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Tax First Name Required!'
           )));
        } 

        if($this->request->data['middleName']==null || $this->request->data['middleName']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Tax Middel Name Required!'
           )));
        } 

        if($this->request->data['lastName']==null || $this->request->data['lastName']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Tax Last Name Required!'
           )));
        } 

        if($this->request->data['dateofBirth']==null || $this->request->data['dateofBirth']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Tax Date of Birth Required!'
           )));
        }

        if($this->request->data['id']==null || $this->request->data['id']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Tax Id Required!'
           )));
        }

        // if(strlen($this->request->data['id']) != 16) {
        //    return $this->render(array('json' => array('success'=>0,
        //     'now'=>time(),
        //     'error'=>'Tax id Length Must Be 16!'
        //    )));
        // }
      

        extract($this->request->data);  
        
            if(isset($this->request->data['file'])){
             
              foreach ($this->request->data['file'] as $img_key => $value) { 
                  if($this->request->data['file'][$img_key]['name'] != ''){
                    $status = $this->upload($key,$img_key,$type[$img_key]);
                    if($status['upload'] == 0){
                       return $this->render(array('json' => array('success'=>0,
                        'now'=>time(),
                        'error'=>$status['msg']
                       ))); 
                    }
                  }  
              }
            }

            $tax = array(
                'firstName' => $firstName,
                'middleName' => $middleName,
                'lastName' => $lastName,
                'dateofBirth'=> $dateofBirth,
                'id'=> $id
            );

          
            $data = array(
                'details.Tax' => $tax
            );
            
            $conditions = array('hash' => $key);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Tax Information save success',
                'firstName' => $firstName,
                'middleName' => $middleName,
                'lastName' => $lastName,
                'dateofBirth'=> $dateofBirth,
                'id'=> $id,
                'hash' => $key
            ))); 
    }

    //Step6
    private function kycDrivingLicenceInfo($key,$kyc){
          
        if($this->request->data['firstname']==null || $this->request->data['firstname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Licence First Name Required!'
           )));
        } 

        if($this->request->data['lastname']==null || $this->request->data['lastname']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Licence Last Name Required!'
           )));
        } 

        if($this->request->data['middlename']==null || $this->request->data['middlename']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Licence Middel Name Required!'
           )));
        }

        if($this->request->data['dob']==null || $this->request->data['dob']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Licence Date of Birth Required!'
           )));
        }

        if($this->request->data['address']==null || $this->request->data['address']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Required!'
           )));
        } 

        if($this->request->data['street']==null || $this->request->data['street']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Address Street Required!'
           )));
        } 

        if($this->request->data['city']==null || $this->request->data['city']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'City Required!'
           )));
        } 

        if($this->request->data['zip']==null || $this->request->data['zip']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Zip Required!'
           )));
        }

        if($this->request->data['state']==null || $this->request->data['state']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'State Required!'
           )));
        }

        if($this->request->data['country']==null || $this->request->data['country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Country Required!'
           )));
        }

        if($this->request->data['no']==null || $this->request->data['no']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Licence Number Required!'
           )));
        }

        if($this->request->data['expiry']==null || $this->request->data['expiry']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Licence Expiry Required!'
           )));
        }

        if($this->request->data['licence_country']==null || $this->request->data['licence_country']=="") {
           return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Licence Country Required!'
           )));
        }

    
            extract($this->request->data);

            if(isset($this->request->data['file'])){
             
              foreach ($this->request->data['file'] as $img_key => $value) { 
                  if($this->request->data['file'][$img_key]['name'] != ''){
                    $status = $this->upload($key,$img_key,$type[$img_key]);
                    if($status['upload'] == 0){
                       return $this->render(array('json' => array('success'=>0,
                        'now'=>time(),
                        'error'=>$status['msg']
                       ))); 
                    }
                  }  
              }
          }



            $licence = array(
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'dob' => $dob,
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'no' =>$no,
                'expiry' =>$expiry,
                'licence_country' =>$licence_country
              );

           
            
            $data = array(
                'details.Driving' => $licence
            );
            
            $conditions = array('hash' => $key);
            KYCDocuments::update($data, $conditions);

            return $this->render(array('json' => array('success'=>1,
              'now'=>time(),
              'result'=>'Driving Licence Information save success',
                'firstname' => $address,
                'middlename' => $address,
                'lastname' => $address,
                'dob' => $dob,
                'address' => $address,
                'street' =>$street,
                'city' =>$city,
                'zip' =>$zip,
                'state' =>$state,
                'country' =>$country,
                'no' =>$no,
                'expiry' =>$expiry,
                'licence_country' =>$licence_country,
                'hash' => $key
            ))); 
    }
    
    /* Common Function */
    private function upload($id=null,$img_key=0,$type="passport"){
          $response = [];
          $uploadOk = 1;

          $document = KYCDocuments::find('first',array(
              'conditions'=>array(
                  'hash'=>$id,
                  )
          ));

          // $countries = Countries::find('all',array(
          //     'order'=>array('order'=>1)
          // ));
          
          if(count($document)==0){
              $response['msg'] = "Documents not found";
              $response['upload'] = 0;
              $uploadOk = 0;
          }

          if ($this->request->data && $uploadOk == 1) {
              $extension = pathinfo($this->request->data['file'][$img_key]['name'],PATHINFO_EXTENSION);
            
              $allowed = array('jpg', 'jpeg', 'png', 'gif');
              if(!in_array(strtolower($extension), $allowed)){
                      $response['msg'] = "Sorry, only JPG, PNG, GIF file is allowed.";
                      $response['upload'] = 0;
                      $uploadOk = 0;
              }
              if(strtolower($extension)=='pdf'){
                      $response['msg'] = "Please do not upload PDF file.";
                      $response['upload'] = 0;
                      $uploadOk = 0;
              }
              $size = round($this->request->data['file'][$img_key]['size']/1024/1024,2)            ;
              if($size >= 10){
                      $response['msg'] = "Sorry, File too large, should be less than 10 MB. It is ".$size."MB!";
                      $response['upload'] = 0;
                      $uploadOk = 0;
              }
              if($uploadOk==1){ 

                   //$option = $this->request->data['option'];
                  $option = $type;
              
                  $data = array(
                      $option => $this->request->data['file'][$img_key],
                      $option.'.verified'=>'No',
                      $option.'.IP'=>$_SERVER['REMOTE_ADDR'],
                  );

                  $field = 'details_'.$type.'_id';
                  $remove = KYCFiles::remove('all',array(
                      'conditions'=>array( $field => (string)$document['_id'])
                  ));
                  $file = $this->request->data['file'];
                  
                  $path = LITHIUM_APP_PATH. '\\webroot\\documents\\';
                  
                  $resizedFile = $path.$this->request->data['file'][$img_key]['name'];
                  
                  //$resizedFileServer = 'https://'.$_SERVER['SERVER_NAME'].'/documents/'.$this->request->data['file']['name'];
                  
                  $resize = $this->smart_resize_image($this->request->data['file'][$img_key]['tmp_name'], null,1024 , 0 , true , $resizedFile , false , false ,100 );

                  if($resize == false){
                      $msg = "File format different, cannot verify.";
                      $uploadOk = 0;
                  }

                  $fileData = array(
                          'file' => file_get_contents($resizedFile),
                          'filename'=>$this->request->data['file'][$img_key]['name'],
                          'metadata'=>array('filename'=>$this->request->data['file'][$img_key]['name']),
                          'details_'.$type.'_id' => (string)$document['_id']
                  );
                  
                  KYCDocuments::find('first',
                       array('conditions'=>array('_id'=> (string)$document['_id']))
                   )->save($data);

                  $file = KYCFiles::create();
                  if ($file->save($fileData)) {
                      $response['msg'] = "Upload OK";
                      $response['upload'] = 1; 
                  }
                  unlink($resizedFile) ;

                  
              }            
          }

          return $response;

          /* Use Image Upload */
          // $this->getImage($id,$type);
          // $this->removeImage($id,$type);
        


          // if(($document['details']['Address']['country']=='IND')){
          //     $image_aadhar = KYCFiles::find('first',array(
          //         'conditions'=>array('details_aadhar_id'=>(string)$document['_id'])
          //     ));
          //     if($image_aadhar['filename']!=""){
          //             $imagename_aadhar = $image_aadhar['_id'].'_'.$image_aadhar['filename'];
          //                 $path = LITHIUM_APP_PATH . '/webroot/documents/'.$imagename_aadhar;
          //             file_put_contents($path, $image_aadhar->file->getBytes());
          //     }
          // }else{
          //     $image_aadhar = "";
          //     $imagename_aadhar = "";
          // }
    }

     /* Common Function */
    private function getImage($id=null,$type='passport'){
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
                     $return_path = 'http://hirath.org/documents/'.$image_name_name;
                     file_put_contents($path, $image_name->file->getBytes());
                    return $return_path;
          }
    }

     /* Common Function */
    private function removeImage($id=null,$type='passport'){
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
                     unlink($path);
                     return true;
          }
    }

     /* Common Function */
    private function smart_resize_image($file,$string= null,$width= 0,$height= 0,$proportional= false,$output= 'file',
            $delete_original= true,$use_linux_commands = false,$quality = 100) {

      
              if ( $height <= 0 && $width <= 0 ) return false;
              if ( $file === null && $string === null ) return false;
              # Setting defaults and meta
              $info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
              $image                        = '';
              $final_width                  = 0;
              $final_height                 = 0;
              //                print_r($info);
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

} // KycController end
?>