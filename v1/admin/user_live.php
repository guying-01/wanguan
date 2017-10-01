<?PHP
	/**租户激活
	@ Auther Jarod qi
	@ time 20170807
	**/
	session_start();
	require_once( 'functions.php' );
    header("Content-type: text/html; charset=utf-8");
	$id  = $_GET['id'];

	$conn     = createMySQLConnect();
	$id  = $_GET['id'];	
	$sql = "SELECT * FROM admin_zuser WHERE id='$id' ";
	$tp  =  mysql_query( $sql, $conn );
	$row =  mysql_fetch_assoc($tp);
	//企业基本信息
	$zuser  = $row['zuser'];
	$zname  = $row['zname'];
	$tel  = $row['tel'];
	$address  = $row['address'];
	$table_profix = $row['db_prefix']."_";
 	//企业参数长度
	$len_zname = "";
	$len_tel  = "";
	$len_address = "";
	$len_zname = strlen(preg_replace('# #','',$zname));
	$len_tel   = strlen(preg_replace('# #','',$tel));
	$len_address = strlen(preg_replace('# #','',$address));
	
	//echo $zname . "====>". $len_zname."<br>";
	//echo $tel . "====>". $len_tel."<br>";
	//echo $address . "====>". $len_address."<br>";
	
	createDatabase($zname,$tel,$address,$len_zname,$len_tel,$len_address,$zuser,$table_profix,$id );
	 
    function createDatabase ($zname,$tel,$address,$len_zname,$len_tel,$len_address,$zuser,$table_profix,$id ) {

        define('DB_HOST','localhost');    
		define('DB_USER','root'); 
		define('DB_PASS','123456'); 
		define('DB_NAME','erpunqq'); 
 
		$content = file_get_contents('demo.sql'); 
		$content = str_replace("ci_",$table_profix,$content);
		$mysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); 
		mysqli_query($mysqli,'set names utf8');
		$ret = $mysqli->multi_query($content); 
		if($ret === false) { 
			echo mysqli_error($mysqli); 
		} 
		while (mysqli_more_results($mysqli)) { 
			if (mysqli_next_result($mysqli) === false) { 
				echo mysqli_error($mysqli); 
				echo "\r\n"; 
				break; 
			} 
		} 
		$mysqli->close(); 
		/*********初始化数据************************************/
		$conn = connect_main();
		$sql = "UPDATE ".$table_profix."admin SET username='".$zuser."' WHERE uid = 1";
		mysql_query( $sql, $conn );
		mysql_query( 'SET NAMES UTF8', $conn );
		$sql = "UPDATE ".$table_profix."options SET option_value='a:10:{s:11:\"companyName\";s:".$len_zname.":\"".$zname."\";s:11:\"companyAddr\";s:".$len_address.":\"".$address."\";s:5:\"phone\";s:".$len_tel.":\"".$tel."\";s:3:\"fax\";s:0:\"\";s:8:\"postcode\";s:0:\"\";s:9:\"qtyPlaces\";s:1:\"1\";s:11:\"pricePlaces\";s:1:\"1\";s:12:\"amountPlaces\";s:1:\"2\";s:10:\"valMethods\";s:13:\"movingAverage\";s:18:\"requiredCheckStore\";s:1:\"1\";}' WHERE option_id = 1 ";
		mysql_query( $sql, $conn );
		
		$sql = "UPDATE admin_zuser SET zstauts=1 WHERE id =$id";
		mysql_query( $sql, $conn );
		
		mysql_close( $conn );

		echo "激活成功！";
		echo "<meta http-equiv='refresh' content='0; url=st_table.php' />";
		
    }
?>