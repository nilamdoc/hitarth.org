<?php
namespace app\controllers;
use app\models\Users;
use app\models\Details;
use app\models\Transactions;
use lithium\storage\Session;
use lithium\util\String;
use lithium\data\Connections;
use app\extensions\action\Functions;

class BalanceController extends \lithium\action\Controller {
	public function index(){
		if(in_array('json',$this->request->params['args'])){
			Session::delete('default');								
			$json = true;
			$email = strtolower($this->request->params['args'][1]);
			$onecode = $this->request->params['args'][2];
		}else{
			$user = Session::read('default');
			$id = $user['id'];
		} //if json
		if($this->request->data){
			Session::delete('default');						
			$email = strtolower($this->request->data['email'])		;
			$onecode = $this->request->data['loginpassword']		;			
		}else{
			$user = Session::read('default');
			$id = $user['id'];
		}	// if request data


			$users = Users::find('all',array('conditions'=>array('email'=>$email)));
			foreach($users as $user){
				$id = $user['_id'];
				$data = array(
					'email' => $user['email'],
					'id'=> $id,
				);
			} // foreach use
			if($id!=""){
				$detail = Details::find('first',array(
						'conditions'=>array(
						'user_id'=>(string)$id,
						'oneCode' => $onecode
						)
				));
				if(count($detail)!=0){				
					$dataOne = array(
						'oneCodeused'=>'Yes'
					);
					$details = Details::find('all',array(
						'conditions' => array(
							'user_id'=>(string)$id,
						)
					))->save($dataOne);
					$transact = Transactions::find('first',array(
						'conditions'=>array(
							'user_id' => (string)$id,
							'Action' => 'FirstTime',
						)
					));
					if(count($transact)==0){
						$dataTransact = array(
							'DateTime' => new \MongoDate(),
							'user_id' => (string)$id,
							'IP' => $_SERVER['REMOTE_ADDR'],
							'Action' => 'FirstTime',
							'Amount' => (float) 100
						);					
						$transact = Transactions::create()->save($dataTransact);
						$details = Details::find('first',array(
							'conditions' => array(
								'user_id'=>(string)$id,
							)
						));
						$dataBalance = array(
							'balance.XGC' => (float)$details['balance']['XGC'] + (float)100,
						);
						$details = Details::find('all',array(
							'conditions' => array(
								'user_id'=>(string)$id,
							)
						))->save($dataBalance);
					} // if count transact

					$detail = Details::find('first',array(
							'conditions'=>array(
							'user_id'=>(string)$id,
							)
					));
					
					if($detail['balance']['XGC']==0){
						$dataXGC = array(
							'balance.XGC'=>(float) 100
						);
											
						$details = Details::find('all',array(
							'conditions' => array(
								'user_id'=>(string)$id,
							)
						))->save($dataXGC);
						$dataTransact = array(
							'DateTime' => new \MongoDate(),
							'user_id' => (string)$id,
							'IP' => $_SERVER['REMOTE_ADDR'],
							'Action' => 'FirstTime',
							'Amount' => (float) 100
						);					
						$transact = Transactions::create()->save($dataTransact);
					} // if detail balance
					Session::write('default',$data);
					$userIn = Session::read('default');
				} // if count detail
			}else{
			} // if id != null
			$transactions = Transactions::find('all',array(
				'conditions'=>array('user_id'=>(string)$id),
				'order'=>array('DateTime'=>-1)
			));

		$mongodb = Connections::get('default')->connection;
		$Total = Transactions::connection()->connection->command(array(
		'aggregate' => 'transactions',
		'pipeline' => array( 
									array( '$project' => array(
											'_id'=>0,
											'Amount' => '$Amount',
											'Fee' => '$Fee',
											'user_id'=>'$user_id'
									)),
			array('$match'=>array('user_id'=>(string)$id)),							
			array('$group' => array( '_id' => array(
							'user_id'=>'$user_id'
						),
					'Amount' => array('$sum' => '$Amount'),  
					'Fee' => array('$sum' => '$Fee'),  					
					)),
			)
		));

		$transactions = Transactions::find('all',array(
			'conditions'=>array('user_id'=>(string)$id),
			'order'=>array('DateTime'=>-1),
			'limit'=>50
		));
		$details = Details::find('first',array(
			'conditions' => array(
				'user_id'=>(string)$id,
			)
		));
		if($json == true){
			return $this->render(array('json' =>  
				compact('userIn','transactions','Total','details'),
			'status'=> 200));
		} // if json

		return compact('userIn','transactions','Total','details');
	} // function

} // class
?>