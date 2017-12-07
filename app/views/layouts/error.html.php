<?php 
header('Content-Type: application/json');
$arr = array("status"=>"0");
echo json_encode($arr,JSON_PRETTY_PRINT);
?>
<?php   echo $this->content() ; ?>