<?php

//包含需求檔案 ------------------------------------------------------------------------

	session_start();
//宣告變數 ----------------------------------------------------------------------------
if(is_numeric($_POST['keyNum'])){
switch($_POST['tables']){
case "storepic"://儲存圖片
//檢查資料夾
if( is_dir("./images/".$_POST['check']) )
{}
else
{
//建立資料夾
mkdir("./images/".$_POST['check'], 0777, true);
}

 define('UPLOAD_PATH', "./images/".$_POST['check']."/");



  // 接收 POST 進來的 base64 DtatURI String
  $img = $_POST['imgsrc'];

  // 轉檔 & 存檔
  $img = str_replace('data:image/png;base64,', '', $img);
  $img = str_replace(' ', '+', $img);
  $data = base64_decode($img);
  $file = UPLOAD_PATH . uniqid() . '.png';
  $success = file_put_contents($file, $data);
  $done= str_replace("./images//","",$file);
  /*
$updateDsc =  date("Y-m-d H:i:s");
	$sql = "insert into `storepic` set `src`='".$file."',`checknum`='".$_POST['check']."',`page`='".$_SESSION['essaypage']."',`edit_dt`='".$updateDsc."',`create_dt`='".$updateDsc."'";
	$ODb->query($sql) or die("新增資料出錯，請聯繫管理員。")*/
	echo $done;
	
break;
break;
default:
break;
}


}	
	
?>