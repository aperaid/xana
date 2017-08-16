<?php

error_reporting(E_ALL^E_NOTICE);

$mysqli = new mysqli('localhost', 'xana', 'password', 'xana');
$text = $mysqli->real_escape_string($_GET['term']);

if(Auth::user()->access=='Administrator'){
	$query = "SELECT CCode FROM customer WHERE CCode LIKE '%$text%' ORDER BY CCode ASC";
}else if(Auth::user()->access=='PPNAdmin'){
	$query = "SELECT CCode FROM customer WHERE CCode LIKE '%$text%' AND PPN = 1 ORDER BY CCode ASC";
}else if(Auth::user()->access=='NonPPNAdmin'){
	$query = "SELECT CCode FROM customer WHERE CCode LIKE '%$text%' AND PPN = 0 ORDER BY CCode ASC";
}
	
$result = $mysqli->query($query);
$json = '[';
$first = true;
while($row = $result->fetch_assoc())
{
    if (!$first) { $json .=  ','; } else { $first = false; }
    $json .= '{"value":"'.$row['CCode'].'"}';
}
$json .= ']';
echo $json;
?>