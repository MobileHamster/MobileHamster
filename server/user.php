<?php
include './utils/jsonPrettyPrint.php';

$debug=$_GET["debug"];

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

$arts = $xpath -> query("//div[@class='status']");
$userData["status"] = $arts -> item(0) -> nodeValue;

$arts = $xpath -> query("//div[@class='last']");
$userData["statusTime"] = $arts -> item(0) -> nodeValue;

$arts = $xpath -> query("//td[@id='avatarB']//img/@src");
$userData["avatar"] = $arts -> item(0) -> nodeValue;

// extract profile stats that change often
$keys = $xpath -> query("//table[@class='breff']//td/span");
$stats = array();
foreach ($keys as $key) {
	$values = $xpath -> query("../../td[2]", $key);
	$k = $key -> nodeValue;
	$k = str_replace(" ", "_", $k);
	$k = str_replace(":", "", $k);
	$stats[$k] = trim($values -> item(0) -> nodeValue);
}
$userData["stats"] = $stats;

$keys = $xpath -> query("//table[@class='w100']//td/span");
$information = array();
$i = 0;
foreach ($keys as $key) {
	if($key->nodeValue=='About Me:') {
		$values = $xpath -> query("../div", $key);
		$val = nl2br($values -> item(0) -> nodeValue);
		
	} else {
		$values = $xpath -> query("../../td[2]", $key);
		$val = $values -> item(0) -> nodeValue;
	}
	$information[$i] = array (
		"key" => $key -> nodeValue,
		"value" => $val 
	);
	$i++;
}
$userData["info"] = $information;

if($debug=="on")
	echo "<pre>";

echo jsonpp(json_encode($userData));
?>

