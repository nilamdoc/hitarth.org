<?php
use lithium\g11n\Message;
extract(Message::aliases());
?><p><?=substr($compact['data']['kyc_id'],0,4)?> is your email code for GreenCoinX App</p>
<p>
email: <?=$compact['data']['email']?><br>
Date and time: <?=gmdate('Y-m-d H:i:s',time())?>
</p>