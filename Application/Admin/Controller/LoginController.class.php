<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-22
 * Time: 下午 2:39
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Verify;
class LoginController extends Controller {
    //验证码
    public function verify(){
        $config =array(
            'fontSize'  =>  20,              // 验证码字体大小(px)
            'useImgBg'  =>  false,          // 使用背景图片
            'length'=>2,
            'reset'=>false,
        );
        $Verify=new Verify($config);
        $Verify->entry();
    }
    public function check_verify($code){
        $verify = new Verify();
        $res = $verify->check($code);
        return $res;
    }
    public function login(){


//        sha1($info['password'].$record['salt']);

//        echo sha1('123456'.'agpsyGM0');

        $web_name = M("config")->where("id=1")->getField("web_name");
        $this->assign('title',$web_name);
        $this->display();
    }


    public function editPassword(){
        $this->display();
    }
    public function passwordDo(){
        $password=I('');
        $record=M('admin_users')->where(array('uid'=>$_SESSION['member']['uid']))->find();
        if(!empty($password)){
            if(sha1($password['oldPassword'].$record['salt']) == $record['password']){
                $result=M('admin_users')->where(array('uid'=>$_SESSION['member']['uid']))->save(array('password'=>sha1($password['newPassword'].$record['salt'])));
                if($result){
                    die(json_encode(array('status'=>200,'msg'=>'')));
                }else{
                    die(json_encode(array('status'=>400,'msg'=>'修改失败')));
                }
            }else{
                die(json_encode(array('status'=>400,'msg'=>'旧密码不正确')));
            }
        }else{
            die(json_encode(array('status'=>400,'msg'=>'修改失败')));
        }
    }
    /*
     * @param $username
     * @param $password
     * 登录方法
     */
    public function doLogin(){
        $info=[
            'username'=>trim(addslashes(I('username'))),
            'password'=>trim(addslashes(I('password')))
        ];
        if(!empty($info)){
            if(!$this->check_verify(I('code'))){
                die(json_encode(array('status'=>400,'msg'=>'验证码错误')));
            }
            $record=M('admin_users')->where(['username'=>$info['username']])->find();
            if($record){
                if(sha1($info['password'].$record['salt']) == $record['password']){
                    $_SESSION['member']=$record;
                    session('admin_id',$record['uid']);
                    M('admin_users')->where(array('id'=>$record['id']))->save(['logintime'=>date('Y-m-d H:i:s'),'loginip'=>get_client_ip()]);
                    die(json_encode(array('status'=>200,'msg'=>'账号密码错误')));
                }else{
                    die(json_encode(array('status'=>400,'msg'=>'账号密码错误')));
                }
            }else{
                die(json_encode(array('status'=>400,'msg'=>'账号密码错误')));
            }
        }
    }
    public function logout(){
        session(null);
        $this->redirect("Login/login");
    }
}