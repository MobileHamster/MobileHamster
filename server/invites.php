<?php
include './utils/jsonPrettyPrint.php';

$debug = $_GET["debug"];

$id = $_GET["id"];
$ckfile = "/tmp/" . $id . ".cookie";

$contacts = array();

$pageCounter = 1;

do {
	$ch = curl_init("http://xhamster.com/invites-" . $pageCounter);
	$pageCounter++;
	curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);

	//var_dump($output);

	$doc = new DOMDocument();

	// this line make troubles
	@$doc -> loadHTML($output);

	$xpath = new DOMXPath($doc);
	$arts = $xpath -> query("//div[@uid]");

	foreach ($arts as $art) {
		$contact = array();
		$links = $xpath -> query("./div[@class='user ']/a/@href", $art);
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

		$links = $xpath -> query(".//div[@class='iconCountry']/img/@src", $art);
		$contact["flagIcon"] = $links -> item(0) -> nodeValue;
		if ($contact["flagIcon"] == null)
			$contact["flagIcon"] = "";

		$links = $xpath -> query(".//div[@class='online']/@class", $art);
		$contact["online"] = ($links -> item(0) -> nodeValue === 'online');

		$links = $xpath -> query("./@uid", $art);
		$contact["uid"] = $links -> item(0) -> nodeValue;

		$contacts[] = $contact;
	}
} while ($arts->length > 0);

if ($debug == "on")
	echo "<pre>";
echo jsonpp(json_encode($contacts));
?>
