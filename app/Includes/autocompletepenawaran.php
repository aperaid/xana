<?php

error_reporting(E_ALL^E_NOTICE);

$mysqli = new mysqli('localhost', 'root', '', 'scaffolding');
$text = $mysqli->real_escape_string($_GET['term']);

$query = "SELECT Penawaran FROM penawaran WHERE Penawaran LIKE '%$text%' GROUP BY Penawaran";
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