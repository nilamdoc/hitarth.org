<?php
use lithium\g11n\Message;
extract(Message::aliases());
?><p><?= $compact['data']['walletid'] ?> is Wallet id.</p>
<p>
payment Transaction Id: <?=$compact['data']['paymentid']?><br>
Date and time: <?=gmdate('Y-m-d H:i:s',time())?>
</p>