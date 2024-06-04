<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
 header("Content-Type: text/html;charset=utf-8");
class DaikController extends AuthController
{
	
	
	//菜单列表
	public function dk_list()
	{
		

		$search_title   = I('search_title');//文章名称
		$article = M('dk_list');
		//查询
		if (IS_GET) {
			
			if ($search_title != '')
			{
				$where['name'] = array("LIKE", '%' .$search_title . '%');
				
				$this->assign('search_title', $search_title);
			}
			
		}
		$where['is_del'] =0;
		$count = $article->where($where)->count();
		
		// 查询满足要求的总记录数
		$page = $this->showPage($count,10); // 分页显示输出
		//赋值数据集
		$this->assign('page', $page->show()); //赋值分页输出
		$list = $article->where($where)->order("sort desc")->limit($page->firstRow . ',' . $page->listRows)->select();
		
		foreach ($list as $key=>$value){
			$list[$key]['f_name']=M('dk_type')->where(array('id'=>$value['cate_id']))->getField("name");
		}
		
		$this->assign('list', $list);
		$this->display();
	}
	
	//添加
	public function dk_add()
	{

		$menu = M('dk_type');
		$where['is_show']=1;
		$where['is_del']=0;
		
		$menu_list = $menu->where($where)->order("sort desc")->select();
		$this->assign('menu_list', $menu_list);
		
		$this->display();
		
	}
	//添加方法
	public function dk_add_do()
	{
		$data = I('');
		$data['is_del']=0;
		$data['addtime']=date('Y-m-d H:i:s',time());
		$article=M("dk_list");
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
	public function dk_alter()
	{
		
		$id = intval(I('id'));
		if($id)
		{
			
			$article  = M('dk_list');
			$list  = $article->where(array('is_del'=>0,'id'=>$id))->find();
			$this->assign('info',$list);
			
			$menu = M('dk_type');
			$where['is_show']=1;
			$where['is_del']=0;
			
			$menu_list = $menu->where($where)->order("sort desc")->select();
			$this->assign('menu_list', $menu_list);
			
			$this->display();
		} else {
			$this->redirect('dk/type_list');
			exit();
		}
	}
	//修改方法
	public function dk_alter_do()
	{
		
		
		$data = I('');

		$banner  = M('dk_list');
		$data['addtime']=date("Y-m-d H:i:s",time());
		
		$isok =  $banner->where(array('is_del'=>0,'id'=>$data['id']))->save($data);
		if($isok)
		{
			
			//系统日志
			$msg = "修改类别id《{$data['id']}》的信息";
			echo 1;
		}
		else
		{
			echo 0;
		}
		
	}
	
	//显示或者隐藏
	public function dk_show()
	{
		//商品ID
		$id = intval(I('id'));
		$t  = intval(I('t'));
		if($id && IS_POST)
		{
			//查找企业是否关联任务
			$banner = M('dk_list');
			$info = $banner->where(array('is_del' => 0, 'id' => $id))->find();
			$res  = $banner->where(array('is_del' => 0, 'id' => $id))->save(array('is_show' =>$t));
			if ($res == 1 || $res == 0)
			{
				//系统日志
				if($t==1){
					$a="显示";
				}else{
					$a='隐藏';
				}

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
	public function dk_del()
	{
		//商品ID
		$id = intval(I('id'));
		if($id && IS_POST)
		{
			
			$banner = M('dk_list');
			$info = $banner->where(array('is_del' => 0, 'id' => $id))->find();
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
	
	
	
	
	//菜单列表
    public function type_list()
    {
	

    	
        $search_title   = I('search_title');//文章名称
        $article = M('dk_type');
        //查询
        if (IS_GET) {

            if ($search_title != '')
            {
	            $where['name'] = array("LIKE", '%' .$search_title . '%');

                $this->assign('search_title', $search_title);
            }
            
        }
        $where['is_del'] =0;
        $count = $article->where($where)->count();

        // 查询满足要求的总记录数
        $page = $this->showPage($count,10); // 分页显示输出
        //赋值数据集
        $this->assign('page', $page->show()); //赋值分页输出
        $list = $article->where($where)->order("sort desc")->limit($page->firstRow . ',' . $page->listRows)->select();
        
        
        $this->assign('list', $list);
        $this->display();
    }

   //添加
    public function type_add()
    {
	

        $this->display();
       
    }
    //添加方法
    public function type_add_do()
    {
        $data = I('');
        $data['is_del']=0;
        $data['is_del']=0;
        $data['addtime']=date('Y-m-d H:i:s',time());
        $article=M("dk_type");
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
    public function type_alter()
    {

        $id = intval(I('id'));
        if($id) 
        {

            $article  = M('dk_type');
            $list  = $article->where(array('is_del'=>0,'id'=>$id))->find();
            $this->assign('info',$list);
            
            $this->display();
        } else {
            $this->redirect('dk/type_list');
            exit();
        }
    }
        //修改方法
    public function type_alter_do()
    {
    	
    	
        $data = I('');
        $banner  = M('dk_type');
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
    public function type_show()
    {
        //商品ID
        $id = intval(I('id'));
        $t  = intval(I('t'));
        if($id && IS_POST) 
        {
                //查找企业是否关联任务
                $banner = M('dk_type');
	            $info = $banner->where(array('is_del' => 0, 'id' => $id))->find();
                $res  = $banner->where(array('is_del' => 0, 'id' => $id))->save(array('is_show' =>$t));
                if ($res == 1 || $res == 0) 
                {
	                //系统日志
	                if($t==1){
	                	$a="显示";
	                }else{
	                	$a='隐藏';
	                }

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
    public function type_del()
    {
        //商品ID
        $id = intval(I('id'));
        if($id && IS_POST) 
        {
                
                $banner = M('dk_type');
	            $info = $banner->where(array('is_del' => 0, 'id' => $id))->find();
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
    

}