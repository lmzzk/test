<?php
class Compress
{
    private $src;
    private $image;
    private $imageinfo;
    private $percent=0.5;
    
    /*
    param    $src源图
    param    $percent压缩比例
    */
    public function __construct($src,$percent=1)
    {
        $this->src = $src;
        $this->percent = $percent;
    }
    
    
    
    /*
    param string $saveName 图片名（可不带扩展名用原图名）用于保存。或不提供文件名直接显示
    */
    public function compressImg($saveName='',$url)
    {
        // 临时设置最大内存占用为3G
        ini_set('memory_limit','3072M');    
        // 设置脚本最大执行时间 为0 永不过期
        set_time_limit(0);  
        $this->_openImage();
        if(!empty($saveName))
        {
          //保存
          return  $this->_saveImage($saveName,$url);
        }
        else
        {
            return 0;
            //$this->_showImage();
        }
    }
    
    
    
    
    /*
    内部：打开图片
    */
    private function _openImage()
    {
        list($width, $height, $type, $attr) = getimagesize($this->src);
        $this->imageinfo = array(
            'width'=>$width,
            'height'=>$height,
            'type'=>image_type_to_extension($type,false),
            'attr'=>$attr
          );
        $fun = "imagecreatefrom".$this->imageinfo['type'];
        $this->image = $fun($this->src);
        $this->_thumpImage();
    }
    
    
    
    
    
    /**
    * 内部：操作图片
    */
    private function _thumpImage()
    {
        $new_width = $this->imageinfo['width'] * $this->percent;
        $new_height = $this->imageinfo['height'] * $this->percent;
        $image_thump = imagecreatetruecolor($new_width,$new_height);
        //将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump,$this->image,0,0,0,0,$new_width,$new_height,$this->imageinfo['width'],$this->imageinfo['height']);
        imagedestroy($this->image);
        $this->image = $image_thump;
    }
    
    
    
    
    
    /**
    * 输出图片:保存图片则用saveImage()
    */
    private function _showImage()
    {
        header('Content-Type: image/'.$this->imageinfo['type']);
        $funcs = "image".$this->imageinfo['type'];
        $funcs($this->image);
    }
    
    
    
    
    
    /**
    * 保存图片到硬盘：
    * @param  string $dstImgName  1、可指定字符串不带后缀的名称，使用源图扩展名 。2、直接指定目标图片名带扩展名。
    */
    private function _saveImage($dstImgName,$url)
    {
        if(empty($dstImgName)) 
        {
             return 0;
        }
        //如果目标图片名有后缀就用目标图片扩展名 后缀，如果没有，则用源图的扩展名
        $allowImgs = array('.jpg', '.jpeg', '.png', '.bmp', '.wbmp','.gif');   
        $dstExt =  strrchr($dstImgName ,".");
        $sourseExt = strrchr($this->src ,".");
        if(!empty($dstExt)) $dstExt =strtolower($dstExt);
        if(!empty($sourseExt)) $sourseExt =strtolower($sourseExt);
     
        //有指定目标名扩展名
        if(!empty($dstExt) && in_array($dstExt,$allowImgs))
        {
            $dstName = $dstImgName;
        }
        elseif(!empty($sourseExt) && in_array($sourseExt,$allowImgs))
        {
            $dstName = $dstImgName.$sourseExt;
        }
        else
        {
            $dstName = $dstImgName.$this->imageinfo['type'];
        }
        $funcs = "image".$this->imageinfo['type'];
        //保存
        if(!is_dir($url))
        {
          if(mkdir($url,0777,true))
          {
             if($funcs($this->image,$url.$dstName))
             {
                 return 1;
             }
             else
                 return 0;
          }
          else
          {
            //文件夹创建失败
            return 2;
          }
        }
        else
        {
             if($funcs($this->image,$url.$dstName))
             {
                 return 1;
             }
             else
                 return 0;
        }
        
        
        
    }
 
 
 
 
 
    /**
    * 销毁图片
    */
    public function __destruct()
    {
       imagedestroy($this->image);
    }
    
    
    

    
    /*
            // 临时设置最大内存占用为3G
        ini_set('memory_limit','3072M');    
        // 设置脚本最大执行时间 为0 永不过期
        set_time_limit(0);  
        //原图地址
        $source = 'http://122.114.96.212:3080/M00/00/03/wKhg1FtxMJuAGZhsAANhMPgn7_M341.jpg';
        //获取图片大小
        $header_array = get_headers($source, true);
        $size = $header_array['Content-Length'];

        //保存名称
        $dst_img = time(); 
        //保存路径
        $url = './yasuo_image/q/';
        #根据大小调整压缩比例
        $percent = 0.5; 
        if($size >='5000000')
        {
             $percent = 0.1; 
        } 
        else if($size >= '3000000' && $size < '5000000' )
        {
             $percent = 0.2; 
        }
        else if($size >= '1000000' && $size < '3000000' )
        {
             $percent = 0.3; 
        } 
        else
        {
             $percent = 0.4; 
        } 
         

        $image = new \Compress($source,$percent);
        $image->compressImg($dst_img,$url);
    
    
    exit();
    
    
    */
    
    
    
    
    
    
    
}

