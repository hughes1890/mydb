<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="lib/jquery-3.4.1.min.js"></script>
<script type="text/javascript">
	
function send(){
		
	$.post( "lib/mydb_function.php",
	$("#text").serialize(),
	function(data) {
		alert(data);
		location.reload();
	});
	
}

function update(){
		
	$.post( "lib/mydb_function.php",
	$("#update").serialize(),
	function(data) {
		alert(data);
		location.reload();
	});
	
}

function getdata(id){
		
	$.post( "lib/mydb_function.php",
	{
		"select":"getdata",
		"getdataid":id
	},
	function(data) {
		
		$("#debug").html(data);
		
		//alert(data);
		//location.reload();
	});
	
}

function del(id){
	
	$.post( "lib/mydb_function.php",
	{
		"select":"delete",
		"delID":id
	},
	function(data) {
		alert(data);
		location.reload();
	});
}
	
</script>
</head>
<body>
<h3> new data </h3>
<form id='text' method='POST'>

	<input type='hidden' name='select' value='input' />
	AID: <input type='text' name='AID' /><br>
	NAME: <input type='text' name='name' /><br>
	MSG: <input type='text' name='msg' /><br>
	TIME: <input type='text' name='time' /><br>
	
</form>
	<input type='button'  value='send' onclick='send()'/>
	
	
<div id='debug'>

</div>
<?php

/**
取出資料 及 解成物件陣列
*/

$file_path = "lib/mydb";
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
				$AID = $mydb2->$dbkey->AID;
				echo "<input type='button'  value='del' onclick='del($AID)' /> 
				<input type='button'  value='getdata' onclick='getdata($AID)' /><br><br>";
			}
		}
		fclose($file);
	}
}
	

?>	
</body>
</html>