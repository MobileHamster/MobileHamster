<?php

$user=$_GET["user"];
$ckfile = "/tmp/".$user.".cookie";
$contact=$_GET["contact"];



$ch = curl_init ("http://xhamster.com/user/".$contact."/messages-1");
curl_setopt($ch,CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch,CURLOPT_COOKIEFILE, $ckfile);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$output = curl_exec ($ch);

var_dump($output);

$doc = new DOMDocument();

// this line make troubles
@$doc->loadHTML($output);

$xpath = new DOMXPath($doc);
$arts = $xpath->query("//td[@width='180px']");

$contacts=array(); 
foreach ($arts as $art)
{
	$contact = array();
	$links = $xpath->query(".//a/@href", $art);
	$contact["link"]= $links->item(0)->value;
	$contact["messages"]= $links->item(0)->value."/messages-1";
	
    $links = $xpath->query(".//a", $art);
	$contact["name"]= str_replace("http://xhamster.com/user/", "", $contact["link"]);
	
	$links = $xpath->query(".//img/@src", $art);
	$contact["pic"]= $links->item(0)->value;
	
	$links = $xpath->query(".//span[contains(@class,'iconSex')]/@title", $art);
	$contact["sex"]= $links->item(0)->nodeValue;
	
	$links = $xpath->query(".//span[contains(@class,'iconFlag')]/@title", $art);
	$contact["flag"]= $links->item(0)->nodeValue;
		
	$links = $xpath->query(".//span[contains(@class,'iconFlag')]/img/@src", $art);
	$contact["flagIcon"]= $links->item(0)->nodeValue;
	if($contact["flagIcon"]==null)
		$contact["flagIcon"]="";
	
	$links = $xpath->query(".//div[contains(@class,'iconOnline')]", $art);
	$contact["online"]= ($links->item(0)->nodeValue==='online');
	
	$contacts[]=$contact;
}


echo json_encode($contacts);


?>
