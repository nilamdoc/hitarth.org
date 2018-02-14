<?php use lithium\core\Environment; 
//if(substr(Environment::get('locale'),0,2)=="en"){$locale = "en";}else{$locale = Environment::get('locale');}
//if(strlen($locale>2)){$locale='en';}
// print_r(Environment::get('locale'));
// print_r($locale);
?>
<?php
//use lithium\g11n\Message;
//extract(Message::aliases());
?><?php
use app\extensions\action\Functions;
$function = new Functions();
ini_set('memory_limit', '-1');
$pdf =& $this->Pdf;
$pdf->SetProtection($permissions=array('modify','extract','assemble'), $user_pass=$printdata['email'], $owner_pass=null, $mode=1, $pubkeys=null);
$this->Pdf->setCustomLayout(array(
    'header'=>function() use($pdf){
        list($r, $g, $b) = array(200,200,200);
        $pdf->SetFillColor($r, $g, $b); 
        $pdf->SetTextColor(0 , 0, 0);
        $pdf->Cell(0,15, "GreenCoinX - XGC Wallet", 0,1,'C', 1);
        $pdf->Ln();
    },
    'footer'=>function() use($pdf){
        $footertext = sprintf('Copyright © %d https://XGCWallet.org. All rights reserved. admin@XGCWallet.org', date('Y')); 
        $pdf->SetY(-10); 
        $pdf->SetTextColor(0, 0, 0); 
        $pdf->SetFont(PDF_FONT_NAME_MAIN,'', 8); 
        $pdf->Cell(0,8, $footertext,'T',1,'C');
    }
));
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetAuthor('https://XGCWallet.com');
$pdf->SetCreator('support@XGCWallet.com');
$pdf->SetSubject('XGCWallet details for '.$printdata['email']);
$pdf->SetKeywords('XGCWallet, GreenCoinX, XGC, Wallet, Web Wallet, Secure');
$pdf->SetTitle('XGCWallet for GreenCoinX XGC - '.$printdata['email']);
$pdf->SetAutoPageBreak(true);
$pdf->AddPage();
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(10,20,false);
$html = '
<div style="text-align:center">
<h2>Wallet ID</h2>
<h1>'.$printdata['walletid'].'</h1>
<h3>Click the link <a href="https://xgcwallet.org/wallet/login/'.$printdata['walletid'].'">https://xgcwallet.org/wallet/login/'.$printdata['walletid'].'</a><br> to signin to your wallet</h3>
<h2>GreenCoinX (XGC) address </h2>
<h1>'.$printdata['greencoinAddress'].'</h1>
<img src="'.LITHIUM_APP_PATH.'/webroot/qrcode/XGCWallet-'.$printdata['walletid'].'-greencoinAddress.png" border="1">
<h1>'.$printdata['email'].'</h1>
<h1>+'.$printdata['phone'].'</h1>
</div>';
$pdf->writeHTML($html, true, 0, true, 0);
$pdf->AddPage();
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(10,20,false);
$html = '<div style="text-align:center">
<h2>GreenCoinX (XGC) Private Key </h2>
<h2>'.$printdata['privkey'].'</h2>
<img src="'.LITHIUM_APP_PATH.'/webroot/qrcode/XGCWallet-'.$printdata['walletid'].'-privkey.png" border="1">
<h2> Passphrase for recovery of password</h2>
<h3>'.$printdata['passphrase'].'</h3>
<img src="'.LITHIUM_APP_PATH.'/webroot/qrcode/XGCWallet-'.$printdata['walletid'].'-passphrase.png" border="1">
</div>';
$html = $html . '';
$pdf->writeHTML($html, true, 0, true, 0);
?>