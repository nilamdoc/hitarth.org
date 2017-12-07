<br>
<table class="table table-condensed table-striped table-bordered">
	<tr>
		<td>Transactions:</td>
		<td><?=count($txout)?></td>
	</tr>
	<tr>
		<td>Address:</td>
		<td><?=$address?></td>
	</tr>
</table>

<table class="table table-condensed table-striped table-bordered">
<?php foreach ($txout as $tx){?>
<tr>
	<td>Block: <?php echo($tx['height'])?></td>
	<td>Confirmations: <?php echo($tx['confirmations'])?></td>
</tr>
</table>
<h3>In</h3>
<table class="table table-condensed table-striped table-bordered">
	<tr>
		<td>Txid</td>
		<td>Address</td>
		<td>value</td>
		<td>time</td>		
	</tr>
	<?php foreach($tx['txid'] as $txid){?>
	<?php if($txid['address'][0]==$address){?>
	<tr>	
		<td><?=substr($txid['txid'],0, 10) ?>...</td>
		<td><?=$txid['address'][0]?></td>
		<td><?=$txid['value']?></td>
		<td><?=gmdate('Y-M-D',$tx['time'])?></td>
</tr>
<?php }?>
<?php }?>
<?php }?>
</table>
<h3>Out</h3>
<?php foreach ($txin as $tx){?>
<table class="table table-condensed table-striped table-bordered">
<tr>
	<td colspan="4">Block: <?php echo($tx['height'])?></td>
</tr>
</table>

<?php }?>
<?php
 function array_to_obj($array, &$obj)
  {
    foreach ($array as $key => $value)
    {
      if (is_array($value))
      {
      $obj->$key = new stdClass();
      array_to_obj($value, $obj->$key);
      }
      else
      {
        $obj->$key = $value;
      }
    }
  return $obj;
  }

function arrayToObject($array)
{
 $object= new stdClass();
 return array_to_obj($array,$object);
}
?>