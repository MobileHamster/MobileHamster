<?php
include './utils/jsonPrettyPrint.php';

$debug = $_GET["debug"];

$user = $_GET["user"];
$pwd = $_GET["pwd"];

$hash = hash('sha512', $user);

$ckfile = "/tmp/" . $hash . ".cookie";

$ch = curl_init("http://xhamster.com/ajax/login.php?act=login&ref=http%3A%2F%2Fxhamster.com%2F&password=" . $pwd . "&remeber=on&username=" . $user);
curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
//curl_setopt($ch, CURLOPT_POSTFIELDS, "act=login&ref=http%3A%2F%2Fxhamster.com%2Fpassword=" . $pwd . "&remeber=on&username=" . $user);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// this header is needed to fake the call, there is a check if it was done via ajax ...
curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest', 'Referer: http://xhamster.com/login.php'));


$output = curl_exec($ch);

$reply = array();

if (strlen($output)==0 || strpos($output, 'login.error')  !== FALSE) {
	$reply["code"] = null;
	$reply["statusmessage"] = "not logged in";
} else {
	$reply["code"] = $hash;
	$reply["statusmessage"] = "logged in";
}

if($debug=="on")
	echo "<pre>";
echo jsonpp(json_encode($reply));
?>

