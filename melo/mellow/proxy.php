<?php
$url = urldecode($_REQUEST["url"]);
if($url){
	$data = file_get_contents($url);
	echo $data;
}
?>