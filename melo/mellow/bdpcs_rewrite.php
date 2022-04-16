<?php 
$contents = $_POST["pcs"];
$contents = str_replace("FLAG_SERVERDATE","<?php echo date('Y/n/j') ?>",$contents);
$fp = fopen("bdpcs_xml.php",'w');
$wr = fwrite($fp,$contents);
if($wr){
fclose($fp);
}
?>
