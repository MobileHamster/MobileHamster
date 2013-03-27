<?php
include './utils/jsonPrettyPrint.php';

$user = $_GET["user"];
$ckfile = "/tmp/" . $user . ".cookie";

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
	$arts = $xpath -> query("//td[@width='180px']");

	foreach ($arts as $art) {
		$contact = array();
		$links = $xpath -> query(".//a/@href", $art);
		$contact["link"] = $links -> item(0) -> value;
		$contact["messages"] = $links -> item(0) -> value . "/messages-1";

		$links = $xpath -> query(".//a", $art);
		$contact["name"] = str_replace("http://xhamster.com/user/", "", $contact["link"]);

		$links = $xpath -> query(".//img/@src", $art);
		$contact["pic"] = $links -> item(0) -> value;

		$links = $xpath -> query(".//span[contains(@class,'iconSex')]/@title", $art);
		$contact["sex"] = $links -> item(0) -> nodeValue;

		$links = $xpath -> query(".//span[contains(@class,'iconFlag')]/@title", $art);
		$contact["flag"] = $links -> item(0) -> nodeValue;

		$links = $xpath -> query(".//span[contains(@class,'iconFlag')]/img/@src", $art);
		$contact["flagIcon"] = $links -> item(0) -> nodeValue;
		if ($contact["flagIcon"] == null)
			$contact["flagIcon"] = "";

		$links = $xpath -> query(".//div[contains(@class,'iconOnline')]", $art);
		$contact["online"] = ($links -> item(0) -> nodeValue === 'online');

		$links = $xpath -> query(".//a[@target='_blank']", $art);
		$messages = array();
		$messages["all"] = intval(preg_replace("/ message.*/", "", $links -> item(0) -> childNodes -> item(0) -> nodeValue));
		$messages["new"] = intval(preg_replace("/\((\d+) new\)/", "$1", $links -> item(0) -> childNodes -> item(2) -> nodeValue));
		$contact["messages"] = $messages;

		$contacts[] = $contact;
	}
} while ($arts->length > 0);

echo "<pre>";
echo jsonpp(json_encode($contacts));
?>
