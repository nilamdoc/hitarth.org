<?php
//use lithium\g11n\Message;
//extract(Message::aliases());
?>
<h3>Welcome <?=$compact['data']['email']?>,</h3>
<h4>This email communication is CONFIDENTIAL. All the information given in the document is financial sensitive.</h4>


<p>You have today created your own XGC wallet. This is the second confidential email you are receiving regarding your new account.</p>

<p>Enclosed is a PDF document which contains a password recovery, public and private keys of XGC in PDF document.</p>

<h4>Print the PDF document and keep it in a safe place.</h4>
<h4>You will need this if at any time you lose your password or keys.

For extra security we recommend that you delete  this email after you have stored your PDF in a safe location. </h4>
<p>We do not save password on the XGCWallet site. So the only way to recover the account information is through the passphrase provided in the PDF document</p>


<p>GreenCoinX (XGC) is an identified crypto currency. You can receive coins directly to email <strong><?=$compact['data']['email']?></strong> or phone <strong>+<?=$compact['data']['phone']?></strong>. Please avoid using the public key as used in other crypto currencies. You can also send coins directly to any other GreenCoinX user by using their registered  email address or mobile phone number.




<p>Thank you for choosing XGCWallet,</p>
<h5><a href="https://XGCWallet.org">XGCWallet</a></h5>

<p><small>This email communication is CONFIDENTIAL. All the information given in the document is financial sensitive. If you are not the intended recipient, please notify the sender by email to support@xgcwallet.org and delete this communication and any attachments and copies associated therewith from your computer immediately. Any dissemination or use of this information by a person other than the intended recipient is unauthorized and may be illegal. Thank you for your assistance and co-operation.
</small></p>
<p>
	<small>
	IP address is recorded on the server<br>
	Date: <?=gmdate('Y M d H:m:s',$compact['data']['DateTime']->sec);?>
	</small>
</p>