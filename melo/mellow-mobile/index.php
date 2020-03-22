<?php
//获取access_token
$path = '../../pcs/';
require($path.'core.php');
$access_token = token($path);
//随机展示banner
$path = './res/banner';
$dat = array();
$fct = array();
$dir = opendir($path);
while (($file=readdir($dir))!==false){
	if(stripos($file,'.jpg')>0 || stripos($file,'.png')>0 || stripos($file,'.jpeg')>0 || stripos($file,'.gif')>0 || stripos($file,'.bmp')>0){
		$dat[$file] = filemtime($path.'/'.$file);
		array_push($fct, filemtime($path.'/'.$file));
	};
};
closedir($dir);
rsort($fct);
$key_rdm = rand(0,count($fct)-1);
$key_new = 0;
$key;
//开发者模式显示最新的一张banner
$tag = $_GET['tag'];
if($tag=='dev'){
	$key = $key_new;
}else{
	$key = $key_rdm;
};
//页面输出
$html = file_get_contents('./template.html');
$html = str_replace('{time}', time(), $html);
$html = str_replace('res/banner.jpg', $path.'/'.array_search($fct[$key], $dat), $html);
echo str_replace('{token}', $access_token, $html);
?>