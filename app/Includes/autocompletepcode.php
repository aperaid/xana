<?php

error_reporting(E_ALL^E_NOTICE);

$mysqli = new mysqli('localhost', 'xana', 'password', 'xana');
$text = $mysqli->real_escape_string($_GET['term']);

$query = "SELECT PCode FROM project WHERE PCode LIKE '%$text%' ORDER BY PCode ASC";
$result = $mysqli->query($query);
$json = '[';
$first = true;
while($row = $result->fetch_assoc())
{
    if (!$first) { $json .=  ','; } else { $first = false; }
    $json .= '{"value":"'.$row['PCode'].'"}';
}
$json .= ']';
echo $json;
?>