<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1
 * Time: 14:21
 */
/*
 * 文件上传方法
 * @param $file 文件对象
 */
defined('AppKey') or define('AppKey','JQeJEAQu8277');
defined('Customer') or define('Customer','7308BEB36B2811CCC5436142199F33DB');


function ismobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    //此条摘自TPM智能切换模板引擎，适合TPM开发
    if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])
        return true;
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
        //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
        );
        //从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}


//模拟请求
function https_request($type="post",$url,$data=null)
{
    //1、初始化curl
    $ch = curl_init();

    //2、设置传输选项
    curl_setopt($ch, CURLOPT_URL, $url);//请求的url地址
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//将请求的结果以文件流的形式返回

    // 判断请求发送的类型
    if($type == "post"){

        if(!empty($data)){
            curl_setopt($ch,CURLOPT_POST,1);//请求POST方式
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//POST提交的内容
        }

    }
    // 跳过SSL验证，本代码用来解决不支持SSL验证的问题（暂时使用，不推荐）
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //3、执行请求并处理结果
    $outopt = curl_exec($ch);

    //把json数据转化成数组
    $outoptArr = json_decode($outopt,TRUE);

    //4、关闭curl
    curl_close($ch);

    //如果返回的结果$outopt是json数据，则需要判断一下
    if(is_array($outoptArr)){
        return $outoptArr;
    }else{
        return $outopt;
    }
}


function uploadImg($file,$allowExts,$path,$filename){
    $exts=explode('.',$file['name']);
    if(!in_array($exts[count($exts)-1],$allowExts)){
        return array('status'=>400,'msg'=>'文件格式错误');
    }
    if($file['size'] > 1024*1024*5){
        return array('status'=>400,'msg'=>'文件大小超过5M');
    }
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize   =     5242880;// 设置附件上传大小
    $upload->exts      =     $allowExts;// 设置附件上传类型
    $upload->subName = false;
    $upload->saveName =$filename;
    $upload->rootPath  =      './'; // 设置附件上传目录
    $upload->savePath  =      $path; // 设置附件上传（子）目录
    $upload->saveExt='png';
    $info   =   $upload->uploadOne($file);
    if(!$info) {
        // 上传错误提示错误信息
        return array('status'=>400,'msg'=>$upload->getError());
    }else{

        // 上传成功 获取上传文件信息
        $url=$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'];

        $path=$url.__ROOT__.$info['savepath'].$info['savename'];

        //压缩图片
        $path=resize_image($path,$info['savepath'],400,400,$info['savename']);

        return array('status'=>200,'msg'=>'上传成功','path'=>$url.__ROOT__.$path,'filename'=>$info['savename']);

    }
}

/**
 * 按照指定的尺寸压缩图片
 * @param $source_path  原图路径
 * @param $target_path  保存路径
 * @param $imgWidth     目标宽度
 * @param $imgHeight    目标高度
 * @return bool|string
 */
function resize_image($source_path,$target_path,$imgWidth,$imgHeight,$savename)
{
    $source_info = getimagesize($source_path);
    $source_mime = $source_info['mime'];
    switch ($source_mime)
    {
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;

        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;

        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;

        default:
            return false;
            break;
    }
    $target_image     = imagecreatetruecolor($imgWidth, $imgHeight); //创建一个彩色的底图
    imagecopyresampled($target_image, $source_image, 0, 0, 0, 0, $imgWidth, $imgHeight, $source_info[0], $source_info[1]);
    //保存图片到本地
    $dir =$target_path. date("Ymd") . '/';
    if (!is_dir($dir)) {
        mkdir("./".$dir, 0777);
    }

    $fileName = $dir.'original'.$savename;
    if(!imagejpeg($target_image,'./'.$fileName)){
        $fileName = '';
    }
    imagedestroy($target_image);
    return $fileName;
}


//生成唯一订单号  手机传入m 电脑传入c
function make_code($type){
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn = $type.$yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $orderSn;
}


//快递100
function kuaidi($com,$num){

    //参数设置
    $post_data = array();
    $post_data["customer"] =Customer;
    $key=AppKey;
    $post_data["param"] =array(
        'com'=>$com,
        'num'=>$num
    );

    $post_data['param']=json_encode($post_data['param']);

    $url='http://poll.kuaidi100.com/poll/query.do';
    $post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
    $post_data["sign"] = strtoupper($post_data["sign"]);
    $o="";
    foreach ($post_data as $k=>$v)
    {
        $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
    }
    $post_data=substr($o,0,-1);

    $data= https_request('post',$url,$post_data);

    return $data;

}

//比价报告jxkj_agency_company_goods1表id
function pdf($id){
    //引入类库
    Vendor('mpdf.mpdf');
    //设置中文编码
    $mpdf=new \mPDF('zh-cn','A4', 0, '宋体', 0, 0);
    //html内容
    $url='https://gwc.juxiangwx.cn/phone.php/Table/Index?id='.$id;

    $html= file_get_contents($url);
    $mpdf->WriteHTML($html);
    $time=time();
    $file_name="Public/report_pdf/".$time.'.pdf';
    $mpdf->Output($file_name);
    if(file_exists($file_name)){
        $file_names="Public/report_png/".$time.'.png';
        $url=$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].'/';
        $img_name= pdf2png($file_name,$file_names);
        $img_name=$url.__ROOT__.$img_name;
        return $img_name;
    }else{
        return false;
    }
}



function pdf2png($PDF, $PNG, $w=256, $h=256){


    if(!extension_loaded('imagick')){
        return false;
    }
    if(!file_exists($PDF)){
        return false;
    }
    $im = new \Imagick();


    $im->setResolution($w,$h); //设置分辨率
    //$im->setCompressionQuality(15);//设置图片压缩的质量

    $im->readImage($PDF);
    $im -> resetIterator();
    $imgs = $im->appendImages(true);
    $imgs->setImageFormat( "png" );
    $img_name = $PNG;
    $imgs->writeImage($img_name);
    $imgs->clear();
    $imgs->destroy();
    $im->clear();
    $im->destroy();

    return $img_name;
}

/**
 * 添加足迹
 */
function add_footstep($uid,$types,$all_id){
    $arr=array();
    $arr['uid']=$uid;
    $arr['types']=$types;
    $arr['all_id']=$all_id;
    $arr['addtime']=date('Y-m-d H:i:s');
    $my_footprint_count=M('my_footprint')->where("uid={$uid}")->count();
    if($my_footprint_count>=30){
        $id=M('my_footprint')->where("uid={$uid}")->order('addtime')->getField('id');
        M('my_footprint')->where("id={$id}")->delete();
    }
    $res=M('my_footprint')->add($arr);
    if(!$res){
        addLog("Public/my_footprint", json_encode($arr, JSON_UNESCAPED_UNICODE), "添加足迹失败");
    }
}

function subtext($text, $length)
{
    if(mb_strlen($text, 'utf8') > $length)

        return mb_substr($text,0,$length,'utf8');

    return $text;

}


function addLog($path, $xml, $msg)
{
    $file = $path . '/' . date("Y-m-d") . ".log";
    if (!file_exists($path)) {
        mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
    }
    $content = "\n $msg 时间 " . date("Y-m-d H:i:s") . " \n" . urldecode($xml. " \n");
    file_put_contents($file, $content, FILE_APPEND);
}


class WinXinRefund{
    protected $SSLCERT_PATH= 'Public/Payment/apiclient_cert.pem';//证书路径
    protected $SSLKEY_PATH= 'Public/Payment/apiclient_key.pem';//证书路径
    protected $APPID;//填写您的appid。微信公众平台里的

    protected $MCHID;//商户id
    protected $KEY;

    function __construct($outTradeNo,$totalFee,$outRefundNo,$refundFee,$appid,$mchid,$key){
        //初始化退款类需要的变量
        $this->outTradeNo= $outTradeNo;//商户订单号
        $this->totalFee= $totalFee;//订单金额
        $this->outRefundNo= $outRefundNo;//商户退款单号
        $this->refundFee= $refundFee;//退款金额
        $this->APPID= $appid;//appid
        $this->MCHID= $mchid;//商户号
        $this->KEY= $key;

    }

    public function refund(){
        //对外暴露的退款接口
        $result = $this->wxrefundapi();
        return $result;
    }

    private function wxrefundapi(){
        //通过微信api进行退款流程
        $parma = array(
            'appid'=> $this->APPID,
            'mch_id'=> $this->MCHID,
            'nonce_str'=> $this->createNoncestr(),
            'out_refund_no'=>$this->outRefundNo,
            'out_trade_no'=> $this->outTradeNo,
            'total_fee'=> $this->totalFee,
            'refund_fee'=> $this->refundFee,
            'op_user_id' => $this->MCHID,
        );
        $parma['sign'] = $this->getSign($parma);
        $xmldata = $this->arrayToXml($parma);
        $xmlresult = $this->postXmlSSLCurl($xmldata,'https://api.mch.weixin.qq.com/secapi/pay/refund');
        $result = $this->xmlToArray($xmlresult);
        return $result;
    }

    protected function xmlToArray($xml){
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    //数组转字符串方法
    protected function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    /*
 * 对要发送到微信统一下单接口的数据进行签名
 */
    protected function getSign($Obj){
        foreach ($Obj as $k => $v){
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$this->KEY;
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        return $result_;
    }

    /*
 *排序并格式化参数方法，签名时需要使用
 */
    protected function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
                $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }

        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /*
 * 生成随机字符串方法
 */
    protected function createNoncestr($length = 32 ){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ ) {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }


//需要使用证书的请求
    protected  function postXmlSSLCurl($xml,$url,$second=30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT, $this->SSLCERT_PATH);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, $this->SSLKEY_PATH);
        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            curl_close($ch);
            return false;
        }
    }
}


/*
 * 1卖
 * 2租
 * 3租卖都有
 * 0错误
 * */
function rent_sell($id){

   $sell=M('company_goods_rental')->where("gid={$id} and isdel=0 and types=1")->find();

   $rent=M('company_goods_rental')->where("gid={$id} and isdel=0 and types=2")->find();

   if(!empty($sell)&&empty($rent)){

       return 1;
   }
    if(!empty($rent)&&empty($sell)){

        return 2;
    }
    if(!empty($rent)&&!empty($sell)){

        return 3;
    }
    if(empty($rent)&&empty($sell)){

        return 0;
    }

}




