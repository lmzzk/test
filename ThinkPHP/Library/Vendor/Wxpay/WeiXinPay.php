<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/16 0016
 * Time: 下午 6:11
 */
//微信支付类
class WeiXinPay{

    //=======【基本信息设置】=====================================
    //微信公众号身份的唯一标识填写您的appid。微信公众平台里的
    protected $APPID = "";
    protected $APPSECRET = "";
    //受理商ID，身份标识 商户id
    protected $MCHID = '';
    //商户支付密钥Key
    protected $KEY = '';
    //回调通知接口
    protected $APPURL ='';
    //交易类型
    protected $TRADETYPE = 'JSAPI';
    //商品描述信息
    protected $BODY = '';
    //用户唯一标识
    private $openid='';
    //商品编号
    private $outTradeNo='';
    //总价分
    private $totalFee=0;
    //附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用。
    private $attach='';
    //微信支付类的构造函数
    function __construct($openid,$outTradeNo,$totalFee,$APPID,$APPSECRET,$MCHID,$KEY,$APPURL,$BODY,$attach)
    {
        $this->openid    = $openid;
        $this->outTradeNo= $outTradeNo; 
        $this->totalFee  = $totalFee; 
        $this->APPID     = $APPID;
        $this->APPSECRET = $APPSECRET;
        $this->MCHID     = $MCHID;
        $this->KEY       = $KEY;
        $this->APPURL    = $APPURL;
        $this->BODY      = $BODY;
        $this->attach    = $attach;
    }

    //微信支付类向外暴露的支付接口
    public function pay(){
        $result = $this->weixinapp();
        return $result;
    }

    //对微信统一下单接口返回的支付相关数据进行处理
    private function weixinapp(){
        $unifiedorder=$this->unifiedorder();

        $parameters=array(
            'appId'=>$this->APPID,//小程序ID
            'timeStamp'=>''.time().'',//时间戳
            'nonceStr'=>$this->createNoncestr(),//随机串
            'package'=>'prepay_id='.$unifiedorder['prepay_id'],//数据包
            'signType'=>'MD5'//签名方式
        );
        $parameters['paySign']=$this->getSign($parameters);

        return $parameters;
    }

    /*
     *请求微信统一下单接口
     */
    private function unifiedorder(){
        $parameters = array(
            'appid' => $this->APPID,//小程序id
            'mch_id'=> $this->MCHID,//商户id
            'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],//终端ip
            'notify_url'=>$this->APPURL, //通知地址
            'nonce_str'=> $this->createNoncestr(),//随机字符串
            'out_trade_no'=>$this->outTradeNo,//商户订单编号
            'total_fee'=>intval($this->totalFee), //总金额
            'openid'=>$this->openid,//用户openid
            'trade_type'=>$this->TRADETYPE,//交易类型
            'body' =>$this->BODY, //商品信息
            'attach' =>$this->attach //附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用
        );
        //dump($parameters);exit();
        $parameters['sign'] = $this->getSign($parameters);
        $xmlData = $this->arrayToXml($parameters);
        $xml_result = $this->postXmlCurl($xmlData,'https://api.mch.weixin.qq.com/pay/unifiedorder',60);
        $result = $this->xmlToArray($xml_result);
        //dump($result);exit();
        return $result;
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

    protected function xmlToArray($xml){
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    //发送xml请求方法
    private static function postXmlCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        set_time_limit(0);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {

            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            curl_close($ch);
            return false;

        }
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
}

/************************************************************************************************************************************************************************************/
//微信退款类
class WinXinRefund{
    //微信公众号身份的唯一标识填写您的appid。微信公众平台里的
    protected $APPID = "";
    //商户号
    protected $MCHID = '';
    //商户支付密钥Key
    protected $KEY = '';
    //证书路径
    protected $SSLCERT_PATH = '';
    //证书路径
    protected $SSLKEY_PATH  = '';
    //商户订单号
    protected $transaction_id   = '';
    //订单金额
    protected $totalFee     = '';
    //商户退款单号
    protected $outRefundNo  = '';
    //退款金额
    protected $refundFee    = '';
    function __construct($transaction_id,$outTradeNo,$totalFee,$refundFee,$SSLCERT_PATH,$SSLKEY_PATH,$APPID,$MCHID,$KEY)
    {
        //初始化退款类需要的变量
        $this->transaction_id = $transaction_id;
        $this->totalFee     = $totalFee;
        $this->outRefundNo  = $outTradeNo;
        $this->refundFee    = $refundFee;
        $this->SSLCERT_PATH = getcwd().$SSLCERT_PATH;
        $this->SSLKEY_PATH  = getcwd().$SSLKEY_PATH;
        $this->APPID        = $APPID;
        $this->MCHID        = $MCHID;
        $this->KEY          = $KEY;
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
            'transaction_id'=>$this->transaction_id,
            'out_refund_no'=> $this->outRefundNo,
            'total_fee'=> $this->totalFee,
            'refund_fee'=> $this->refundFee
        );
        $parma['sign'] = $this->getSign($parma);
        //dump($parma);exit();
        $xmldata = $this->arrayToXml($parma);
        $xmlresult = $this->postXmlSSLCurl($xmldata,'https://api.mch.weixin.qq.com/secapi/pay/refund');
        $result = $this->xmlToArray($xmlresult);
        return $result;
    }

    //需要使用证书的请求
    function postXmlSSLCurl($xml,$url,$second=30)
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
    
   
    
    protected function xmlToArray($xml){
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
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
     
}