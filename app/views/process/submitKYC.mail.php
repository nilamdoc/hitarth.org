<?php
use lithium\g11n\Message;
extract(Message::aliases());
?><p><?= $compact['data']['kyc_id'] ?> is Completed Kyc information please check.</p>
<p>
email: <?=$compact['data']['email']?><br>
Date and time: <?=gmdate('Y-m-d H:i:s',time())?>
</p>