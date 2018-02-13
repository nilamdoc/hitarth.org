<?php
namespace app\controllers;
use app\models\Wallets;
use app\models\Countries;
use app\models\Templates;
use app\models\KYCDetails;
use app\models\KYCCompanies;
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
use app\models\Apps;

class XgcController extends \lithium\action\Controller {
	public function index(){
  return $this->render(array('json' => array('success'=>0,
		'now'=>time())));
  
  $kycCompanies = KYCCompanies::count();
  $kycCountries = KYCCountries::count();
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
  
  
  
		)));
	}

  public function transactionhistoryTEST($key = null){
      extract($this->request->data); 
      
      if($key==null || $key == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
        )));
      }

      if($walletid==null || $walletid == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Wallet id missing!'
        )));
      }

      if($limit==null || $limit == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Limit missing!'
        )));
      }

      if($offset==null || $offset == ""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Offset missing!'
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
        $chkwallet = XGCUsers::find('first',array('conditions' => array('hash'=>$record['hash'],'walletid'=>$walletid)));
        if(count($chkwallet) > 0){
          $COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
          // $walletid = "Default"; 
          $traslist = $COINGREEN->listtransactions($walletid,(int)$limit,(int)$offset);

          if(!empty($traslist['error']))
          {
            return $this->render(array('json' => array('success'=>0,
                  'now'=>time(),
                  'error'=>$traslist['error']
              )));
          }

          $globalArray = [];
          foreach ($traslist as $kl => $lists) {
            $innerArray = [];

            $xgc = XGCUsers::find('first',array('conditions' => array('greencoinAddress.0'=>$lists['address']))); 

            foreach ($lists as $kt => $list) {
              $innerArray[$kt] = $list;
            }
            $innerArray['greencoinwallet'] = $xgc['walletid'];
            $globalArray[$kl] =  $innerArray;
              
          }

          return $this->render(array('json' => array('success'=>1,
                'now'=>time(),
                'result'=>'success transaction list',
                'prfilehost' =>FTP_HOST."/profiles/",
                'transaction' => array_reverse($globalArray)
            )));
          }else{
          return $this->render(array('json' => array('success'=>0,
                'now'=>time(),
                'error'=>'Enter your valid wallet'
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
?>