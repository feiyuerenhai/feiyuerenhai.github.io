<?php
#获取所有列表的最后更新时间
$opt = '';
$path = "playlist";
$dir = opendir($path);
while (($file=readdir($dir))!==false){
	if($file!=='.'&&$file!=='..'){
		$opt .= $file.'+'.filemtime($path.'/'.$file).'|';
	};
};
echo $opt;
closedir($dir);
?>