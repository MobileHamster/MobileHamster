<?php

$user = $_GET["user"];
$pwd = $_GET["pwd"];

$hash = hash('sha512', $user);

$ckfile = "/tmp/" . $hash . ".cookie";

$ch = curl_init("http://xhamster.com/login.php");
curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
curl_setopt($ch, CURLOPT_POSTFIELDS, "password=" . $pwd . "&remeber=on&username=" . $user);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);

$reply = array();

if ($output) {
	$reply["code"] = null;
	$reply["statusmessage"] = "not logged in";
} else {
	$reply["code"] = $hash;
	$reply["statusmessage"] = "logged in";
}

echo json_encode($reply);
?>

