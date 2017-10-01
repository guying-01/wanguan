<?PHP
/*
* @Description    学生用户列表
* @Author         Jarod qi(2014-08-30)
*/
	session_start();
	define('IN_ECS', true);
	error_reporting(E_ALL & ~E_NOTICE);
	require_once("connect.php");
	$conn = connect_main();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!--
        ===
        This comment should NOT be removed.

        Charisma v2.0.0

        Copyright 2012-2014 Muhammad Usman
        Licensed under the Apache License v2.0
        http://www.apache.org/licenses/LICENSE-2.0

        http://usman.it
        http://twitter.com/halalit_usman
        ===
    -->
    <meta charset="utf-8">
    <title>万贯云商平台-后台管理平台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Charisma, a fully featured, responsive, HTML5, Bootstrap admin template.">
    <meta name="author" content="Muhammad Usman">

    <!-- The styles -->
    <link id="bs-css" href="css/bootstrap-cerulean.min.css" rel="stylesheet">

    <link href="css/charisma-app.css" rel="stylesheet">
    <link href='bower_components/fullcalendar/dist/fullcalendar.css' rel='stylesheet'>
    <link href='bower_components/fullcalendar/dist/fullcalendar.print.css' rel='stylesheet' media='print'>
    <link href='bower_components/chosen/chosen.min.css' rel='stylesheet'>
    <link href='bower_components/colorbox/example3/colorbox.css' rel='stylesheet'>
    <link href='bower_components/responsive-tables/responsive-tables.css' rel='stylesheet'>
    <link href='bower_components/bootstrap-tour/build/css/bootstrap-tour.min.css' rel='stylesheet'>
    <link href='css/jquery.noty.css' rel='stylesheet'>
    <link href='css/noty_theme_default.css' rel='stylesheet'>
    <link href='css/elfinder.min.css' rel='stylesheet'>
    <link href='css/elfinder.theme.css' rel='stylesheet'>
    <link href='css/jquery.iphone.toggle.css' rel='stylesheet'>
    <link href='css/uploadify.css' rel='stylesheet'>
    <link href='css/animate.min.css' rel='stylesheet'>

    <!-- jQuery -->
    <script src="bower_components/jquery/jquery.min.js"></script>

    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- The fav icon -->
    <link rel="shortcut icon" href="img/favicon.ico">

</head>

<body>
<?php
require "left_menu.php";
?>

 

    <div class="row">
    <div class="box col-md-12">
    <div class="box-inner">
    <div class="box-header well" data-original-title="">
        <h2><i class="glyphicon glyphicon-user"></i> 租户列表</h2>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 <a href="user_add.php"><button class="btn btn-primary btn-sm">添加新租户</button></a>
        <div class="box-icon">
            <a href="#" class="btn btn-setting btn-round btn-default"><i class="glyphicon glyphicon-cog"></i></a>
            <a href="#" class="btn btn-minimize btn-round btn-default"><i
                    class="glyphicon glyphicon-chevron-up"></i></a>
            <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
        </div>
    </div>
    <div class="box-content">
    
    <table class="table table-striped table-bordered bootstrap-datatable datatable responsive">
    <thead>
    <tr>
        <th>租户名</th>
        <th>管理员帐户</th>
        <th>电话</th>
        <th>地址</th>
        <th>数据标识</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
<?PHP
    $sql = '';
	$sql = "SELECT * FROM admin_zuser order by id desc" ;
    $all = fetchAll( $sql );
    foreach( $all AS $v ) {
		$type_u ='111';
		if($v['zstauts']==="0")
		{
			$type_u ="未激活";
		}else if($v['zstauts']==="1")
		{
			$type_u ="已激活";
		}
        echo "<tr>
				<td>$v[zname]</td>
				<td>$v[zuser]</td>
				<td class='center'>$v[tel]</td>
				<td class='center'>$v[address]</td>
				<td class='center'>$v[db_prefix]</td>
				<td class='center'>
					<span class='label-success label label-default'>$type_u</span>
				</td>
				<td class='center'>
					<a class='btn btn-info' href='user_edit.php?id=$v[id]'>
						<i class='glyphicon glyphicon-edit icon-white'></i>
						编辑
					</a>
					<a class='btn btn-danger' href='user_del.php?id=$v[id]'>
						<i class='glyphicon glyphicon-trash icon-white'></i>
						删除
					</a>
					<a class='btn btn-danger' href='user_live.php?id=$v[id]'>
						<i class='glyphicon glyphicon-cog icon-white'></i>
						激活
					</a>
				</td>
			</tr>";
    }
	
/**
 * 创建数据库连接
 * @param  string   $sql    SQL查询语句
 * @param  resource $conn   MySQL数据库连接资源
 * @return array    $result 查询结果(ACOSS数组)
**/
function fetchAll( $sql, $conn = '' ) {
    if ( $conn == '' ) {
        $flag = true;
        $conn = connect_main();
    }

    $tmp = mysql_query( $sql, $conn ) or die( '无法完成查询' );
    $result = array();
    while ( $temp = mysql_fetch_assoc( $tmp ) ) {
        array_push( $result, $temp );
    }

    if ( $flag ) {
        closeMySQLConnect( $conn );
    }
    return $result;
}	
/**
 * 关闭数据库连接
 * @param  resource $conn  MySQL数据库连接资源
 * @return bool   true/false
**/
function closeMySQLConnect( $conn ) {
    return mysql_close( $conn );
}	
?>    



    </tbody>
    </table>
    </div>
    </div>
    </div>
    <!--/span-->

    </div><!--/row-->


    <!-- content ends -->
    </div><!--/#content.col-md-0-->
</div><!--/fluid-row-->


</div><!--/.fluid-container-->

<!-- external javascript -->

<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- library for cookie management -->
<script src="js/jquery.cookie.js"></script>
<!-- calender plugin -->
<script src='bower_components/moment/min/moment.min.js'></script>
<script src='bower_components/fullcalendar/dist/fullcalendar.min.js'></script>
<!-- data table plugin -->
<script src='js/jquery.dataTables.min.js'></script>

<!-- select or dropdown enhancer -->
<script src="bower_components/chosen/chosen.jquery.min.js"></script>
<!-- plugin for gallery image view -->
<script src="bower_components/colorbox/jquery.colorbox-min.js"></script>
<!-- notification plugin -->
<script src="js/jquery.noty.js"></script>
<!-- library for making tables responsive -->
<script src="bower_components/responsive-tables/responsive-tables.js"></script>
<!-- tour plugin -->
<script src="bower_components/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
<!-- star rating plugin -->
<script src="js/jquery.raty.min.js"></script>
<!-- for iOS style toggle switch -->
<script src="js/jquery.iphone.toggle.js"></script>
<!-- autogrowing textarea plugin -->
<script src="js/jquery.autogrow-textarea.js"></script>
<!-- multiple file upload plugin -->
<script src="js/jquery.uploadify-3.1.min.js"></script>
<!-- history.js for cross-browser state change on ajax -->
<script src="js/jquery.history.js"></script>
<!-- application script for Charisma demo -->
<script src="js/charisma.js"></script>


</body>
</html>
