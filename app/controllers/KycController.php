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
 
}
?>