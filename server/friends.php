<?php
include './utils/jsonPrettyPrint.php';

$debug = $_GET["debug"];

$id = $_GET["id"];
$ckfile = "/tmp/" . $id . ".cookie";

$ch = curl_init("http://xhamster.com/my_friends.php");
curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);

$doc = new DOMDocument();

// this line make troubles
@$doc -> loadHTML($output);

$xpath = new DOMXPath($doc);
$arts = $xpath -> query("//div[@class='user ']");

$contacts = array();
foreach ($arts as $art) {
	$contact = array();

	$links = $xpath -> query(".//a/@href", $art);
	$contact["link"] = $links -> item(0) -> value;
	$contact["messages"] = $links -> item(0) -> value . "/messages-1";

	$links = $xpath -> query(".//a", $art);
	$contact["name"] = str_replace("http://xhamster.com/user/", "", $contact["link"]);

	$links = $xpath -> query(".//img/@src", $art);
	$contact["pic"] = $links -> item(0) -> value;

	$links = $xpath -> query(".//div[contains(@class,'iconGender')]/@hint", $art);
	$contact["sex"] = $links -> item(0) -> nodeValue;

	$links = $xpath -> query(".//div[contains(@class,'iconCountry')]/@hint", $art);
	$contact["flag"] = $links -> item(0) -> nodeValue;

	$links = $xpath -> query(".//div[contains(@class,'iconCountry')]/img/@src", $art);
	$contact["flagIcon"] = $links -> item(0) -> nodeValue;
	if ($contact["flagIcon"] == null) {
		$contact["flagIcon"] = "";
	}

	$links = $xpath -> query(".//div[@class='online']/@class", $art);
	$contact["online"] = ($links -> item(0) -> nodeValue === 'online');

	$contacts[] = $contact;
}

if($debug=="on")
	echo "<pre>";
echo jsonpp(json_encode($contacts));
?>

