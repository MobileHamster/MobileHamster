<?php
include './utils/jsonPrettyPrint.php';

$id = $_GET["id"];
$ckfile = "/tmp/" . $id . ".cookie";

$debug = $_GET["debug"];

$contacts = array();

$pageCounter = 1;

do {
	$ch = curl_init("http://xhamster.com/messages-" . $pageCounter);
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
	$arts = $xpath -> query("//tr[contains(@class, 'c')]");

	foreach ($arts as $art) {
		$contact = array();
		$links = $xpath -> query(".//a/@href", $art);
		$contact["link"] = $links -> item(0) -> value;
		$contact["messages_link"] = $links -> item(0) -> value . "/messages-1";

		$links = $xpath -> query(".//a", $art);
		$contact["name"] = str_replace("http://xhamster.com/user/", "", $contact["link"]);

		$links = $xpath -> query("./td[@class='avatar']/a/img/@src", $art);
		if($links->length>0) {
			$contact["pic"] = $links -> item(0) -> value;
		} else {
			$contact["pic"] = "";
		}

		$links = $xpath -> query("./td[@class='uinfo']/span/@hint", $art);
		$contact["sex"] = $links -> item(0) -> nodeValue;

		$links = $xpath -> query("./td[@class='uinfo']/div/@hint", $art);
		if($links->length>0) {
				$contact["flag"] = $links -> item(0) -> nodeValue;
		} else {
			$contact["flag"] = "";
		}

		$links = $xpath -> query("./td[@class='uinfo']/div/img/@src", $art);
		if($links->length>0) {
			$contact["flagIcon"] = $links -> item(0) -> nodeValue;
		} else {
			$contact["flagIcon"] = "";
		}

		$links = $xpath -> query("./td[@class='uinfo']/span[contains(@hint, 'online')]/@hint", $art);
		$contact["online"] = ($links -> length > 0) ? true : false;

		$messages = array();
		$links = $xpath -> query("./td[@class='msgNum']//span[@class='num']", $art);
		$messages["all"] = $links -> item(0) -> nodeValue;
		$links = $xpath -> query("./td[@class='msgNum']//span[@class='numNew']", $art);
		if($links->length==0)
			$messages["new"]=0;
		else
			$messages["new"] = substr($links -> item(0) -> nodeValue,1);
			
		$contact["messages"] = $messages;

		$contacts[] = $contact;
	}
} while ($arts->length > 0);

if ($debug == "on")
	echo "<pre>";
echo jsonpp(json_encode($contacts));
?>
