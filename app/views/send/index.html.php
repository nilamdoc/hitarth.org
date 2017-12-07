<?php 
use lithium\storage\Session;
use app\extensions\action\Functions;

$user = Session::read('default');
$function = new Functions();
if($user['email']!=''){
?>
<div class="container">
<h2>Hi <?=$user['email']?></h2>
<h3>Balance: <?php print_r($Total['result'][0]['Amount'])?> XGC</h3>
<?=$this->form->create($Users, array('role'=>'form','class'=>'form-horizontal','style'=>'padding:10px')); ?>
<?=$this->form->field('Error', array('type'=>'hidden','id'=>'Error','value'=>'Yes'));?>
			<div class="form-group has-error">
				<div class="alert alert-danger">
					<p>Email Address to whom you want to send XGC </p>				
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-asterisk" id="emailIcon"></i>
						</span>
						<?=$this->form->field('email', array('label'=>'', 'class'=>'form-control','onBlur'=>'CheckEmail(this.value,"'.$user['email'].'");', 'placeholder'=>'name@email.com','value'=>$email)); ?>
					</div>
					<p id="EmailError">If the user does not claim the amount within a week, then the amount will be returned (credited) back to your account.</p>									
				</div>
			</div>
			<div class="form-group has-error" >
				<div class="alert alert-danger">
					<p>XGC Amount from your balance: </p>
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-asterisk" id="amountIcon"></i>
						</span>
					<?=$this->form->field('Amount', array('type' => 'text', 'label'=>'','class'=>'form-control','maxlength'=>'12', 'placeholder'=>'99.001','max'=>$Total['result'][0]['Amount'],'onFocus'=>'initialAmount('.$Total['result'][0]['Amount'].');','onBlur'=>'CheckAmount(this.value,'.$Total['result'][0]['Amount'].');')); ?>
					</div>
					<p id="amountError"></p>									
				</div>
			</div>		
<?=$this->form->submit('Send Now!' ,array('class'=>'btn btn-primary', 'disabled'=>'disabled','id'=>'SendButton' )); ?>
<?=$this->form->end(); ?>
</div>
<?php }?>