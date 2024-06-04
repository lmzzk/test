<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;

class AccountController extends AuthController {
    //系统用户列表
   public function user_list(){
       $User = M('admin_users'); // 实例化User对象
       $count      = $User->where('`delete`=0 and `uid`<> 1')->count();// 查询满足要求的总记录数
       $page       = $this->showPage($count);// 分页显示输出
       //赋值数据集
       $this->assign('page',$page->show());//赋值分页输出
       $user_list=$User->field("jxkj_admin_users.*,jxkj_role_group.name")->where("jxkj_admin_users.`delete`=0 and `uid`<> 1")
           ->join('jxkj_role_group ON jxkj_role_group.id = jxkj_admin_users.group_id','LEFT')->limit($page->firstRow.','.$page->listRows)->select();
       $this->assign('user_list',$user_list);//赋值分页输出
       $this->display();
   }
    //权限分组
    public function role(){
        //权限组列表
        $group_list=M('role_group')->where(array('delete'=>0))->select();
        $this->assign('group_list',$group_list);
        $this->display();
    }
    //添加用户
    public function addUser()
    {
        $uid=intval(I('uid'));
        //权限组列表
        $group_list=M('role_group')->where(array('delete'=>0))->select();
        //编辑状态
        if($uid){
            $user=M('admin_users')->where(array('delete'=>0,'uid'=>$uid))->find();
            $this->assign('user',$user);
        }
        $this->assign('group_list',$group_list);
        $this->display();
    }
    
    //添加权限分组
    public function addRole(){
        $id=intval(I('id'));
        //编辑状态
        if($id){
            //权限组信息
            $group_info=M('role_group')->where(array('delete'=>0,'id'=>$id))->find();
            $group_info['role']=explode(',',$group_info['role']);
            $this->assign('group_info',$group_info);
        }
        //读出左侧菜单栏
        $nav_list=array();
        $nav_list=M('left_nav')->where(array('parent_id'=>0))->order('sort DESC')->select();
        foreach($nav_list as &$val)
        {
            $parent=M('left_nav')->field('id')->where(array('parent_id'=>$val['id']))->order('sort DESC')->select();
            foreach($parent as &$v)
            {
                $v=$v['id'];
            }
            $val['all_in']=false;
            
            $diff = array_diff($parent,$group_info['role']);
            if(empty($diff) && $group_info['role'])
            {
                $val['all_in']=true;
            }
            $val['nav']=M('left_nav')->where(array('parent_id'=>$val['id']))->order('sort DESC')->select();
        }
        $this->assign('nav_list',$nav_list);
        $this->display();
    }
    //添加、编辑用户方法
    public function editUser()
    {
        $data=I('');
        if(!empty($data)){
            if($data['password'] != $data['sure_password']){
                die(json_encode(array('status'=>400,'msg'=>'两次密码输入不一致')));
            }
            $salt=create_str();
            $insert=array(
                'username'=>$data['username'],
                'password'=>sha1($data['password'].$salt),
                'passwords'=>$data['password'],
                'salt'=>$salt,
                'group_id'=>$data['role_group'],
                'is_points'=>$data['is_points'],
                'regtime'=>date('Y-m-d H:i:s')
            );
            //编辑状态
            if($data['uid']){
                //编辑用户时，检查用户名是否可用
                $oldname=M('admin_users')->where(array('delete'=>0,'uid'=>$data['uid']))->getField('username');
                if($oldname != $data['username']){
                    $record=M('admin_users')->where(array('delete'=>0,'username'=>$data['username']))->find();
                    if(!empty($record)){
                        die(json_encode(array('status'=>400,'msg'=>'用户名不可用')));
                    }
                }
                $res=M('admin_users')->where(array('delete'=>0,'uid'=>$data['uid']))->save($insert);
                //系统日志
                system_log('编辑','编辑账号');
            }
            else
            {
                //添加用户时，检查用户名是否可用
                $record=M('admin_users')->where(array('delete'=>0,'username'=>$data['username']))->find();
                if(!empty($record))
                {
                    die(json_encode(array('status'=>400,'msg'=>'用户名不可用')));
                }
                $res=M('admin_users')->add($insert);
                //系统日志
                system_log('新增','添加账号');
            }
            if($res > 0)
            {
                die(json_encode(array('status'=>200,'msg'=>'操作成功')));
            }
            else
            {
                die(json_encode(array('status'=>400,'msg'=>'操作失败')));
            }
        }else{
            die(json_encode(array('status'=>400,'msg'=>'请求错误')));
        }
    }
    //删除账号
    public function deleteUser(){
        $uid=intval(I('uid'));
        if($uid){
            if($uid == 1){
                die(json_encode(array('status'=>400,'msg'=>'此账号无法删除')));
            }
            $res=M('admin_users')->where(array('delete'=>0,'uid'=>$uid))->save(array('delete'=>1));
            if($res == 1 || $res==0){
                die(json_encode(array('status'=>200,'msg'=>'操作成功')));
            }else{
                die(json_encode(array('status'=>400,'msg'=>'删除账号失败，请重试')));
            }
        }else{
            die(json_encode(array('status'=>400,'msg'=>'请求错误')));
        }
    }
    //批量删除分组
    public function deleteRole(){
        $arr_id=I('id');
        $arr_id=is_array($arr_id) ? $arr_id : array($arr_id);
        if(!empty($arr_id)){
            foreach($arr_id as $val){
                M('role_group')->where(array('id'=>$val))->save(array('delete'=>1));
            }
            die(json_encode(array('status'=>200,'msg'=>'')));
        }else{
            die(json_encode(array('status'=>400,'msg'=>'请求错误')));
        }
    }
    //添加、编辑权限分组方法
    public function editRole(){
        $data=I('');
        if(!empty($data)){
            //编辑状态
            $data['role']=is_array($data['role'])? implode(',',$data['role']) : null;
            $data['createtime']=date('Y-m-d H:i:s');
            if($data['id']){
                //编辑用户时，检查用户名是否可用
                $oldname=M('role_group')->where(array('delete'=>0,'id'=>$data['id']))->getField('name');
                if($oldname != $data['name']){
                    $record=M('role_group')->where(array('delete'=>0,'name'=>$data['name']))->find();
                    if(!empty($record)){
                        die(json_encode(array('status'=>400,'msg'=>'权限分组名不能重复')));
                    }
                }
                $res=M('role_group')->where(array('delete'=>0,'id'=>$data['id']))->save($data);
            }else{
                //添加用户时，检查用户名是否可用
                $record=M('role_group')->where(array('delete'=>0,'name'=>$data['name']))->find();
                if(!empty($record)){
                    die(json_encode(array('status'=>400,'msg'=>'权限分组名不能重复')));
                }
                $res=M('role_group')->add($data);
            }
            if($res > 0){
                die(json_encode(array('status'=>200,'msg'=>'操作成功')));
            }else{
                die(json_encode(array('status'=>400,'msg'=>'操作失败')));
            }
        }else{
            die(json_encode(array('status'=>400,'msg'=>'请求错误')));
        }
    }
}