<?php

/**
 * ERP 登录接口
 * $Author:  Jarod qi 20170810
 * $Id:  
 */
@header('Content-type: text/html;charset=UTF-8');
@header("Access-Control-Allow-Origin:*");
@header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
@header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
define('IN_ECS', true);

require('../admin/connect.php');
require_once('cls_json.php');
$conn  = connect_main();
$json = new JSON;

$action = isset($_REQUEST['action'])? $_REQUEST['action']:'';

if (empty($_REQUEST['action']))
{
	$results = array('result'=>'false', 'data'=>'缺少action必要的参数');
	exit($json->encode($results));
}	

switch ($action)
{
    case 'register':    //用户注册
    {
        
		if (empty($_REQUEST['mobile_phone']))
		{
			$results = array('result'=>'false2', 'data'=>'缺少电话号码参数');
			exit($json->encode($results));
		}		
        exit($json->encode($results));
        break;
    }
    case 'login':    //用户登录
    {
        
		if (empty($_REQUEST['username']) || empty($_REQUEST['pw']) || empty($_REQUEST['cid']))
		{
			$results = array('result'=>'false', 'data'=>'用户名或密码或商户标识不能为空');
			exit($json->encode($results));
		}
		$username = trim($_REQUEST['username']);
		$pw = 	trim($_REQUEST['pw']);	
		$cid = 	trim($_REQUEST['cid']);	
		$db_prefix = $cid."_";
		$sql = "SELECT uid,username,userpwd FROM " . $db_prefix . "admin WHERE username = '$username'";

		$title  = mysql_query($sql);
		$result = mysql_fetch_assoc($title);

        if (!empty($result) and @$result['userpwd']==$pw)
        {
 
			$results['result'] = 'true';
			$results['cid'] = $cid;
			$results['data'] = $result;
		 
        }else
		{
			$results = array('result'=>'false', 'data'=>'密码不正确');
			exit($json->encode($results));		
		}		
        exit($json->encode($results));
        break;
    }
	case 'logout':    //用户退出
    {
        
		//set_cookie();
		set_session();	
		$results = array('result'=>'true', 'data'=>'退出成功');	
        exit($json->encode($results));
        break;
    }		
    default:
    {
        $results = array('result'=>'false', 'data'=>'缺少动作');
        exit(json_encode($results));
        break;
    }
}

/**
 * 设置指定用户SESSION
 *
 * @access public
 * @param        	
 *
 * @return void
 */
 function set_session ($username = '')
{
	if(empty($username))
	{
		$GLOBALS['sess']->destroy_session();
	}
	else
	{
		$sql = "SELECT user_id, password, email FROM " . $GLOBALS['ecs']->table('users') . " WHERE user_name='$username' LIMIT 1";
		$row = $GLOBALS['db']->getRow($sql);
		
		if($row)
		{
			$_SESSION['user_id'] = $row['user_id'];
			$_SESSION['user_name'] = $username;
			$_SESSION['email'] = $row['email'];
		}
	}
}
?>
?>