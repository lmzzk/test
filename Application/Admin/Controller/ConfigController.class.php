<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;

class ConfigController extends AuthController 
{

     //修改微信配置
   public function xg_weixin()
   {
       $wechat_pay = M('wechat_pay'); 
       $list = $wechat_pay->where("id=1")->find();
       $this->assign('conf',$list);
       $this->display();
   }
   
   //修改微信配置
   public function xg_weixin_do()
   {
      $appid     = I('appid');
      $appsecret = I('appsecret');
      $mchid     = I('mchid');
      $mchkey    = I('mchkey');  
      
      if($appid!='' && $appsecret!='' && $mchid!='' && $mchkey!='')
      {
          $wechat_pay = M('wechat_pay');
          $save['appid'] = $appid;
          $save['appsecret'] = $appsecret;
          $save['mchid']   = $mchid;
          $save['mchkey']  = $mchkey; 
          $save['addtime'] = time(); 
          $conf = $wechat_pay->where("id=1")->save($save);
          if($conf)
          {
             echo 1;
          }
          else
          {
            echo 0;
          }  
      }
      else
      {
         echo 2;
      }  
   }
  
     
  //基本配置
   public function xg_config()
   {
       $wechat_pay = M('config'); 
       $list = $wechat_pay->where("id=1")->find();
       $this->assign('conf',$list);
       $this->display();
   }
   
   //修改基本配置
   public function xg_config_do()
   {
          $config = M('config');
          $data   =I();
          $data['addtime']=date("Y-m-d H:i:s");
          $conf = $config->where("id=1")->save($data);
          if($conf)
          {
             echo 1;
          }
          else
          {
            echo 0;
          }  

   }
   
 /***************************************************************************************************************************************************************************/
      //图片
    public function upload_img()
    {
            if (isset($_FILES['file']) && $_FILES['file']["name"] != '') 
            {
                $dar = "./Upload/logo/"; //上传文件保存的路径
                if (!is_dir($dar)) 
                { //目录不存在
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
                $pathname = date('YmdHis'). '.' . $name[1]; //上传文件保存的文件名
                $type = $_FILES['file']["type"]; //上传文件的类型
                $size = $_FILES['file']["size"]; //上传文件的大小
                //$path= "./Upload/company/$id/";   //上传文件保存的路径
                $allowtype = array(
                    'image/jpeg',
                    'image/gif',
                    'image/png',
                    'image/bmp',
                    'image/pjpeg'); //设置限制上传文件的类型
                $maxsize = 20971520; //限制文件上传大小 3M（字节）
                if (in_array($type, $allowtype)) {
                    if ($size <= $maxsize) {
                        $up_ok = move_uploaded_file($_FILES['file']["tmp_name"], $dar . $pathname);
                        if ($up_ok) {
                            //成功
                            $pathimg = __ROOT__ . "/Upload/logo/" . $pathname;
                            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                            $pathname = $protocol.$_SERVER[HTTP_HOST]."/Upload/logo/" . $pathname;
                            //返回ajax数据
                            $ajax = array(
                                'code' => 5,
                                'msg' => '上传成功！',
                                'pathimg' => $pathimg,
                                'pathname' => $pathname);
                            $return_ajax = json_encode($ajax);
                            echo $return_ajax;
                        } else {
                            //错误信息
                            //返回ajax数据
                            $ajax = array('code' => 4, 'msg' => '图片上传失败！');
                            $return_ajax = json_encode($ajax);
                            echo $return_ajax;
                        }
                    } else {
                        //错误信息
                        //返回ajax数据
                        $ajax = array('code' => 3, 'msg' => '图片上传失败,图片太大了！');
                        $return_ajax = json_encode($ajax);
                        echo $return_ajax;
                    }
                } else {
                    //错误信息
                    //返回ajax数据
                    $ajax = array('code' => 2, 'msg' => '图片上传失败,只能上传图片！');
                    $return_ajax = json_encode($ajax);
                    echo $return_ajax;
                }
            } 
            else 
            {
                //错误信息
                //返回ajax数据
                $ajax = array('code' => 1, 'msg' => '图片上传失败,系统错误！');
                $return_ajax = json_encode($ajax);
                echo $return_ajax;
            }
    }
    
    
    
}