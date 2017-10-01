<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: gy
 * Date: 2017/8/21 0021
 * Time: 下午 7:27
 */
class Mobile extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
    }
    public function  index()
    {
        $keywords=$_REQUEST['keywords'];
      die(json_encode($keywords));
    }
    public function test()
    {
        $sql='select categoryName from lei_goods';
        $query = $this->db->query($sql);
        $result = $query->result_array();
        $r=array();
        foreach ($query->result_array() as $row=>$key)
        {
            $r[$row]=$key['categoryName'];
        }
        die($r);
    }
    public function test1()
    {
        $sql = 'select invId,locationId,billDate
					
				from lei_INVOICE_INFO 
				where 
					(isDelete=0) and billDate<="2017-08-22"';
        $query = $this->db->query($sql);
        $result = $query->result();
        die($result);
    }


}


//sum(qty) as qty,
//sum(case when transType=150501 or transType=150502 or transType=150807 or transType=150706 or billType="INI" then amount else 0 end) as incost,
//sum(case when transType=150501 or transType=150502 or transType=150706 or billType="INI" then qty else 0 end) as inqty

?>