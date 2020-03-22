<?php
#获取所有列表的最后更新时间
$opt = '<?xml version="1.0" encoding="gb2312"?><data>';
$path = "playlist";
$dir = opendir($path);
while (($file=readdir($dir))!==false&&($file=readdir($dir))!=='.'&&($file=readdir($dir))!=='..'){
	$opt.='<file>'.$file.'</file>'.'<time>'.filemtime($path.'/'.$file).'</time>';
};
$opt.= '</data>';
echo $opt;
closedir($dir);
?>