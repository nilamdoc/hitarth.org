<?php 
namespace app\controllers;
use app\models\Wallets;
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\Coingreen;

class VerifyController extends \lithium\action\Controller {
	public function index(){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Email missing!'
			)));
	}
	public function email($email=null,$key=null,$code=null){
		$ga = new GoogleAuthenticator();
		if($email==null || $email==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Email missing!'
			)));
		}
		if($key==null || $key==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Key missing!'
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
			'conditions'=> array(
				'email'=>$email,
				'key'=>$key,
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
					'key'=>$key,
					'email'=>$email,
					'oneCode'=>$code
				);
				$data = array(
					'oneCodeused' => 'Yes'
				);
				Wallets::update($data,$conditions);
				return $this->render(array('json' => array('success'=>1,
					'now'=>time(),
					'email'=>'Email is verified!'
				)));
		}
		return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Something is incorrect!'				
		)));		
	}
	public function phone($phone=null,$key=null,$code=null){
		$ga = new GoogleAuthenticator();
		if($phone==null || $phone==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Phone missing!'
			)));
		}
		if($key==null || $key==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Key missing!'
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
			'conditions'=> array(
				'phone'=>$phone,
				'key'=>$key,
				'twoCode'=>$code,
			)
		));
		if(count($wallet)==0){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Something is incorrect!'				
			)));
		}else{
				$conditions = array(
					'key'=>$key,
					'phone'=>$phone,
					'twoCode'=>$code
				);
				$data = array(
					'twoCodeused' => 'Yes'
				);
				Wallets::update($data,$conditions);
				
				return $this->render(array('json' => array('success'=>1,
					'now'=>time(),
					'phone'=>'Phone is verified!'
				)));
		}
		return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Something is incorrect!'				
		)));
	}
	
	public function verified($key=null,$emailcode=null, $phonecode=null,$addresses=null,$extra=null){
		if($key==null || $key==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Key missing!'
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
		$coingreen = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
				
	$arrayAddress = explode(",",$addresses);
		foreach($arrayAddress as $address){
			if($address==null || $address==""){
				return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'GreenCoin address missing!'
				)));
			}

		$validateAddress = $coingreen->validateaddress($address);
			if($validateAddress['isvalid']==0 || $validateAddress==null || $validateAddress==''){
				return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Address incorrect!'
				)));
			}
		}
		
			$wallet = Wallets::find('first',array(
			'conditions'=> array(
				'key'=>$key,
				'oneCode'=>$emailcode,
				'twoCode'=>$phonecode,
				'oneCodeused'=>'Yes',
				'twoCodeused'=>'Yes',
				)));
			
			if($wallet['secret']){
				return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Already verified!!',
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
					'key'=>$key,
					'oneCode'=>$emailcode,
					'twoCode'=>$phonecode
				);

			$data = array(
					'addresses' => $arrayAddress,
					'amount' => (float)VERIFY_AMOUNT,
					'secret' => $secret,
					'extra'  => $extra
				);
	$txid = $coingreen->sendopreturn($address,$secret)		;
	
/*
	$send = new OP_Return();
	$send_address = $address;
	$send_amount = VERIFY_AMOUNT;
	$metadata = $wallet['email'].";".$wallet['phone'];

	$txid = $send->send($send_address, $send_amount, $secret);
*/	

	if (strlen($txid)==64){
				Wallets::update($data,$conditions);
//				$newtx = $coingreen->sendtoaddress($address,10, false); // removed 10 XGC after press release
			$wallet = Wallets::find('first',array(
			'conditions'=> array(
				'key'=>$key,
				'oneCode'=>$emailcode,
				'twoCode'=>$phonecode,
				'oneCodeused'=>'Yes',
				'twoCodeused'=>'Yes',
				)));

				return $this->render(array('json' => array('success'=>1,
					'now'=>time(),
					'msg'=>'Client verified',
					'secret'=>$secret,
					'email'=>$wallet['email'],
					'phone'=>$wallet['phone'],
					'txid'=>$txid,
					'txidbonus'=>$newtx
				)));
		}else{
				return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Not valid txid!'
				)));

		}
		
		return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Something is incorrect!'				
		)));
	}
	
	public function address($address=null){
		if($address==null || $address==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'GreenCoin address missing!'
			)));
		}
		
		$wallet = Wallets::find('first',array(
			'conditions'=>array(
			'addresses'=>$address,
			'secret'=>array('$ne'=>null)
			)
		));
		
		if(count($wallet)!=0){
			return $this->render(array('json' => array('success'=>1,
			'now'=>time(),
			'address'=>$address,
			'email'=>$wallet['email'],
//			'email' => 'Valid',
			'phone'=>$wallet['phone'],
//			'phone' => 'Valid',
			'ip'=>$wallet['IPinfo']['ip'],
			'country'=>$wallet['IPinfo']['country'],
//			'country'=>'Valid',
			'city'=>$wallet['IPinfo']['city'],
//			'city'=>'Valid',
			'DateTime'=>gmdate(DATE_RFC850,$wallet['DateTime']->sec),
//			'extra'=>$wallet['extra'],
			'extra'=> 'Valid',
			)));
		}
		
		
		return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Something is incorrect!'				
		)));

	}
public function getAddress($address=null){
		if($address==null || $address==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'GreenCoin address missing!'
			)));
		}
		
		$wallet = Wallets::find('first',array(
			'conditions'=>array(
			'addresses'=>$address,
			'secret'=>array('$ne'=>null)
			)
		));
		
		if(count($wallet)!=0){
			return $this->render(array('json' => array('success'=>1,
			'now'=>time(),
			'address'=>$address,
			'email'=>$wallet['email'],
			'phone'=>$wallet['phone'],
			'ip'=>$wallet['IPinfo']['ip'],
			'country'=>$wallet['IPinfo']['country'],
			'city'=>$wallet['IPinfo']['city'],
			'DateTime'=>gmdate(DATE_RFC850,$wallet['DateTime']->sec),
			'extra'=>$wallet['extra'],
			)));
		}
		
		
		return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Something is incorrect!'				
		)));

	}
	
	public function addaddress($secret=null,$address=null){
		if($address==null || $address==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'GreenCoin address missing!'
			)));
		}
		$coingreen = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
			$validateAddress = $coingreen->validateaddress($address);
			if($validateAddress['isvalid']==0 || $validateAddress==null || $validateAddress==''){
				return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Address incorrect!'
				)));
			}

		if($secret==null || $secret==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Something is wrong!'
			)));
		}
	
		$wallet = Wallets::find('first',array(
			'conditions'=> array(
				'secret'=>$secret,
		)));

		if(count($wallet)==0){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Key incorrect!'
			)));
		}
		
		$arrayAddresses = array();
		foreach($wallet['addresses'] as $add){
			array_push($arrayAddresses,$add);
		}
		array_push($arrayAddresses,$address);
		
			$data = array(
					'addresses' => $arrayAddresses,
				);

		
		$conditions = array(
					'secret'=>$secret,
				);
$txid = $coingreen->sendopreturn($address,$secret)		;
/*
	$send = new OP_Return();
	$send_address = $address;
	$send_amount = VERIFY_AMOUNT;
	$metadata = $wallet['email'].";".$wallet['phone'];

	$txid = $send->send($send_address, $send_amount, $secret);
*/	
	if(strlen($txid)==64){
				Wallets::update($data,$conditions);
				return $this->render(array('json' => array('success'=>"1",
					'now'=>time(),
					'msg'=>'Client verified',
					'secret'=>$secret,
					'txid'=>$txid,
					'server'=>md5($_SERVER[SERVER_ADDR])
				)));
		}else{
				return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Not valid txid!'
				)));

		}

		return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Something is incorrect!'				
		)));

		
	}
}
?>