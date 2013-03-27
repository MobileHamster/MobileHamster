<?php

$user=$_GET["user"];

$hash=hash('sha512', $user);

$ckfile = "/tmp/".$hash.".cookie";


$ch = curl_init ("http://xhamster.com/logout.php");
curl_setopt($ch,CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch,CURLOPT_COOKIEFILE, $ckfile);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$output = curl_exec ($ch);

echo json_encode("done");


?>
