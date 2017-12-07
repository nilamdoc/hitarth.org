<?php
namespace app\controllers;
use app\models\Users;
use app\models\Details;
use app\models\Transactions;
use lithium\storage\Session;
use lithium\util\String;
use lithium\data\Connections;
use app\extensions\action\Functions;
use app\extensions\action\Coingreen;

class WithdrawController extends \lithium\action\Controller {
	public function index(){
		if(in_array('json',$this->request->params['args'])){
			Session::delete('default');								
			$json = true;
			$email = strtolower($this->request->params['args'][1]);
			$user = Users::find('first',array('conditions'=>array('email'=>$email)));
			$id = $user['_id'];
		}else{
			$user = Session::read('default');
			$id = $user['id'];
		}

		if($id!=""){
			$details = Details::find('first',
				array('conditions'=>array('user_id'=> (string) $id)
				));
		
			$secret = $details['secret'];
			$userid = $details['user_id'];		
		}else{
			return $this->redirect('/');	
		}
		$txfee = 0.000005;			

		if($json == true){
			return $this->render(array('json' =>  
				compact('details','txfee'),
			'status'=> 200));
		}
		return compact('details','txfee');
	}

	public function XGCAddress($address = null){
		$coingreen = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
			$verify = $coingreen->validateaddress($address);
			return $this->render(array('json' => array(
			'verify'=> $verify,
		)));
	}

	public function payment(){
		if(in_array('json',$this->request->params['args'])){
			$json = true;
		}
	
		$user = Session::read('default');
		$id = $user['id'];
		$email = $user['email'];
		if($id==""){			return $this->redirect('/');			}
		if($this->request->data){
			$address = $this->request->data['coingreenaddress'];
			$amount = $this->request->data['amount'];
			$fee = $this->request->data['txFee'];
			
			$details = Details::find('first', array(
				'conditions'=>array('user_id'=> (string) $id)
			));
			if((float)$details['balance']['XGC']<=(float)$amount){
				$txmessage = "Not Sent! Amount does not match!";
				return compact('txmessage');
			}			
			if((float)$details['balance']['XGC']>(float)$amount){
				$coingreen = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
						$comment = "User: ".$email."; Address: ".$address."; Amount:".$amount.";";
						$settxfee = $coingreen->settxfee($fee);
						$txid = $coingreen->sendfrom('NilamDoctor', $address, (float)$amount,(int)1,$comment);
					if($txid!=null){
						$data = array(
							'DateTime' => new \MongoDate(),
							'TransactionHash' => $txid,
							'Added'=>false,
							'Paid'=> 'Yes',
							'IP'=> $_SERVER['REMOTE_ADDR'],
							'Transfer'=>$comment,
							'Action'=>'Withdraw',
							'Address'=>$address,
							'Amount'=>(float)-$amount,
							'Fee'=>(float)$fee,
							'Currency'=>'XGC',
							'user_id'=>(string)$id
						);							
						$tx = Transactions::create();
						$tx->save($data);							
						$txmessage = number_format($amount,8) . " XGC transfered to ".$address;
					
						$balance = (float)$details['balance']['XGC'] - (float)$amount;
						$balance = (float)($balance) - (float)$fee;

						$dataDetails = array(
							'balance.XGC' => (float)round($balance,8),
						);
						$details = Details::find('all',
							array(
									'conditions'=>array(
										'user_id'=> (string) $id
									)
						))->save($dataDetails);
					}
				}
		}
		if($json == true){
			return $this->render(array('json' =>  
				compact('txmessage'),
			'status'=> 200));
		}

		return compact('txmessage');
	}

}
?>