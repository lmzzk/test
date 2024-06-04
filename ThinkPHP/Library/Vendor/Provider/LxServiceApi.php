<?php
header("Content-Type:text/html;charset=utf-8");
//浙江蓝犀信息技术有限公司 https://easydoc.top/s/85699829/b52BsZU0/ErN2EoIb
class LxServiceApi
{
    //测试网关地址
    private $gatewayUrl = 'https://pay-dev.lx-rhino.com';
    //正式网关
    //private $gatewayUrl = 'https://api.lx-rhino.com'; 
    // 商户签约后发放提供的唯一识别号
    private $appKey='lxe80e2f8369aa493815';
    // 公钥，参数加解密使用
    private $publicKey='MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAIeQZCYDoEm3jA5zCTLG/wFMgGCWf9FosWjl+BBq+A0vtCqeGXo3R7WctU1j6GeNk9zP4018iv0DIOpNwN3K5q8CAwEAAQ==';
    // 密钥
    private $secretKey='0bc8c3a5eb25d47feef5b0091ed97f79';
    // 版本
    private $version = '1.0';
    // 错误信息
    private $_error;

    // 构造方法
    /*
    public function __construct($configs = []) {
        $configs = $configs ?  $configs : C('LX_OPTIONS');
        if ($gatewayUrl = $configs['gatewayUrl']) {
            $this->gatewayUrl = $gatewayUrl;
        } else {
            throw new Exception('gatewayUrl未配置');
        }

        if ($appKey = $configs['appKey']) {
            $this->appKey = $appKey;
        } else {
            throw new Exception('appKey未配置');
        }

        if ($publicKey = $configs['publicKey']) {
            $this->publicKey = $publicKey;
        } else {
            throw new Exception('publicKey未配置');
        }

        if ($secretKey = $configs['secretKey']) {
            $this->secretKey = $secretKey;
        } else {
            throw new Exception('secretKey未配置');
        }

        if ($mainstayId = $configs['mainstayId']) {
            $this->mainstayId = $mainstayId;
        } else {
            throw new Exception('mainstayId未配置');
        }

        if ($invoiceCode = $configs['invoiceCode']) {
            $this->invoiceCode = $invoiceCode;
        } else {
            throw new Exception('invoiceCode未配置');
        }
    }
*/

/*
   // 提交预代付订单
    public function submitBill($data = array()) {
        $params = array(
            'payeeName' => $data['payeeName'], // 收款人姓名 必填
            'payeeAccount' => $data['payeeAccount'], // 收款账户 必填
            'payeeIdCard' => $data['payeeIdCard'], // 收款人身份证号码 必填
            'amount' => $data['amount'], // 金额单位为元精确到分 必填
            'thirdOrderId' => $data['thirdOrderId'], // 商户订单号 必填
            'payeePhone' => $data['payeePhone'], // 收款人手机号码 非必填
            'payeeBankName' => $data['payeeBankName'], // 开户行 非必填
            'payeeBankNo' => $data['payeeBankNo'], // 开户行联行号 非必填
            'thirdBizOrderId' => $data['thirdBizOrderId'], // 第三方业务订单ID 非必填
            'payType' => $data['payType'], // 支付类型 非必填 0 银行卡 1支付宝
        );

        $response = $this->requestApi('/open/api/v1/bill/submitBill', $params);
        return $response;
    }

    
    //确认发放订单
    public function sureGrant($data = []) {
        $params = [
            'billId' => $data['billId'],
            'mainstayId' => $this->mainstayId,
            'invoiceCode' => $this->invoiceCode,
        ];
        $response = $this->requestApi('/open/api/v1/bill/sureGrant', $params);
        return $response;
    }

    //代付结果查询[订单号1，订单号2，...]
    public function queryResult($data = []) {
        $params = $data;
        $response = $this->requestApi('/open/api/v1/bill/queryResult', $params);
        return $response;
    }


    // 取消订单 billId	平台返回的订单ID	是	reason	取消原因(30字)	否 该接口响应参数中无响应data字段 ,success 等于true代表取消成功，false 代表失败 errorMessage中有错误信息反馈
     
    public function cancelBill($data = []) {
        $response = $this->requestApi('/open/api/v1/bill/cancel', $params);
        return $response;
    }

    // 余额查询 mainstayId 主体ID payType 0-银行卡    1-支付宝
    public function queryBalance($data = []) {
        $params = [
            'mainstayId' => $this->mainstayId,
            'payType' => $data['payType'],
        ];
        $response = $this->requestApi('/open/api/v1/balance/query', $params);
        return $response;
    }


     // 获取对账单date	需要对账的日期	是	该参数为String类型 yyyy-MM-dd,例如 2019-07-20
    public function companyReconciliation($data = []) {
        $response = $this->requestApi('/open/api/v1/bill/companyReconciliation', $params);
        return $response;
    }


    // 查询用户每月可发放金额数 idCards	需要查询的身份证号码集合	是	例如：[身份证1,身份证2,身份证3] mainstayId	主体ID	是
    public function availableCredit($data = []) {
        $params = [

        ];
        $response = $this->requestApi('/open/api/v1/available/credit', $params);
        return $response;
    }


    //查询商户可开票列表，以及所拥有的主体接口  无需参数 通过该接口可获取公司的可开发票列表，以及所拥有主体列表,无需频繁请求。请求到之后保存自己库即可。当签署主体信息有变更。或者发票内容有调整重新请求接口即可
    public function queryInvoices($data =[]) {
        $params = [];
        $response = $this->requestApi('/open/api/v1/company/queryInvoices', $params);
        return $response;
    }
 */   
    
    
    /**
    * 20号 个人信息认证    
    * cardNo   银行卡
    * realName 姓名 
    * idCard   身份证号
    * signingType 签约类型 0
    * $data = array('realName'=>'xxx','cardNo'=>'1111111','idCard'=>'1111111','signingType'=>0);
    * 返回
    * array{
          ["resopnseType"]=>0
          ["errorCode"]=>"PARAM_EMPTY"
          ["errorMessage"]=>"参数不能为空"
          ["status"]=>2
          ["data"]=>NULL
          ["ext"]=>NULL
          ["extMessage"]=>NULL
          ["success"]=>bool(false)
      }
    * 
    * success = true
    * 
    */
    
     public function personInfo_check($data) 
     {
        $url = '/open/api/v1/personInfo/check';
        $response = $this->requestApi($url,$data);
        return $response;
     }
     
    /**
    * 14在线签约信息接口
    * realName	真实姓名		
    * cardNo	银行卡号	
    * mobile	手机号      
    * idCard	身份证号码  
    * signingType	签约类型	2:银行卡四要素 3:运营商三要素(无银行卡号)
    * $data = array('realName'=>'xxx','cardNo'=>'1111111','idCard'=>'1111111','mobile'=>'1111111','signingType'=>2);
    * 返回
    * array{
          ["resopnseType"]=>0
          ["errorCode"]=>"PARAM_EMPTY"
          ["errorMessage"]=>"参数不能为空"
          ["status"]=>2
          ["data"]=>NULL
          ["ext"]=>NULL
          ["extMessage"]=>NULL
          ["success"]=>bool(false)
      }
    * 
    * success = true
    * personAccountId  签约ID
    */
    public function sign_contract($data) 
    {
        $url = '/open/api/v1/sign/contract';
        $response = $this->requestApi($url,$data);
        return $response;
    }  
    
    /**
    * 15号 在线签约信息接口
    * code          	验证码	
    * personAccountId	签约账户ID		
    * mobile	        手机号	
    * $data = array('code'=>'xxx','personAccountId'=>'1111111','mobile'=>'1111111');
    * 返回
    * array{
          ["resopnseType"]=>0
          ["errorCode"]=>"PARAM_EMPTY"
          ["errorMessage"]=>"参数不能为空"
          ["status"]=>2
          ["data"]=>NULL
          ["ext"]=>NULL
          ["extMessage"]=>NULL
          ["success"]=>bool(false)
      }
    * 
    * success = true
    * url	签约成功的合同链接
    */
    public function sign_contract_submit($data) 
    {
        $url = '/open/api/v1/sign/contract/submit';
        $response = $this->requestApi($url,$data);
        return $response;
    } 
##########################https://easydoc.top/s/85699829/b52BsZU0/ErN2EoIb################################################################
    private function sslEncrypt($source, $type, $key) {
        $maxlength = 53;
        $output = '';
        while ($source) {
            $input = substr($source, 0, $maxlength);
            $source = substr($source, $maxlength);
            if ($type == 'private') {
                $ok = openssl_private_encrypt($input, $encrypted, $key);
            } else {
                $ok = openssl_public_encrypt($input, $encrypted, $key);
            }

            $output .= $encrypted;
        }
        return $output;
    }

    private function sslDecrypt($source, $type, $key) {
        $maxlength = 64;
        $output = '';
        while ($source) {
            $input = substr($source, 0, $maxlength);
            $source = substr($source, $maxlength);
            if ($type == 'private') {
                $ok = openssl_private_decrypt($input, $out, $key);
            } else {
                $ok = openssl_public_decrypt($input, $out, $key);
            }

            $output .= $out;
        }
        return $output;
    }

    // 生成毫秒
    private function msectime() 
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = floatval(sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000));
        return $msectime;
    }
    


    // 公钥加密数据
    protected function encrytByPublicKey($jsonParams = "") {
        if (empty($jsonParams)) 
        {
            return $jsonParams;
        }
        $pubKeyId = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($this->publicKey, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        $encrypted = $this->sslEncrypt($jsonParams, 'public', openssl_pkey_get_public($pubKeyId));
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    // 公钥解密
    public function dencrytByPublicKey($encrypted = "") {
        $dencrypted = $this->sslDecrypt(base64_decode($encrypted, true), 'public', openssl_pkey_get_public("file://$this->publicKey"));
        $dencrypted = json_decode($dencrypted, true);
        return $dencrypted;
    }

    /**
     * 生成签名
     * @return 签名
     */
    protected function makeSign($encryptResult = "", $defaultParams =array()) {
        $defaultStr = '';
        //encryptResult + "&Timestamp=" + timestamp + "&AppKey=" + appKey + "&Version=1.0" + "&SecretKey" + secretKey;
        $encStr = $encryptResult . '&Timestamp=' . $defaultParams['Timestamp']  . '&AppKey=' . $defaultParams['AppKey']  . '&Version=' . $defaultParams['Version']  . '&SecretKey' . $defaultParams['SecretKey']   ;
        $sign = md5($encStr);
        return $sign;
    }

    /**
     * 签名校验
     * @param  array $params
     * @param  string $sign
     * @return boolean 是否匹配
     */
    protected function checkSign($params, $sign) {
        
    }


    // API请求统一处理
     function requestApi($url,$params) {
        $paramsJson = empty($params) ? '' : json_encode($params);
        // 加密数据
        $encryptParamsJson = $this->encrytByPublicKey($paramsJson);
        // 默认参数保持顺序,用作签名
        $defaultParams = array(
            'Timestamp' => $this->msectime(),
            'AppKey' => $this->appKey,
            'Version' => $this->version,
            'SecretKey' => $this->secretKey,
        );

        // 生成签名
        $sign = $this->makeSign($encryptParamsJson, $defaultParams);

        $headerParams = array_merge($defaultParams,array(
            'Sign' => $sign,
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Length' => strlen($encryptParamsJson),
        ));
        unset($headerParams['SecretKey']);
        $headerPostParam =array();
        foreach ($headerParams as $key => $value) {
            $headerPostParam[] = $key . ":" . $value;
        }

        $fullParams = array(
            'header' => $headerPostParam,
            'body' => $encryptParamsJson,
        );
   
       //var_dump($fullParams);
         //返回接口数据 array
         $response =  $this->https_request('post',$this->gatewayUrl.$url,$headerPostParam,$encryptParamsJson);
         return $response;
 
    }

    // 需要进入待重试
    public function isStatusWaitingRetry($status, $errorCode) {
        return in_array($status . '_' . $errorCode,array(
            '2015_BILL_PAY_TYPE_ERROR',
            '2007_BILL_INSUFFICIENT_BALANCE',
            '9005_IP_FORBID',
            '99999_SYS_ERROR',
        ));
    }

    /**
     * 错误代码
     * @param  $code       服务器输出的错误代码
     * return string
    */
    public function getErrorCode($code){
        $errList = array(
            // status_errorCode 响应码_错误码
            '9001_PARAM_NOT_EXIST' =>    'xxx参数为空',
            '9002_SIGN_CHECK_FAIL' =>    '签名验证失败',
            '9003_REQUESRT_INVALID' =>   '请求失效一般由于请求时间超过两分钟导致',
            '9004_VERSION_ERROR' =>  '版本号不符',
            '9005_IP_FORBID' =>  '该IP禁止访问',
            '9006_PARAM_ERROR' =>    '参数有误',
            '9008_ORDERNO_REPEAT' => '订单号重复',
            '9009_DECRYPT_ERROR' =>  '系统解密失败请确认加密是否正确',
            '99999_SYS_ERROR' =>  '服务器繁忙',
            '2002_BILL_NOT_EXIST' => '发放记录不存在',
            '4002_HAS_NOT_CHECK' =>  '系统正在检测用户信息,请稍后再试',
            '2007_BILL_INSUFFICIENT_BALANCE' =>  '余额不足,请充值',
            '2003_BILL_CAN_NOT_GRANT' => '该笔订单不能发放',
            '9012_NOT_INVOICE' =>    '您暂无该发票',
            '4004_REPEAT_INVOICE' => '请勿重复开票',
            '4009_INVOICE_NOT_ADD' =>    '本月低于80万次数已用完,本次开票金额必须大于等于80万',
            '4010_INVOICE_NOT_ADD_26' => '仅每月1号—26号可申请',
            '1032_AUTH_OVER_TIMES' =>    '你已连续3次认证失败，请24小时后再进行认证',
            '1005_KEYS_ERROR' => '四要素(姓名,身份证,银行卡,手机号)校验错误',
            '1006_KEYS_ERROR' => '三要素(姓名,身份证,手机号)校验错误',
            '2015_BILL_PAY_TYPE_ERROR' => '该主体暂不支持支付宝发放',
        );

        return array_key_exists($code, $errList) ? $errList[$code] : ($code ? $code : '未知错误');
    }

    public function setErrorMsg($errMsg) {
        $this->_error = $errMsg;
        return true;
    }

    public function getErrorMsg() {
        return $this->_error;
    }
    
    
    
    //模拟请求
    function https_request($type = "post", $url,$header  = null, $data = null)
    {
        //1、初始化curl
        $ch = curl_init();
        //2、设置传输选项
        curl_setopt($ch, CURLOPT_URL, $url);//请求的url地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//将请求的结果以文件流的形式返回
        // 判断请求发送的类型
        if ($type == "post") {
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POST, 1);//请求POST方式
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST提交的内容
            }
        }
        //设置header头
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        // 跳过SSL验证，本代码用来解决不支持SSL验证的问题（暂时使用，不推荐）
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        //3、执行请求并处理结果
        $outopt = curl_exec($ch);
    
        //把json数据转化成数组
        $outoptArr = json_decode($outopt, TRUE);
    
        //4、关闭curl
        curl_close($ch);
    
        //如果返回的结果$outopt是json数据，则需要判断一下
        if (is_array($outoptArr)) {
            return $outoptArr;
        } else {
            return $outopt;
        }
    }  
    
    
 
    
    
    
    
    
    
    
}

/***********************************************************************************************************************************************/

  $content   =  array('realName'=>'巍子','idCard'=>'510105198603222299','mobile'=>13980562513,'signingType'=>'0');
      $LxService = new LxService();
      $LxService->requestApi($content);
























