<?PHP
	/**租户激活
	@ Auther Jarod qi
	@ time 20170807
	**/
	session_start();
	require_once( 'functions.php' );
    header("Content-type: text/html; charset=utf-8");
	$id  = $_GET['id'];
	//$zuname  = $_GET['zuname'];
	$table_profix  = $_GET['zdb'];
	//$tel  = $_GET['tel'];
 	//$con = connect_main();
	//$sql_up = "";
    $school="";
	createDatabase( $school, $table_profix );

	 
    function createDatabase ( $db_name, $table_profix ) {
        $conn = connect_main();
        //mysql_query( 'SET NAMES UTF8', $conn );
        //mysql_query( 'CREATE DATABASE IF NOT EXISTS ' . $db_name . ' DEFAULT CHARACTER SET UTF8', $conn );
        //mysql_query( 'USE ' . $db_name, $conn );
        
		$_sql = file_get_contents('test.sql');
 
		$_arr = explode(';', $_sql);
		$_mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS);
		if (mysqli_connect_errno()) {
			exit('连接数据库出错');
		}
		//执行sql语句
		foreach ($_arr as $_value) {
			$_mysqli->query($_value.';');
		}
		$_mysqli->close();
		$_mysqli = null;
		
        $sql = str_replace( 'ci_', $table_profix, $sql );
		//echo $sql;
		
		mysql_query( $sql, $conn );
        	


	
        mysql_close( $conn );
		
		
		echo "激活成功！";
		echo "<meta http-equiv='refresh' content='0; url=st_table.php' />";
		
    }
?>