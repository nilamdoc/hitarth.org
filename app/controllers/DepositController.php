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
use li3_qrcode\extensions\action\QRcode;


class DepositController extends \lithium\action\Controller {
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
		} //if json

		if($id!=""){
				$mongodb = Connections::get('default')->connection;
				$Total = Transactions::connection()->connection->command(array(
				'aggregate' => 'transactions',
				'pipeline' => array( 
											array( '$project' => array(
													'_id'=>0,
													'Amount' => '$Amount',
													'user_id'=>'$user_id'
											)),
					array('$match'=>array('user_id'=>(string)$id)),							
					array('$group' => array( '_id' => array(
									'user_id'=>'$user_id'
								),
							'Amount' => array('$sum' => '$Amount'),  
							)),
					)
				));
			$detail = Details::find('first',array(
					'conditions'=>array(
					'user_id'=>(string)$id,
					)
			));

				if($detail['address']==""){
					$coingreen = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
					$address = $coingreen->getnewaddress($user['email']);
					$data = array(
						'address' => $address,
						'newaddress' => 'No'
					);
					$detail = Details::find('first',array(
						'conditions'=>array(
							'user_id'=>(string)$id,
						)))->save($data);
				}else{
					$address = $detail['address'];
				}
			}else{
				return $this->redirect('/');	
			}
		if($detail['newaddress']=='Yes'){
			$coingreen = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
			$address = $coingreen->getnewaddress($user['email']);
			$data = array(
				'address' => $address,
				'newaddress' => 'No'				
			);
			$detail = Details::find('first',array(
				'conditions'=>array(
					'user_id'=>(string)$id,
				)))->save($data);
		}
			$qrcode = new QRcode();				
			$qrcode->png($address, QR_OUTPUT_DIR.$address.'.png', 'H', 7, 2);			
			$dir = QR_OUTPUT_RELATIVE_DIR;


		if($json == true){
			return $this->render(array('json' =>  
				compact('Total','address','user','dir'),
			'status'=> 200));
		}


	return compact('Total','address','user','dir')	;
	}
}
?>