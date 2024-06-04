<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
 header("Content-Type: text/html;charset=utf-8");
class ArticleController extends AuthController
{
   
    //菜单列表
    public function article_list()
    {
    	
        $search_title   = I('search_title');//文章名称
        $cate_id   = I('cate_id');//文章分类id
        $start_time = I('start_time');//日期区间
        $article = M('article');
        //查询
        if (IS_GET) {

            if ($search_title != '')
            {
	            $where['title'] = array("LIKE", '%' .$search_title . '%');

                $this->assign('search_title', $search_title);
            }

            if ($cate_id != '')
            {

                $where['cate_id'] = $cate_id;
                $this->assign('cate_id', $cate_id);

            }

            if($start_time != ''){
                $s_time = substr($start_time,0,19);
                $end_time = mb_substr($start_time,-19,19);
                $where['addtime'] = array('between',array("$s_time","$end_time"));
                $this->assign('start_time', $start_time);
            }





        }
        $where['is_del'] =0;
        $count = $article->where($where)->count();

        // 查询满足要求的总记录数
        $page = $this->showPage($count,10); // 分页显示输出
        //赋值数据集
        $this->assign('page', $page->show()); //赋值分页输出
        $list = $article->where($where)->order("addtime desc")->limit($page->firstRow . ',' . $page->listRows)->select();

        foreach ($list as $key=>$value){
            $list[$key]['f_name']=M('menu')->where(array('id'=>$value['cate_id']))->getField("name");
        }

        //菜单
        $menu = M('menu');
        $where['is_show']=1;
        $where['is_del'] =0;
        $where['is_type']=1;
        $menu_list = $menu->where($where)->order("sort desc")->select();
        $this->assign('menu_list', $menu_list);

        $this->assign('list', $list);
        $this->display();
    }

   //添加
    public function article_add()
    {

        $menu = M('menu');
        $where['is_show']=1;
        $where['is_del']=0;
        $where['is_type']=1;

        $menu_list = $menu->where($where)->order("sort desc")->select();
        $this->assign('menu_list', $menu_list);

        $this->display();
       
    }
    //添加方法
    public function article_add_do()
    {
        $data = I('');
        $data['is_del']=0;
        $article=M("article");
        $isok =  $article->add($data);
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
    public function article_alter()
    {
        $id = intval(I('id'));
        if($id) 
        {

            $article  = M('article');
            $list  = $article->where(array('is_del'=>0,'id'=>$id))->find();
            $this->assign('info',$list);

            $menu = M('menu');
            $where['is_show']=1;
            $where['is_del']=0;
            $where['is_type']=1;

            $menu_list = $menu->where($where)->order("sort desc")->select();
            $this->assign('menu_list', $menu_list);

            $this->display();
        } else {
            $this->redirect('article/article_list');
            exit();
        }
    }
        //修改方法
    public function article_alter_do()
    { 
        $data = I('');
        $banner  = M('article');
	    $data['name']=$data['title'];
        $data['updatetime']=date("Y-m-d H:i:s",time());
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
    public function article_types()
    {
        //商品ID
        $id = intval(I('id'));
        $t  = intval(I('t'));
        if($id && IS_POST) 
        {
                //查找企业是否关联任务
                $banner = M('article');
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
    public function article_del()
    {
        //商品ID
        $id = intval(I('id'));
        if($id && IS_POST) 
        {
                //查找企业是否关联任务
                $banner = M('article');
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