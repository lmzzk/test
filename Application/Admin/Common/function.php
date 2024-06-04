<?php
/**
 * author: 后台公共方法
 * Date: 2018/3/29
 * Time: 19:11
 */

/*
 * 文件上传方法
 * @param $file 文件对象
 */
function uploadFile($file,$allowExts,$path){
    $exts=explode('.',$file['name']);
    if(!in_array($exts[count($exts)-1],$allowExts)){
        return array('status'=>400,'msg'=>'文件格式错误');
    }
    if($file['size'] > 1024*1024*2){
        return array('status'=>400,'msg'=>'文件大小超过2M');
    }
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize   =     2097152;// 设置附件上传大小
    $upload->exts      =     $allowExts;// 设置附件上传类型
    $upload->subName = array('date','Ymd');
    $upload->saveName = 'time';
    $upload->rootPath  =      './'; // 设置附件上传目录
    $upload->savePath  =      $path; // 设置附件上传（子）目录
    $info   =   $upload->uploadOne($file);
    if(!$info) {
        // 上传错误提示错误信息
        return array('status'=>400,'msg'=>$upload->getError());
    }else{
        // 上传成功 获取上传文件信息
        $url=$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'];
        return array('status'=>200,'msg'=>'上传成功','path'=>$url.__ROOT__.$info['savepath'].$info['savename'],'filename'=>$info['savename']);
    }
}
/*
 * 删除文件
 * @param $url 文件路径
 */
function deleteFile($url){
    $url=iconv('utf-8','gbk',".$url");
    $res=@unlink($url);
    if($res==true){
        return true;
    }
    return false;
}

/**
 * 随机字符串生成
 * @param 字符串长度 int $pw_length
 * @return string
 */
function create_str($pw_length =8)
{
    // 密码字符集，可任意添加你需要的字符
    $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    // 在 $chars 中随机取 $length 个数组元素键名
    $keys = array_rand($chars,$pw_length);
    $password = '';
    for($i = 0; $i < $pw_length; $i++)
    {
        // 将 $length 个数组元素连接成字符串
        $password .= $chars[$keys[$i]];
    }
    return $password;
}









    //获取access_token
    function access_token1($appid, $appsecret, $access_token_time, $access_token)
    {
        $data = array();
        //判断access_token有效期)
        if ((time() - $access_token_time) >= 7200) {
            //接口地址http请求方式: GET
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
            $retdate = https_request1('get', $url);

            //判断获取access_token
            if ($retdate['access_token'] != '' && $retdate['expires_in'] > 0) {

                //修改数据库
                $conf = M('wechat_conf');
                $data['access_token'] = $retdate['access_token'];
                $data['access_token_time'] = time();
                $i = $conf->where("id=1")->save($data);
                if ($i) {
                    return $data['access_token'];

                } else {
                    exit('数据库写入access_token失败！');
                }
            } else {
                exit('获取access_token失败');
            }
        } else {

            return $access_token;
        }

    }


    //模拟请求
    function https_request1($type = "post", $url, $data = null)
    {
        //1、初始化curl
        $ch = curl_init();

        //2、设置传输选项
        curl_setopt($ch, CURLOPT_URL, $url); //请求的url地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //将请求的结果以文件流的形式返回

        // 判断请求发送的类型
        if ($type == "post") {

            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POST, 1); //请求POST方式
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //POST提交的内容
            }

        }
        // 跳过SSL验证，本代码用来解决不支持SSL验证的问题（暂时使用，不推荐）
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        //3、执行请求并处理结果
        $outopt = curl_exec($ch);

        //把json数据转化成数组
        $outoptArr = json_decode($outopt, true);

        //4、关闭curl
        curl_close($ch);

        //如果返回的结果$outopt是json数据，则需要判断一下
        if (is_array($outoptArr)) {
            return $outoptArr;
        } else {
            return $outopt;
        }
    }

    
    //获取毫秒
    function getMillisecond1()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }


 