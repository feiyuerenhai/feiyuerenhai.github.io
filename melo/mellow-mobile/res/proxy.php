<?php
function curl($url,$user_agent){
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_USERAGENT,$user_agent);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
	curl_setopt($ch,CURLOPT_TIMEOUT,120);
	$result = curl_exec ($ch);
	curl_close($ch);
	return $result;
};
$url = urldecode($_REQUEST["url"]);
if($url){
	header("Content-type: text/html; charset=utf-8");
	header("Content-Transfer-Encoding: binary");
	echo curl($url,"Mozilla/4.0");
};
?>