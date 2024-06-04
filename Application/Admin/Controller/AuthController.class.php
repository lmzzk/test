<?php

namespace Admin\Controller;


use Think\Controller;
use Think\Model;
use Think\Page;

class AuthController extends Controller {

    public function  _initialize(){

        $web_name = M("config")->where("id=1")->getField("web_name");
        $this->assign('title',$web_name);

        if(!isset($_SESSION['member']) || empty($_SESSION['member'])){
            $this->redirect('Login/login');
        }
        //权限验证
        $role_list=M('role_group')->where(array('id'=>session('member')['group_id']))->getField('role');
        $role_list=explode(',',$role_list);
        $url=CONTROLLER_NAME."/".ACTION_NAME;
        //排除第一个超级账号
        if($url !='Index/Index' && $url !='Index/main' && session('member')['uid'] != '1') {
            $role_id = M('left_nav')->where(array('nav_url' => $url))->getField('id');
            if (!in_array($role_id, $role_list) && $role_id) {
                $this->redirect('Index/main');
            }
        }

    }

    /**
     * 上传图片
     */
    public function uploadImage(){
        $file=$_FILES['file'];
        $allowExts=array('png','jpg','jpeg','gif');
        //图片类型
        $type=I('path') ? I('path') : 'normal';
        $path="/Upload/$type/";
        die(json_encode(uploadFile($file,$allowExts,$path)));
    }
    /**
     * 分页方法
     * @param $count 分页总记录数
     * @param int $pagesize 每页显示数量
     * @return Page
     */
    public function showPage($count, $pagesize = 10) {
        $page = new \Think\Page($count, $pagesize);
        $page->setConfig('header', '<span class="layui-laypage-count">共 %TOTAL_ROW% 条</span>');
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        $page->setConfig('last', '末页');
        $page->setConfig('first', '首页');
        $page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $page->lastSuffix = false;//最后一页不显示为总页数
        return $page;
    }
}