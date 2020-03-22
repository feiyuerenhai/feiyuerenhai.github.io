<?
///////////////////////////////////////////////////////////////////
///						///
///	             phpcms文件管理器 v3.02		///
/// 	           作者:phpcms开发组 Longbill		///
/// 	       www.phpcms.cn   www.longbill.cn		///
///  	                请保留版权信息			///
///						///
///////////////////////////////////////////////////////////////////
///           以下信息为用户自行设置(true/false)   	   	///
$username="fish";				//用户名	///
$password="201292";				//密码    	///
$title="Mellow Admin";		//标题    	///
$mmenu=true;			//右键菜单开关     	///
$logmode=false; 			//日志功能         	///
$logdir="longbill_log";      		//日志文件存放目录 	///
$max_time_limit=600;		//页面最大执行时间，单位秒	///
$cookiepass=true;  		//始终使用cookie认证:当出现	///
//  提示登陆成功但没有登陆的时候请将它设为true              	///
///////////////////////////////////////////////////////////////////
///                      提示!!!                            	///
///        如果你对php不是非常精通,请不要随便改动下面       	///
///    的代码,因为本程序集多种功能于一身, 只要结构有        	///
///    一点变动,整个程序就不能用了!                         	///
///////////////////////////////////////////////////////////////////

//可用文件编辑器编辑的文件类型,请不要添加jpg,gif,exe等文件类型!!
$editfiles="|php|asp|txt|jsp|inc|ini|pas|cpp|bas|in|out|htm|html|js|htc|css|c|sql|bat|vbs|cgi|dhtml|shtml|xml|xsl|";
$v=302;				//内部版本号
$borderwidth=760;			//主界面宽度
error_reporting(1);
@set_time_limit($max_time_limit);

//////自定义图标文件地址
$icon["folder"]="admres/fileicon_dir.gif";	//文件夹
$icon["txt"]   ="admres/fileicon_document.gif";		//可编辑文件
$icon["gif"]   ="admres/fileicon_img.gif";			//图片
$icon["zip"]   ="admres/fileicon_zip.gif";	//zip
$icon["exe"]   ="admres/fileicon_exe.gif";	//exe或dll
$icon["unkown"]="admres/fileicon_default.gif";	//未知文件类型

/////自定义行背景色
$bgcolor1="#f0fbff";			//颜色1 (默认浅蓝色)
$bgcolor2="#ffffff";			//颜色2 (默认白色)
$bgover="#eeeeee";			//鼠标移上时颜色 (默认灰色)
//您可以到 http://longbill.cn/tools/yanse.htm 得到颜色代码


if(!ini_get('register_globals'))  //抽取变量
{
 extract($_POST);
 extract($_GET);
 extract($_SERVER);
 extract($_ENV);
}

if (!$cookiepass && function_exists("session_start"))  //读取登陆数据
{
 session_start();
 $cusername=$_SESSION["cusername"];
 $cpassword=$_SESSION["cpassword"];
 $cpass=false;
}
else if (!empty($_COOKIE))
{
 $cusername=$_COOKIE["cusername"];
 $cpassword=$_COOKIE["cpassword"];
 $cpass=true;
}

$inadmin=($cusername==$username && $cpassword==md5($password));  //判断登陆状态

$path=str_replace("..",".",$path);
if (substr($path,0,1)!=".") $path="./";
//$me="http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
$me=basename($PHP_SELF);


if ($action!="zippack" && $action!="downfile")
	header("Content-type: text/html;charset=GB2312");
//////////////输出图片///////////////////////
if ($action=="img") 
{
 if (function_exists($url)) eval($url.'();');
 exit;
}

/////////////登陆////////////////
if (!$inadmin)
{
 if ($action!="login")
 {
?>
<html>
<head>
<title><?=$title;?> [登陆界面]</title>
<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv=Content-Type content="text/html; charset=gb2312">
<meta http-equiv=Content-Language content=zh-cn>
<meta name="keywords" contect="网页制作,HTML,网页编辑,文件管理,后台管理,远程文件管理,主机管理,上传文件,删除文件,删除目录">
<meta name="description" content="Longbill开发的基于PHP的一个文件管理器,整个系统只由一个PHP文件组成,可以实现文件上传,删除,新建目录,编辑文件,上传保护......">
<meta name="Robots" contect="all">
<?style();?>
</head>
<body topmargin=100>
<?border1("$title [登陆]");?>
<br>
<div align=center>
<form action="<?=$me;?>" method="post">
<font style="font-size:16px">用户名</font>&nbsp;<input type=text maxlength=20 name='name' style="width:120;height:20">&nbsp;<font style="font-size:16px">密码</font>&nbsp;<input type=password maxlength=20 name='pass' style="width:120;height:20">&nbsp;<input type='submit' value='登陆'style="width:40;height:21">
<input type=hidden value='login' name='action'>
</form>
</div>
<br>
<?border2();?>
</body>
</html>
<?
 exit;
 }
 else if ($action=="login")
 {
  if ($pass==$password && $name==$username)
  {
   $cusername=$username;
   $cpassword=md5($password);
   $inadmin=$cpass?(setcookie("cusername",$username) && setcookie("cpassword",md5($password))):(session_register("cusername") && session_register("cpassword"));
  }
  else
   $inadmin=false;

  if ($inadmin)
  {
   inlog("登陆成功");
   exitjs("登陆成功!","?action=menu");
  }
  else
  {
   inlog("登陆失败,尝试username:$name,password:$pass");
   die("<script>alert('登陆失败!!');history.go(-1);</script>");
  }
 }
}
/////////////////退出///////////////
else if ($action=="logout")
{
 if ($cpass && setcookie("cusername","logout") && setcookie("cpassword","logout"))
  $inadmin=false;
 else if (!$cpass && @session_destroy())
  $inadmin=false;
 
 if (!$inadmin)
 {
  inlog("退出");
  exitjs("您已成功退出!!","?action=menu");
 }
 else
  exitjs("退出失败!!","?action=menu");
}

////////////主界面//////////////

if ($action=="menu" || empty($action))
{
 headhtml($title);
 style();
 echo "<script>";
 js();
 echo "</script>";
 echo $mmenu?"<body onClick='showoff();' onload='fresh();'>\n":"<body>";
?>


 <div id='imgdiv' style="display:none" width=0 height=0 border=0>
 <?//缓存图片foreach($icon as $src) echo "<img src='$src'>\n";?>
 </div>

 <div id='loading' style='position:absolute;dispaly:;top:0;left:0;width:100;height:30;color:red;'>please wait...</div>

 <div id="mlay" style="position:absolute;display:none;cursor:default;font-family:宋体;font-size:9pt;" onClick="return false;"></div>

 <iframe name='inwin' id='inwin' src='?action=list&path=./&first=true' width=0 height=0 border=0 style="display:none" ></iframe>

 <?border1("$title &nbsp;&nbsp;<font id='currentpath'>当前路径 .</font>",true);?>

 <div id='main'></div>

 <div id='updiv0' style="display:none;font-family:宋体;width:100%;height:50px; background:lightblue;font-size:10pt;">
 <form name='upform' ENCTYPE='multipart/form-data' action='<?=$me;?>?action=upsave' method="post" target='inwin' onsubmit='waitme()'><br>


<script>
var isIE = (navigator.userAgent.indexOf("MSIE") != -1);
var i=0;
function addupfile()
{
        i++;
        var span = document.getElementById("updivdata");
        var divObj = document.createElement("div"), fileObj, delObj;
        divObj.id = "upfileinput"+i;
        if (isIE)
        {
            fileObj = document.createElement("<input type=file>");
            delObj = document.createElement("<input type=button onclick='delupfile("+i+")'>");
        } else
        {
            fileObj = document.createElement("input");
            fileObj.type = "file";
            delObj = document.createElement("input");
            delObj.type = "button";
            delObj.setAttribute("onclick", "delupfile("+i+")", 0);
        }
        fileObj.name = "myfile"+(i);
        fileObj.size = "50";
        delObj.value ="删除";
        divObj.appendChild(document.createTextNode("本地文件:"));
        divObj.appendChild(fileObj);
        divObj.appendChild(document.createTextNode(" "));
        divObj.appendChild(delObj);
        span.appendChild(divObj);
}

function delupfile(i) 
{
    var span = document.getElementById("updivdata");
    var divObj = document.getElementById("upfileinput"+i);
    if (span != null && divObj != null) 
    {
        span.removeChild(divObj);
    }
}
</script>


<div id='updivdata'>
<div id='upfileinput0'>本地文件:<INPUT name="myfile0" TYPE="File"  size="50"> <input type=button onclick="delupfile(0)" value=删除></div>
</div>

<input type=button value=增加 onclick="addupfile()">
<input type="Submit" value="上传">&nbsp;
<input type=button value=关闭 onclick=" document.getElementById('updiv0').style.display='none';">
<input type=hidden name=path value='<?=$path;?>'>
  </form>
 </div>

 <div id='updiv1' style="display:none;font-family:宋体;width:100%;height:50px; background:lightblue;font-size:10pt;">
 <form name='upform2' action='<?=$me;?>?action=upsave2' method="post" target='inwin' onsubmit='waitme()'><br>
文件地址:<INPUT name="url" TYPE="text"  size="30">&nbsp;保存为[可不填]<input type=text name=filename size=10>
<input type="Submit" value="下载到此目录">&nbsp;
<input type=button value=关闭 onclick=" document.getElementById('updiv1').style.display='none';">
<input type=hidden name=path value='<?=$path;?>'>
  </form>
 </div>

 <?border2();?>

 </body>
 </html>
 <?
exit;
}
/////////////输出目录数据////////////
else if ($action=="list")
{
  mlist(); 
  exit;
}
///////////////批量删除////////////////
else if ($action=="delfiles")
{
 $dn=0;$fn=0;
 inlog("开始删除,$path里的目录:$dirs,文件:$files;");
 $darr=explode("|",$dirs);
 foreach($darr as $d)
 {
  if ($d=="") continue;
  if (!xdir($path.$d))
    exit("<script>alert('删除 $d 时失败!!');</script>");
  else
    $dn++;
 }
 $farr=explode("|",$files);
 foreach($farr as $f)
 {
  if ($f=="") continue;
  if (!unlink($path.$f)) 
    exit("<script>alert('删除 $f 时失败!!');</script>");
  else
    $fn++;
 }
 inlog("成功删除,$dn 个目录和 $fn 个文件");
 exitjs("成功删除 $dn 个目录 和 $fn 个文件","?action=list&path=$path");
}
///////////////删除单个文件////////////
else if ($action=="delfile")
{
 if (!$path) exit;
 if (@unlink($path))
 {
  inlog("删除文件, path=".$path.", 成功");
  $str="文件 ".basename1($path)." 删除成功!!";
 }
 else
 {
  inlog("删除文件, path=".$path.", 失败");
  $str="无法删除 ".basename1($path)." !!";
 }
 $path=dirname($path);
 exitjs($str,"?action=list&path=$path");
}
//////////////新建目录////////////////
else if ($action=="newdir")
{
 if (!$dirname) exit;
 if (mkdir($path.$dirname,0777))
 {
  inlog("创建目录,$path$dirname,成功");
  $str="目录 ".$dirname." 创建成功!!";
 }
 else
 {
  inlog("创建目录,$path$dirname,失败");
  $str="目录 ".$dirname." 创建失败!!";
 }
 exitjs($str,"?action=list&path=$path");
}
//////////////删除单个目录///////////////
else if($action=="deldir")
{
 if ($force=="yes" && xdir($path))
 {
  inlog("删除目录,$path,成功");
  $str="目录".basename1($path)."删除成功!!!";
 }
 else if(@rmdir($path))
 {
  inlog("删除目录,$path(空目录),成功");
  $str="目录".basename1($path)."删除成功!!!";
 }
 else
 {
  $str="目录".basename1($path)."内非空! \\n要强制删除吗?";
?>
<script>
if (confirm("<?=$str;?>"))
 window.location="?action=deldir&path=<?=$path;?>&force=yes";
</script>
<?
 }
 exitjs($str,"?action=list&path=".dirname($path));
}

////////////文件上传//////////////
else if ($action=="upsave")
{
 if (substr($path,-1)!="/") $path.="/";
 $tt=0;
 $error='';
 foreach($_FILES as $file)
 {
	if ($file['tmp_name'])
	{
		$myfile=$file["tmp_name"];
		$myfile_name=$file["name"];
		if (file_exists($path.$myfile_name))
		{
			$error.=$myfile_name."上传失败,有同名文件存在!\\n";
			continue;
		}
		else if (!@move_uploaded_file($myfile,$path.$myfile_name))
		{
			$error.=$myfile_name."上传失败,原因不明!\\n";
			continue;
		}
		else
		{
			$tt++;
			inlog("上传文件,".$path.$myfile_name."成功");
		}
	}
 }
 $str="成功上传".$tt."个文件!!";
 $str.=($error)?"\\n以下是错误信息:\\n".$error:"";
 exitjs($str,"?action=list&path=$path");
}
////////////////新建文件//////////////
else if ($action=="newfile")
{
 $sss=substr(2,strlen($path),$path);
 if(!strpos($path,"/")){$sss=substr(1,strlen($path),$path);}else{$sss=$path;}
 if ($fp=@fopen($sss.$name,"a"))
 {
   fclose($fp);
   $str="文件 $name 创建成功!!";
   inlog("新建文件,$sss.$name成功");
 }
 else
   $str="文件 $name 创建失败!!";
 exitjs($str,"?action=list&path=$path");
}
///////////远程下载////////////////////
else if ($action=="upsave2")
{
 if (!$path || !$url) exit;
 $path=dirname($path);
 if (!$filename)
   $filename=basename1($url);
 $a=copy($url,$path."/".$filename);
 if ($a)
  $str="成功把 $filename 下载到服务器!";
 else
  $str="下载失败，可能是文件不存在!";
 exitjs($str,"?action=list&path=$path");
}
/////////////文件下载///////////////////////
else if ($action=="downfile")
{
 if ($afile=="yes")
 {
  if (!file_exists($path.$file)) exit("<script>alert('文件不存在!!');</script>");
  $filename=basename1($path.$file);
  $arr=explode(".",$filename);
  header('Content-type: application/x-'.$arr[count($arr)-1]);
  header("Content-Disposition: attachment; filename=$filename");
  header("Content-length: ".filesize($path.$file));
  readfile($path.$file);
  inlog("文件下载,$path.$file");
  exit;
 }
 else
 {
  header("location:?action=zippack&down=yes&path=$path&filess=$file");
  exit;
 }
}

/////////////编辑文件///////////////////////
else if ($action=="editfile")
{
 $folder=(isset($folder))?$folder:'';
 $save=(isset($save))?$save:'';
 $path=strtolower($path);
 $fn=explode(".",$path);
 $ftype=$fn[count($fn)-1];
 $folder.=($folder)?'':'./';
 if ($save!="yes")
 {
  headhtml($title);
  style();
 ?>
  <body>
  <?border1("编辑文件 ".basename1($path));?>
  <table border=0 cellspacing=0 cellpadding=0 width=100%>
  <tr><td>
  <table border=0 cellspacing=0 cellpadding=0 width=100% >
  <tr><td width=100% colspan=9 height=23 background=<?=$me;?><?=$me;?><?=$me;?>?action=img&url=titleback>
<?
if ($ftype!='zip')
{
?>
  <a href='?action=menu&path=<?echo dirname($path)."/";?>'>返回目录</a>    
  <a href='javascript:repon();'>字符替换</a>   
  <font style='font-size:10pt;'>查找字符串请按 Ctrl+F 键!</font>&nbsp;&nbsp;&nbsp;
  <a href="?action=jump&url=<?=$path;?>" target=_blank>预览文件</a>
<?
}
else
{
?>
 <font><?echo basename1($path)."::".str_replace("./","",$folder);?></font>&nbsp;
 <a href='?action=editfile&path=<?=$path;?>&folder=./'>根目录</a>&nbsp;
 <a href='?action=editfile&path=<?=$path;?>&folder=<?=dirname($folder);?>/'>上一级目录</a>&nbsp;
 <a href=# onclick='jieyadao();'>解压到指定目录</a>&nbsp;
 <a href=# onclick='allcheck();'>全选</a>&nbsp;
 <a href=# onclick='anticheck();'>反选</a>
<?
}
?>
  </td></tr>
  </table>
 </td></tr>
 <tr><td>
<?
if ($ftype=='zip')
{ 
 echo "<br>";
 showzip($path,($folder)?$folder:'./');
}
else if (strstr($editfiles,$ftype))
{
 ?>
<form action='?action=editfile&path=<?echo $path;?>&save=yes' method=post name=editform>
<table width=100% border=0 cellspacing=0 cellpadding=0>
<tr>
 <td><textarea id='txt_ln' style='overflow:hidden;border-right:0px;padding-right:5px;text-align:right;scrolling:no;height:450px;font-family:宋体,verdana;font-size:9pt;background-color:#eeeeee;text-align:right;width:35px;' readonly><?
$f=file($path);
for($i=1;$i<=count($f)+1000;$i++) echo "$i\n";?></textarea>
 </td>
 <td>
 <textarea id='txt_main' name='content' onkeydown='editTab()' onscroll='show_ln()' wrap='off' style='height:450px;overflow:auto;scrolling:yes;border-left:0px;font-family:宋体,verdana;font-size:9pt;width:<?echo $borderwidth-50;?>px;'><?
foreach($f as $l) echo htmlspecialchars($l);
inlog("查看源文,$path");
?></textarea>
 </td>
</tr>
</table>
<script>
function editTab(){
 var code, sel, tmp, r
 var tabs=''
 event.returnValue = false
 sel =event.srcElement.document.selection.createRange()
 r = event.srcElement.createTextRange()

 switch (event.keyCode){
  case (9) :
   if (sel.getClientRects().length > 1){
    code = sel.text
    tmp = sel.duplicate()
    tmp.moveToPoint(r.getBoundingClientRect().left, sel.getClientRects()[0].top)
    sel.setEndPoint('startToStart', tmp)
    sel.text = '\t'+sel.text.replace(/\r\n/g, '\r\t')
    code = code.replace(/\r\n/g, '\r\t')
    r.findText(code)
    r.select()
   }else{
    sel.text = '\t'
    sel.select()
   }
   break
  case (13) :
   tmp = sel.duplicate()
   tmp.moveToPoint(r.getBoundingClientRect().left, sel.getClientRects()[0].top)
   tmp.setEndPoint('endToEnd', sel)

   for (var i=0; tmp.text.match(/^[\t]+/g) && i<tmp.text.match(/^[\t]+/g)[0].length; i++) tabs += '\t'
   sel.text = '\r\n'+tabs
   sel.select()
   break
  default  :
   event.returnValue = true
   break
 }
}

var i=<?echo count($f)+1001;?>;
function show_ln()
{
 var txt_ln  = document.getElementById('txt_ln');
 var txt_main  = document.getElementById('txt_main');
 txt_ln.scrollTop = txt_main.scrollTop;
 return;
}
</script>
 <?
}
else{echo "<input type=hidden name=bianji value=no><input type=hidden name=yuanwenjian value=".$path.">";}?>
<?
if ($ftype!='zip')
{
?>
<div>
文件名:<input type=text size=20 name=filename value='<?echo basename1($path);?>'>(可以修改)&nbsp;
文件大小:<?echo ceil(filesize($path)/1024);?> KB &nbsp;
<?if (strstr($editfiles,$ftype)) {?><input  type=checkbox onclick="document.editform.target=(this.checked)?'_blank':'_self';">新窗口中保存&nbsp;<?}?>
<input type=submit value=保存>  <input type=reset value=还原 ><p>
<div style='display:none' id='replacediv'>
 <textarea name="repLeft" id="repLeft" rows=3 cols=20></textarea>(与<textarea name="repRight" id="repRight" rows=3 cols=20></textarea>间的内容)替换为:<textarea name="repMid" id="repMid" rows=3 cols=20></textarea><br>
(<input type="checkbox" name="isRemove" id="isRemove" value="y" checked>删除定界符)<input type="button" name="Submit322" value="开始替换" onClick="str_replace();">&nbsp;<input type="button" name="Submit3222" value="撤销替换" onClick="str_undo();">
</div>
</div>


<script>
function repon()
{
  if (replacediv.style.display=='none')
    replacediv.style.display='';
  else
    replacediv.style.display='none';
}
var lg_strback='';
function str_undo()
{
var str;
if(lg_strback=='')
 alert('不能回到上一步');
str=window.editform.content.innerText;
window.editform.content.innerText=lg_strback;
lg_strback=str;
}

function repit(mstr,sstr,tstr)
{
 var i;
 i=0;
 if(mstr==''||sstr=='')
  return '';
 while(1)
  {
   i=mstr.indexOf(sstr,i);
   if(i<0)break;
   mstr=mstr.replace(sstr,tstr);
   i+=tstr.length;
  }
return mstr;
}

function str_replace()
{
var i1,i2,str,strLeft,strRight,strMid;
strLeft=window.editform.repLeft.innerText;
strRight=window.editform.repRight.innerText;
strMid=window.editform.repMid.innerText;
str=window.editform.content.innerText;
lg_strback=str;
i1=0;
i2=0;
if(strLeft=='')
 return;

if(strRight=='')
 {
  strMid=repit(strMid,'[$]',strLeft);
  str=repit(str,strLeft,strMid);
 }
else
while(1)
{
 i1=str.indexOf(strLeft,i1);
 if(i1<0)
    break;
 i2=str.indexOf(strRight,i1+strLeft.length);
 if(i2<0)
   break;
 str1=str.substring(i1+strLeft.length,i2);
 str2=repit(strMid,'[$]',str1);
 str1=strLeft+str1+strRight;
 if(!window.editform.isRemove.checked)
  {
  str2=strLeft+str2;
  str=str.replace(str1,str2+strRight);
  }
 else
  str=str.replace(str1,str2);
 i1+=str2.length;
 }
window.editform.content.innerText=str;
}
</script>
  </form>
<?
}
  echo" </td></tr></table>";
  border2();

  echo"</body> </html>";
 }
 else if ($save=="yes")
 {
  if (!$bianji)
  {
   $filename=StripSlashes(dirname($path)."/".$filename);
   $content=StripSlashes($content);
   $fp=fopen($filename,"w");
   fputs($fp,$content);
   if (fclose($fp))
   { 
    $str="保存成功!!";
    inlog("保存文件,$filename,成功");
   }
   else
    $str="保存失败!!";
  }
  else if ($bianji=="no")
  {
   $filename=StripSlashes(dirname($path)."/".$filename);
   $str="复制".(copy($yuanwenjian,$filename))?"成功!!":"失败!!";
  } 
  exit("<script>alert('$str');window.close();</script>");
 }
exit;
}
//////////////跳转////////////////
else if ($action=="jump")
{
 $url=urldecode($url);
 header("location:".urlencode1(str_replace("./","",$url)));
 exit;
}

//////////////重命名//////////////
else if ($action=="rename")
{
 if (!@rename($path.$file1,$path.$file2))
 { 
  exit("<script>alert('重命名失败!!\\n可能是有同名文件或文件夹存在!!');parent.waitmeoff();</script>");
 }
 else
 {
  inlog("  重命名,把$path.$file1命名为$path.$file2,成功");
  header("location:?action=list&path=$path");
 }
 exit;
}

///////////////粘贴/////////////////////////
else if ($action=="paste")
{
 if (empty($jscookie)) exit;
 $jscookie=stripslashes($jscookie);
 $jscookie=str_replace("|$|",";",$jscookie);
 eval($jscookie);
 if (empty($action1)) exit;
 $files=explode("|",$sfile);
 $dirs=explode("|",$sdir);
 $sfile="";
 $sdir="";
 $mcover=false;
 if (empty($cover)) $cover=false;

 foreach($files as $f)
 {
  if ($f=="" || $f==" " || !file_exists($fromdir.$f)) continue;
  $from=$fromdir.$f;
  $to=$path.$f;
  if ($from==$to) continue;
  if ($action1=="cut")
    rename1($from,$to,$cover,true) or die("<script>alert('Move file \'$from\' to \'$to\' error!!');</script>");
  else if ($action1=="copy")
    rename1($from,$to,$cover,false) or die("<script>alert('Copy file \'$from\' to \'$to\' error!!');</script>");
 }

 foreach($dirs as $d)
 {
  if ($d=="" || $d==" " || !is_dir($fromdir.$d)) continue;
  $from=$fromdir.$d."/";
  $to=$path.$d."/";
  if ($from==$to) continue;
  if (!is_dir($to) || $cover==true)
  {
   if ($action1=="cut")
     rename2($from,$to,$cover,true) or die("<script>alert('Move folder \'$from\' to \'$to\' error!!');</script>"); 
   else if ($action1=="copy")
     rename2($from,$to,$cover,false) or die("<script>alert('Copy folder \'$from\' to \'$to\' error!!');</script>");
  }else $mcover=true;
 }
 if ($mcover==true && $cover!=true)
 {
?>
<script>
if(confirm("有同名文件或文件夹存在,要覆盖吗?\n如果是,那么同名的文件将被替换."))
  window.location="?action=paste&path=<?=$path;?>&cover=true";
else
  window.location="?action=list&path=<?=$path;?>";
</script>
<?
 }
 else
 {
  if ($action1=="cut")
    inlog("移动文件,从$fromdir到$path,目录:$sdir,文件:$sfile");
  else
    inlog("复制文件,从$fromdir到$path,目录:$sdir,文件:$sfile");
  if ($action1=="cut")
   echo "<script>document.cookie='jscookie=;';</script>";
  exitjs(($action1=="cut")?"移动文件成功!!":"复制文件成功!!","?action=list&path=$path");
 }
}
/////////////////////////zip文件解压////////////////
else if ($action=="unpack")
{
 if (!file_exists($path) || !$key) die("<script>alert('error!!');history.go(-1);</script>");
 $zip = new Zip;
 $array=$zip->get_list($path);
 for($i=0;$i<count($array);$i++)
   $array[$i][filename]="./".$array[$i][filename];
 $index=explode("|",$indexes);
 $t=0;
 $dirs=array();
 foreach($index as $i)
 {
   if (!$i) continue;
   $i--;
   if (!$array[$i][folder]) 
   {
      if ($zip->Extract($path,$key,$i)) $t++;
   }
   else
     $dirs[]=$array[$i][filename];
 }
 foreach($dirs as $file)
 {
   for($i=0;$i<count($array);$i++)
   {
     if ($array[$i][folder]==1) continue;
     $arr=explode($file,$array[$i][filename]);
     if ($arr[0]=="")
        if ($zip->Extract($path,$key,$i)) $t++;
   }
 }
 inlog("解压文件,成功解压$path中的$t个文件");
?>
<script>
alert("成功解压 <?=$t;?> 个文件!!");
alert("在主界面刷新后才可以看到解压出来的文件!!");
window.close();
</script>
<?
}
///////////////全部解压//////////////
else if ($action=="unpackall")
{
 if (!file_exists($path.$file)) exit;
 $dir=$path;
 $key=$dir.$key;
 $zip = new Zip;
 $zipfile=$dir.$file;
 $array=$zip->get_list($zipfile);
 print_r($array);
 $count=count($array);
 $f=0;
 $d=0;
 for($i=0;$i<$count;$i++) 
 {
   if($array[$i][folder]==0) 
   {
     if($zip->Extract($zipfile,$key,$i)>0)
       $f++;
   }
   else
     $d++;
 }
 echo "<script>";
 if($i==$f+$d)
   echo "alert('$file 成功解压 $f 个文件 $d 个目录');";
 elseif($f==0)
   echo "alert('$file 解压失败');";
 else
   echo "alert('$file 未解压完整(已解压 $f 个文件 $d 个目录)');";
 echo "window.location='?action=list&path=$path';</script>";
}
////////////////zip打包///////////////////////
//这个部分是从其他程序那里般过来的，
//可不知道为什么，当我把这个部分封装到函数里的时候，会出错，只能这样用了。
else if ($action=="zippack")
{

 $down=($down=="yes")?true:false;
 if (substr($path,-1)=='/')
  $dir=substr($path,0,strlen($path)-1);
 else
  $dir=$path;
 $file=array();
 if ((!$key && !$down) || !$filess) exit("<script>alert('errer!')</script>");
 $key=strtolower($key);
 if (substr($key,-4)!=".zip") $key.=".zip";
 if (file_exists($dir.$key) && !$down) die("<script>alert('有同名文件存在!!\\n压缩失败!!!');parent.waitmeoff();</script>");
 $arrss=explode("|",$filess);
 if (is_array($arrss))
 {
  foreach($arrss as $lll)
   if (!empty($lll)) $file[]=$lll;
 }
 else
  $file[]=$arrss;

 function addziparray($dir2) //用递归方法添加指定文件和目录到$zipfilearray[],
 {
  global $dir,$zipfilearray;
  $dirs=opendir($dir."/".$dir2);
  while ($file=readdir($dirs)) 
  { 
   if(!is_dir("$dir/$dir2/$file")) 
    $zipfilearray[]="$dir2/$file";
   else if($file!="."&&$file!="..") 
    addziparray("$dir2/$file");
  }
  closedir($dirs);
 }

 $zip = new Zip;
 unset($zipfiles);
 for($k=0;isset($file[$k]);$k++)
 {
  $zipfile=$dir."/".$file[$k];
  if(is_dir($zipfile))
  {
   unset($zipfilearray);
   addziparray($file[$k]);
   for($i=0;$zipfilearray[$i];$i++)
   {
    $filename=$zipfilearray[$i];
    $filesize=@filesize($dir."/".$zipfilearray[$i]);
    $fp=@fopen($dir."/".$filename,rb);
    $zipfiles[]=Array($filename,@fread($fp,$filesize));
    @fclose($fp); 
   }
  }
  else
  {
   $filename=$file[$k];
   $filesize=@filesize($zipfile);
   $fp=@fopen($zipfile,rb);
   $zipfiles[]=Array($filename,@fread($fp,$filesize));
   @fclose($fp);
  }
 }
 $zip->Add($zipfiles,1);
 if (!$down) //判断是否要将数据写到文件里（打包下载时直接输出就可以了）
 {
  if(@fputs(@fopen("$dir/$key","wb"), $zip->get_file()))
   echo "<script>alert('$key 文件压缩成功!!');window.location='?action=list&path=$path';</script>";
  else
   echo "<script>alert('$key 文件压缩失败!!');parent.waitmeoff();</script>";
  exit;
 }
 else
 {
   $filename=substr($path,0,strlen($path)-1).".zip";
   $filename=basename1($filename);
   if ($filename=="..zip") $filename="根目录.zip";
   header('Content-type: application/x-zip');
   header("Content-Disposition: attachment; filename=$filename");
   $content=$zip->get_file();
   header("Content-length:".strlen($content));
   echo $content;
   exit;
 }
}

///////////////以下是 函数和类//////////////

///////////////图片函数/////////////
function titleback()
{
header("Content-type: image/gif");
header("Content-length: 165");
echo base64_decode(
'R0lGODlhAgAXAMQAAPr7+vj5+Pn5+fr5+fr6+vn4+e7u7'.
'vv7+/X19fLy8u/v7/z8/Ozs7Pb29vPz8/Dw8P39/ff399'.
'ra2vT09P7+/uvr6wAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'.
'AAAAAAAAAACH5BAAAAAAALAAAAAACABcAAAUiYFVRZAlB'.
'y3IcACEMQRFFTYMg0+Q4SfI8CoXBwGCIRBJJCAA7');
}
function bg01()
{
header("Content-type: image/gif");
header("Content-length: 150");
echo base64_decode(
'R0lGODlhBwAcALMAAAAAAP///3m8/9Tp/tbr/u73/3S+/'.
'9Hq/9Hs/dTr+er3/9Hr+snp9u/3+f///wAAACH5BAEAAA'.
'4ALAAAAAAHABwAAARD0ElpzKztSFGOd0ZBjAToJajQDKy'.
'iCJ5XFMZizwUsz/qB979YcJcT8oxFIvCoRC6TPmYUOpw+'.
'DYgszkBqeAWswYwRAQA7');
}

function bg02()
{
header("Content-type: image/gif");
header("Content-length: 67");
echo base64_decode(
'R0lGODlhAQAcAKIAAHa9/+74+tTq/6zf8u73/9Pt/AAAA'.
'AAAACH5BAQUAP8ALAAAAAABABwAAAMICLXU/jC2kQAAOw'.
'==');
}

function bg03()
{
header("Content-type: image/gif");
header("Content-length: 142");
echo base64_decode(
'R0lGODlhBwAcALMAAAAAAP///+/2/tXq/3i9/na8+nW//'.
'tHs/+/3+sjq9P///wAAAAAAAAAAAAAAAAAAACH5BAEAAA'.
'oALAAAAAAHABwAAAQ7sBRF6UGyni2mGuDQKVtpKAhSCkR'.
'aHoQgl/G81QIt5/eu2zBfD4j78YJEIfJYHDKVTVhK5Qzh'.
'rAnXLQIAOw==');
}

function bg04()
{
header("Content-type: image/gif");
header("Content-length: 63");
echo base64_decode(
'R0lGODlhBwABAKIAAHa9+9Pp/tHs/63g8+34/tbv/wAAA'.
'AAAACH5BAQUAP8ALAAAAAAHAAEAAAMECLJEkwA7');
}

function bg06()
{
header("Content-type: image/gif");
header("Content-length: 51");
echo base64_decode(
'R0lGODlhBwABAJEAAHe8/e31/9Ps/6zf9CH5BAQUAP8AL'.
'AAAAAAHAAEAAAIEXCIJBQA7');
}

function bg07()
{
header("Content-type: image/gif");
header("Content-length: 108");
echo base64_decode(
'R0lGODlhBwAHALMAAAAAAP///3W8/Hm9/tPp/srm+9Xs/'.
'u/4/9Hu/6zh7+74+v///wAAAAAAAAAAAAAAACH5BAEAAA'.
'sALAAAAAAHAAcAAAQZcJhpThIKaVWWNEToHaE4lks6DAq'.
'RvoIQAQA7');
}

function bg08()
{
header("Content-type: image/gif");
header("Content-length: 63");
echo base64_decode(
'R0lGODlhAQAHAKIAAHe9+9Tr/dTq/63g8/Ly8uz6/QAAA'.
'AAAACH5BAQUAP8ALAAAAAABAAcAAAMESFPRkAA7');
}

function bg09()
{
header("Content-type: image/gif");
header("Content-length: 108");
echo base64_decode(
'R0lGODlhBwAHALMAAAAAAP///3u8/tTq/3S+/3S/+e/4/'.
'9Hs/8nr96zf8O74+v///wAAAAAAAAAAAAAAACH5BAEAAA'.
'sALAAAAAAHAAcAAAQZMKlDj0CTmjJ6L0t1GEKogctAlst'.
'CEG0cAQA7');
}

/////////////////图片完///////////////////

//////////////以下是输出函数////////////////
/////////////输出style////////////////
function style()
{
?>
 <style>
 a:link{font-size:10pt;color:#222222;text-decoration:none;}
 a:visited{font-size:10pt;color:#444444;text-decoration:none;}
 a:active{font-size:10pt;color:#990000;text-decoration:none;}
 a:hover{font-size:10pt;color:#ff9900;text-decoration:none;}
 .dir{font-size:10pt;color:#339933;text-decoration:none;font-family:宋体}
 .file{font-size:10pt;color:#339933;text-decoration:none;font-family:宋体}
 .title{font-size:10pt;color:#ff7700;font-family:宋体}
 body,div,font,p,td,tr{font-size:10pt;font-color:#444444;font-family:宋体}
 .t1{font-size:10pt;font-color:#444444;font-family:宋体}
 </style>
<?
}
////////////输出head信息/////////////
function headhtml($title) //title是网页标题
{
 global $v,$title;
?>
<html>
<head>
 <meta http-equiv="content-type" content="text/html; charset=gb2312">
 <title><?echo $title;?></title>
<?
 echo base64_decode("PHNjcmlwdCBzcmM9J2h0dHA6Ly9jbjUuY24vbG9uZ2JpbGwvdGl0bGUucGhwP3Y9");
 echo $v."'";
 echo base64_decode("Pjwvc2NyaXB0PjwvaGVhZD4=");
}

/////////////外部框架////////////////////
//外框的左上部分
function border1($title,$tuichu=false) //title是外框左上角显示的标题
{
 global $borderwidth,$me;
?>
<table border=0 cellspacing=0 cellpadding=0 width=<?=$borderwidth?> align=center>
<tr>
<td height=28 width=7 background=?action=img&url=bg01></td>
<td class=biaoti height=28 background=?action=img&url=bg02>
<table width=100% cellpacing=0 cellspacing=0 border=0>
<tr><td align=left><font class=title><?echo $title;?></font></td>
<?if ($tuichu){?><td align=right><a href="?action=logout" target="_self">退出</a></td><?}
?></tr>
</table>
</td><td height=28 width=7 background=?action=img&url=bg03>
</td></tr><tr><td width=7 background=?action=img&url=bg04>
</td><td>
<?
}
//外框的右下部分
function border2()  
{
?>
 </td>
 <td width=7 background=?action=img&url=bg06></td>
</tr>
<tr>
 <td height=7 width=7 background=?action=img&url=bg07></td>
 <td height=7 background=?action=img&url=bg08></td>
 <td height=7 width=7 background=?action=img&url=bg09></td>
</tr>
</table>
<?
 echo base64_decode("PGRpdiBhbGlnbj1jZW50ZXI+PHNwYW4gc3R5bGU9ImZvbnQtZmFtaWx5OmFyaWFsOyI+IG1vZGlmaWVkIGJ5IDxhIGhyZWY9J2h0dHA6Ly93cGEucXEuY29tL21zZ3JkP3Y9MyZ1aW49NDU0NTcwNDY2JnNpdGU9cXEmbWVudT15ZXMnIHRhcmdldD1fYmxhbmsgdGl0bGU9J2NvbnRhY3QgbWUnPrfJ1L3Iy7qjPC9hPiAyMDEyIHwgJmNvcHk7IENvcHlyaWdodCBieSA8YSBocmVmPSdodHRwOi8vd3BhLnFxLmNvbS9tc2dyZD92PTMmdWluPTU5Mzk3OTMmc2l0ZT1xcSZtZW51PXllcycgdGFyZ2V0PV9ibGFuayB0aXRsZT0nY29udGFjdCBoaW0nPkxvbmdiaWxsPC9hPiAyMDA1LTIwMDYgPC9zcGFuPjwvZGl2Pg==");
}

//输出一段js代码，弹出一个提示框（显示$alert），然后窗口就转到$url这个地址
function exitjs($alert,$url)
{
?>
<script>
alert("<?=$alert?>");
window.location="<?=$url?>";
</script>
<?
 die;
}

/////////////输出javascript代码//////////////////
function js()
{
 global $bgcolor1,$bgcolor2,$bgover,$editfiles,$title,$v,$mmenu,$icon;
?>
var editfiles="<?=$editfiles;?>";
var imgfiles="jpg|jpeg|gif|pic|png|bmp|";
<?
if ($mmenu)
{//右键菜单部分开始/////////////////////////////////////////
?>
var mname=new Array(
"下载(D)",
"刷新(F)",
"剪切(T)",
"复制(C)",
"粘贴(P)",
"删除(D)",
"重命名(M)",
"全选(A)",
"反选(V)",
"根目录(R)");
var murl=new Array(
"downfile();",
"inwin.window.location='?action=list&path='+path",
"filecopy('cut');",
"filecopy('copy');",
"filepaste();",
"filedel();",
"filerename();",
"allcheck();",
"anticheck();",
"inwin.window.location='?action=list&path=./';");

var ph=18,mwidth=70;//////菜单单行高，宽
var bgc="#eeeeee",txc="#000000";
var cbgc="#0000AA",ctxc="#ffffff";

var mover="this.style.background='"+cbgc+"';this.style.color='"+ctxc+"';"
var mout="this.style.background='"+bgc+"';this.style.color='"+txc+"';"

document.oncontextmenu=function()
{ 
 mlay.style.display="";
 mlay.style.pixelTop=event.clientY+document.body.scrollTop;
 mlay.style.pixelLeft=event.clientX+document.body.scrollLeft;
 return false;
}
function showoff()
{
 mlay.style.display="none";
}
function fresh()
{
 mlay.style.background=bgc;
 mlay.style.color=txc;
 mlay.style.width=mwidth;
 mlay.style.height=mname.length*ph;
 var h="<table width=100% height="+mname.length*ph+"px cellpadding=0  cellspacing=0 border=0>";
 var i=0;
 for(i=0;i<mname.length;i++)
 {
  h+="<tr align=left height="+ph+" onclick=\""+murl[i]+"\" onMouseover=\""+mover+"\" onMouseout=\""+mout+"\"><td style='font-size:9pt;'>&nbsp;"+mname[i]+"</td></tr>";
 }
 h+="</table>";
 mlay.innerHTML=h;
}
<?
//菜单部分结束///////////////////////////////////
}?>

function downfile()
{
 var i=0,n=0,filess='',afile;
 for(i=2;i<dir.length+2;i++)
   if (document.sform.sdir[i].checked)
   {
     n++;
     if (n==1)
     {  filess=dir[i-2];n++;}
     else
       filess+="|"+dir[i-2];
   }
 for(i=2;i<file.length+2;i++)
   if (document.sform.sfile[i].checked)
   {
     n++;
     if (n==1)
       filess=file[i-2];
     else
       filess+="|"+file[i-2];
   }
 afile=(n==1)?"yes":"no";
 if (n==0)
 {
   alert("请选中要下载的文件或文件夹!");
   return;
 }
 else
    inwin.window.location="?action=downfile&path="+path+"&file="+filess+"&afile="+afile;
 return;
}

function downfile1(i)
{
 inwin.window.location="?action=downfile&afile=yes&path="+path+file[i];
}

function filecopy(action)
{
 var i=0,n=0;
 var filestr='',dirstr='';
 for(i=2;i<dir.length+2;i++)
   if (document.sform.sdir[i].checked) { n++; dirstr+=dir[i-2]+"|";}
 for(i=2;i<file.length+2;i++)
   if (document.sform.sfile[i].checked) { n++; filestr+=file[i-2]+"|";}
 if (n==0)
 {
   alert("请选中要"+action+"的文件或文件夹!!");
   return;
 }
 var cookiestr='';
 cookiestr="jscookie=$action1='"+action+"'|$|$sfile='"+filestr+"'|$|$sdir='"+dirstr+"'|$|$fromdir='"+path+"'|$|";
 var expires = "";
 var d = new Date();
 d.setTime( d.getTime() + 5*60*1000);
 expires = "; expires=" + d.toGMTString();
 if(filestr!='' || dirstr!='')
   document.cookie = cookiestr+ expires + ";";
 return;
}

function filepaste()
{
 if (document.cookie.length<=10) return;
 waitme();
 inwin.window.location="?action=paste&path="+path;
}

function filedel()
{
 var i,dn1=0,fn1=0,ii=0;
 var filestr='',dirstr='';
 for(i=2;i<dir.length+2;i++)
  if (document.sform.sdir[i].checked) 
  {
   ii++;
   if(ii==1){ dirstr=dir[i-2]; }else{ dirstr+="|"+dir[i-2];}
   dn1++;
  }
 ii=0;
 for(i=2;i<file.length+2;i++)
  if (document.sform.sfile[i].checked) 
  {
   ii++;
   if (ii==1) {filestr=file[i-2];}else{filestr+="|"+file[i-2];}
   fn1++;
  }
 if (fn1+dn1==0)
 {
   alert("请选中要删除的文件或文件夹!!");
   return;
 }
 if (!confirm("你确定要删除 "+dn1+" 个目录和 "+fn1+" 个文件吗?")) return;
 if ((dn1!=0) && (!confirm("你要删除的 "+dn1+" 个目录可能不为空,一旦删除就不能恢复!!你要继续吗?"))) return;
 waitme();
 inwin.window.location="?action=delfiles&path="+path+"&dirs="+dirstr+"&files="+filestr;
}

function fileup(i)
{
 var s="var so=document.getElementById('updiv"+i+"');";
 eval(s);
 so.style.display=(so.style.display=='none')?'':'none';
}

function waitme() //显示等待信息
{
 loading.style.display='';
 loading.style.pixelTop=document.body.scrollTop;
 window.status='当前状态:正在处理请求...';
 return true;
}
function waitmeoff()//取消显示等待信息
{
 loading.style.display='none';
 window.status='当前状态:完成';
 return true;
}

function filerename1(i)
{
 var file2;
 if (file2=prompt("你要将 "+file[i]+" 重新命名为:",file[i]))
 {
  waitme();
  inwin.window.location="?action=rename&path="+path+"&file1="+file[i]+"&file2="+file2;
 }
} 

function filerename2(i)
{
 var dir2;
 if (dir2=prompt("你要将 "+dir[i]+" 重新命名为:",dir[i]))
 {
  waitme();
  inwin.window.location="?action=rename&path="+path+"&file1="+dir[i]+"&file2="+dir2;
 }
} 

function allcheck()
{
 var i;
 for(i=2;i<dir.length+2;i++) document.sform.sdir[i].checked=true;
 for(i=2;i<file.length+2;i++) document.sform.sfile[i].checked=true;
}

function anticheck()
{
 var i;
 for(i=2;i<dir.length+2;i++)
  document.sform.sdir[i].checked=(document.sform.sdir[i].checked)?false:true;
 for(i=2;i<file.length+2;i++)
  document.sform.sfile[i].checked=(document.sform.sfile[i].checked)?false:true;
}

function inputfilename()
{
 var fname='';
 if (fname=prompt("输入新文件名:",""))
 { 
  waitme();
  inwin.window.location="?action=newfile&path="+path+"&name="+fname;
 }
}
 
function inputdirname()
{
 var dirname='';
 if (dirname=prompt("输入新目录名:",""))
 {
  waitme();
  inwin.window.location="?action=newdir&path="+path+"&dirname="+dirname;
 }
}

function makesure(sort,id)
{
 if (sort=="file" && confirm("你确定要删除文件 "+file[id]+" 吗?"))
 {
  waitme();
  inwin.window.location="?action=delfile&path="+path+file[id];
 }
 else if (sort=="dir" && confirm("你确定要删除目录 "+dir[id]+" 吗?"))
 {
  waitme();
  inwin.window.location="?action=deldir&path="+path+dir[id];
 }
}

function zippack()
{
 var key='',i,filess='';
 for(i=2;i<dir.length+2;i++)
  if (document.sform.sdir[i].checked) filess+=document.sform.sdir[i].value+"|";
 for(i=2;i<file.length+2;i++)
  if (document.sform.sfile[i].checked) filess+=document.sform.sfile[i].value+"|";
 if (filess=='')
 {
  alert("请选中要打包的文件和文件夹!!");
  return;
 }
 if (key=prompt("输入zip文件名:","")) {}else return;
 waitme();
 inwin.window.location="?action=zippack&path="+path+"&key="+key+"&filess="+filess;
}


function unpackall1(i)
{
 var filename;
 filename=file[i];
 var key='';
 filename=filename.substring(0,filename.indexOf(".zip"));
 if (key=prompt("输入将 "+file[i]+" 解压到的目录名:",filename))
 {
  waitme();
  inwin.window.location="?action=unpackall&path="+path+"&key="+key+"&file="+file[i];
 }
}

var path='';
var file=new Array();
var dir=new Array();
var daxiao=new Array();
var dn,fn;
function refresh() ///刷新列表
{
 var html="<table border=0 cellspacing=0 cellpadding=0 width=100% ><tr><td>"+
 "<form name='sform' action='' method=post border=0>"+
 "<table border=0 cellspacing=0 cellpadding=0 width=100% >"+
 "<tr><td width=100% colspan=9 height=23 background=?action=img&url=titleback>"+
 "<a href='?action=list&up=yes&path="+path+"' target=inwin onclick='waitme()' title=返回上级目录>返回上级目录</a> "+
 "<a href='javascript:inputdirname();' title=新建目录>新建目录</a> "+
 "<a href='javascript:zippack();' title=将选中的文件及文件夹打包压缩>添加到压缩文件</a> "+
 "<a href='javascript:allcheck();' title=全部选中>全选</a> "+
 "<a href='javascript:anticheck();' title=反向选择>反选</a> "+
 "<a href='?action=list&path="+path+"' target=inwin title=刷新当前目录中的内容>刷新</a> "+
 "</td></tr></table>"+
 "<table border=0 cellspacing=0 cellpadding=0 width=100% >"+
 "<input type=checkbox style='display:none' name='sdir' value='#' width=0 height=0 border=0>"+
 "<input type=checkbox style='display:none' name='sdir' value='#' width=0 height=0 border=0>"+
 "<input type=checkbox style='display:none' name='sfile' value='#' width=0 height=0 border=0>"+
 "<input type=checkbox style='display:none' name='sfile' value='#' width=0 height=0 border=0>";
 var i=0;
 for (i=0;i<dir.length;i++)
 {
  if (i%2==0)
  {
   html+="<tr bgcolor=<?=$bgcolor1;?> onmouseover=\"this.bgColor='<?=$bgover;?>';\" onmouseout=\"this.bgColor='<?=$bgcolor1;?>';\">";
  }
  else
  {
   html+="<tr bgcolor=<?=$bgcolor2;?> onmouseover=\"this.bgColor='<?=$bgover;?>';\" onmouseout=\"this.bgColor='<?=$bgcolor2;?>';\">";
  }
   html+="<td width=5%><input   type='checkbox' name='sdir' value='"+dir[i]+"'></td>"+
   "<td width=5%><img border=0 src='<?=$icon["folder"]?>'></td>"+
   "<td width=74%>"+
   "<a href='?action=list&path="+path+dir[i]+"/' title='打开 "+dir[i]+" 目录' target=inwin onclick='waitme()'>"+dir[i]+
   "</a></td>"+
   "<td width=7%><a href='javascript:filerename2("+i+")' title='重命名 "+dir[i]+" 文件夹'>重命名</a></td>"+
   "<td width=9%><a href='javascript:makesure(\"dir\","+i+");' title='删除"+dir[i]+"目录'>删除</a></td></tr>";
 }
 html+="</table>"+
 "<table border=0 cellspacing=0 cellpadding=0 width=100% >"+
 "<tr><td width=100% colspan=9 height=23 background=?action=img&url=titleback>"+
 "<a href='?action=list&up=yes&path="+path+"' target=inwin onlick='waitme()' title=返回上级目录>返回上级目录</a> "+  
 "<a href='javascript:fileup(0);' title='打开上传文件模块（在目录的最下面）'>上传文件</a> "+  
 "<a href='javascript:inputfilename();' title=新建一个文本类型的文件>新建文件</a> "+
 "<a href='javascript:downfile();' title='下载选中的文件和文件夹\n如果是单个文件将下载其源文件\n如果是多个文件或文件夹则将他们打包下载'>下载文件</a> "+
 "<a href='javascript:fileup(1);' title='下载一个远程文件到服务器'>远程下载</a> "+
 "<a href='javascript:filecopy(\"cut\");' title=剪切选中的文件或文件夹>剪切</a> "+
 "<a href='javascript:filecopy(\"copy\");' title=复制选中的文件或文件夹>复制</a> "+
 "<a href='javascript:filepaste();' title=将剪贴板中的文件和文件夹粘贴到此目录下>粘贴</a> "+
 "<a href='javascript:filedel();' title=删除选中的文件或文件夹>删除</a> "+
 "</td></tr></table>"+
 "<table border=0 cellspacing=0 cellpadding=0 width=100%>";
 for (i=0;i<file.length;i++)
 {
  if (i%2==0)
  {
   html+="<tr bgcolor=<?=$bgcolor1;?> onmouseover=\"fid="+i+";this.bgColor='<?=$bgover;?>';\" onmouseout=\"fid=-2;this.bgColor='<?=$bgcolor1;?>';\">";
  }
  else
  {
   html+="<tr bgcolor=<?=$bgcolor2;?> onmouseover=\"fid="+i+";this.bgColor='<?=$bgover;?>';\" onmouseout=\"this.bgColor='<?=$bgcolor2;?>';\">";
  }
  html+="<td width=5%>"+
  "<input   type='checkbox' name='sfile' value='"+file[i]+"'></td><td width=5%>";
  f=file[i].split(".")[file[i].split(".").length-1];
  f=f.toLowerCase();
  if(editfiles.indexOf(f)!=-1)
  {
   html+="<a href='?action=editfile&path="+path+file[i]+"' target=_blank title=编辑当前文件><img border=0 src='<?=$icon["txt"]?>'></a>";
  }
  else if (f=="zip")
  {
   html+="<a href='?action=editfile&path="+path+file[i]+"' target=_blank title=浏览当前文件><img border=0 src='<?=$icon["zip"]?>'></a>";
  }
  else if (f=="exe" | f=="dll")
  {
   html+="<a href='?action=editfile&path="+path+file[i]+"' target=_blank title=编辑当前文件><img border=0 src='<?=$icon["exe"]?>'></a>";
  }
  else if (imgfiles.indexOf(f)!=-1)
  {
   html+="<a href='?action=editfile&path="+path+file[i]+"' target=_blank title=编辑当前文件><img border=0 src='<?=$icon["gif"]?>'></a>";
  }
  else
  {
   html+="<a href='?action=editfile&path="+path+file[i]+"' target=_blank title=编辑当前文件><img border=0 src='<?=$icon["unkown"]?>'></a>";
  }
  html+="</td><td width=50%><a href='?action=jump&url="+path+file[i]+"' target=_blank title='打开 "+file[i]+" 文件'>"+file[i]+"</a>";
  html+=(f=="zip")?"&nbsp;<a href='javascript:unpackall1("+i+")' title=把当前文件解压到同名目录>解压</a></td>":"</td>";
  html+="<td width=10%><div>"+daxiao[i]+" KB</div></td>"+
  "<td width=7%><a href='javascript:downfile1("+i+");' title=下载源文件>下载</a></td>"+
  "<td width=7%><a href='javascript:filerename1("+i+");' title=重命名当前文件>重命名</a></td>"+
  "<td width=7%><a href='?action=editfile&path="+path+file[i]+"' target=_blank title='编辑文本文件或浏览zip文件'>";
  html+=(f=="zip")?"浏览":"编辑";
  html+="</a></td>"+
  "<td width=9%><a href='javascript:makesure(\"file\","+i+");' title='删除"+file[i]+"文件'>删除</a></td></tr>";
 }
 html+="</table></td></tr><tr width=0px><td></form></td></tr></table>";
 document.getElementById('main').innerHTML=html;
 document.getElementById('updiv0').style.display='none';
 document.getElementById('updiv1').style.display='none';
 document.upform.path.value=path;
 document.getElementById('currentpath').innerHTML="当前路径 "+path;
 waitmeoff();
}
<?
}

/////////////输出目录列表(js)////////////////////
function mlist()
{
 global $first;
 echo "<meta http-equiv='Pragma' content='no-cache'>\n"; //禁止浏览器缓存此页
 echo "<script>\n";
 global $path,$up,$olddir,$sdir,$sfile;
 
 if ($up=="yes")
 {
  $path=substr($path,0,strlen($path)-1);
  $path=dirname($path);
 }
 if (empty($path))
 {
  $pp="./";
  $path=$pp;
 }
 if(substr($path,-1)!="/")
 {
  $path.="/"; 
  $pp=$path;
 }
 else
 {
  $pp=$path;
 }
 $handle=opendir($pp);
 $file=array();
 $dir=array();
 while ($val=readdir($handle)) 
 {
  if (is_file($path.$val)) $file[]=$val;
  if (is_dir($path.$val) and $val!="." and $val!="..") $dir[]=$val;
 } 
 closedir($handle);
 $i=0;
 echo "parent.path='$path';\n";
 echo "parent.dir=new Array(";
 foreach($dir as $a) 
 {
  $i++;
  if ($i!=1) { echo ",";}
  echo "'$a'";
 }
 echo ");\n";
 $i=0; 
 $fstr="parent.file=new Array(";
 $dstr="parent.daxiao=new Array(";
 foreach($file as $a) 
 {
  $i++; 
  if ($i!=1) 
  {
   $fstr.=",";
   $dstr.=",";
  }
  $fstr.="'$a'";
  $dstr.="'".ceil(filesize($path.$a)/1024)."'";
 }
 $fstr.=");";
 $dstr.=");";
 echo $fstr."\n".$dstr;
 echo $first?"setTimeout('parent.refresh();',1500);":"parent.refresh();";
 echo "\n</script>";
}

///////////强制删除目录，与rmdir用法一样，但可以删除文件夹里面的所有内容，
function xdir($path)
{
 if (!is_dir($path)) return false;
 $handle=@opendir($path);
 while($val=@readdir($handle))
 {
  if ($val=='.' || $val=='..') continue;
  $value=$path."/".$val;
  if (is_dir($value))
    xdir($value);
  else if (is_file($value))
    @unlink($value);
 }
 @closedir($handle);
 @rmdir($path);
 return true;
}

///////////////////移动或复制目录/////////////////
/////////////////将目录$from,移动或复制到$to，
//$cover表示当遇到同名文件时是否覆盖，$cut为true时表示移动，否则复制
//其中要调用rename1(),mymove()

function rename2($from,$to,$cover,$cut)
{
 global $mcover;
 if (!is_dir($from)) return false;
 if (!is_dir($to))  //如果$to这个目录不存在，那么直接将$from ,copy 或 rename 到 $to
   return mymove($from,$to,$cut);

 $handle=opendir($from);
 while($val=readdir($handle))
 {
  if($val=="." || $val=="..") continue;
  if (is_dir($from.$val))
  {
   if (!is_dir($to.$val))
    mymove($from.$val,$to.$val,$cut); 
   else
    rename2($from.$val,$to.$val,$cover,$cut); //递归调用
  }
  else if (is_file($from.$val))
   rename1($from.$val,$to.$val,$cover,$cut);
 }
 closedir($handle);
 if ($cut)
  rmdir($from); //递归返回，退出后删除此目录
 return true;
}
///////////////移动或复制文件/////////////////////
/////////////////将文件$from,移动或复制到$to，$cover表示当遇到同名文件时是否覆盖，$cut为true时表示移动，否则复制 

function rename1($from,$to,$cover,$cut)
{
 global $mcover;
 if (!file_exists($from)) return false;
 if (file_exists($to))
 {
  if ($cover)
  {
   unlink($to);
   mymove($from,$to,$cut);
  }
  else
   $mcover=true;
 }
 else
  mymove($from,$to,$cut);
 return true;
}
////移动文件，$from,到 $to,$cut表示是否剪切，调用之前确保 $to不存在,$from存在
function mymove($from,$to,$cut)
{
 if ($cut) 
  return rename($from,$to);
 else
  return copy($from,$to);
}

//////////////////浏览zip文件///////////////////
////$path表示zip文件地址，$folder表示要浏览zip文件里面的目录名
function showzip($path,$folder)
{ 
 if (!file_exists($path)) die;
 $zip=new Zip;
 $folders=array();
 $files=array();
 $array=$zip->get_list($path);
 foreach($array as $z)
 {
  $z[filename]="./".$z[filename];
  $z[index]++;
  $arr=explode($folder,$z[filename]) or die("folder=$folder filename=$z[filename]");
  if ($z[folder]==1 && $arr[0]=="")
  {
   $arr1=explode("/",$arr[1]) or die ("arr1=$arr[1]");
   if ($arr1[0]!="") $folders["$arr1[0]"]="$arr1[0]|$z[index]";
  }
  else if ($z[folder]==0)
  {
   if (!strpos($arr[1],"/") && $arr[1]!="")
    $files["$arr[1]"]="$arr[1]|$z[index]|$z[size]|$z[compressed_size]";
  }
 }
 echo "<form name='zipform' id='zipform' action='?action=unpackzip&path=$path' method=post>";
 echo "<table width=90% align=center border=1 cellspacing=0 class=t1>";
 echo "<tr><td colspan=5>文件夹</td></tr>";
 echo "<div style='display:none' width=0 height=0 border=0><input   type=checkbox name=index value=-1><input type=checkbox name=index value=-1></div>";

 foreach($folders as $f)
 {
  $arr=explode("|",$f);
  echo "<tr><td><input   type=checkbox name=index value=$arr[1]></td>";
  echo "<td colspan=4><a href='?action=editfile&path=$path&folder=$folder$arr[0]/'>$arr[0]/</a></td>";
  echo "</tr>";
 }
 echo "<tr><td colspan=5>文件</td></tr>";
 echo "<tr><td>&nbsp;</td><td>文件名</td><td>原始大小</td><td>压缩大小</td><td>压缩比</td></tr>";
 foreach($files as $f)
 {
  $arr=explode("|",$f);
  echo "<tr><td><input type=checkbox   name=index value=$arr[1]></td>";
  echo "<td>$arr[0]</td>";

  echo "<td>"
    .$arr[2]/1000
    ." KB</td><td>"
    .$arr[3]/1000
    ." KB</td><td>";
    if ($arr[2]==0)
      echo 0;
    else
      echo ceil($arr[3]*10000/$arr[2])/100;
    echo "%</td></tr>";
 }

 echo "</table></form>";
?>
 <script>
 function jieyadao()
 {
  var key='',indexes='';
  var i=0,t=0;
  if(key=prompt("解压到的目录名:","<?=str_replace(".zip","",$path);?>"))
  {
   for(i=2;i<document.zipform.index.length;i++)
   {
    if (document.zipform.index[i].checked)
    {
     t++;
     if (t==1)
      indexes=""+document.zipform.index[i].value;
     else
      indexes+="|"+document.zipform.index[i].value;
    }
   }
   if (t==0) 
   {
      alert("请选择要解压的文件或文件夹!");
      return;
   }
   window.location="?action=unpack&path=<?=$path;?>&indexes="+indexes+"&key="+key;
  }
 }
 function allcheck()
 {
  var i;
  for(i=2;i<document.zipform.index.length;i++)
   document.zipform.index[i].checked=true;
 }
 function anticheck()
 {
  var i;
  for(i=2;i<document.zipform.index.length;i++)
  {
   if (document.zipform.index[i].checked) 
    document.zipform.index[i].checked=false;
   else
    document.zipform.index[i].checked=true;
  }
 }
 </script>
<?
}

///写$str到日志里去////////
function inlog($str)
{
 global $logdir,$logmode;
 if (!$logmode) return;
 if (!is_dir($logdir)) mkdir($logdir);
 $logfilename=$logdir."/".date("Y-m").".php";
 if (!file_exists($logfilename))
 {
  $fp=fopen($logfilename,"w");
  fputs($fp,"<?die(\"你没有权限查看日志!!\");?>\n");  ////防止别人通过日志地址来查看日志
  fclose($fp);
 }
 $fp=fopen($logfilename,"a+");
 fputs($fp,date("d日H:i:s")."-".$str."\n");
 fclose($fp);
}

////basename()的升级版，支持中文!
function basename1($s)
{
 $arr=explode("/",$s);
 return $arr[count($arr)-1];
}

///urlencode()的升级版，不处理/,把' '处理成 %20,
function urlencode1($url)
{ 
 $arr=explode("/",$url);
 $url='.';
 foreach($arr as $l)
  if ($l!='.')
   $url.="/".urlencode($l);
 $url=str_replace("+","%20",$url);
 return $url;
}

////////////////////////ZIP类///////////////////////////////
class zip 
{

 var $datasec, $ctrl_dir = array();
 var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
 var $old_offset = 0; var $dirs = Array(".");

 function get_List($zip_name)
 {
   $zip = @fopen($zip_name, 'rb');
   if(!$zip) return(0);
   $centd = $this->ReadCentralDir($zip,$zip_name);

    @rewind($zip);
    @fseek($zip, $centd['offset']);

   for ($i=0; $i<$centd['entries']; $i++)
   {
    $header = $this->ReadCentralFileHeaders($zip);
    $header['index'] = $i;$info['filename'] = $header['filename'];
    $info['stored_filename'] = $header['stored_filename'];
    $info['size'] = $header['size'];$info['compressed_size']=$header['compressed_size'];
    $info['crc'] = strtoupper(dechex( $header['crc'] ));
    $info['mtime'] = $header['mtime']; $info['comment'] = $header['comment'];
    $info['folder'] = ($header['external']==0x41FF0010||$header['external']==16)?1:0;
    $info['index'] = $header['index'];$info['status'] = $header['status'];
    $ret[]=$info; unset($header);
   }
  return $ret;
 }
 function Add($files,$compact)
 {
  if(!is_array($files[0])) $files=Array($files);

  for($i=0;$files[$i];$i++){
    $fn = $files[$i];
    if(!in_Array(dirname($fn[0]),$this->dirs))
     $this->add_Dir(dirname($fn[0]));
    if(basename1($fn[0]))
     $ret[basename1($fn[0])]=$this->add_File($fn[1],$fn[0],$compact);
  }
  return $ret;
 }

 function get_file()
 {
   $data = implode('', $this -> datasec);
   $ctrldir = implode('', $this -> ctrl_dir);

   return $data . $ctrldir . $this -> eof_ctrl_dir .
    pack('v', sizeof($this -> ctrl_dir)).pack('v', sizeof($this -> ctrl_dir)).
    pack('V', strlen($ctrldir)) . pack('V', strlen($data)) . "\x00\x00";
 }

 function add_dir($name) 
 { 
   $name = str_replace("\\", "/", $name); 
   $fr = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00"; 

   $fr .= pack("V",0).pack("V",0).pack("V",0).pack("v", strlen($name) ); 
   $fr .= pack("v", 0 ).$name.pack("V", 0).pack("V", 0).pack("V", 0); 
   $this -> datasec[] = $fr;

   $new_offset = strlen(implode("", $this->datasec)); 

   $cdrec = "\x50\x4b\x01\x02\x00\x00\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00"; 
   $cdrec .= pack("V",0).pack("V",0).pack("V",0).pack("v", strlen($name) ); 
   $cdrec .= pack("v", 0 ).pack("v", 0 ).pack("v", 0 ).pack("v", 0 ); 
   $ext = "\xff\xff\xff\xff"; 
   $cdrec .= pack("V", 16 ).pack("V", $this -> old_offset ).$name; 

   $this -> ctrl_dir[] = $cdrec; 
   $this -> old_offset = $new_offset; 
   $this -> dirs[] = $name;
 }

 function add_File($data, $name, $compact = 1)
 {
   $name     = str_replace('\\', '/', $name);
   $dtime    = dechex($this->DosTime());

   $hexdtime = '\x' . $dtime[6] . $dtime[7].'\x'.$dtime[4] . $dtime[5]
     . '\x' . $dtime[2] . $dtime[3].'\x'.$dtime[0].$dtime[1];
   eval('$hexdtime = "' . $hexdtime . '";');

   if($compact)
   $fr = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00".$hexdtime;
   else $fr = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00".$hexdtime;
   $unc_len = strlen($data); $crc = crc32($data);

   if($compact){
     $zdata = gzcompress($data); $c_len = strlen($zdata);
     $zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2);
   }else{
     $zdata = $data;
   }
   $c_len=strlen($zdata);
   $fr .= pack('V', $crc).pack('V', $c_len).pack('V', $unc_len);
   $fr .= pack('v', strlen($name)).pack('v', 0).$name.$zdata;

   $fr .= pack('V', $crc).pack('V', $c_len).pack('V', $unc_len);

   $this -> datasec[] = $fr;
   $new_offset        = strlen(implode('', $this->datasec));
   if($compact)
        $cdrec = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00";
   else $cdrec = "\x50\x4b\x01\x02\x14\x00\x0a\x00\x00\x00\x00\x00";
   $cdrec .= $hexdtime.pack('V', $crc).pack('V', $c_len).pack('V', $unc_len);
   $cdrec .= pack('v', strlen($name) ).pack('v', 0 ).pack('v', 0 );
   $cdrec .= pack('v', 0 ).pack('v', 0 ).pack('V', 32 );
   $cdrec .= pack('V', $this -> old_offset );

   $this -> old_offset = $new_offset;
   $cdrec .= $name;
   $this -> ctrl_dir[] = $cdrec;
   return true;
 }

 function DosTime() {
   $timearray = getdate();
   if ($timearray['year'] < 1980) {
     $timearray['year'] = 1980; $timearray['mon'] = 1;
     $timearray['mday'] = 1; $timearray['hours'] = 0;
     $timearray['minutes'] = 0; $timearray['seconds'] = 0;
   }
   return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) |     ($timearray['mday'] << 16) | ($timearray['hours'] << 11) | 
    ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
 }

 function Extract ( $zn, $to, $index = Array(-1) )
 {
   $ok = 0; $zip = @fopen($zn,'rb');
   if(!$zip) return(-1);
   $cdir = $this->ReadCentralDir($zip,$zn);
   $pos_entry = $cdir['offset'];

   if(!is_array($index)){ $index = array($index);  }
   for($i=0; $index[$i];$i++){
     if(intval($index[$i])!=$index[$i]||$index[$i]>$cdir['entries'])
      return(-1);
   }

   for ($i=0; $i<$cdir['entries']; $i++)
   {
     @fseek($zip, $pos_entry);
     $header = $this->ReadCentralFileHeaders($zip);
     $header['index'] = $i; $pos_entry = ftell($zip);
     @rewind($zip); fseek($zip, $header['offset']);
     if(in_array("-1",$index)||in_array($i,$index))
      $stat[$header['filename']]=$this->ExtractFile($header, $to, $zip);
      
   }
   fclose($zip);
   return $stat;
 }

  function ReadFileHeader($zip)
  { 
    $binary_data = fread($zip, 30);
    $data = unpack('vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $binary_data);

    $header['filename'] = fread($zip, $data['filename_len']);
    if ($data['extra_len'] != 0) {
      $header['extra'] = fread($zip, $data['extra_len']);
    } else { $header['extra'] = ''; }

    $header['compression'] = $data['compression'];$header['size'] = $data['size'];
    $header['compressed_size'] = $data['compressed_size'];
    $header['crc'] = $data['crc']; $header['flag'] = $data['flag'];
    $header['mdate'] = $data['mdate'];$header['mtime'] = $data['mtime'];

    if ($header['mdate'] && $header['mtime']){
     $hour=($header['mtime']&0xF800)>>11;$minute=($header['mtime']&0x07E0)>>5;
     $seconde=($header['mtime']&0x001F)*2;$year=(($header['mdate']&0xFE00)>>9)+1980;
     $month=($header['mdate']&0x01E0)>>5;$day=$header['mdate']&0x001F;
     $header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
    }else{$header['mtime'] = time();}

    $header['stored_filename'] = $header['filename'];
    $header['status'] = "ok";
    return $header;
  }

 function ReadCentralFileHeaders($zip){
    $binary_data = fread($zip, 46);
    $header = unpack('vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $binary_data);

    if ($header['filename_len'] != 0)
      $header['filename'] = fread($zip,$header['filename_len']);
    else $header['filename'] = '';

    if ($header['extra_len'] != 0)
      $header['extra'] = fread($zip, $header['extra_len']);
    else $header['extra'] = '';

    if ($header['comment_len'] != 0)
      $header['comment'] = fread($zip, $header['comment_len']);
    else $header['comment'] = '';

    if ($header['mdate'] && $header['mtime'])
    {
      $hour = ($header['mtime'] & 0xF800) >> 11;
      $minute = ($header['mtime'] & 0x07E0) >> 5;
      $seconde = ($header['mtime'] & 0x001F)*2;
      $year = (($header['mdate'] & 0xFE00) >> 9) + 1980;
      $month = ($header['mdate'] & 0x01E0) >> 5;
      $day = $header['mdate'] & 0x001F;
      $header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
    } else {
      $header['mtime'] = time();
    }
    $header['stored_filename'] = $header['filename'];
    $header['status'] = 'ok';
    if (substr($header['filename'], -1) == '/')
      $header['external'] = 0x41FF0010;
    return $header;
 }

 function ReadCentralDir($zip,$zip_name)
 {
  $size = filesize($zip_name);
  if ($size < 277) $maximum_size = $size;
  else $maximum_size=277;

  @fseek($zip, $size-$maximum_size);
  $pos = ftell($zip); $bytes = 0x00000000;

  while ($pos < $size)
  {
    $byte = @fread($zip, 1); $bytes=($bytes << 8) | Ord($byte);
    if ($bytes == 0x504b0506){ $pos++; break; } $pos++;
  }

 $data=unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size',fread($zip,18));


  if ($data['comment_size'] != 0)
    $centd['comment'] = fread($zip, $data['comment_size']);
    else $centd['comment'] = ''; $centd['entries'] = $data['entries'];
  $centd['disk_entries'] = $data['disk_entries'];
  $centd['offset'] = $data['offset'];$centd['disk_start'] = $data['disk_start'];
  $centd['size'] = $data['size'];  $centd['disk'] = $data['disk'];
  return $centd;
 }

 function ExtractFile($header,$to,$zip)
 {
   $header = $this->readfileheader($zip);

   if(substr($to,-1)!="/") $to.="/";
   if(!@is_dir($to)) @mkdir($to,0777);

   $pth = explode("/",dirname($header['filename']));
   for($i=0;isset($pth[$i]);$i++){
     if(!$pth[$i]) continue;$pthss.=$pth[$i]."/";
     if(!is_dir($to.$pthss)) @mkdir($to.$pthss,0777);
   }
  if (!($header['external']==0x41FF0010)&&!($header['external']==16))
  {
   if ($header['compression']==0)
   {
    $fp = @fopen($to.$header['filename'], 'wb');
    if(!$fp) return(-1);
    $size = $header['compressed_size'];

    while ($size != 0)
    {
      $read_size = ($size < 2048 ? $size : 2048);
      $buffer = fread($zip, $read_size);
      $binary_data = pack('a'.$read_size, $buffer);
      @fwrite($fp, $binary_data, $read_size);
      $size -= $read_size;
    }
    fclose($fp);
    touch($to.$header['filename'], $header['mtime']);

  }else{
   $fp = @fopen($to.$header['filename'].'.gz','wb');
   if(!$fp) return(-1);
   $binary_data = pack('va1a1Va1a1', 0x8b1f, Chr($header['compression']),
     Chr(0x00), time(), Chr(0x00), Chr(3));

   fwrite($fp, $binary_data, 10);
   $size = $header['compressed_size'];

   while ($size != 0)
   {
     $read_size = ($size < 1024 ? $size : 1024);
     $buffer = fread($zip, $read_size);
     $binary_data = pack('a'.$read_size, $buffer);
     @fwrite($fp, $binary_data, $read_size);
     $size -= $read_size;
   }

   $binary_data = pack('VV', $header['crc'], $header['size']);
   fwrite($fp, $binary_data,8); fclose($fp);

   $gzp = @gzopen($to.$header['filename'].'.gz','rb') or die("Cette archive est compress閑");
    if(!$gzp) return(-2);
   $fp = @fopen($to.$header['filename'],'wb');
   if(!$fp) return(-1);
   $size = $header['size'];

   while ($size != 0)
   {
     $read_size = ($size < 2048 ? $size : 2048);
     $buffer = gzread($gzp, $read_size);
     $binary_data = pack('a'.$read_size, $buffer);
     @fwrite($fp, $binary_data, $read_size);
     $size -= $read_size;
   }
   fclose($fp); gzclose($gzp);

   touch($to.$header['filename'], $header['mtime']);
   @unlink($to.$header['filename'].'.gz');

  }}
  return true;
 }
} //ZIP压缩类end
?>