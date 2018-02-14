<?php
use lithium\g11n\Message;
extract(Message::aliases());
?>
<h3><?=$t('Welcome')?> <?=$compact['data']['email']?>,</h3>
<p><?=$t('Thank you for creating your GreenCoinX online wallet. This is the first of two confidential emails you will be receiving regarding your new account.')?></p>

<p><?=$t('You can access your account by using your email and passphrase.')?> <?=$t('Click the link')?> <a href="https://xgcwallet.org/wallet/login/<?=$compact['data']['walletid']?>">https://xgcwallet.org/wallet/login/<?=$compact['data']['walletid']?></a> <?=$t('to sign in to your wallet')?>.</p> 
<h4><?=$t('SECOND ACCOUNT OPENING EMAIL FOLLOWS SEPARATELY')?><h4>
<p><?=$t('We will also be sending you second email with a PDF attachment which contains a password recovery, public and private keys of XGC. The PDF document is password protected.')?> <strong><?=$t('The PDF document password is your email address')?></strong>. <?=$t('The password is case sensitive.')?></p>


<p><?=$t('GreenCoinX (XGC) is an identified crypto currency.')?> <?=$t('You can receive coins directly to email')?> <strong><?=$compact['data']['email']?></strong> <?=$t('or phone')?> <strong>+<?=$compact['data']['phone']?></strong>. <?=$t('Please avoid using the public key as used in other crypto currencies. You can also send coins directly to anyone registered on XGCWallet or using GreenCoinX (XGC).')?>


<p><?=$t('We respect your privacy and security.')?></p>

<p><?=$t('Thank you for choosing XGCWallet')?>,</p>
<h5><a href="https://XGCWallet.org">XGCWallet</a></h5>
<p><small><?=$t('This email communication is CONFIDENTIAL. All the information given in the document is financial sensitive. If you are not the intended recipient, please notify the sender by email to support@xgcwallet.org and delete this communication and any attachments and copies associated therewith from your computer immediately. Any dissemination or use of this information by a person other than the intended recipient is unauthorized and may be illegal. Thank you for your assistance and co-operation.')?></small></p>
<p>
	<small>
	<?=$t('IP address is recorded on the server')?><br>
	<?=$t('Date')?>: <?=gmdate('Y M d H:m:s',$compact['data']['DateTime']->sec);?>
	</small>
</p>