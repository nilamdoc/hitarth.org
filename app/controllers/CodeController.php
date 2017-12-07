<?php
namespace app\controllers;
use app\models\Wallets;
use app\models\Countries;
use app\models\Ipv6s;
use app\extensions\action\GoogleAuthenticator;
use lithium\util\Validator;
use app\extensions\action\Functions;
use \lithium\template\View;


class CodeController extends \lithium\action\Controller {
	public function index($ip = null,$kyc_id=null){
		$WalletCount = Wallets::count();
		$ga = new GoogleAuthenticator();
		$key = $ga->createSecret(64);
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
		'key'=>$key,
		'kyc_id'=>$kyc_id,
		'DateTime' => new \MongoDate(),
		'IPinfo' => $IPResponse,
		'ISDPhone'=>$country['Phone'],
		);
		$wallets = Wallets::create()->save($data);
		return $this->render(array('json' => array('success'=>1,
			'now'=>time(),
			'code'=>($key),
			'IP'=>$IPResponse->ip,
			'city'=>$IPResponse->city,
			'org'=>$IPResponse->org,
			'latlon'=>$IPResponse->loc,
			'country'=>$IPResponse->country,
			'phone'=>$country['Phone'],
		)));
		}else{
			$ipcountry = Ipv6s::find('first',array(
				'conditions' => array('from'=>array('$lt'=>$ip),'to'=>array('$gte'=>$ip)),
			));
				$country = Countries::find('first',array(
				'conditions' => array('ISO'=>$ipcountry['ISO'])
			));
			$data = array(
			'key'=>$key,
			'kyc_id'=>$kyc_id,
			'DateTime' => new \MongoDate(),
			'IPinfo.ip' => $ip,
			'ISDPhone'=>$country['Phone'],
			'IPinfo.country'=>$ipcountry['ISO'],
			);
			$wallets = Wallets::create()->save($data);
			return $this->render(array('json' => array('success'=>1,
			'now'=>time(),
			'code'=>($key),
			'IP'=>$ip,
			'city'=>null,
			'org'=>null,
			'latlon'=>null,
			'country'=>$ipcountry['country'],
			'phone'=>$country['Phone'],
		)));
		}
		
		
	}
	public function email($email=null,$code=null){
		$ga = new GoogleAuthenticator();
		if($email==null || $email==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Email missing!'
			)));
		}
		if($code==null || $code==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Code missing!'
			)));
		}
		if(Validator::rule('email',$email)==""){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Email not correct!'				
			)));
		}		
		$wallet = Wallets::find('first',array(
			'conditions'=>array('key'=>$code)
		));
		if(count($wallet)==0){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Code incorrect!'				
			)));
		}
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
		$conditions = array("key"=>$code);

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
			'now'=>time(),
//			'oneCode'=> $oneCode,
			'server'=> md5($_SERVER[SERVER_ADDR]),
			'email'=>'Code sent to email!'
		)));
	}
	
	public function phone ($phone=null,$code=null){
		$ga = new GoogleAuthenticator();
		if($phone==null || $phone==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Phone missing!'
			)));
		}
		if($code==null || $code==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Code missing!'
			)));
		}
		if(Validator::rule('intphone',$phone)==""){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Phone not correct!'				
			)));
		}		
		$wallet = Wallets::find('first',array(
			'conditions'=>array('key'=>$code)
		));
		if(count($wallet)==0){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Code incorrect!'				
			)));
		}
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
		$conditions = array("key"=>$code);
		$wallet = Wallets::update($data,$conditions);

		

		$function = new Functions();
		if(substr($phone,0,3)=='+91'){
		$msg = 'Please enter GreenCoin verification code: '.$twoCode.' on your GreenCoinX client.';
		$phone = str_replace("+","",$phone);
			$returnvalues = $function->SMS($phone,$msg);
			$returnvalues = $function->twilio($phone,$msg,$twoCode);	 // Testing if it works 
			//$function->ThroughFile($phone,$msg,$twoCode);				
		}else{
			$msg = 'Please enter GreenCoinX mobile phone verification code: '.$twoCode;
			$phone = str_replace("+","",$phone);
//			$returnvalues = $function->WorldSMS($phone,$msg);	
			$returnvalues = $function->send_sms_infobip($phone,$msg);	
					
			$returnvalues = $function->twilio($phone,$msg,$twoCode);	 // Testing if it works 
//			$function->ThroughFile($phone,$msg,$twoCode);	
					
		}

			return $this->render(array('json' => array('success'=>1,
				'now'=>time(),
				'phone'=>'Code sent to phone!'
			)));

			

	}
	
	public function say($code){
		$newcode = '';
		for($i=0;$i<=strlen($code);$i++){
			$newcode = $newcode . substr($code,$i,1).',,,,,';
		}
		
		$layout = false;
	$view  = new View(array(
		'paths' => array(
			'template' => '{:library}/views/{:controller}/{:template}.{:type}.php',
			'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php',
		)
		));
		echo $view->render(
		'all',
		compact('newcode'),
		array(
			'controller' => 'code',
			'template'=>'say',
			'type' => 'xml',
			'layout' =>'default'
		)
		);	
		return $this->render(array('layout' => false));
//<Response>
//	<Say>Please enter GreenCoinX mobile phone verification code.
//	I repeat 3,4,5,6,9,1.
//	Again. 3,4,5,6,9,1.</Say>
//</Response>

		
	}
}
?>