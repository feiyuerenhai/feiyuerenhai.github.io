<?php
//获取传递过来的三个参数：文件名、原内容和新内容
//若使用$_REQUEST[]方法会牵扯到自动URL解码问题，相当麻烦
$dat = split("&",$_SERVER["argv"][0]);
$tmp = split("=",$dat[0]);
$filename = $tmp[1];
$tmp = split("=",$dat[1]);
$find = $tmp[1];
$tmp = split("=",$dat[2]);
$replace = $tmp[1];
//浏览器所在ip地址及对应区域
$ip=$_SERVER["REMOTE_ADDR"];
$ipdat = file_get_contents('http://www.youdao.com/smartresult-xml/search.s?type=ip&q='.$ip);
$ipadr = substr($ipdat,strpos($ipdat,"<location>")+10,strpos($ipdat,"</location>")-strpos($ipdat,"<location>")-10);
echo "参数: ".$filename." ".$find." ".$replace." -> ";
//操作文件，替换内容
if(substr($filename,strrpos($filename,"."))==".xml"&&file_exists($filename)){
  $contents = file_get_contents($filename);
  $contents = str_replace($find,$replace,$contents);
  $fp = fopen($filename,'w');
  $wr = fwrite($fp,$contents);
  fclose($fp);
  if($wr){
    echo $wr;
    //操作成功则记录操作具体内容
    $handle=fopen("log.txt","a");
    fwrite($handle,"[".date('Y/m/d H:i:s')."][".$ip." ".$ipadr."] -> ".$filename."\r\n");
    fclose($handle);
  }else{
    echo "数据写入失败！";
  }
}else{
  echo "参数违规！";
}
?>
