<?php

class WxPayApi
{
    //小程序appId
    private static  $appId;

    //微信支付商户号
    private static $mch_id;

    //支付秘钥
    private static $key;

    //ssl cert证书路径

    private static $ssl_cert;

    //ssl key证书路径
    private static $ssl_key;

    //日志路径
    const LOG_PATH="Public/wxpay";
    public function __construct($appId,$mch_id,$key,$ssl_cert=null,$ssl_key=null)
    {
        self::$appId=$appId;
        self::$mch_id=$mch_id;
        self::$key=$key;
        self::$ssl_cert=$ssl_cert;
        self::$ssl_key=$ssl_key;
    }

    /**
     *
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param array $inputObj
     * @param int $timeOut
     * @return 成功时返回，其他抛异常
     */
    public static function unifiedOrder($inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //检测必填参数
        if(!array_key_exists('out_trade_no',$inputObj)) {
            return array('status'=>'failed','message'=>"缺少统一支付接口必填参数out_trade_no！");
        }else if(!array_key_exists('body',$inputObj)){
            return array('status'=>'failed','message'=>"缺少统一支付接口必填参数body！");
        }else if(!array_key_exists('total_fee',$inputObj)) {
            return array('status'=>'failed','message'=>"缺少统一支付接口必填参数total_fee！");
        }else if(!array_key_exists('trade_type',$inputObj)) {
            return array('status'=>'failed','message'=>"缺少统一支付接口必填参数trade_type！");
        }else if(!$inputObj['notify_url']){
            return array('status'=>'failed','message'=>"缺少统一支付接口必填参数notify_url！");
        }

        //关联参数
        if($inputObj['trade_type'] == "JSAPI" && !isset($inputObj['openid'])){
            return array('status'=>'failed','message'=>"统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if($inputObj['trade_type'] == "NATIVE" && !isset($inputObj['product_id'])){
            return array('status'=>'failed','message'=>"统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        }
        $WxPayData=new WxPayData();
        $inputObj['appid']=self::$appId;//公众账号ID
        $inputObj['mch_id']=self::$mch_id;//商户号
        $inputObj['spbill_create_ip']=$_SERVER['REMOTE_ADDR'];//终端ip
        $inputObj['nonce_str']=$WxPayData->getNonceStr();//随机字符串

        //签名
        $inputObj['sign']=$WxPayData->MakeSign($inputObj,self::$key);
        $xml = $WxPayData->ToXml($inputObj);
		self::addLog($xml,"预支付请求数据");
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        self::addLog($response['data'],"预支付返回数据");
        if($response['status'] == 'failed'){
            return $response;
        }
        $result = self::Init($WxPayData,$response['data']);

        return $result;
    }

    /**
     *
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param array $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function orderQuery($inputObj, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        if(!array_key_exists('out_trade_no',$inputObj) && !array_key_exists('transaction_id',$inputObj)) {
            throw new WxPayException("订单查询接口中，out_trade_no、transaction_id至少填一个！");
        }
        $WxPayData=new WxPayData();
        $inputObj['appid']=self::$appId;//公众账号ID
        $inputObj['mch_id']=self::$mch_id;//商户号
        $inputObj['nonce_str']=$WxPayData->getNonceStr();//随机字符串

        $inputObj['sign']=$WxPayData->MakeSign($inputObj,self::$key);//签名
        $xml = $WxPayData->ToXml($inputObj);

        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        if($response['status'] == 'failed'){
            return $response;
        }
        $result = self::Init($WxPayData,$response['data']);

        return $result;
    }

    /**
     * 支付结果返回
     * @param WxPayData $dataObj
     * @param string $xml
     * @return array
     */
    public static  function Init($dataObj,$xml)
    {

        $arr=$dataObj->FromXml($xml);

        if($arr['return_code'] != 'SUCCESS' || $arr['result_code'] != 'SUCCESS'){
            return array('status'=>"failed",'message'=>$arr['return_msg']);
        }
        $checkSign=$dataObj->CheckSign($arr,self::$key);
        if($checkSign == true){
            return array('status'=>"success",'message'=>"",'data'=>$arr);
        }
        return array('status'=>"failed",'message'=>"签名错误！");
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @return string
     */
    private static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, self::$ssl_cert);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, self::$ssl_key);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return array('status'=>'success','data'=>$data);
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return array('status'=>'failed','message'=>"curl出错，错误码:$error");
        }
    }
    /**
     * 生成APP端支付参数
     * @param $prepay_id 预支付id
     */
    public static function  getAppPayParams($prepay_id){
        $WxPayData=new WxPayData();
        $data['appId'] = self::$appId;
        $data['package'] ="prepay_id=".$prepay_id;
        $data['nonceStr'] = $WxPayData->getNonceStr();
        $data['timeStamp'] = time();
        $data['signType'] = "MD5";
        $data['paySign'] = $WxPayData->MakeSign($data,self::$key);

        return $data;
    }

    /**
     * @param string $xml 记录日志内容
     * @param string $msg 消息内容
     */
    public static function addLog($xml,$msg){
        $path=self::LOG_PATH;
        $file=$path.'/'.date("Y-m-d").".log";
        if(!file_exists($path)){
            mkdir(iconv("UTF-8", "GBK", $path),0777,true);
        }
        $content ="\n $msg 时间 ".date("Y-m-d H:i:s")." \n".$xml;
        file_put_contents($file,$content,FILE_APPEND);
    }
    /**
     * 接收通知成功后应答输出XML数据
     * @param string $xml
     */
    public static function  replyNotify(){
        $WxPayData=new WxPayData();
        $data['return_code'] = 'SUCCESS';
        $data['return_msg'] = 'OK';
        $xml = $WxPayData->ToXml($data);
        echo $xml;
    }
}

class WxPayData{

    /**
     * 检测签名
     *@param $arr
     * @param $key
     * @return array
     */
    public function CheckSign($arr,$key)
    {
        //fix异常
        if(!$arr['sign']){
            return false;
        }
        $sign = $this->MakeSign($arr,$key);
        if($arr['sign'] == $sign){
            return true;
        }
        return false;
    }

    /**
     * 输出xml字符串
     * @param $arr
     * @return string
     */
    public function ToXml($arr)
    {
        if(!is_array($arr) || count($arr) <= 0)
        {
            return "数组数据异常！";
        }

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

    /**
     * 将xml转为array
     * @param string $xml
     * @return array
     */
    public function FromXml($xml)
    {
        if(!$xml){
            return "xml数据异常！";
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    /**
     * 格式化参数格式化成url参数
     * @param array $arr
     * @return string
     */
    public function ToUrlParams($arr)
    {
        $buff = "";
        if(!is_array($arr) || count($arr) <= 0)
        {
            return "数组数据异常！";
        }
        foreach ($arr as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 生成签名
     * @param array $arr
     * @param string $key
     * @return sign
     */
    public function MakeSign($arr,$key)
    {
        //签名步骤一：按字典序排序参数
        ksort($arr);
        $string = $this->ToUrlParams($arr);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public  function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

}