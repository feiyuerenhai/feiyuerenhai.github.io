<?php
//��ȡ���ݹ����������������ļ�����ԭ���ݺ�������
//��ʹ��$_REQUEST[]������ǣ�����Զ�URL�������⣬�൱�鷳
$dat = split("&",$_SERVER["argv"][0]);
$tmp = split("=",$dat[0]);
$filename = $tmp[1];
$tmp = split("=",$dat[1]);
$find = $tmp[1];
$tmp = split("=",$dat[2]);
$replace = $tmp[1];
//���������ip��ַ����Ӧ����
$ip=$_SERVER["REMOTE_ADDR"];
$ipdat = file_get_contents('http://www.youdao.com/smartresult-xml/search.s?type=ip&q='.$ip);
$ipadr = substr($ipdat,strpos($ipdat,"<location>")+10,strpos($ipdat,"</location>")-strpos($ipdat,"<location>")-10);
echo "����: ".$filename." ".$find." ".$replace." -> ";
//�����ļ����滻����
if(substr($filename,strrpos($filename,"."))==".xml"&&file_exists($filename)){
  $contents = file_get_contents($filename);
  $contents = str_replace($find,$replace,$contents);
  $fp = fopen($filename,'w');
  $wr = fwrite($fp,$contents);
  fclose($fp);
  if($wr){
    echo $wr;
    //�����ɹ����¼������������
    $handle=fopen("log.txt","a");
    fwrite($handle,"[".date('Y/m/d H:i:s')."][".$ip." ".$ipadr."] -> ".$filename."\r\n");
    fclose($handle);
  }else{
    echo "����д��ʧ�ܣ�";
  }
}else{
  echo "����Υ�棡";
}
?>
