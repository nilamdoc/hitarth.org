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

class SiiController extends \lithium\action\Controller {
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

	public function Rates($FirstCurrency="BTC",$SecondCurrency="USD") {

		$title = $FirstCurrency . "/" . $SecondCurrency;
		$back = strtolower($FirstCurrency . "_" . $SecondCurrency);		

		$Refresh = "No";
		
			$trades = SIITrades::find('first',array(
				'conditions' => array('trade'=>$title),
			));
			
			if($trades['refresh']==true || $trades['refresh']==1){
				$data = array(
				'refresh' => false
				);
				SIITrades::find('all',array(
					'conditions' => array('trade'=>$title)
				))->save($data);
				$Refresh = "Yes";
			}

//		$mongodb = Connections::get('default')->connection;
		$Rates = SIIOrders::connection()->connection->command(array(
			'aggregate' => 'orders',
			'pipeline' => array( 
				array( 
				'$project' => array(
					'_id'=>0,
					'Action' => '$Action',
					'PerPrice'=>'$PerPrice',					
					'Completed'=>'$Completed',					
					'FirstCurrency'=>'$FirstCurrency',
					'SecondCurrency'=>'$SecondCurrency',	
					'TransactDateTime' => '$Transact.DateTime',
				)),
				array('$match'=>array(
					'Completed'=>'Y',					
					'FirstCurrency' => $FirstCurrency,
					'SecondCurrency' => $SecondCurrency,					
					)),
				array('$group' => array( '_id' => array(
							'year'=>array('$year' => '$TransactDateTime'),
							'month'=>array('$month' => '$TransactDateTime'),						
							'day'=>array('$dayOfMonth' => '$TransactDateTime'),												
//							'hour'=>array('$hour' => '$TransactDateTime'),
						),
					'min' => array('$min' => '$PerPrice'), 
					'max' => array('$max' => '$PerPrice'), 
				)),
				array('$sort'=>array(
					'_id.year'=>-1,
					'_id.month'=>-1,
					'_id.day'=>-1,					
//					'_id.hour'=>-1,					
				)),
				array('$limit'=>1)
			)
		));

//		print_r($Rates['result']);
		foreach($Rates['result'] as $r){
			$Low = $r['min'];
			$High = $r['max'];			
		}

		$Last = SIIOrders::find('all',array(
			'conditions'=>array(
				'Completed'=>'Y',					
				'FirstCurrency' => $FirstCurrency,
				'SecondCurrency' => $SecondCurrency,					
				),
			'limit'=>1,
			'order'=>array('Transact.DateTime'=>'DESC')
		));
		foreach($Last as $l){
			$LastPrice = $l['PerPrice'];
		}
		
		$TotalOrders = SIIOrders::connection()->connection->command(array(
			'aggregate' => 'orders',
			'pipeline' => array( 
				array( '$project' => array(
					'_id'=>0,
					'Action'=>'$Action',					
					'Amount'=>'$Amount',
					'Completed'=>'$Completed',					
					'FirstCurrency'=>'$FirstCurrency',
					'SecondCurrency'=>'$SecondCurrency',	
					'TransactDateTime' => '$Transact.DateTime',					
					'TotalAmount' => array('$multiply' => array('$Amount','$PerPrice')),
				)),
				array('$match'=>array(
					'Completed'=>'Y',	
					'Action'=>'Buy',										
					'FirstCurrency' => $FirstCurrency,
					'SecondCurrency' => $SecondCurrency,					
					)),
				array('$group' => array( '_id' => array(
					'year'=>array('$year' => '$TransactDateTime'),
					'month'=>array('$month' => '$TransactDateTime'),						
					),
					'Amount' => array('$sum' => '$Amount'), 
					'TotalAmount' => array('$sum' => '$TotalAmount'), 
				)),
				array('$sort'=>array(
					'_id.year'=>-1,
					'_id.month'=>-1,
				)),
				array('$limit'=>1)
			)
		));
//		print_r($SecondCurrency);
		return $this->render(array('json' => array(
			'Refresh'=> $Refresh,
			'URL'=> $URL,
			'Low'=> number_format($Low,5),
			'High' => number_format($High,5),
			'Last'=> number_format($LastPrice,5),			
			'VolumeFirst'=> number_format($TotalOrders['result'][0]['Amount'],5),
			'VolumeSecond'=> number_format($TotalOrders['result'][0]['TotalAmount'],5),
			'VolumeFirstUnit'=> $FirstCurrency,			
			'VolumeSecondUnit'=> $SecondCurrency,
		)));
	}
	
}
?>