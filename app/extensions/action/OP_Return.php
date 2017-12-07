<?php
namespace app\extensions\action;
use app\extensions\action\Bitcoin;
use app\extensions\action\Coingreen;

class OP_Return extends \lithium\action\Controller {

	function send($send_address, $send_amount, $metadata, $testnet=false)
	{
	
	//	Validate some parameters
//		$coin = new Bitcoin('http://'.BITCOIN_WALLET_SERVER.':'.BITCOIN_WALLET_PORT,BITCOIN_WALLET_USERNAME,BITCOIN_WALLET_PASSWORD);
		$coin = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
	
		$result=$coin->validateaddress($send_address);
		
		if (!$result['isvalid'])
			return array('error' => 'Send address could not be validated: '.$send_address);
			
		if (strlen($metadata)>75)
			return array('error' => 'Metadata limit is 75 bytes, and you should probably stick to 40.');

	
	//	List and sort unspent inputs by priority
	
		$unspent_inputs=$coin->listunspent(0);		
		
		if (!is_array($unspent_inputs))
			return array('error' => 'Could not retrieve list of unspent inputs');
		
		foreach ($unspent_inputs as $index => $unspent_input)
			$unspent_inputs[$index]['priority']=$unspent_input['amount']*$unspent_input['confirmations']; // see: https://en.bitcoin.it/wiki/Transaction_fees

		$this->sort_by($unspent_inputs, 'priority');
		$unspent_inputs=array_reverse($unspent_inputs); // now in descending order of priority
	
	//	Identify which inputs should be spent
	
		$inputs_spend=array();
		$input_amount=0;
		$output_amount=$send_amount+CONST_BITCOIN_FEE;		
		
		foreach ($unspent_inputs as $unspent_input) {
			$inputs_spend[]=$unspent_input;

			$input_amount+=$unspent_input['amount'];
			if ($input_amount>=$output_amount)
				break; // stop when we have enough
		}
		
		if ($input_amount<$output_amount)
			return array('error' => 'Not enough funds are available to cover the amount and fee');
	
	//	Build the initial raw transaction
			
		$change_amount=$input_amount-$output_amount;		
		$change_address=$coin->getrawchangeaddress();
		
		$raw_txn=$coin->createrawtransaction($inputs_spend, array(
			$send_address => (float)$send_amount,
			$change_address => $change_amount,
		));
		
		$txn_unpacked=$this->unpack_raw_txn($raw_txn);
	
		$txn_unpacked['vout'][]=array(
			'value' => 0,
			'scriptPubKey' => '6a'.reset(unpack('H*', chr(strlen($metadata)).$metadata)), // here's the OP_RETURN
		);
		
		$raw_txn=$this->pack_raw_txn($txn_unpacked);

		$signed_txn=$coin->signrawtransaction($raw_txn);
		if (!$signed_txn['complete'])
			return array('error' => 'Could not sign the transaction');
		$send_txid=$coin->sendrawtransaction($signed_txn['hex']);
//		print_r($send_txid);
		if (strlen($send_txid)!=64)
			return array('error' => 'Could not send the transaction');
	
	//	Return the result if successful
		return array('txid' => $send_txid);
	}
	

//	Unpacking and packing coin transactions	
	function unpack_raw_txn($raw_txn_hex)
	{
		// see: https://en.bitcoin.it/wiki/Transactions
		
		$binary=pack('H*', $raw_txn_hex);
		
		$txn=array();
		
		$txn['version']=$this->string_shift_unpack($binary, 4, 'V'); // small-endian 32-bits

		for ($inputs=$this->string_shift_unpack_varint($binary); $inputs>0; $inputs--) {
			$input=array();
			
			$input['txid']=$this->string_shift_unpack($binary, 32, 'H*', true);
			$input['vout']=$this->string_shift_unpack($binary, 4, 'V');
			$length=$this->string_shift_unpack_varint($binary);
			$input['scriptSig']=$this->string_shift_unpack($binary, $length, 'H*');
			$input['sequence']=$this->string_shift_unpack($binary, 4, 'V');
			
			$txn['vin'][]=$input;
		}
		
		for ($outputs=$this->string_shift_unpack_varint($binary); $outputs>0; $outputs--) {
			$output=array();
			
			$output['value']=$this->string_shift_unpack_uint64($binary)/100000000;
			$length=$this->string_shift_unpack_varint($binary);
			$output['scriptPubKey']=$this->string_shift_unpack($binary, $length, 'H*');
			
			$txn['vout'][]=$output;
		}
		
		$txn['locktime']=$this->string_shift_unpack($binary, 4, 'V');
		
		if (strlen($binary))
			die('More data in transaction than expected');
		
		return $txn;
	}
	
	function pack_raw_txn($txn)
	{
		$binary='';
		
		$binary.=pack('V', $txn['version']);
		
		$binary.=$this->pack_varint(count($txn['vin']));
		
		foreach ($txn['vin'] as $input) {
			$binary.=strrev(pack('H*', $input['txid']));
			$binary.=pack('V', $input['vout']);
			$binary.=$this->pack_varint(strlen($input['scriptSig'])/2); // divide by 2 because it is currently in hex
			$binary.=pack('H*', $input['scriptSig']);
			$binary.=pack('V', $input['sequence']);
		}
		
		$binary.=$this->pack_varint(count($txn['vout']));
		
		foreach ($txn['vout'] as $output) {
			$binary.=$this->pack_uint64(round($output['value']*100000000));
			$binary.=$this->pack_varint(strlen($output['scriptPubKey'])/2); // divide by 2 because it is currently in hex
			$binary.=pack('H*', $output['scriptPubKey']);
		}
		
		$binary.=pack('V', $txn['locktime']);
		
		return reset(unpack('H*', $binary));
	}
	
	function string_shift(&$string, $chars)
	{
		$prefix=substr($string, 0, $chars);
		$string=substr($string, $chars);
		return $prefix;
	}
	
	function string_shift_unpack(&$string, $chars, $format, $reverse=false)
	{
		$data=$this->string_shift($string, $chars);
		if ($reverse)
			$data=strrev($data);
		$unpack=unpack($format, $data);
		return reset($unpack);
	}
	
	function string_shift_unpack_varint(&$string)
	{
		$value=$this->string_shift_unpack($string, 1, 'C');
		
		if ($value==0xFF)
			$value=$this->string_shift_unpack_uint64($string);
		elseif ($value==0xFE)
			$value=$this->string_shift_unpack($string, 4, 'V');
		elseif ($value==0xFD)
			$value=$this->string_shift_unpack($string, 2, 'v');
			
		return $value;
	}
	
	function string_shift_unpack_uint64(&$string)
	{
		return $this->string_shift_unpack($string, 4, 'V')+($this->string_shift_unpack($string, 4, 'V')*4294967296);
	}
	
	function pack_varint($integer)
	{
		if ($integer>0xFFFFFFFF)
			$packed="\xFF".$this->pack_uint64($integer);
		elseif ($integer>0xFFFF)
			$packed="\xFE".pack('V', $integer);
		elseif ($integer>0xFC)
			$packed="\xFD".pack('v', $integer);
		else
			$packed=pack('C', $integer);
		return $packed;
	}
	
	function pack_uint64($integer)
	{
		$upper=floor($integer/4294967296);
		$lower=$integer-$upper*4294967296;
		return pack('V', $lower).pack('V', $upper);
	}
	

//	Sort-by utility functions
	function sort_by(&$array, $by1, $by2=null)
	{
		global $sort_by_1, $sort_by_2;
		
		$sort_by_1=$by1;
		$sort_by_2=$by2;
		
		uasort($array, Array($this,'sort_by_fn'));
	}

	function sort_by_fn($a, $b)
	{
		global $sort_by_1, $sort_by_2;
		
		$compare=$this->sort_cmp($a[$sort_by_1], $b[$sort_by_1]);

		if (($compare==0) && $sort_by_2)
			$compare=$this->sort_cmp($a[$sort_by_2], $b[$sort_by_2]);

		return $compare;
	}

	function sort_cmp($a, $b)
	{
		if (is_numeric($a) && is_numeric($b)) // straight subtraction won't work for floating bits
			return ($a==$b) ? 0 : (($a<$b) ? -1 : 1);
		else
			return strcasecmp($a, $b); // doesn't do UTF-8 right but it will do for now
	}

}
?>