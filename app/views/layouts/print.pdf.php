<?php
header("Content-type: application/pdf");
echo $this->Pdf->Output(QR_OUTPUT_DIR.'XGCWallet-'.$printdata['walletid']."-Wallet".".pdf","F");
?>