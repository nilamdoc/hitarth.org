<?php
use app\models\Users;
use app\models\Details;
use app\extensions\action\Coingreen;
$countx = Users::count();

use lithium\data\Connections;

	$mongodb = Connections::get('default')->connection;
		$TotalCoins = Details::connection()->connection->command(array(
			'aggregate' => 'details',
			'pipeline' => array( 
				array( '$project' => array(
					'_id'=>0,
					'balanceXGC' => '$balance.XGC',
				)),
				array('$group' => array( '_id' => null,
					'total'=>array('$sum' => '$balanceXGC'),
				)),
			)
		));
//print_r($TotalCoins['result']['total']);
		$coin = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
		$getinfo = $coin->getinfo();
?>
<div class="jumbotron container" style="text-align:center ">
	<h1>Payments with GreenCoin</h1>
	<p class="lead">Now you can pay with virtual currency (GreenCoin) to any email with ease!</p>
	<p><a class="btn btn-lg btn-success" href="/balance" role="button">Get Free GreenCoins!</a></p>
</div>

<div class="row marketing">
	<div class="col-lg-6">
		<div style="min-height:100px " class="col-lg-12">
			<a href="/balance" class="btn btn-primary btn-lg btn-block" role="button">Check Balance / Login</a>
			<p>Check balance of GreenCoin in your email account, if your email is not registered, we will just register it and give you 100 free coins.</p>
		</div>
		<div style="min-height:100px " class="col-lg-12">		
			<a href="/send" class="btn btn-warning btn-lg btn-block" role="button">Send</a>
			<p>Send GreenCoin to any email address, with just a click!</p>
		</div>
		<div style="min-height:100px " class="col-lg-12">						
			<a href="/download" class="btn btn-primary btn-lg btn-block" role="button">Download</a>
			<p>Download GreenCoin client for Windows, Linux. We are developing for iOS too.</p>
		</div>
	</div>

	<div class="col-lg-6">
		<div style="min-height:100px " class="col-lg-12">					
			<a href="/deposit" class="btn btn-success btn-lg btn-block" role="button">Deposit</a>
			<p>Deposit GreenCoin to your online wallet on GreenCoin.io for sending to a friend.</p>
		</div>
		<div style="min-height:100px " class="col-lg-12">				
			<a href="/withdraw" class="btn btn-danger btn-lg btn-block" role="button">Withdraw</a>
			<p>Withdraw GreenCoin to your personal wallet!</p>
		</div>
		<div style="min-height:100px " class="col-lg-12">						
			<a href="/open" class="btn btn-primary btn-lg btn-block" role="button">Open Source</a>
			<p>GreenCoin is an open-source project cloned from bitcoin. We would like you to take a look and collabarate to this revolution of payments made easy through emails. </p>
		</div>
	</div>
</div>
<?=$countx;?> Users / 
<?php
print_r($TotalCoins['result'][0]['total']);
?> User XGC / 
<?php
print_r($getinfo['balance']);
?> Server XGC