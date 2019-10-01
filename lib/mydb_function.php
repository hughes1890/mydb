<?php

$file_path = "mydb";

$mydb = array();
$mydb["database"]="sining";
$mydb["dbtable"]="sining";

if($_POST["select"] == "output"){
	
	if(file_exists($file_path)){
		$file = fopen($file_path, "r");
		if($file != NULL){
			
			//當檔案未執行到最後一筆，迴圈繼續執行(fgets一次抓一行)
			while (!feof($file)) {
				//$str .= fgets($file)."<br>";
				$str = fgets($file);
				echo "$str<br>";
				
				if($str != ""){
					$mydb2 = json_decode($str);
					foreach( $mydb2 as $dbkey=>$dbvalue ){
						if (preg_match('/\{"/', $dbvalue)){
							//echo "$dbkey=>$dbvalue<br>";
							$mydb2->$dbkey = json_decode($dbvalue);
						}
					}
					print_r($mydb2);
					echo "<br><br>";
				}
			}
			fclose($file);
		}
	}
	
}else if($_POST["select"] == "input" ){
	
	$mydbjson="";
		
	$mydb["context"] = json_encode($_POST);
	//$context = json_encode($_POST);
	$mydbjson = json_encode($mydb);
	
	$mydbjson .= "\n";
	
    $file = fopen($file_path,"a+"); 
    fwrite($file,$mydbjson);
    fclose($file);
	
	echo "寫入完成";
	
	
}else if($_POST["select"] == "delete" ){
	
	$delID = $_POST["delID"];
	deldata("AID",$delID);
	
}else if($_POST["select"] == "getdata" ){
	$getdataid = $_POST["getdataid"];
	
	getdata("AID",$getdataid);
	
}else if($_POST["select"] == "updata" ){
	$delID = $_POST["AID"];
	deldata("AID",$delID);
	
	$mydb["context"] = json_encode($_POST);
	//$context = json_encode($_POST);
	$mydbjson = json_encode($mydb);
	
	$mydbjson .= "\n";
	
    $file = fopen($file_path,"a+"); 
    fwrite($file,$mydbjson);
    fclose($file);
	
	echo "updata完成";
	
}

function deldata( $idname , $id ){

	global $file_path;
	global $mydb;
	
	$filearray = file($file_path);
	
	//取出每行資料
	foreach($filearray as $filekey=>$filerow ){
		$filerow = str_replace('\"','"',$filerow);
		$filerow = str_replace('":"','',$filerow);
		//找到要刪除的資料行
		if (preg_match("/".$idname.$id."/", $filerow)){
			//echo $filerow;
			unset($filearray[$filekey]);
		}
	}
	
	//重整陣列
	$filearray = array_values($filearray);
	
	//清空資料庫
	$file = fopen($file_path,"w"); 
	fwrite($file,'');
	
	//
	$file = fopen($file_path,"a+"); 
	foreach($filearray as $filekey=>$filerow ){
		fwrite($file,$filerow);
	}
	fclose($file);
	
}

function getdata($idname , $id ){
	
	global $file_path;
	global $mydb;
	
	$filearray = file($file_path);
	$datastring = "";
	
	//取出每行資料
	foreach($filearray as $filekey=>$filerow ){
		$filerow = str_replace('\"','"',$filerow);
		$filerow = str_replace('":"','',$filerow);
		//找到要的資料行
		if (preg_match("/".$idname.$id."/", $filerow)){
			//echo $filerow;
			$datastring = $filearray[$filekey];
		}
	}
	
	$dataarray = json_decode($datastring);
	foreach( $dataarray as $dbkey=>$dbvalue ){
		if (preg_match('/\{"/', $dbvalue)){ 
			//echo "$dbkey=>$dbvalue<br>";
			$dataarray->$dbkey = json_decode($dbvalue);
		}
	}

	//print_r($dataarray->context);
	
	echo "<h3> update </h3>
	<form id='update' method='POST'>
	<input type='hidden' name='select' value='updata' />
	AID:  <input type='text' name='AID'  value='".$dataarray->context->AID ."' /><br>
	NAME: <input type='text' name='name' value='".$dataarray->context->name ."' /><br>
	MSG:  <input type='text' name='msg'  value='".$dataarray->context->msg ."' /><br>
	TIME: <input type='text' name='time' value='".$dataarray->context->time ."'/><br>
	</form>
	<input type='button'  value='update' onclick='update()'/>";
	
}

?>