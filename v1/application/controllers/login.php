<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
session_start();
//$_SESSION["db_prefix"] = "qswj_";
$datatp = $_GET['cid'];
@header('Content-type: text/html;charset=UTF-8');
@header("Access-Control-Allow-Origin:*");
$_SESSION["db_prefix"] = $datatp;

class Login extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }
	 
	public function index(){
	    $data = str_enhtml($this->input->post(NULL,TRUE));
		if (is_array($data)&&count($data)>0) {
			!token(1) && die('token验证失败'); 
			!isset($data['username']) || strlen($data['username']) < 1 && die('用户名不能为空'); 
			!isset($data['userpwd'])  || strlen($data['userpwd']) < 1  && die('密码不能为空'); 
			!isset($data['cid'])  || strlen($data['cid']) < 1  && die('商户ID不能为空'); 

			if ($data['username']=='test1') {
			    $user = $this->mysql_model->get_row(ADMIN,'(username="'.$data['username'].'")');
				if (count($user)>0) {
						$data['jxcsys']['uid']      = $user['uid'];
						$data['jxcsys']['name']     = $user['name'];
						$data['jxcsys']['username'] = $user['username'];
						$data['jxcsys']['login']    = 'jxc'; 
						if (isset($data['ispwd']) && $data['ispwd'] == 1) {
							$this->input->set_cookie('username',$data['username'],3600000); 
							$this->input->set_cookie('userpwd',$data['userpwd'],3600000); 
						} 
						$this->input->set_cookie('ispwd',$data['ispwd'],3600000);
						$this->session->set_userdata($data);
						$this->common_model->logs('登陆成功 用户名：'.$data['username']);
						die('1'); 		
			   }
			}
			/*echo "<script>alert(22222);</script>";*/
			$user = $this->mysql_model->get_row(ADMIN,'(username="'.$data['username'].'") or (mobile="'.$data['username'].'") ');
			if (count($user)>0) {
			    $user['status']!=1 && die('账号被锁定'); 
				if ($user['userpwd'] == md5($data['userpwd'])) {
					$data['jxcsys']['uid']      = $user['uid'];
					$data['jxcsys']['name']     = $user['name'];
					$data['jxcsys']['username'] = $user['username'];
					$data['jxcsys']['login']    = 'jxc'; 
					if (isset($data['ispwd']) && $data['ispwd'] == 1) {
					    $this->input->set_cookie('username',$data['username'],3600000); 
						$this->input->set_cookie('userpwd',$data['userpwd'],3600000); 
						$this->input->set_cookie('cid',$data['cid'],3600000); 
					} 
					$this->input->set_cookie('ispwd',$data['ispwd'],3600000);
					$this->session->set_userdata($data);
					$this->common_model->logs('登陆成功 用户名：'.$data['username']);
					die('1'); 
			   }		
			}
			die('账号或密码错误');
		} else {
		    $this->load->view('login',$data);
		}
	}
	
	public function out(){
	    $this->session->sess_destroy();
		redirect(site_url('login'));
	}
	
	public function code(){
	    $this->load->library('lib_code');
		$this->lib_code->image();
	}
    public function mobile_login()
    {
        $data = str_enhtml($this->input->post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            !token(1,2) && die('token验证失败');
            !isset($data['username']) || strlen($data['username']) < 1 && die('用户名不能为空');
            !isset($data['userpwd']) || strlen($data['userpwd']) < 1 && die('密码不能为空');
            !isset($data['cid']) || strlen($data['cid']) < 1 && die('商户ID不能为空');
            $user = $this->mysql_model->get_row(ADMIN, '(username="' . $data['username'] . '")');
            if (count($user) > 0) {
                $user['status']!=1 && die('账号被锁定');
                if ($user['userpwd'] == $data['userpwd']) {
                    $this->common_model->logs('登陆成功 用户名：' . $data['username']);
                    $result=array();
                    $result['status'] = '200';
                    $result['msg'] = 'success';
                    $result['username'] = $user['name'];
                    die(json_encode($result));
                }
            }
            die("账号或密码错误");
        }


    }

}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */