<?php

/**
 * 
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 * 
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 * 
 * @author widy
 *
 */
class JsApiPay
{
	/**
	 * 
	 * 网页授权接口微信服务器返回的数据，返回样例如下
	 * {
	 *  "access_token":"ACCESS_TOKEN",
	 *  "expires_in":7200,
	 *  "refresh_token":"REFRESH_TOKEN",
	 *  "openid":"OPENID",
	 *  "scope":"SCOPE",
	 *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
	 * }
	 * 其中access_token可用于获取共享收货地址
	 * openid是微信支付jsapi支付接口必须的参数
	 * @var array
	 */
   	 public $data = null;
     public $APPID;
     public $APPSECRET;
     //代理地址
     public $CURL_PROXY_HOST = '0.0.0.0';
     //是否使用
     public $CURL_PROXY_PORT = 0;
     
     //构造方法
     function __construct($APPID,$APPSECRET) 
     {
        $this->APPID     = $APPID;
        $this->APPSECRET = $APPSECRET;
     }
    
	/**
	 * 
	 * 通过跳转获取用户的openid，跳转流程如下：
	 * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
	 * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
	 * 
	 * @return 用户的openid
	 */
	public function GetOpenid()
	{
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING']);
			$url = $this->__CreateOauthUrlForCode($baseUrl);
			Header("Location: $url");
			exit();
		} else {
			//获取code码，以获取openid
		    $code = $_GET['code'];
			$openid = $this->getOpenidFromMp($code);
			return $openid;
		}
	}
    
    
    
   	/**
	 * 
	 * 通过跳转获取用户的openid，跳转流程如下：
	 * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
	 * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
	 * 
	 * @return 用户的User
	 */
	public function Getuser()
	{
		//通过code获得openid
		if (!isset($_GET['code']))
        {
			//触发微信返回code码
			$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING']);
			$url = $this->__CreateOauthUrlForCode1($baseUrl);
			Header("Location: $url");
			exit();
		} 
        else 
        {
			//获取code码，以获取openid
		    $code = $_GET['code'];
			$User = $this->GetUserFromMp($code);
			return $User;
		}
	}
    
    
    
	
	/**
	 * 
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 * 
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function GetJsApiParameters($UnifiedOrderResult)
	{
		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
			
			throw new WxPayException("参数错误");
		}
		$jsapi = new WxPayJsApiPay();
		$jsapi->SetAppid($UnifiedOrderResult["appid"]);
		$timeStamp = time();
		$jsapi->SetTimeStamp("$timeStamp");
		$jsapi->SetNonceStr(WxPayApi::getNonceStr());
		$jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
		$jsapi->SetSignType("MD5");
		$jsapi->SetPaySign($jsapi->MakeSign());
		$parameters = json_encode($jsapi->GetValues());
		return $parameters;
	}
	
	/**
	 * 
	 * 通过code从工作平台获取openid机器access_token
	 * @param string $code 微信跳转回来带上的code
	 * 
	 * @return openid
	 */
	public function GetOpenidFromMp($code)
	{
		$url = $this->__CreateOauthUrlForOpenid($code);
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
		if($this->CURL_PROXY_HOST != "0.0.0.0"  && $this->CURL_PROXY_PORT != 0)
        {
			curl_setopt($ch,CURLOPT_PROXY,$this->CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT,$this->CURL_PROXY_PORT);
		}
        
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$this->data = $data;
		$openid = $data['openid'];
        if($data['openid']!='')
        {
            $wxusers = D('wxusers');
            $is_s =  $wxusers->where("openid='{$data['openid']}'")->getField('id');
            if($is_s=='')
            {
                //获得数据(放入数据库)
                $data1['openid']     = $data['openid'];
                $data1['logintime']  = time();
                $s3  = $wxusers->add($data1);
                if($s3)
                { 
                      //openid存入$_SESSION
                      session('openid',$data['openid']); 
                      //返回openid
                      return 1; 
                }
                else
                {
                    //用户信息写入失败
                   return 3; 
                }
            }
            else
            {
                //openid存入$_SESSION
                session('openid',$data['openid']); 
                //返回openid
                return 1; 
            }
        }
        else
        {
            return 0;
        }
		
	}
    
    
    
   	/**
	 * 
	 * 通过code从工作平台获取openid机器access_token
	 * @param string $code 微信跳转回来带上的code
	 * 
	 * @return userinfo
	 */
	public function GetUserFromMp($code)
	{
		$url = $this->__CreateOauthUrlForOpenid($code);
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
		if($this->CURL_PROXY_HOST != "0.0.0.0"  && $this->CURL_PROXY_PORT != 0)
        {
			curl_setopt($ch,CURLOPT_PROXY,$this->CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT,$this->CURL_PROXY_PORT);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$this->data = $data;
	    if($data['openid']!='' && $data['access_token']!='')
        {
            $wxusers = D('wxusers');
            $is_s =  $wxusers->where("openid='{$data['openid']}'")->getField('id');
            if($is_s=='')
            {
            
                //拉取用户信息
                $url   = "https://api.weixin.qq.com/sns/userinfo?access_token={$data['access_token']}&openid={$data['openid']}&lang=zh_CN";
                $is_ok1= $this->curl_get($url);
                $is1   = json_decode($is_ok1,true);  
                //dump($is1);
                if($is1['errcode']=='')
                {
                       if($is1['privilege']!='')
                       {
                         $privilege   = json_encode($is1['privilege']);
                       }
                        
                        //获得数据(放入数据库)
                        $data1['openid']     = $data['openid'];
                        $data1['scope']      = $is1['scope'];
                        $data1['nickname']   = $is1['nickname'];
                        $data1['sex']        = $is1['sex'];
                        $data1['province']   = $is1['province'];
                        $data1['city']       = $is1['city'];
                        $data1['country']    = $is1['country'];
                        $data1['headimgurl'] = $is1['headimgurl'];
                        $data1['privilege']  = $privilege;
                        $data1['unionid']    = $is1['unionid'];
                        $data1['language']   = $is1['language'];
                        $data1['u_access_token'] = $is['access_token'];
                        $data1['expires_in']     = $is['expires_in'];
                        $data1['u_access_token_time'] = time();
                        $data1['refresh_token'] = $is['refresh_token'];
                        $s3  = $wxusers->add($data1);
                        if($s3)
                        { 
                              //openid存入$_SESSION
                              session('openid',$data['openid']); 
                              //返回openid
                              return 1; 
                        }
                        else
                        {
                            //用户信息写入失败
                           return 3; 
                        }
                }
                else
                {
                  //user获取失败
                  return 2;  
                } 
            }
            else
            {
                //openid存入$_SESSION
                session('openid',$data['openid']); 
                //拉取用户信息
                $url   = "https://api.weixin.qq.com/sns/userinfo?access_token={$data['access_token']}&openid={$data['openid']}&lang=zh_CN";
                $is_ok1 = $this->curl_get($url);
                $is1   = json_decode($is_ok1,true); 
                //dump($is1); 
                if($is1['errcode']=='')
                {
                       if($is1['privilege']!='')
                       {
                         $privilege   = json_encode($is1['privilege']);
                       }
                        
                        //获得数据(放入数据库)
                        $save['scope']      = $is1['scope'];
                        $save['nickname']   = $is1['nickname'];
                        $save['sex']        = $is1['sex'];
                        $save['province']   = $is1['province'];
                        $save['city']       = $is1['city'];
                        $save['country']    = $is1['country'];
                        $save['headimgurl'] = $is1['headimgurl'];
                        $save['privilege']  = $privilege;
                        $save['unionid']    = $is1['unionid'];
                        $save['language']   = $is1['language'];
                        $save['u_access_token'] = $is['access_token'];
                        $save['expires_in']     = $is['expires_in'];
                        $save['u_access_token_time'] = time();
                        $save['refresh_token']  = $is['refresh_token'];
                        $s3  = $wxusers->where("openid='{$data['openid']}'")->save($save);
                        if($s3)
                        { 
                              //openid存入$_SESSION
                              session('openid',$data['openid']); 
                              //返回openid
                              return 1; 
                        }
                        else
                        {
                            //用户信息写入失败
                           return 3; 
                        }
                }
                else
                {
                  //user获取失败
                  return 2;  
                } 
            }
        }
        else
        {
            //openid获取失败
            return 0;
        }
	}
    
    
    
    
	
	/**
	 * 
	 * 拼接签名字符串
	 * @param array $urlObj
	 * 
	 * @return 返回已经拼接好的字符串
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 
	 * 获取地址js参数
	 * 
	 * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
	 */
	public function GetEditAddressParameters()
	{	
		$getData = $this->data;
		$data = array();
		$data["appid"] = $this->APPID;
		$data["url"] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$time = time();
		$data["timestamp"] = "$time";
		$data["noncestr"] = "1234568";
		$data["accesstoken"] = $getData["access_token"];
		ksort($data);
		$params = $this->ToUrlParams($data);
		$addrSign = sha1($params);
		
		$afterData = array(
			"addrSign" => $addrSign,
			"signType" => "sha1",
			"scope" => "jsapi_address",
			"appId" => $this->APPID,
			"timeStamp" => $data["timestamp"],
			"nonceStr" => $data["noncestr"]
		);
		$parameters = json_encode($afterData);
		return $parameters;
	}
	
	/**
	 * 
	 * 构造获取code的url连接
	 * @param string $redirectUrl 微信服务器回跳的url，需要url编码
	 * 
	 * @return 返回构造好的url
	 */
	private function __CreateOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = $this->APPID;
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}
    
    
    
   	/**
	 * 
	 * 构造获取code的url连接（）
	 * @param string $redirectUrl 微信服务器回跳的url，需要url编码
	 * 
	 * @return 返回构造好的url
	 */
	private function __CreateOauthUrlForCode1($redirectUrl)
	{
		$urlObj["appid"] = $this->APPID;
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_userinfo";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}
    
    
	
	/**
	 * 
	 * 构造获取open和access_toke的url地址
	 * @param string $code，微信跳转带回的code
	 * 
	 * @return 请求的url
	 */
	private function __CreateOauthUrlForOpenid($code)
	{
		$urlObj["appid"]  = $this->APPID;
		$urlObj["secret"] = $this->APPSECRET;
		$urlObj["code"] = $code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}
    
    
   	/**
	 * 
	 * 获取分享验证access_token的url地址
	 * @param string $code，微信跳转带回的code
	 * 
	 * @return access_token(array)
	 */
	public function GetUrlForaccess_token()
	{
		$appid     = $this->APPID;
		$appsecret = $this->APPSECRET; 
		$url       = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
		if($this->CURL_PROXY_HOST != "0.0.0.0"  && $this->CURL_PROXY_PORT != 0)
        {
			curl_setopt($ch,CURLOPT_PROXY,$this->CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT,$this->CURL_PROXY_PORT);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$this->data = $data;
        return $data;
 
	}
    
    
    
   	/**
	 * 
	 * 获取验证jsapi_ticket
	 * @param string $code，微信跳转带回的code
	 * 
	 * @return jsapi_ticket(array)
	 */
    
    //获取验证jsapi_ticket
    public function jsapi_ticket($access_token)
    {  
                //接口地址http请求方式: GET
                $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$access_token&type=jsapi";
                $retdate=  $this->curl_get($url);
                //判断获取jsapi_ticket
                $jsapi_ticket= json_decode($retdate,true);
                return $jsapi_ticket;
    }
    
    
    
    
      /* PHP CURL HTTPS GET */
   public   function curl_get($url){
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        $tmpInfo = curl_exec($curl);     //返回api的json对象
        //关闭URL请求
        curl_close($curl);
        return $tmpInfo;    //返回json对象
    }  
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}