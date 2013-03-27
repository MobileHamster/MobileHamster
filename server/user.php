<?php
include './utils/jsonPrettyPrint.php';

$user = $_GET["user"];
$id = $_GET["id"];
$ckfile = "/tmp/" . $id . ".cookie";

$ch = curl_init("http://xhamster.com/user/" . $user);
curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);

$doc = new DOMDocument();

// this line make troubles
@$doc -> loadHTML($output);

$xpath = new DOMXPath($doc);

$userData = array();
$userData["name"] = $user;

$arts = $xpath -> query("//span[@id='u_status']");
$userData["status"] = $arts -> item(0) -> nodeValue;

$arts = $xpath -> query("//span[@class='u_status_time']");
$userData["statusTime"] = $arts -> item(0) -> nodeValue;

$arts = $xpath -> query("//img[@id='avatarB']/@src");
$userData["avatar"] = $arts -> item(0) -> nodeValue;

// extract profile stats that change often
$keys = $xpath -> query("//td[@align='right']/b");
$stats = array();
foreach ($keys as $key) {
	$values = $xpath -> query("../../td[@align='center']", $key);
	$stats[$key -> nodeValue] = trim($values -> item(0) -> nodeValue);
}
$userData["stats"] = $stats;

$keys = $xpath -> query("//span[@class='label']");
$information = array();
foreach ($keys as $key) {
	$values = $xpath -> query("../../td", $key);
	$information[$key -> nodeValue] = $values -> item(1) -> nodeValue;
}
$userData["info"] = $information;


echo jsonpp(json_encode($userData));
?>

