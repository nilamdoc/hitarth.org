<?php 
use lithium\storage\Session;
use app\extensions\action\Functions;

$user = Session::read('default');
$function = new Functions();
if($user['email']!=''){
?>
<div class="container">
<h2>Hi <?=$user['email']?></h2>
<h3>Balance: <?php print_r($details['balance']['XGC'])?> XGC</h3>
<table class="table table-bordered table-condensed">
	<tr>
		<th>Date</th>
		<th>IP</th>
		<th>Action</th>
		<th>Amount XGC</th>
	</tr>
	<?php foreach ($transactions as $tran){?>
	<tr>
		<td><?=gmdate('Y-m-d H:i',$tran['DateTime']->sec)?></td>
		<td><?=$tran['IP']?></td>
		<td><?=$tran['Action']?></td>
		<td style="text-align:right"><?php print_r($function->XGCFormat(round($tran['Amount'])+round($tran['Fee'],8)))?>
	</tr>
	<?php }?>
</table>
</div>
<?php 
}else{
?>
<h2>Check Balance</h2>
<div class="container">
	<div class="row">
		<div class="col-10 col-sm-10 col-lg-8">
		<?=$this->form->create($Users, array('role'=>'form','class'=>'form-horizontal','style'=>'padding:10px')); ?>
			<div class="form-group has-error">
				<div class="alert alert-danger">
						<p>First time users, if your email is not present on the system, we will create a new email and send the "Login Email Password". We will also credit 100 XGC to your account, once you confirm your email address.</p>

					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-asterisk" id="emailIcon"></i>
						</span>
						<?=$this->form->field('email', array('label'=>'', 'class'=>'form-control','onBlur'=>'SendPassword();', 'placeholder'=>'name@email.com','value'=>$email)); ?>
					</div>
				</div>
			</div>
			<div class="form-group has-error" id="LoginEmailPassword">
				<div class="alert alert-danger">
					<p>Please check your email in 30 seconds. <br>You will receive "<strong>Login Email Password</strong>" use it in the box below.</p>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-envelope"></i>
						</span>
					<?=$this->form->field('loginpassword', array('type' => 'password', 'label'=>'','class'=>'form-control','maxlength'=>'6', 'placeholder'=>'123456')); ?>
					</div>
				</div>
			</div>		
			<?=$this->form->submit('Check Balance' ,array('class'=>'btn btn-primary')); ?>
			<?=$this->form->end(); ?>
		</div>
	</div>
</div>
<?php }?>