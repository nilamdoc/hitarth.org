<?php
namespace app\controllers;
use app\models\Wallets;
use app\models\Countries;
use app\models\Templates;
use MongoID;	
use app\extensions\action\GoogleAuthenticator;
use app\extensions\action\OP_Return;
use lithium\util\Validator;
use app\extensions\action\Functions;
use app\extensions\action\Coingreen;

class SearchController extends \lithium\action\Controller {
	public function index(){
		return $this->render(array('json' => array('success'=>0,
		'now'=>time(),
		'error'=>'Email missing!'
		)));
	}
	
	public function email($email=null){
  if($email==null || $email==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Email missing!'
			)));
		}
		$wallet = Wallets::find('first',array(
			'conditions'=> array(
				'email'=>$email,
				'secret'=>array('$ne'=>null)
			),
			'order' => array('DateTime'=>-1)
		));		
		if(count($wallet)==0){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Email not registered with GreenCoinX!'				
			)));
		}else{
			$arrayAddresses = array();
			foreach($wallet['addresses'] as $add){
				$address = $add;
			}
			$phone = $wallet['phone']; 
			
				return $this->render(array('json' => array('success'=>2,
					'now'=>time(),
					'email'=>''.$email,
//					'phone'=>'Valid',
					'phone'=>$phone,
					'address'=>$address,
					'ip'=>$wallet['IPinfo']['ip'],
					'country'=>$wallet['IPinfo']['country'],
					'city'=>$wallet['IPinfo']['city'],
					'DateTime'=>gmdate(DATE_RFC850,$wallet['DateTime']->sec),
					'extra'=>$wallet['extra'],
					'Server'=>md5($_SERVER["SERVER_ADDR"]),
					'Refer'=>md5($_SERVER["REMOTE_ADDR"])
				)));
		}
	}

	public function phone($phone=null){
		if($phone==null || $phone==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Phone missing!'
			)));
		}
		$wallet = Wallets::find('first',array(
			'conditions'=> array(
				'phone'=>$phone,
				'secret'=>array('$ne'=>null)
			),
			'order' => array('DateTime'=>-1)
		));		
		if(count($wallet)==0){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Phone not registered with GreenCoinX!'				
			)));
		}else{
			$arrayAddresses = array();
			foreach($wallet['addresses'] as $add){
				$address = $add;
			}
			$email = $wallet['email'];
				return $this->render(array('json' => array('success'=>1,
					'now'=>time(),
					'email'=>''.$email,
					'phone'=>$phone,
					'address'=>$address,
					'ip'=>$wallet['IPinfo']['ip'],
					'country'=>$wallet['IPinfo']['country'],
					'city'=>$wallet['IPinfo']['city'],
					'DateTime'=>gmdate(DATE_RFC850,$wallet['DateTime']->sec),
					'extra'=>$wallet['extra'],
					'Server'=>md5($_SERVER[SERVER_ADDR]),
					'Refer'=>md5($_SERVER["REMOTE_ADDR"])
				)));
		}
	}
	public function address($address=null){
		if($address==null || $address==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Address missing!'
			)));
		}
		$wallet = Wallets::find('first',array(
			'conditions'=> array(
				'addresses'=>$address,
				'secret'=>array('$ne'=>null)
			),
			'order' => array('DateTime'=>-1)
		));		
		if(count($wallet)==0){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Address not registered with GreenCoinX!'				
			)));
		}else{
//			$arrayAddresses = array();
//			foreach($wallet['addresses'] as $add){
//				$address = $add;
//			}
			$email = $wallet['email'];
			$phone = $wallet['phone'];
				return $this->render(array('json' => array('success'=>1,
					'now'=>time(),
					'email'=>''.$email,
					'phone'=>$phone,
					'address'=>$address,
					'ip'=>$wallet['IPinfo']['ip'],
					'country'=>$wallet['IPinfo']['country'],
					'city'=>$wallet['IPinfo']['city'],
					'DateTime'=>gmdate(DATE_RFC850,$wallet['DateTime']->sec),
					'extra'=>$wallet['extra'],
					'Server'=>md5($_SERVER[SERVER_ADDR]),
					'Refer'=>md5($_SERVER["REMOTE_ADDR"])
				)));
		}
	}

	public function phoneno($phone=null){
		if($phone==null || $phone==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Phone missing!'
			)));
		}
		$wallet = Wallets::find('first',array(
			'conditions'=> array(
				'phone'=>'+'.$phone,
				'secret'=>array('$ne'=>null)
			),
			'order' => array('DateTime'=>-1)
		));		
		if(count($wallet)==0){
			return $this->render(array('json' => array('success'=>0,
				'now'=>time(),
				'error'=>'Phone not registered with GreenCoinX!'				
			)));
		}else{
			$arrayAddresses = array();
			foreach($wallet['addresses'] as $add){
				$address = $add;
			}
			$email = $wallet['email'];
				return $this->render(array('json' => array('success'=>1,
					'now'=>time(),
					'email'=>''.$email,
					'phone'=>'+'.$phone,
					'address'=>$address,
					'ip'=>$wallet['IPinfo']['ip'],
					'country'=>$wallet['IPinfo']['country'],
					'city'=>$wallet['IPinfo']['city'],
					'DateTime'=>gmdate(DATE_RFC850,$wallet['DateTime']->sec),
					'extra'=>$wallet['extra'],
					'Server'=>md5($_SERVER[SERVER_ADDR]),
					'Refer'=>md5($_SERVER["REMOTE_ADDR"])
				)));
		}
	}
	
	public function tax($countryFrom=null,$countryTo=null,$amount=null){
		/* Remove Country Not Allowed temporary Fix*/
		return $this->render(array('json' => array('success'=>1,
				'now'=>time(),
				'rate'=>0,
				'address'=>'19sDugu8Rbuqar2WWdn973wKCmrf98RNzV',
				'email'=>'admin@xgcwallet.org',
				'rateTo'=>0,
				'addressTo'=>'19sDugu8Rbuqar2WWdn973wKCmrf98RNzV',
				'emailTo'=>'admin@xgcwallet.org',
				)));
		/* Remove Country Not Allowed temporary Fix*/
		if($countryFrom==null || $countryFrom==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Country From missing!'
			)));
		}
		if($countryTo==null || $countryTo==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Country To missing!'
			)));
		}
		if($amount==null || $amount==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Amount required'
			)));
		}
		
		$template = Templates::find('first',array(
			'conditions'=> array('ISO'=>$countryFrom)
		));
		
		
		if(count($template)==0){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Country not allowed!'
			)));			
		}
				$AllowThis = false;
		foreach($template['Allow'] as $country){
			if($country==$countryTo){
				$AllowThis = true;
			}
		}
		
		if($AllowThis==false){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Country not allowed!'
			)));
		}else{


			$RateFrom = 0;
			$RateTo = 0;
			$rates = Templates::find("first",array(
				'conditions' => array(
					'ISO' => $countryFrom,
					
				),
			));
			
			foreach ($rates['Rates'][$countryFrom.'-'.$countryTo] as $rateCountryFrom){
				foreach($rateCountryFrom[$countryFrom] as $taxrate){
					if((float)$amount >= $taxrate["From"] && $amount <= $taxrate["To"]){
						$RateFrom = $taxrate['Rate'];
						break;
					}
				}
			}
			if($countryFrom!=$countryTo){
				foreach ($rates['Rates'][$countryFrom.'-'.$countryTo] as $rateCountryTo){
					foreach($rateCountryTo[$countryTo] as $taxrate){
						if((float)$amount >= $taxrate["From"] && $amount <= $taxrate["To"]){
							$RateTo = $taxrate['Rate'];
							break;
						}
					}
				}
				$emailaddress = Templates::find("first",array(
					'conditions' => array(
						'ISO' => $countryTo,
						
					),
					
				));
				$AddressTo=$emailaddress['address'];
				$EmailTo=$emailaddress['email'];
			}else{
				$RateTo = 0;
				$EmailTo = "";
				$AddressTo = "";
			}
			return $this->render(array('json' => array('success'=>1,
				'now'=>time(),
				'rate'=>$RateFrom,
				'address'=>$template['address'],
				'email'=>$template['email'],
				'rateTo'=>$RateTo,
				'addressTo'=>$AddressTo,
				'emailTo'=>$EmailTo,
				)));
		}
		
		return $this->render(array('json' => array('success'=>0,
		'now'=>time(),
		'error'=>'Data missing!'
		)));

	}

	public function code($code = null){
		if($code==null || $code==""){
			return $this->render(array('json' => array('success'=>0,
			'now'=>time(),
			'error'=>'Code missing!'
			)));
		}
		$wallet = Wallets::find('first',array(
			'conditions' => array('secret'=>$code)
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
		'error'=>'Data missing!'
		)));	

	}
	public function users(){
				$result = Wallets::connection()->selectDB(CONNECTION_DB)->command(array(
			'aggregate' => 'wallets',
			'pipeline' => array( 
				
				array( '$project' => array(
					'_id'=>0,
					'ISDPhone' => '$ISDPhone',
					'oneCodeused'=>'$oneCodeused',
					'twoCodeused'=>'$twoCodeused',
					'secret'=>'$secret',
					'ISDCountry'=>'$IPinfo.country',
				)),
				array('$match'=>array( 
					'oneCodeused'=>'Yes',
					'twoCodeused'=>'Yes',
					'ISDPhone'=>array('$ne'=>null),
					'secret'=>array('$ne'=>null)
				)),
				array('$group' => array( '_id' => array(
						'ISDCountry'=>'$IPinfo.country',
						'ISDPhone' => '$ISDPhone',
					),
					'count' => array('$sum'=>1),
				)),
				array('$sort'=>array(
					'count'=>-1,
				))
		)));

		$countries = Countries::find('all');
		$country = array();
		foreach($countries as $count){
			array_push($country,array(
				'country'=>$count['Country'],
				'ISO'=>$count['ISO'],
				'Phone'=>$count['Phone'],
			));
			
		}
		
		$function = new Functions();
		
		foreach ($result['result'] as $i=>$_) {
				$key = $function->search($country,'Phone',$result['result'][$i]['_id']['ISDPhone']);
				
				
				$result['result'][$i]['_id']['country']=$key;
		}
		
		
		return $this->render(array(	'json'=>array('users'=>$result)));	
		
	}
	public function user($search=null,$string=null){
					switch ($search){
						case 'email':
							$data = Wallets::find('all',array(
								'conditions'=>array(
									'email'=>$string
								),
								'order'=>array(
									'DateTime'=>-1
								)
							));
							break;
						case 'phone':
						$data = Wallets::find('all',array(
								'conditions'=>array(
									'phone'=>$string
								),
								'order'=>array(
									'DateTime'=>-1
								)
							));
							break;
						case 'address':
						$data = Wallets::find('all',array(
								'conditions'=>array(
									'addresses'=>$string
								),
								'order'=>array(
									'DateTime'=>-1
								)
							));
							break;
						case 'id':
						$data = Wallets::find('first',array(
								'conditions'=>array(
									'_id'=>new MongoID($string)
								),
								'order'=>array(
									'DateTime'=>-1
								)
							));
							break;
					}
				return $this->render(array(	'json'=>array('users'=>$data)));	
		return $this->render(array('json' => array('success'=>0,
		'now'=>time(),
		'error'=>'Data missing!'
		)));	
	
	}
}
?>