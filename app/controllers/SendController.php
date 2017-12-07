<?php
namespace app\controllers;
use app\models\Users;
use app\models\Details;
use app\models\Transactions;
use lithium\storage\Session;
use lithium\util\String;
use lithium\data\Connections;
use app\extensions\action\Functions;
use app\extensions\action\GoogleAuthenticator;

class SendController extends \lithium\action\Controller {
	public function index(){
		if(in_array('json',$this->request->params['args'])){
			$json = true;
			$fromEmail = strtolower($this->request->params['args'][1]);
			$user = Users::find('first',array('conditions'=>array('email'=>$fromEmail)));
			$id = $user['_id'];
			$fromID = $id;						
			$toEmail = strtolower($this->request->params['args'][2]);
			$toAmount = (float)$this->request->params['args'][3];
		}else{
			$user = Session::read('default');
			$id = $user['id'];
			$fromID = $id;			
		}
		if($this->request->data){
			$toEmail = strtolower($this->request->data['email']);
			$toAmount = $this->request->data['Amount'];
			$fromEmail = strtolower($user['email']);
		}	


			$NewUser = Users::find('all',array('conditions'=>array('email'=>$toEmail)));

			if(count($NewUser)==0){
				$data = array(
					'email' => $toEmail,
					'firstname' => '',
					'lastname' => ''
				);
				$Users = Users::create($data);
				$saved = $Users->save();

				if($saved==true){
					$toID = $Users->_id;				
					$ga = new GoogleAuthenticator();
					$data = array(
						'user_id'=>(string)$toID,
						'key'=>$ga->createSecret(64),
						'secret'=>$ga->createSecret(64),
						'balance.XGC' => (float)$toAmount,
					);
					$Detail = Details::create()->save($data);
				}
			}else{
				foreach($NewUser as $NU){
					$toID = $NU['_id'];
				}
				$details = Details::find('first',array(
					'conditions' => array(
						'user_id'=>(string)$toID,
					)
				));
				$dataBalance = array(
					'balance.XGC' => (float)$details['balance']['XGC'] + (float)$toAmount,
				);
				$details = Details::find('all',array(
					'conditions' => array(
						'user_id'=>(string)$toID,
					)
				))->save($dataBalance);
			
			}
			if($toEmail!="" && $toAmount!=""){
				$dataTransact = array(
					'DateTime' => new \MongoDate(),
					'user_id' => (string)$toID,
					'from_IP' => $_SERVER['REMOTE_ADDR'],
					'from_user_id' => (string)$fromID,
					'Action' => 'Received',
					'Amount' => (float) $toAmount,
				);					
				$transact = Transactions::create()->save($dataTransact);
				$dataTransact = array(
					'DateTime' => new \MongoDate(),
					'user_id' => (string)$fromID,
					'IP' => $_SERVER['REMOTE_ADDR'],
					'Action' => 'Send',
					'Amount' => (float) -($toAmount),
				);					
				$transact = Transactions::create()->save($dataTransact);
		
			$details = Details::find('first',array(
				'conditions' => array(
					'user_id'=>(string)$fromID,
				)
			));
			$dataBalance = array(
				'balance.XGC' => (float)$details['balance']['XGC'] - (float)$toAmount,
			);
			$details = Details::find('all',array(
				'conditions' => array(
					'user_id'=>(string)$fromID,
				)
			))->save($dataBalance);
			//Send email to both users....
					$function = new Functions();
					$dataEmail = array(
						'toEmail' => $toEmail,
						'toAmount' => $toAmount,
						'fromEmail' => $fromEmail,
					);
					$compact = array('data'=>$dataEmail);
					// sendEmailTo($email,$compact,$controller,$template,$subject,$from,$mail1,$mail2,$mail3)
					$from = array(NOREPLY => "noreply".COMPANY_URL);
					$mail1 = MAIL_1;
					$mail2 = null;
					$function->sendEmailTo($toEmail,$compact,'send','toemail',"XGC(CoinGreen) received!",$from,$mail1,$mail2);
					$function->sendEmailTo($fromEmail,$compact,'send','fromemail',"XGC(CoinGreen) send!",$from,$mail1,$mail2);					
			//Send email ends....			
//			return $this->redirect('/');			
	}
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
			}else{
				return $this->redirect('/');	
			}
		if($json == true){
			return $this->render(array('json' =>  
				compact('Total'),
			'status'=> 200));
		}
		
	return compact('Total')	;
	}
}
?>