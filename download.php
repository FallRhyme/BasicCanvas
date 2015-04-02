<?php
if(isset($_GET['filename'])&&$_GET['filename']!=""){
	$filename=$_GET['filename'];

header("Content-Disposition: attachment; filename=" . urlencode($filename));
header("Content-Transfer-Encoding: binary");
header("Content-Type: application/download");

readfile("./images/".$filename."");
}
?>
