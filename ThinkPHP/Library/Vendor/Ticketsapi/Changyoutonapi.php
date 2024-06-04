<?php
//set_time_limit(0);
class ChangyoutongService {
	public $supplierIdentity="CYTDMS";
	public $key="12345678";
	public $createUser="cytttly";
	public $url="http://dy.jingqu.cn/service/distributor.do";
	public $securityType="MD5";
	public $pageNo=1;
	public $pageSize=100;	
	public $service;

	public function __construct($data)
    {
		$this->supplierIdentity = $data['supplieridentity'];
		$this->key          = $data['key'];
		$this->createUser   = $data['createuser'];
		$this->url          = $data['url'];
		$this->securityType = $data['securitytype'];
	}
    
    //异步
   	public function asyn_requestxml($method,$msg)
    {
		     
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml.= '<qm:request xmlns:qm="http://piao.ectrip.com/2014/QMenpiaoRequestSchema" xsi:schemaLocation="http://piao.ectrip.com/2014/QMenpiaoRequestSchema QMRequestDataSchema-1.1.0.xsd"	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
		$xml.= '<qm:header>';
		$xml.= '<qm:application>tour.ectrip.com</qm:application>';
		$xml.= '<qm:processor>DataExchangeProcessor</qm:processor>';
		$xml.= '<qm:version>v2.0.0</qm:version>';
		$xml.= '<qm:bodyType>'.$method.'RequestBody</qm:bodyType>';
		$xml.= '<qm:createUser>'.$this->createUser.'</qm:createUser>';
		$xml.= '<qm:createTime>'.date("Y-m-d H:i:s").'</qm:createTime>';
		$xml.= '<qm:supplierIdentity>'.$this->supplierIdentity.'</qm:supplierIdentity>';
        $xml.= '<code>1000</code>';              
        $xml.= '<describe>'.$msg.'</describe>';        
		$xml.= '</qm:header>';
        $xml.= '<body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="NoticeOrderConsumedResponseBody">';
        $xml.= '<message>success</message>';
        $xml.= '</body>';
		$xml.= '</qm:request>';
		$data = base64_encode($xml);
		$signed = strtoupper(md5($this->key.$data));
		$requestParam = array('data'=>$data,'signed'=>$signed,'securityType'=>$this->securityType);
		echo $requestParam = json_encode($requestParam);
	}
    
    
    
    
    
    
    
    
    
    
	public function requestxml($mhd,$body){
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml.= '<qm:request xmlns:qm="http://piao.ectrip.com/2014/QMenpiaoRequestSchema" xsi:schemaLocation="http://piao.ectrip.com/2014/QMenpiaoRequestSchema QMRequestDataSchema-1.1.0.xsd"	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
		$xml.= '<qm:header>';
		$xml.= '<qm:application>tour.ectrip.com</qm:application>';
		$xml.= '<qm:processor>DataExchangeProcessor</qm:processor>';
		$xml.= '<qm:version>v2.0.0</qm:version>';
		$xml.= '<qm:bodyType>'.$mhd.'RequestBody</qm:bodyType>';
		$xml.= '<qm:createUser>'.$this->createUser.'</qm:createUser>';
		$xml.= '<qm:createTime>'.date("Y-m-d H:i:s").'</qm:createTime>';
		$xml.= '<qm:supplierIdentity>'.$this->supplierIdentity.'</qm:supplierIdentity>';
		$xml.= '</qm:header>';
		$xml.= '<qm:body xsi:type="qm:'.$mhd.'RequestBody">';
		$xml.= $body;
		$xml.= '</qm:body>';
		$xml.= '</qm:request>';
        
		$data = base64_encode($xml);
		$signed = strtoupper(md5($this->key.$data));
		$requestParam = array('data'=>$data,'signed'=>$signed,'securityType'=>$this->securityType);
		$requestParam = json_encode($requestParam);

        //提交格式
        $request_data['method']=$mhd;
        $request_data['requestParam']=$requestParam;
        $response = $this->curl_post($this->url,$request_data);	
		$response = json_decode($response);
		$response = base64_decode($response->data);		
		$response = str_replace("qm:","",$response);		
		$products = simplexml_load_string($response);
        $jsonStr  = json_encode($products);
        $jsonArray = json_decode($jsonStr,true);
		return $jsonArray ;
	}
    
    /**
     * 获取产品(每日更新) 全部产品信息
     */  
	public function getProduct_all()
    {
			$jsondata['data']='';
			$jsondata['data'].='<qm:method>ALL</qm:method>';
			$jsondata['data'].='<qm:currentPage>'.$this->pageNo.'</qm:currentPage>';
			$jsondata['data'].='<qm:pageSize>'.$this->pageSize.'</qm:pageSize>';
			$jsondata['data'].='<qm:resourceId></qm:resourceId>';		
			//只需要构造body内容 传递method方法就可以了
            $method = 'GetProductByOTA';
			$products=$this->requestxml($method,$jsondata['data']);
           //返回数组
           return $products; 	
	}
    
    /**
     * 获取产品(更新)单个产品信息
     * $resourceId  门票ID
     */ 
	public function getProduct_one($resourceId)
    {
			$jsondata['data']='';
			$jsondata['data'].='<qm:method>SINGLE</qm:method>';
			$jsondata['data'].='<qm:currentPage></qm:currentPage>';
			$jsondata['data'].='<qm:pageSize></qm:pageSize>';
			$jsondata['data'].='<qm:resourceId>'.$resourceId.'</qm:resourceId>';		
			//只需要构造body内容 传递method方法就可以了
            $method = 'GetProductByOTA';
			$products=$this->requestxml($method,$jsondata['data']);
              //返回数组
             return $products;	
	} 
    
    
    
    /**
     * 创建已支付订单
     * 判定依据：         code=1000，orderStatus =  PREPAY_ORDER_PRINT_SUCCESS
     * 如果订单状态为     PREPAY_ORDER_PRINT_SUCCESS，表示订单出票成功
     * 如果订单状态为     PREPAY_ORDER_PRINTING，表示订单正在出票中，出票后，畅游通会通知我们订单出票信息，需要我们提供接口地址；
     * 如果出票通知结果为 PREPAY_ORDER_PRINT_FAILED，需要OTA取消订单，给游客退款；
     * $orderId            OTA订单号
     * $resourceId         供应商产品  ID
     * $productName        产品名称
     * $visitDate          使用yyyy-MM-dd
     * $sellPrice          产品售卖单价单位：分
     * $orderQuantity      订单票数
     * $orderPrice         订单总价 单位：分
     * 取票人信息
     * $get_tickets_name               订单取票人姓名
     * $get_tickets_mobile             订单取票人的电话
     * $get_tickets_credentials        取票人证件号
     * $get_tickets_credentialsType    取票人证件类型  身份证:ID_CARD, 护照:HUZHAO,台胞证  : TAIBAO港澳通行证: GANGAO其它：OTHER配合credentials生效
     * 实名制
     * $data[]             数组
     * $tourist_name          游玩人姓名
     * $credentials_number       游玩人证件号
     * $credentials_type    游玩人证件类型  身份证:ID_CARD, 护照:HUZHAO,台胞证  : TAIBAO港澳通行证: GANGAO其它：OTHER配合credentials生效
     * 
     * Response返回
     * header['code']      编码
     * header['describe']  说明
     * body['orderInfo']['partnerorderId'] 畅游通的订单ID
     * body['orderInfo']["orderStatus"]    订单状态   PREPAY_ORDER_PRINT_FAILED出票失败  PREPAY_ORDER_PRINTING已付款未出票  PREPAY_ORDER_PRINT_SUCCESS出票成功
     * body['orderInfo']["qrCodeStr"]      入园二维码
     * body['orderInfo']["qrCodeUrl"]      入园链接
     * body['orderInfo']["verifyCode"]     取票密码          
     */   
   	//创建已支付订单(非实名制)
	public function createPaymentOrder_norealname($orderId,$resourceId,$productName,$visitDate,$sellPrice,$get_tickets_name,$get_tickets_mobile,$get_tickets_credentials,$get_tickets_credentialsType,$orderQuantity,$orderPrice)
    {
			$jsondata['data']='';
            $jsondata['data'].='<qm:orderInfo>';
            $jsondata['data'].='<qm:orderId>'.$orderId.'</qm:orderId>';
            
            $jsondata['data'].='<qm:product>';
            $jsondata['data'].='<qm:resourceId>'.$resourceId.'</qm:resourceId>';
            $jsondata['data'].='<qm:productName>'.$productName.'</qm:productName>';
            $jsondata['data'].='<qm:visitDate>'.$visitDate.'</qm:visitDate>';
            $jsondata['data'].='<qm:sellPrice>'.$sellPrice.'</qm:sellPrice>';
            $jsondata['data'].='</qm:product>';
            //取票人信息
            $jsondata['data'].='<qm:contactPerson>';
            $jsondata['data'].='<qm:name>'.$get_tickets_name.'</qm:name>';
            $jsondata['data'].='<qm:mobile>'.$get_tickets_mobile.'</qm:mobile>';
            $jsondata['data'].='<qm:credentials>'.$get_tickets_credentials.'</qm:credentials>';
            $jsondata['data'].='<qm:credentialsType>'.$get_tickets_credentialsType.'</qm:credentialsType>';
            $jsondata['data'].='</qm:contactPerson>';
            //实名制(不填)
            $jsondata['data'].='<qm:visitPerson>';
            $jsondata['data'].='<qm:person>';
            $jsondata['data'].='<qm:name></qm:name>';
            $jsondata['data'].='<qm:credentials></qm:credentials>';
            $jsondata['data'].='<qm:credentialsType></qm:credentialsType>';
            $jsondata['data'].='</qm:person>';
            $jsondata['data'].='</qm:visitPerson>';
            
			$jsondata['data'].='<qm:orderQuantity>'.$orderQuantity.'</qm:orderQuantity>';
			$jsondata['data'].='<qm:orderPrice>'.$orderPrice.'</qm:orderPrice>';
			$jsondata['data'].='<qm:orderStatus>PREPAY_ORDER_PRINTING</qm:orderStatus>';  
            $jsondata['data'].='</qm:orderInfo>';
            
			//只需要构造body内容 传递method方法就可以了
            $method = 'createPaymentOrder';
			$products=$this->requestxml($method,$jsondata['data']);
            //返回数组
            return $products; 
	} 
    
   	//创建已支付订单(实名制)
	public function createPaymentOrder_realname($orderId,$resourceId,$productName,$visitDate,$sellPrice,$get_tickets_name,$get_tickets_mobile,$get_tickets_credentials,$get_tickets_credentialsType,$data,$orderQuantity,$orderPrice)
    {
			$jsondata['data']='';
            $jsondata['data'].='<qm:orderInfo>';
            $jsondata['data'].='<qm:orderId>'.$orderId.'</qm:orderId>';
            
            $jsondata['data'].='<qm:product>';
            $jsondata['data'].='<qm:resourceId>'.$resourceId.'</qm:resourceId>';
            $jsondata['data'].='<qm:productName>'.$productName.'</qm:productName>';
            $jsondata['data'].='<qm:visitDate>'.$visitDate.'</qm:visitDate>';
            $jsondata['data'].='<qm:sellPrice>'.$sellPrice.'</qm:sellPrice>';
            $jsondata['data'].='</qm:product>';
            //取票人信息
            $jsondata['data'].='<qm:contactPerson>';
            $jsondata['data'].='<qm:name>'.$get_tickets_name.'</qm:name>';
            $jsondata['data'].='<qm:mobile>'.$get_tickets_mobile.'</qm:mobile>';
            $jsondata['data'].='<qm:credentials>'.$get_tickets_credentials.'</qm:credentials>';
            $jsondata['data'].='<qm:credentialsType>'.$get_tickets_credentialsType.'</qm:credentialsType>';
            $jsondata['data'].='</qm:contactPerson>';
            //实名制
            $jsondata['data'].='<qm:visitPerson>';
            //遍历实名用户
            foreach($data as $v)
            {
              $jsondata['data'].='<qm:person>';
              $jsondata['data'].='<qm:name>'.$v['tourist_name'].'</qm:name>';
              $jsondata['data'].='<qm:credentials>'.$v['credentials_number'].'</qm:credentials>';
              $jsondata['data'].='<qm:credentialsType>'.$v['credentials_type'].'</qm:credentialsType>';
              $jsondata['data'].='</qm:person>';  
            } 
            $jsondata['data'].='</qm:visitPerson>';
            
			$jsondata['data'].='<qm:orderQuantity>'.$orderQuantity.'</qm:orderQuantity>';
			$jsondata['data'].='<qm:orderPrice>'.$orderPrice.'</qm:orderPrice>';
			$jsondata['data'].='<qm:orderStatus>PREPAY_ORDER_PRINTING</qm:orderStatus>';  
            $jsondata['data'].='</qm:orderInfo>';
            
			//只需要构造body内容 传递method方法就可以了
            $method = 'createPaymentOrder';
			$products=$this->requestxml($method,$jsondata['data']);
             //返回数组
             return $products; 	
	}   
    
    
    /**
     * 修改已支付订单用户信息
     * partnerorderId      畅游通的订单  ID
     * visitDate           游览日期 yyyy-MM-dd
     * 取票人信息
     * $get_tickets_name               订单取票人姓名
     * $get_tickets_mobile             订单取票人的电话
     * $get_tickets_credentials        取票人证件号
     * $get_tickets_credentialsType    取票人证件类型  身份证:ID_CARD, 护照:HUZHAO,台胞证  : TAIBAO港澳通行证: GANGAO其它：OTHER配合credentials生效
     * 实名制
     * $data[]             数组
     * $name               游玩人姓名
     * $credentials        游玩人证件号
     * $credentialsType    取票人证件类型  身份证:ID_CARD, 护照:HUZHAO,台胞证  : TAIBAO港澳通行证: GANGAO其它：OTHER配合credentials生效
     * Response返回
     * header['code']      编码
     * header['describe']  说明
     */  
   	public function pushOrder($partnerorderId,$get_tickets_name,$get_tickets_mobile,$get_tickets_credentials,$get_tickets_credentialsType,$data,$visitDate)
    {
			$jsondata['data']='';
            $jsondata['data'].='<qm:orderInfo>';
            $jsondata['data'].='<qm:partnerOrderId>'.$partnerorderId.'</qm:partnerOrderId>';
            $jsondata['data'].='<qm:visitDate>'.$visitDate.'</qm:visitDate>';
            //取票人信息
            $jsondata['data'].='<qm:contactPerson>';
            $jsondata['data'].='<qm:name>'.$get_tickets_name.'</qm:name>';
            $jsondata['data'].='<qm:mobile>'.$get_tickets_mobile.'</qm:mobile>';
            $jsondata['data'].='<qm:credentials>'.$get_tickets_credentials.'</qm:credentials>';
            $jsondata['data'].='<qm:credentialsType>'.$get_tickets_credentialsType.'</qm:credentialsType>';
            $jsondata['data'].='</qm:contactPerson>';
            //实名制
            $jsondata['data'].='<qm:visitPerson>';
            //遍历实名用户
            if(count($data)>0 )
            {
                foreach($data as $v)
                {
                  $jsondata['data'].='<qm:person>';
                  $jsondata['data'].='<qm:name>'.$v['name'].'</qm:name>';
                  $jsondata['data'].='<qm:credentials>'.$v['credentials'].'</qm:credentials>';
                  $jsondata['data'].='<qm:credentialsType>'.$v['credentialsType'].'</qm:credentialsType>';
                  $jsondata['data'].='</qm:person>';  
                }  
            }
            else
            {
                  $jsondata['data'].='<qm:person>';
                  $jsondata['data'].='<qm:name></qm:name>';
                  $jsondata['data'].='<qm:credentials></qm:credentials>';
                  $jsondata['data'].='<qm:credentialsType></qm:credentialsType>';
                  $jsondata['data'].='</qm:person>';   
            } 
            $jsondata['data'].='</qm:visitPerson>';
            $jsondata['data'].='</qm:orderInfo>';	
			//只需要构造body内容 传递method方法就可以了
            $method = 'pushOrder';
			$products=$this->requestxml($method,$jsondata['data']);
            //返回数组
            return $products;	
	} 
    
    /**
     * 获取订单信息
     * partnerorderId   畅游通的订单  ID
     * 
     * Response返回
     * [header]['code']      编码
     * [header]['describe']  说明 
     * [body][orderInfo][partnerorderId] 
     * [body][orderInfo][orderStatus]       订单状态PREPAY_ORDER_INIT预付：初始订单PREPAY_ORDER_BOOK_FAILED预付：预订失败PREPAY_ORDER_NOT_PAYED预付：预订成功，待支付PREPAY_ORDER_CANCEL预付：订单已取消PREPAY_ORDER_PRINTING预付：已付款，出票中PREPAY_ORDER_PRINT_FAILED预付：出票失败PREPAY_ORDER_PRINT_SUCCESS预付：出票成功PREPAY_ORDER_REFUNDED预付：已退订PREPAY_ORDER_CONSUMED预付：已消费CASHPAY_ORDER_INIT现付：初始订单CASHPAY_ORDER_PRINT_FAILED现付：出票失败CASHPAY_ORDER_PRINT_SUCCESS现付：出票成功CASHPAY_ORDER_REFUNDED现付：已退订CASHPAY_ORDER_CONSUMED现付：已消费  
     * [body][orderInfo][orderQuantity]    原始订单总票数
     * [body][orderInfo][eticketNo]        电子票信息
     * [body][orderInfo][eticketSended]    TRUE：电子票已发送FALSE：电子票未发送如果没有电子票，则记录实际通知用户后，设置为  true返回。
     * [body][orderInfo][useQuantity]      已消费票数
     * [body][orderInfo][consumeInfo]      消费额外信息，相当于消费时的备注。
     */ 
   	public function getOrderByOTA($partnerorderId)
    {
			$jsondata['data']='';
            $jsondata['data'].='<qm:partnerOrderId>'.$partnerorderId.'</qm:partnerOrderId>';
			//只需要构造body内容 传递method方法就可以了
            $method = 'getOrderByOTA';
			$products=$this->requestxml($method,$jsondata['data']);
            //返回数组
            return $products;	
	}
    
    /**
     * 短信重发入园凭证  
     * partnerorderId   畅游通的订单  ID
     * phoneNumber      重发短信时的手机号
     * Response返回
     * [header]['code']      编码
     * [header]['describe']  说明
     * message          空消息即可
     */  
	public function sendOrderEticket($partnerorderId,$phoneNumber)
    {
			$jsondata['data']='';
            $jsondata['data'].='<qm:orderInfo>';
            $jsondata['data'].='<qm:partnerOrderId>'.$partnerorderId.'</qm:partnerOrderId>';
            $jsondata['data'].='<qm:phoneNumber>'.$phoneNumber.'</qm:phoneNumber>'; 
            $jsondata['data'].='</qm:orderInfo>';	
			//只需要构造body内容 传递method方法就可以了
            $method = 'sendOrderEticket';
			$products=$this->requestxml($method,$jsondata['data']);
            //返回数组
            return $products;	
	}
    
    
    /**
     * 退款申请  
     * partnerorderId   畅游通的订单  ID
     * refundSeq        退款流水号，用于标记每一笔退款，自定义(唯一)
     * orderPrice       原始订单金额单位：分
     * orderQuantity    原始订单票数
     * refundQuantity   退款票数
     * orderRefundPrice 订单退款的金额 单位：分
     * orderRefundCharge 订单退款的手续费总额 单位：分
     * refundExplain    退款说明
     *
     * $data[]             数组
     * $tourist_name          游玩人姓名
     * $credentials_number       游玩人证件号
     * $credentials_type    游玩人证件类型  身份证:ID_CARD, 护照:HUZHAO,台胞证  : TAIBAO港澳通行证: GANGAO其它：OTHER配合credentials生效
     * 
     * Response返回
     * [header]['code']      编码
     * [header]['describe']  说明
     */  
	public function applyOrderRefundByUser($partnerorderId,$refundSeq,$orderPrice,$orderQuantity,$refundQuantity,$orderRefundPrice,$orderRefundCharge,$refundExplain,$data)
    {
			$jsondata['data']='';
            
            $jsondata['data'].='<qm:orderInfo>';
            $jsondata['data'].='<qm:partnerorderId>'.$partnerorderId.'</qm:partnerorderId>';
            $jsondata['data'].='<qm:refundSeq>'.$refundSeq.'</qm:refundSeq>';
            $jsondata['data'].='<qm:orderPrice>'.$orderPrice.'</qm:orderPrice>';
            $jsondata['data'].='<qm:orderQuantity>'.$orderQuantity.'</qm:orderQuantity>';
            $jsondata['data'].='<qm:refundQuantity>'.$refundQuantity.'</qm:refundQuantity>';
            $jsondata['data'].='<qm:orderRefundPrice>'.$orderRefundPrice.'</qm:orderRefundPrice>';
            $jsondata['data'].='<qm:orderRefundCharge>'.$orderRefundCharge.'</qm:orderRefundCharge>';
            $jsondata['data'].='<qm:refundExplain>'.$refundExplain.'</qm:refundExplain>';
            $jsondata['data'].='</qm:orderInfo>';
            
            //遍历实名用户
            if(count($data)>0 )
            {
                foreach($data as $v)
                {
                  $jsondata['data'].='<qm:person>';
                  $jsondata['data'].='<qm:name>'.$v['tourist_name'].'</qm:name>';
                  $jsondata['data'].='<qm:credentials>'.$v['credentials_number'].'</qm:credentials>';
                  $jsondata['data'].='<qm:credentialsType>'.$v['credentials_type'].'</qm:credentialsType>';
                  $jsondata['data'].='</qm:person>';  
                }  
            }
            else
            {
                  $jsondata['data'].='<qm:person>';
                  $jsondata['data'].='<qm:name></qm:name>';
                  $jsondata['data'].='<qm:credentials></qm:credentials>';
                  $jsondata['data'].='<qm:credentialsType></qm:credentialsType>';
                  $jsondata['data'].='</qm:person>';   
            } 
           
 	        
			//只需要构造body内容 传递method方法就可以了
            $method = 'applyOrderRefundByUser';
			$products=$this->requestxml($method,$jsondata['data']);
            //返回数组
            return $products;	
	}
    
    /*************************************************************异步返回接口   需要我们些接口给票务中心**************************************************************************************************/
    /**
     * 4.1出票通知 (异步接口)
     * 1．畅游通调用此接口通知OTA订单出票状态
     * 2．订单状态为PREPAY_ORDER_PRINT_FAILED时，畅游通会将账户余额返还，我们需要取消订单，退款给游客；
     * partnerorderId  畅游通生成的订单  ID
     * otaorderId      订单  ID
     * orderStatus     订单状态  PREPAY_ORDER_PRINT_FAILED预付：出票失败  PREPAY_ORDER_PRINT_SUCCESS预付：出票成功
     * Response返回
     * message          结果返回信息
     */  
	public function noticeOrderPrintSuccess($partnerorderId,$otaorderId,$orderStatus)
    {
			$jsondata['data']='';
            $jsondata['data'].='<qm:orderInfo>';
            $jsondata['data'].='<qm:partnerOrderId>'.$partnerorderId.'</qm:partnerOrderId>';
            $jsondata['data'].='<qm:otaorderId>'.$otaorderId.'</qm:otaorderId>'; 
            $jsondata['data'].='<qm:orderStatus>'.$orderStatus.'</qm:orderStatus>';
            $jsondata['data'].='</qm:orderInfo>';	
			//只需要构造body内容 传递method方法就可以了
            $method = 'noticeOrderPrintSuccess';
			$products=$this->requestxml($method,$jsondata['data']);
            //返回数组
            return $products;	
	}
    
   /**
     * 4.2用户消费通知 (异步接口)
     * 1.用户消费后畅游通主动调用此接口通知我们
     * 2.支持非实时的通知
     * partnerorderId  畅游通生成的订单  ID
     * orderQuantity   原始订单总票数
     * useQuantity     已消费票数 累计的消费张数
     * consumeInfo     电子票消费信息
     * Response返回
     * message          结果返回信息
     */  
	public function noticeOrderConsumed($partnerorderId,$orderQuantity,$useQuantity,$consumeInfo)
    {
			$jsondata['data']='';
            $jsondata['data'].='<qm:orderInfo>';
            $jsondata['data'].='<qm:partnerOrderId>'.$partnerorderId.'</qm:partnerOrderId>';
            $jsondata['data'].='<qm:orderQuantity>'.$orderQuantity.'</qm:orderQuantity>'; 
            $jsondata['data'].='<qm:useQuantity>'.$useQuantity.'</qm:useQuantity>';
            $jsondata['data'].='<qm:consumeInfo>'.$consumeInfo.'</qm:consumeInfo>';
            $jsondata['data'].='</qm:orderInfo>';	
			//只需要构造body内容 传递method方法就可以了
            $method = 'noticeOrderConsumed';
			$products=$this->requestxml($method,$jsondata['data']);
            //返回数组
            return $products;
	}  
    
     /**
     * 4.3 通知OTA申请退款结果
     * 1．供应商审核完用户的退款后，畅游通主动调用此接口向  OTA通知审核结果
     * 2．该接口和  ApplyOrderRefundByUser配套使用
     * partnerorderId  畅游通生成的订单  ID
     * refundSeq       退款流水号，用于标记每一笔退款，自定义
     * orderQuantity   原始订单票数
     * refundResult    退款审核结果 APPROVE：同意退款 REJECT：拒绝退款
     * refundQuantity  退款票数
     * orderRefundPrice  退款金额
     * orderRefundCharge 退款手续费
     * Response返回
     * message          结果返回信息
     */  
	public function noticeOrderRefundApproveResult($partnerorderId,$refundSeq,$orderQuantity,$refundResult,$refundQuantity,$orderRefundPrice,$orderRefundCharge)
    {
			$jsondata['data']='';
            $jsondata['data'].='<qm:orderInfo>';
            $jsondata['data'].='<qm:partnerOrderId>'.$partnerorderId.'</qm:partnerOrderId>';
            $jsondata['data'].='<qm:refundSeq>'.$refundSeq.'</qm:refundSeq>'; 
            $jsondata['data'].='<qm:orderQuantity>'.$orderQuantity.'</qm:orderQuantity>';
            $jsondata['data'].='<qm:refundResult>'.$refundResult.'</qm:refundResult>';
            $jsondata['data'].='<qm:refundQuantity>'.$refundQuantity.'</qm:refundQuantity>'; 
            $jsondata['data'].='<qm:orderRefundPrice>'.$orderRefundPrice.'</qm:orderRefundPrice>';
            $jsondata['data'].='<qm:orderRefundCharge>'.$orderRefundCharge.'</qm:orderRefundCharge>';
            $jsondata['data'].='</qm:orderInfo>';	
			//只需要构造body内容 传递method方法就可以了
            $method = 'noticeOrderRefundApproveResult';
			$products=$this->requestxml($method,$jsondata['data']);
            //返回数组
            return $products;
	}   
    
    
 
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //提交方法POST
    function curl_post($url,$post_data)
    { 
       //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //超时时间
        curl_setopt($curl, CURLOPT_TIMEOUT,10);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        /*
        $post_data = array(
            "username" => "coder",
            "password" => "12345"
            );*/
        //curl_setopt($curl, CURLOPT_UPLOAD, 1); 
        //关闭SSL认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		//提交数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        //执行命令
        $data = curl_exec($curl);

        if(curl_errno($curl))
		{
           // return  'errno'.curl_error($curl);
           return 0;
        }
		else
		{
			//显示获得的数据
		   return $data;
		}
         
    }
    
    
    	
}



