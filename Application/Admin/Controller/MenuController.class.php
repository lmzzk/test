<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
 header("Content-Type: text/html;charset=utf-8");
class MenuController extends AuthController
{
   
    //菜单列表
    public function menu_list()
    {
    	
        $search_title   = I('search_title');//菜单名称
        $menu = M('menu');
        //查询
        if (IS_GET) {

            if ($search_title != '')
            {
	            $where['name'] = array("LIKE", '%' .$search_title . '%');

                $this->assign('search_title', $search_title);
            }

        }
        $where['is_del'] =0;
//        $where['f_id'] =0;
        $count = $menu->where($where)->count();

        // 查询满足要求的总记录数
        $page = $this->showPage($count,100); // 分页显示输出
        //赋值数据集
        $this->assign('page', $page->show()); //赋值分页输出
        $list = $menu->where($where)->order("sort desc")->limit($page->firstRow . ',' . $page->listRows)->select();

        foreach ($list as $key=>$value){
            $list[$key]['son_menu']=$menu->where(array('f_id'=>$value['id']))->select();
        }

        //赋值分页输出
        $this->assign('list', $list);
        $this->display();
    }

   //添加
    public function menu_add()
    {

        $menu = M('menu');
        $where['is_show']=1;
        $where['is_del']=0;
        $where['f_id']=0;

        $menu_list = $menu->where($where)->select();
        $this->assign('menu_list', $menu_list);

        $this->display();
       
    }
    //添加方法
    public function menu_add_do()
    {
        $data = I('');
	    $data['name']=$data['title'];
     
        
        $data['is_del']=0;
        $data['addtime']=date("Y-m-d H:i:s",time());

        $menu=M("menu");
        $isok =  $menu->add($data);
        if($isok)
        {
            echo 1;
        }
        else
        {
            echo 0;
        }

    }
    
 //*********************************************************************************************************************************************/   
    //商品修改
    public function menu_alter()
    {
        $id = intval(I('id'));
        if($id) 
        {

            $menu  = M('menu');
            $list  = $menu->where(array('is_del'=>0,'id'=>$id))->find();
            $this->assign('info',$list);

            $where['is_show']=1;
            $where['is_del']=0;
            $where['f_id']=0;

            $menu_list = $menu->where($where)->select();
            $this->assign('menu_list', $menu_list);

            $this->display();
        } else {
            $this->redirect('Menu/menu_list');
            exit();
        }
    }
        //修改方法
    public function menu_alter_do()
    { 
        $data = I('');
        $banner  = M('menu');
	    $data['name']=$data['title'];
        $data['addtime']=date("Y-m-d H:i:s",time());
        $isok =  $banner->where(array('is_del'=>0,'id'=>$data['id']))->save($data);

        if($isok)
        {
            echo 1;
        }
        else
        {
            echo 0;
        }

    }
 
     //显示或者隐藏
    public function menu_types()
    {
        //商品ID
        $id = intval(I('id'));
        $t  = intval(I('t'));
        if($id && IS_POST) 
        {
                //查找企业是否关联任务
                $banner = M('menu');
                $xgtime = Date('Y-m-d H:i:s');
                $res = $banner->where(array('is_del' => 0, 'id' => $id))->save(array('is_show' =>$t));
                if ($res == 1 || $res == 0) 
                {
                    die(json_encode(array('status' => 200, 'msg' => '操作成功')));
                } 
                else 
                {
                    die(json_encode(array('status' => 400, 'msg' => '删除失败，请重试')));
                }
        }
        else 
        {
            die(json_encode(array('status' => 400, 'msg' => '请求错误')));
        }
    }
    
    
    
    //删除
    public function menu_del()
    {
        //商品ID
        $id = intval(I('id'));
        if($id && IS_POST) 
        {
                //查找企业是否关联任务
                $banner = M('menu');
                $res = $banner->where(array('is_del' => 0, 'id' => $id))->save(array('is_del' =>1));
                if ($res == 1 || $res == 0) 
                {
                    die(json_encode(array('status' => 200, 'msg' => '操作成功')));
                } 
                else 
                {
                    die(json_encode(array('status' => 400, 'msg' => '删除失败，请重试')));
                }
        }
        else 
        {
            die(json_encode(array('status' => 400, 'msg' => '请求错误')));
        }
    }
    
    
    //上传
    public function upload_tag()
    {
        $classid = I('classid');
        if (isset($_FILES['file']) && $_FILES['file']["name"] != '' && $classid != '')  
        {
            $dar = "./Upload/banner/"; //上传文件保存的路径
            if (!is_dir($dar)) 
            { 
                //目录不存在
                if (!mkdir($dar,0777,true)) 
                {
                    //创建失败
                    $ajax = array('code' => 0, 'msg' => '文件夹创建失败！');
                    $return_ajax = json_encode($ajax);
                    echo $return_ajax;
                    exit();
                }
            }
            //清除缓存并再次检查文件大小
            clearstatcache();
            $name = $_FILES['file']["name"]; //上传文件的文件名
            //处理后缀
            $name = explode('.', $name);
            $t=1;
            if($name[1]=='mp4')
            {
              $t=2;  
            }
            
            $pathname =  time(). '.' . $name[1]; //上传文件保存的文件名
            $type = $_FILES['file']["type"]; //上传文件的类型
            $size = $_FILES['file']["size"]; //上传文件的大小
            //$path= "./Upload/company/$id/";   //上传文件保存的路径
            $allowtype = array(
                'image/jpeg',
                'image/gif',
                'image/png',
                'image/bmp',
                'image/pjpeg',
                'video/mp4'); //设置限制上传文件的类型
            $maxsize = 10485760; //限制文件上传大小 10M（字节）
            
            if (in_array($type, $allowtype)) 
            {        
                if ($size <= $maxsize) 
                {
                    $up_ok = move_uploaded_file($_FILES['file']["tmp_name"], $dar . $pathname);
                    if ($up_ok) {
                        //成功
                        $pathimg = __ROOT__ . "/Upload/banner/" . $pathname;
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                        $pathname = $protocol.$_SERVER[HTTP_HOST]."/Upload/banner/" . $pathname;
                        //返回ajax数据
                        $ajax = array(
                            'code' => 5,
                            'msg' => '上传成功！',
                            'classid' => $classid,
                            'pathimg' => $pathimg,
                            't' => $t,
                            'pathname' => $pathname);
                        $return_ajax = json_encode($ajax);
                        echo $return_ajax;
                    } else {
                        //错误信息
                        //返回ajax数据
                        $ajax = array('code' => 4, 'msg' => '上传失败！');
                        $return_ajax = json_encode($ajax);
                        echo $return_ajax;
                    }
                } else {
                    //错误信息
                    //返回ajax数据
                    $ajax = array('code' => 3, 'msg' => '上传失败,>10M太大了！');
                    $return_ajax = json_encode($ajax);
                    echo $return_ajax;
                }
            } 
            else 
            {
                //错误信息
                //返回ajax数据
                $ajax = array('code' => 2, 'msg' => '上传失败,只能上传图片/mp4！');
                $return_ajax = json_encode($ajax);
                echo $return_ajax;
            }
    
        } else {
            //错误信息
            //返回ajax数据
            $ajax = array('code' => 1, 'msg' => '上传失败,系统错误！');
            $return_ajax = json_encode($ajax);
            echo $return_ajax;
        }
    }
    

}