<?php

/**
 * ERP 订单插入接口
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

    case 'pay':    //用户登录
    {
        
		if (empty($_REQUEST['goodsList']) || empty($_REQUEST['totalMoney']) || empty($_REQUEST['cid']))
		{
			$results = array('result'=>'false', 'data'=>'参数不全，请查证');
			exit($json->encode($results));
		}
		$goodsList = trim($_REQUEST['goodsList']);
		$totalMoney = 	trim($_REQUEST['totalMoney']);	
		$cid = 	trim($_REQUEST['cid']);	
		$data = array();
		$data['goodsList']=$goodsList;
		$data['totalMoney']=$totalMoney;
		$data['cid']=$cid;
		$data['ordernum']='xs12313132';
		$results = array('result'=>'true', 'data'=>$data);
		
		//$db_prefix = $cid."_";
		//$sql = "SELECT uid,username,userpwd FROM " . $db_prefix . "admin WHERE username = '$username'";

		/*$title  = mysql_query($sql);
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
		}		*/
        exit($json->encode($results));
        break;
    }
    case 'payok':    //用户登录
    {
        
		if (empty($_REQUEST['oid'])  || empty($_REQUEST['cid']) || empty($_REQUEST['pid']))
		{
			$results = array('result'=>'false', 'data'=>'参数不全，请查证');
			exit($json->encode($results));
		}
		/*$goodsList = trim($_REQUEST['goodsList']);
		$totalMoney = 	trim($_REQUEST['totalMoney']);	
		$cid = 	trim($_REQUEST['cid']);	
		$data = array();
		$data['goodsList']=$goodsList;
		$data['totalMoney']=$totalMoney;
		$data['cid']=$cid;
		$data['ordernum']='xs12313132';*/
		$results = array('result'=>'true', 'data'=>'成功');

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

?>
?>