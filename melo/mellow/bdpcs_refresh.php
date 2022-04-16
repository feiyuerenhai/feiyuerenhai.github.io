<?php
$bdpcs_json = $_POST["json_url"];
function getdat($url){
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
return curl_exec($ch); 
}
$dat=getdat($bdpcs_json);
$json=json_decode($dat);
echo ($json->expires_in)."|".($json->refresh_token)."|".($json->access_token)."|".(date('Y/n/j'));
?>