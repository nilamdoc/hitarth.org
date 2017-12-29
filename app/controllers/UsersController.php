<?php
namespace app\controllers;
use app\models\Users;
use app\models\Details;
use app\models\Transactions;
use app\extensions\action\GoogleAuthenticator;

use app\extensions\action\Functions;

use lithium\security\Auth;
use lithium\storage\Session;
use lithium\util\String;
use MongoID;

use app\extensions\action\Coingreen;


class UsersController extends \lithium\action\Controller {

	public function SendPassword($email=null){
		$email = strtolower($email);
		$user = Users::find('first',array('conditions'=>array('email'=>$email)));
///// Add User if not present
		if(count($user)==0){
			$data = array(
				'email' => $email,
				'firstname' => '',
				'lastname' => ''
			);
			$Users = Users::create($data);
            $saved = $Users->save();
			if($saved==true){
				$ga = new GoogleAuthenticator();
				$data = array(
					'user_id'=>(string)$Users->_id,
					'key'=>$ga->createSecret(64),
					'secret'=>$ga->createSecret(64),
					'balance.XGC' => (float)0,
				);
				$Detail = Details::create()->save($data);
			}
		}
/////////////User added if not presnt		END
		$user = Users::find('first',array('conditions'=>array('email'=>$email)));

		$ga = new GoogleAuthenticator();
		$secret = $ga->createSecret(64);

		$details = Details::find('first',array(
				'conditions'=>array('user_id'=>(string)$user['_id'])
		));

		if($details['oneCodeused']=='Yes' || $details['oneCodeused']==""){
			$oneCode = $ga->getCode($secret);	
			$data = array(
				'oneCode' => $oneCode,
				'oneCodeused' => 'No'
			);
			$details = Details::find('all',array(
					'conditions'=>array('user_id'=>(string)$user['_id'])
			))->save($data);
		}
		$details = Details::find('first',array(
				'conditions'=>array('user_id'=>(string)$user['_id'])
		));
		$oneCode = $details['oneCode'];

/////////////////////////////////Email//////////////////////////////////////////////////
					$function = new Functions();
					$compact = array('user'=>$user,'oneCode'=>$oneCode);
					// sendEmailTo($email,$compact,$controller,$template,$subject,$from,$mail1,$mail2,$mail3)
					$from = array(NOREPLY => "noreply@".COMPANY_URL);
					$mail1 = MAIL_1;
					$mail2 = null;
					$function->sendEmailTo($email,$compact,'users','onecode',"Sign in password from ",$from,$mail1,$mail2);
/////////////////////////////////Email//////////////////////////////////////////////////				

		return $this->render(array('json' => array("Password"=>"Password sent to email","TOTP"=>$totp)));
	
	}
}
?>