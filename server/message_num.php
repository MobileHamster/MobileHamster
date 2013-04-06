<?php

$id=$_GET["id"];
$ckfile = "/tmp/".$id.".cookie";

$ch = curl_init ("http://xhamster.com/ajax/get_pm_messages_num.php?rand=".mt_rand(10000,99999));
curl_setopt($ch,CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch,CURLOPT_COOKIEFILE, $ckfile);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$output = curl_exec ($ch);


//var_dump($output);

$doc = new DOMDocument();

// this line make troubles
@$doc->loadHTML($output);

$xpath = new DOMXPath($doc);
$arts = $xpath->query("//a[@id='messages_link']");

$message_count=0;
if($arts->length >0 ) {
	$message_count = intval($arts->item(0)->nodeValue);
}
	
$arts = $xpath->query("//a[@id='invites_link']");
if($arts->length > 0)
	$invite_count = intval($arts->item(0)->nodeValue);
else 
	$invite_count=0;

$result=array();
$result["messages"]=$message_count;
$result["invites"]=$invite_count;


echo json_encode($result);

?>
