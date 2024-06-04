<?php
/**
 * @var
 *  修改文件
 */
namespace Home\Controller;

use Think\Controller;

class ApiController extends Controller
{

    public function addImg()
    {


//         "http://tool.cc/index.php/home/api/addimg";
        $this->display();

    }

    public function addImgDo()
    {

        $img_url = './Img/'.$_POST['img'].'/';
        $img_urls = '/Img/'.$_POST['img'].'/';
        $files_img = scandir( $img_url );
        $img = [];
        foreach ($files_img as $key => $file) {
            if ($file != "." && $file != "..") { // 排除当前目录和上级目录的引用
                $img[$key]['img'] = $img_urls . $file;
            }
        }

        addLog("Log/Img", json_encode($img,  JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), "图片数据");
        
        $lj = './Files/'.$_POST['img'].'/';
        $files = scandir( $lj ); // 使用'.'来获取当前目录下的文件和子目录

        $data = [];
        $img_count = count($img);
        foreach ($files as $key => $file) {
//            if ($file != "." && $file != ".."  && $key <=4) { // 排除当前目录和上级目录的引用
            if ($file != "." && $file != "..") { // 排除当前目录和上级目录的引用

                $index = $key % $img_count;
                $title = pathinfo( $file, PATHINFO_FILENAME );
                $data[$key]['img'] = $img[$index]['img'];
                $content = file_get_contents( $lj . $file );
                $content = "<img src='" . $data[$key]['img'] . "' alt='" . $title . "'>" . $content;
                $data[$key]['title']=$title;
                $data[$key]['id']=$key;

                $filePath = $lj.$title.".txt"; // 文件路径
                // 打开文件进行读取和写入
                $edit_file = fopen($filePath, 'r+') or die('无法打开文件');
                // 读取文件内容
                $contentC = fread($edit_file, filesize($filePath));
                // 编辑内容
                $editedContent = $content;
                // 将新内容从开头开始写入，这将覆盖现有内容
                ftruncate($edit_file, 0); // 将文件截断到给定长度
                rewind($edit_file); // 将文件指针移动到开头
                fwrite($edit_file, $editedContent); // 写入新内容
                // 关闭文件
                fclose($edit_file);
//                echo "文件已保存".$key ."<br>";
                $data[$key]['id']=$key-1;

            }
        }


        $msg['status'] = 1;

        if(count($data)){
            $msg['data']   = $data;
            $msg['msg']    = '执行成功';
        }else{
            $msg['status'] = 0;
            $msg['msg']    = '请求异常';
        }

        addLog("Log/Files", json_encode($msg,  JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT), "文件数据");

        return  $this->ajaxReturn($msg);

    }

}