<?php

error_reporting(E_ALL^E_NOTICE);

$mysqli = new mysqli('localhost', 'root', '', 'scaffolding');
$text = $mysqli->real_escape_string($_GET['term']);

$query = "SELECT CCode FROM customer WHERE CCode LIKE '%$text%' ORDER BY CCode ASC";
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