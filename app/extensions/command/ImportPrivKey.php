<?php
namespace app\extensions\command;
use app\extensions\action\Coingreen;
use app\models\XGCUsers;

class ImportPrivKey extends \lithium\console\Command {
 public function run($pubkey = null) {
  $COINGREEN = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
if($pubkey==null){return false;exit;}
  $walletXGC = XGCUsers::find('first', [
   'conditions' => array(
   'greencoinAddress.0' => $pubkey,
   ),
  ]);
var_dump($walletXGC['greencoinPriv'][0])  ;
var_dump($walletXGC['walletid']);
//var_dump($COINGREEN);
  $import = $COINGREEN->importprivkey($walletXGC['greencoinPriv'][0],$walletXGC['walletid'],false);
var_dump($import);
 }
}
?>