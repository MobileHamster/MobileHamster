<?php
include './utils/jsonPrettyPrint.php';

$debug = $_GET["debug"];

$id = $_GET["id"];
$ckfile = "/tmp/" . $id . ".cookie";

$action = $_GET["action"];
$uid = $_GET["uid"];


$url = "http://xhamster.com/ajax/invites_control.php?response=" . $action . "&uid=" . $uid . "&_=1234";
echo $url;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

// this header is needed to fake the call, there is a check if it was done via ajax ...
curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest'));
$output = curl_exec($ch);

echo "<pre>";
echo $output;
?>
