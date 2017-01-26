<?php

error_reporting(E_ALL^E_NOTICE);

$mysqli = new mysqli('localhost', 'root', '', 'xana');
$text = $mysqli->real_escape_string($_GET['term']);

$query = "SELECT Barang FROM inventory WHERE Barang LIKE '%$text%' GROUP BY Barang";
$result = $mysqli->query($query);
$json = '[';
$first = true;
while($row = $result->fetch_assoc())
{
    if (!$first) { $json .=  ','; } else { $first = false; }
    $json .= '{"value":"'.$row['Barang'].'"}';
}
$json .= ']';
echo $json;
?>