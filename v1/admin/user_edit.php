<?PHP
/*
* @Description    登录文件
* @Author         Jarod qi(2014-08-30)
*/
	session_start();
	define('IN_ECS', true);
	require_once( 'functions.php' );
 
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
	<script language="javascript">
        function check() {
            if( document.getElementById('un').value=="" ) {
                alert( "租户户名不能为空！" );
                return false;
            }
            if( document.getElementById('pw').value=="" ) {
                alert("电话不能为空！");
                return false;
            }
            if( document.getElementById('nn').value=="" ) {
                alert("地址不能为空！");
                return false;
            }
            if( document.getElementById('tp').value=="" ) {
                alert("数据库标识不能为空！");
                return false;
            }
			if( document.getElementById('zu').value=="" ) {
                alert("管理员帐户不能为空！");
                return false;
            }						
            return true;
        }
    </script>
</head>

<body>
 
<?php
require "left_menu.php";
$conn     = createMySQLConnect();
@$id  = $_GET['id'];
$sql = "SELECT * FROM admin_zuser WHERE id='$id' ";
$tp  =  mysql_query( $sql, $conn );
$row =  mysql_fetch_assoc($tp);
?>


<div class="row">
    <div class="box col-md-12">
        <div class="box-inner">
            <div class="box-header well" data-original-title="">
                <h2><i class="glyphicon glyphicon-edit"></i> 编辑用户信息</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-setting btn-round btn-default"><i
                            class="glyphicon glyphicon-cog"></i></a>
                    <a href="#" class="btn btn-minimize btn-round btn-default"><i
                            class="glyphicon glyphicon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round btn-default"><i
                            class="glyphicon glyphicon-remove"></i></a>
                </div>
            </div>
          
            <div class="box-content">
                <form method="post" action="user_edit.php?action=edit" onSubmit="return check();">
                    <div class="form-group">
                        <label for="exampleInputEmail1">租户名称</label>
                        <input type="text" class="form-control" id="un" name="zname"  value="<?php echo $row['zname'];?>" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">管理员帐户</label>
                        <input type="text" class="form-control" id="zu" name="zuser"  value="<?php echo $row['zuser'];?>" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">联系电话</label>
                        <input type="text" class="form-control" id="pw" name="tel"  value="<?php echo $row['tel'];?>" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">联系地址</label>
                        <input type="text" class="form-control" id="nn"  name="add" value="<?php echo $row['address'];?>" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">数据标识</label>
                        <input type="text" class="form-control" id="tp" name="db_prefix" value="<?php echo $row['db_prefix'];?>" >
                        <input type="hidden" class="form-control"  name="uid" value="<?php echo $row['id'];?>" >
                    </div>               
                    <button type="submit" class="btn btn-default">提交</button>
                </form>

            </div>
        </div>
    </div>
    <!--/span-->

</div><!--/row-->

    <!-- content ends -->
    </div><!--/#content.col-md-0-->
</div><!--/fluid-row-->

 <?PHP
	@$action    = $_GET['action'];
	@$zname     = $_POST['zname'];
	@$tel 	   = $_POST['tel'];
	@$address   = $_POST['add'];
	@$db_prefix = $_POST['db_prefix'];
	@$zuser     = $_POST['zuser'];
	@$uid       = $_POST['uid'];	
	if ( isset($action) ) {

		if ( $action == "edit" ) {
			$sql = "update admin_zuser set zname = '$zname',tel = '$tel',address='$address',db_prefix='$db_prefix', zuser='$zuser' where id = $uid";
	 
			$res = mysql_query($sql) or die("Bad query:");
			if($res)
			{
				echo "<center>修改成功</center>";
				echo "<meta http-equiv='refresh' content='0; url=st_table.php' />";
			}else
			{
				echo "<center>修改失败</center>";
				echo "<meta http-equiv='refresh' content='0; url=st_table.php' />";
			}
		}
	}
?>    

    

    

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

