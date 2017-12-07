<?php 
use lithium\storage\Session;
$user = Session::read('default');

if($user['email']!=''){
?>
<div class="container" style="border-bottom: double black;border-top:1px solid gray;margin-top:20px ">
	<ul class="nav  navbar-nav">
		<li><a href="/logout">Logout</a></li>
		<li><a href="/balance">Balance</a></li>		
		<li><a href="/send">Send</a></li>				
		<li><a href="/deposit">Deposit</a></li>						
		<li><a href="/withdraw">Withdraw</a></li>								
		<li><a href="/download">Download</a></li>										
		<li><a href="/open">Open Source</a></li>												
	</ul>
</div>
<?php }?>
<div id="footer"  style="max-height:50px ">
	<div class="container">
		<ul class="nav navbar-nav">
			<li><a href="/company/about">&copy; GreenCoin 2013 - <?=gmdate('Y',time())?></a></li>
			<li><a href="/download">Download</a></li>										
			<li><a href="/open">Open Source</a></li>												
		</ul>
	</div>
</div>
