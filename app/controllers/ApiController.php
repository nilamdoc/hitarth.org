<?php
namespace app\controllers;
use app\models\Blocks;
use app\models\Txs;
use app\models\Apps;
use app\models\Orders;
use app\models\XGCUsers;
use app\models\KYCDocuments;
use app\models\KYCFiles;
use app\models\XGCDetails;
use app\extensions\action\Coingreen;
use app\models\Wallets;

class ApiController extends \lithium\action\Controller {

	public function index() {
		$COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
  
		$getblockcount = $COINGREEN->getblockcount();

	  	$getconnectioncount = $COINGREEN->getconnectioncount();
	  	$getblockhash = $COINGREEN->getblockhash($getblockcount);
	  	$getblock = $COINGREEN->getblock($getblockhash);
 		$title = "Network connectivity ";		
	  	return compact('getblockcount','getconnectioncount','getblock','title');
	}

    public function sendcoin_BKP($key = null){
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

	    if($greencoinaddress==null || $greencoinaddress == ""){
	        return $this->render(array('json' => array('success'=>0,
	        	'now'=>time(),
	        	'error'=>'please select receiver address!'
	    	)));
	    }

	    if($amount == null || $amount < 0){
	        return $this->render(array('json' => array('success'=>0,
	        	'now'=>time(),
	        	'error'=>'please enter coin!'
	    	)));
	    }

	    if($comment == null || $comment == ""){
	        return $this->render(array('json' => array('success'=>0,
	        	'now'=>time(),
	        	'error'=>'please enter comment!'
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
	  			$check = XGCUsers::find('first',array('conditions' => array('greencoinAddress.0'=>$greencoinaddress)));  
	  			if(count($check) > 0){
		  			$COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);	
				    
		  			//check Balance in Wallet
				    $balance = $COINGREEN->getbalance($walletid);
				    
				    if(!empty($balance['error'])){
				    	return $this->render(array('json' => array('success'=>0,
			          		'now'=>time(),
			          		'error'=>"something went wrong!"
			        	)));
				    }	

				    if($balance >= $amount){
						// send coin
				    	//var_dump($walletid,$greencoinaddress,(float)$amount,false,6,"donation","seans outpost");

				    //	echo $walletid." ".$greencoinaddress." ".(float)$amount." ".'true'." ".'1'." ".$comment;
				    
						$transaction = $COINGREEN->sendfrom($walletid,$greencoinaddress,(float)$amount,true,1,$comment);
					 
					    if(!empty($transaction['error'])){
					    	return $this->render(array('json' => array('success'=>0,
				          		'now'=>time(),
				          		'error'=>'Sending coins failed!!'
				        	)));
					    }else{
					    	return $this->render(array('json' => array('success'=>1,
				          		'now'=>time(),
				          		'result'=>'sending coins successful!!',
				          		'transactionid' => $transaction
				        	)));
					    }
				    }else{
				    	return $this->render(array('json' => array('success'=>0,
			          		'now'=>time(),
			          		'result'=>'Please purchase coin!'
			        	)));
				    }
		        }else{
		        	return $this->render(array('json' => array('success'=>0,
		          		'now'=>time(),
		          		'error'=>'Enter valid receiver wallet '
		        	)));
		        }	
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

    public function sendcoin($key = null){
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

	    if($greencoinaddress==null || $greencoinaddress == ""){
	        return $this->render(array('json' => array('success'=>0,
	        	'now'=>time(),
	        	'error'=>'please select receiver address!'
	    	)));
	    }

	    if($amount == null || $amount < 0){
	        return $this->render(array('json' => array('success'=>0,
	        	'now'=>time(),
	        	'error'=>'please enter coin!'
	    	)));
	    }

	    if($comment == null || $comment == ""){
	        return $this->render(array('json' => array('success'=>0,
	        	'now'=>time(),
	        	'error'=>'please enter comment!'
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
	  			$check = XGCUsers::find('first',array('conditions' => array('greencoinAddress.0'=>$greencoinaddress)));  
	  			if(count($check) > 0){
		  			$COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);	
				    
		  			//check Balance in Wallet
				   $unspent = $COINGREEN->listunspent(0,9999999,[$chkwallet->greencoinAddress[0]]);
				    
				    if(!empty($unspent['error'])){
				    	return $this->render(array('json' => array('success'=>0,
			          		'now'=>time(),
			          		'error'=>"something went wrong!"
			        	)));
				    }
				    // echo "<pre>";
				    // print_r($unspent);
				    // exit();
		  			$balance = array_sum(array_column($unspent, 'amount'));
				    if($balance >= $amount){
						// send coin
				    	//var_dump($walletid,$greencoinaddress,(float)$amount,false,6,"donation","seans outpost");

				    //	echo $walletid." ".$greencoinaddress." ".(float)$amount." ".'true'." ".'1'." ".$comment;
				    
						$transaction = $COINGREEN->sendfrom($walletid,$greencoinaddress,(float)$amount,true,1,$comment);
					 
					    if(!empty($transaction['error'])){
					    	return $this->render(array('json' => array('success'=>0,
				          		'now'=>time(),
				          		'error'=>'Sending coins failed!!'
				        	)));
					    }else{
					    	return $this->render(array('json' => array('success'=>1,
				          		'now'=>time(),
				          		'result'=>'sending coins successful!!',
				          		'transactionid' => $transaction
				        	)));
					    }
				    }else{
				    	return $this->render(array('json' => array('success'=>0,
			          		'now'=>time(),
			          		'result'=>'Please purchase coin!'
			        	)));
				    }
		        }else{
		        	return $this->render(array('json' => array('success'=>0,
		          		'now'=>time(),
		          		'error'=>'Enter valid receiver wallet '
		        	)));
		        }	
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

	public function getwalletbalance($key = null){
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
			    $balance = $COINGREEN->getbalance($walletid);

			    if(!empty($balance['error']))
			    {
			    	return $this->render(array('json' => array('success'=>0,
		          		'now'=>time(),
		          		'error'=>$balance['error']
		        	)));
			    }

	  			return $this->render(array('json' => array('success'=>1,
	          		'now'=>time(),
	          		'result'=>'success get balance',
	          		'balance' => $balance
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

    public function transactionhistory($key = null){
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

	  			return $this->render(array('json' => array('success'=>1,
	          		'now'=>time(),
	          		'result'=>'success transaction list',
	          		'prfilehost' =>FTP_HOST."/profiles/",
	          		'transaction' => array_reverse($traslist)
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

    public function paymentsuccess($key = null){
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

	    if($paymentid==null || $paymentid == ""){
	        return $this->render(array('json' => array('success'=>0,
	        	'now'=>time(),
	        	'error'=>'Paymentid id missing!'
	    	)));
	    }

	    if($amount==null || $amount == ""){
	        return $this->render(array('json' => array('success'=>0,
	        	'now'=>time(),
	        	'error'=>'Amount missing!'
	    	)));
	    }

	    if($ip==null || $ip == ""){
	        return $this->render(array('json' => array('success'=>0,
	        	'now'=>time(),
	        	'error'=>'Ip missing!'
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
	  			
	  			$data = array(
			        'walletid'=>$walletid,
			        'paymentid'=>$paymentid,
			        'amount' => $amount,
			        'hash' => $record['hash'],
			        'IP' => $ip,
			    	'Server'=>md5($_SERVER["SERVER_ADDR"]),
			    	'Refer'=>md5($_SERVER["REMOTE_ADDR"]),
			    	'DateTime' => new \MongoDate(),
			     );
			     Orders::create()->save($data);

			    ////////////////////////////////////////Send Email
		       
		        // $payment = array(
		        //   'walletid'=>$walletid,
		        //   'paymentid'=>$paymentid
		        // );
		        // $function = new Functions();
	            // $compact = array('data'=>$payment);
	            // $from = array(NOREPLY => "noreply@".COMPANY_URL);
	            // $email = 'nilamsir@gmail.com';
	            // $function->sendEmailTo($email,$compact,'process','purchseCoin',"Purchase Coin Request Coin Transfer Wallet",$from,'','','',null);

		        //////////////////////////////////////////////////////////////////////


	  			return $this->render(array('json' => array('success'=>1,
	          		'now'=>time(),
	          		'result'=>'Order history suceess',
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

    public function walletbackup($key = null){
        extract($this->request->data);
        if($key==null || $key==""){
          return $this->render(array('json' => array('success'=>0,
            'now'=>time(),
            'error'=>'Key missing!'
          )));
        }

        if($kyc_id==null || $kyc_id == ""){
	        return $this->render(array('json' => array('success'=>0,
	        'now'=>time(),
	        'error'=>'Something went wrong!'
	        )));
	      }
        
        $conditions = array('key' => $key,'isdelete' =>'0');
        $record = Apps::find('first',array('conditions'=>$conditions));
        if(count($record)!=0){
        	$document = KYCDocuments::find('first',array(
	            'conditions' => array(
	              'hash'=>$record['hash'],
	              'kyc_id'=>$kyc_id
	              )
	            )
	        );
        
            if(count($document) > 0){
	            $walletXGC = XGCUsers::find('all', [
	                'conditions' => array(
	                    'hash' => $record['hash'],
	                    'greencoinAddress'=>array('$ne'=>null)
	                )
	            ]);         
              
                $wallets = [];
                foreach ($walletXGC as $k => $wallet) { 
                  
                    $walletXGC = wallets::find('first', [
                      'conditions' => array(
                        'key' => $wallet['code'],
                        'oneCodeused' => 'Yes',
                        'twoCodeused' => 'Yes',
                        'secret'=>array('$ne'=>null)
                      )
                    ]);

                    if(count($walletXGC) == 0){
                        continue;
                    }    

                    $wallets[] = array(
		              'email'=>$wallet['email'],
		              'phone'=>$wallet['phone'],
		              'code'=>$wallet['code']
                    );
                }  
	               	return $this->render(array('json' => array('success'=>1,
	                	'now'=>time(),
	                	'result' =>'Wallet list',
	                	'wallets' => $wallets
					)));
            }else{
		        return $this->render(array('json' => array('success'=>0,
	                'now'=>time(),
	                'error'=>'Something went wrong!',
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