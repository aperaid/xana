<?php

error_reporting(E_ALL^E_NOTICE);

$mysqli = new mysqli('localhost', 'xana', 'password', 'xana');
$text = $mysqli->real_escape_string($_GET['term']);

if(Auth::user()->access=='Administrator'){
	$query = "SELECT Penawaran FROM penawaran WHERE Penawaran LIKE '%$text%' GROUP BY Penawaran";
}else if(Auth::user()->access=='PPNAdmin'){
	$query = "SELECT Penawaran FROM penawaran LEFT JOIN project on penawaran.PCode=project.PCode LEFT JOIN customer on project.CCode=customer.CCode WHERE Penawaran LIKE '%$text%' AND PPN = 1 ORDER BY Penawaran ASC";
}else if(Auth::user()->access=='NonPPNAdmin'){
	$query = "SELECT Penawaran FROM penawaran LEFT JOIN project on penawaran.PCode=project.PCode LEFT JOIN customer on project.CCode=customer.CCode WHERE Penawaran LIKE '%$text%' AND PPN = 0 ORDER BY Penawaran ASC";
}
$result = $mysqli->query($query);
$json = '[';
$first = true;
while($row = $result->fetch_assoc())
{
    if (!$first) { $json .=  ','; } else { $first = false; }
    $json .= '{"value":"'.$row['Penawaran'].'"}';
}
$json .= ']';
echo $json;
?>