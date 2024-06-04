<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;

class IndexController extends AuthController {
   public function index(){
       //读出左侧菜单栏
       $member = session('member');
       $role_list=M('role_group')->where(array('id'=>$member['group_id']))->getField('role');
       $role_list=explode(',',$role_list);
       $nav_list=array();
       $left_nav = M('left_nav');
       $nav_list=$left_nav->where(array('parent_id'=>0,'isdel'=>0))->order('sort DESC')->select();
       foreach($nav_list as $key=>&$val){
           //排除第一个超级账号
           if($member['uid'] == '1')
           {
               $val['nav']=$left_nav->where(array('parent_id'=>$val['id'],'isdel'=>0))->order('sort DESC')->select();
           }
           else
           {
               $nav=$left_nav->field('id')->where(array('parent_id'=>$val['id'],'isdel'=>0))->select();
               $intersect=array_intersect($role_list,$this->dealArray($nav));
               if(!empty($intersect)){
                   $val['nav']=$left_nav->where("id IN (".implode(',',$intersect).")")->select();
               }else{
                   unset($nav_list[$key]);
               }
           }
       }

       $this->assign('nav_list',$nav_list);

       $this->display();
   }

    /**
     * 二维数组处理成一维数组
     * @param $arr
     * @return array
     */
    public function dealArray($arr){
        if(!empty($arr) && is_array($arr)){
            $newArr=array();
            foreach($arr as $val){
                $newArr[]=$val['id'];
            }
            return $newArr;
        }
        return array();
    }
    public function main(){

		
        //查询mysql版本
        $mysql_version=(new Model())->query("Select VERSION() as version");
        //查询用户分组
        $group_name=M('role_group')->where(array('id'=>session('member')['group_id']))->getField('name');
        $this->assign('mysql_version',$mysql_version[0]['version']);
        $this->assign('group_name',$group_name);
        $this->display();
    }
    //修改密码页面
    public function changePwd(){
        $this->display();
    }
}