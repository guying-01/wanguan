<?php

/**
 * ECSHOP 用户信息API
 * ============================================================================
 * 版权所有 2005-2011 天合阳光，并保留所有权利。
 * 网站地址: http://www.nafvip.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: Jarod qi $
 * $Id: user.php 17217 2017-05-15 06:29:08Z  $
 */


define('IN_ECS', true);

require('./init.php');
require_once(ROOT_PATH . 'includes/cls_json.php');

$json = new JSON;

$action = isset($_REQUEST['action'])? $_REQUEST['action']:'';

if (empty($_REQUEST['action']))
{
	$results = array('result'=>'false1', 'data'=>'缺少action必要的参数');
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
        
		if (empty($_REQUEST['username']) || empty($_REQUEST['pw']))
		{
			$results = array('result'=>'false2', 'data'=>'用户名或密码');
			exit($json->encode($results));
		}
		$username = trim($_REQUEST['username']);
		$pw = 	trim($_REQUEST['pw']);	
		$sql = "SELECT `user_id`, `email`, `user_name`, `password`, `ec_salt`, `headimg` FROM " . $ecs->table('users') . " WHERE user_name = '$username'";
		$result = $db->getRow($sql);
		//echo $result['password']."<br>";
		$ppww = md5(md5($pw).$result['ec_salt']);
        if (!empty($result) and md5($result['password']==md5($pw)))
        {
            if($ppww==$result['password'])
			{
				$results['result'] = 'true';
				$results['data'] = $result;
			}else
			{
				$results = array('result'=>'false3', 'data'=>'密码不正确');
				exit($json->encode($results));		
			}
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