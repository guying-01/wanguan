<?php

/**
 * ERP 商品分类接口
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
    case 'cate':    //订单列表
    {
        
		if (empty($_REQUEST['cid']))
		{
			$results = array('result'=>'false', 'data'=>'商户标识不能为空');
			exit($json->encode($results));
		}
		$cid = 	trim($_REQUEST['cid']);	
		$results = array('result' => 'false', 'data' => array());
		$db_prefix = $cid."_";
		$sql = "SELECT id, NAME, parentId FROM ".$db_prefix."category WHERE typeNumber='trade' AND isDelete = 0";
		//$sql = "SELECT id, NAME, parentId FROM ci_category WHERE typeNumber='trade' AND isDelete = 0";

        $results = array('result' => 'false', 'data' => array());
        $query = mysql_query($sql);
        $record_count = 0;
        while ($goods = mysql_fetch_assoc($query))
        {
            $results['data'][] = $goods;
            $record_count++;
        }
        if ($record_count > 0)
        {
            $results['result'] = 'true';
        }else
		{
			$results = array('result'=>'false', 'data'=>'数据为空');
		}
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