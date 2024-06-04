<?php
//set_time_limit(0);
class RepastApi {
	public $salt="dLkumPfO";
	public $flag="DJY-3653";
	public $url="http://124.90.43.59:3025";
     
     

  	public function __construct($data)
    {
		$this->salt  = $data['salt'];
		$this->flag  = $data['flag'];
		$this->url   = $data['url']; 
	} 
 
    /**
     * 获取产品 OTA主动获取餐饮产品信息的接口
     * 注: book_person_type  是否需要游客信息  
     * CONTACT_PERSON 只需要预订人信息
     * CONTACT_PERSON_AND_VISIT_PERSON：需要游客和预订人信息
     * 返回json
     * code： 0错误 1正确
     * msg： 错误返回错误原因  正确返回数据 
     */ 
	public function cateringGetProduct_all($page_num)
    {
            // 临时设置最大内存占用为3G
            ini_set('memory_limit','3072M');    
            // 设置脚本最大执行时间 为0 永不过期
            set_time_limit(0);  
            $request_body   = array('method'=>'ALL','product_type'=>'CATERING_PRODUCT','page_num'=>$page_num,'page_size'=>99);
            $request_header = array('method'=>'cateringGetProduct','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data);
            $return_data    = $this->curl_post($this->url.'/catering/cateringGetProduct/cateringGetProduct',$post_data);
            //echo '<pre>';  var_dump($return_data);exit();
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     if($response_header=='8200')
                     {
                       //echo '<pre>';var_dump($return_data_array);exit();
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }  
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}
    
    
    
    /**
     * 获取产品 单个产品信息 
     * 注: book_person_type  是否需要游客信息  
     * CONTACT_PERSON 只需要预订人信息
     * CONTACT_PERSON_AND_VISIT_PERSON：需要游客和预订人信息
     * $product_num  编号
     * 返回json
     * code： 0错误 1正确
     * msg： 错误返回错误原因  正确返回数据 
     */  
	public function cateringGetProduct_one($product_num)
    {
      
            $request_body   = array('method'=>'SINGLE',"product_num"=>$product_num,'product_type'=>'CATERING_PRODUCT','page_num'=>1,'page_size'=>20);
            $request_header = array('method'=>'cateringGetProduct','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data);
            $return_data    = $this->curl_post($this->url.'/catering/cateringGetProduct/cateringGetProduct',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     if($response_header=='8200')
                     { 
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }   
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}
    
    
               
    /**
     * 3.8.	OTA主动获取全部景区POI酒店信息接口
     * 返回json （景区基本信息）
     * code： 0错误 1正确
     * msg： 错误返回错误原因  正确返回数据 
     */  
	public function cateringGetPoi_all()
    {
      
            $request_body   = array('method'=>'ALL','poi_type'=>'SCENIC_CONSUME_TYPE','page_num'=>1,'page_size'=>99);
            $request_header = array('method'=>'cateringGetPoi','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data);
            $return_data    = $this->curl_post($this->url.'/catering/cateringGetPoi/cateringGetPoi',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     if($response_header=='8200')
                     { 
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }   
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}
    
    
        
    /**
     * 3.8.	OTA主动获取门票 单个 景区POI酒店信息接口
     * poi_code  poi编码，method为SINGLE时必填
     * 返回json （景区基本信息）
     * code： 0错误 1正确
     * msg： 错误返回错误原因  正确返回数据 
     */  
	public function cateringGetPoi_one($poi_code)
    {
      
            $request_body   = array('method'=>'SINGLE',"poi_code"=>$poi_code,'poi_type'=>'SCENIC_CONSUME_TYPE','page_num'=>1,'page_size'=>20);
            $request_header = array('method'=>'cateringGetPoi','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data);
            $return_data    = $this->curl_post($this->url.'/catering/cateringGetPoi/cateringGetPoi',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     if($response_header=='8200')
                     { 
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }   
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}
    
    /**
     * OTA餐饮下单（预订）请求的接口
        distributor_order_num       OTA的订单号，不同订单不能重复
        total_price                 订单总价
        product_name                产品名称
        poi_name                    餐馆名称
        product_num                 产品编号
        product_type                产品类型CATERING_PRODUCT
        purchase_count              购买数量
        subscriber_name             预订人姓名
        subscriber_phone_num        预订人手机号
        operation_settlement_price  销售单价
        unit_price                  OTA售卖单价
 
     * 返回json
     * code： 0错误 1正确
     * msg： 错误返回错误原因  正确返回数据
     * 
     * order_num     订单编号
     * order_status  订单状态 2：下单失败，4：待付款）
     */  
	public function cateringTakeOrder($data)
    {
      
            $request_body   = $data;
            $request_header = array('method'=>'cateringTakeOrder','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $t_data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //echo '<pre>';var_dump($t_data);
            //加密提交数据
            $post_data      = $this->data_encode($t_data);
            $return_data    = $this->curl_post($this->url.'/catering/cateringTakeOrder/cateringTakeOrder',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               //echo '<pre>';var_dump($return_data);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     //echo '<pre>';var_dump($return_data_array);exit();
                     $response_header=$return_data_array['response_header']['code'];
                     if($response_header=='8200')
                     { 
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	} 
    
    
    
    
   /**
     * OTA餐饮支付请求（已支付订单验证）的接口
     * data['order_num']   订单编号
     * data['payment_way'] 支付方式（PRECHARGE_PAYMENT：预充值支付），目前只支持预充值支付  
     * data['user_payment_way'] 用户支付方式，非必填（WECHAT_PAY：微信支付，ALIPAY：支付宝支付，OTHER_PAY：其他支付方式）
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
     * order_num 订单编号
     * order_status  当前订单状态（4：待支付，6：支付失败，7：已支付）
     **/  
  	public function cateringVerifyPaymentOrder($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'cateringVerifyPaymentOrder','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            // echo '<pre>';var_dump($data);
            //加密提交数据
            $post_data      = $this->data_encode($data);
            //echo '<pre>';var_dump($post_data);exit();
            $url = $this->url.'/catering/cateringVerifyPaymentOrder/cateringVerifyPaymentOrder';
            //echo '<pre>';var_dump($url);exit();
            $return_data    = $this->curl_post($url,$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
                     if($response_header=='8200')
                     { 
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }   
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}  
    
     
    
     /**
     * OTA取消餐饮订单的接口
     * data['order_num']   订单编号  
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
     * order_num 订单编号
     * order_status  当前订单状态：3已取消
     **/   
	public function cateringCancelOrder($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'cateringCancelOrder','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data);
            $return_data    = $this->curl_post($this->url.'/catering/cateringCancelOrder/cateringCancelOrder',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
                     if($response_header=='8200')
                     { 
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }  
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}   
    
    
    
    
       
     /**
     * OTA请求（重）发餐饮入店凭证的接口
     * data['order_num']    订单编号  
     * data['phone_number'] 发送的手机号码，非必传，默认为
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
     * message_code 入园凭证（取票密码，组合产品为多个）
     **/ 
	public function cateringSendOrderEticket($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'cateringSendOrderEticket','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data);
            $return_data    = $this->curl_post($this->url.'/catering/cateringSendOrderEticket/cateringSendOrderEticket',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
                     if($response_header=='8200')
                     { 
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }  
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}   
     
    
    
    /**
     * OTA获取餐饮订单详细信息的接口
     * data['order_num']    订单编号  
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
        order_status    订单状态
        purchase_count  订单购买数量
        order_num       订单编号
        product_name    产品名称
        message_code    入店凭证（电子票）,成功支付后生成
        eticket_status  电子票状态，0表示未发送，1表示已发送
        consume_count   累计消费数量
        consume_info    消费信息，非必传
        left_count      剩余数量
      **/   
	public function cateringGetOrderInfo($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'cateringGetOrderInfo','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data);
            $return_data    = $this->curl_post($this->url.'/catering/cateringGetOrderInfo/cateringGetOrderInfo',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
                     if($response_header=='8200')
                     { 
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }  
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}
    
    
    
    /**
     * OTA餐饮退款申请的接口（异步请求）
        actually_refund   退款金额
        order_num         订单编号
        purchase_count    订单购买数量
        refund_charge     退款手续费
        refund_explain    退款说明，非必传
        refund_seq        退款流水号
        total_price       订单总金额
        unsubscribe_count 退订数量（目前只支持全部退款，不支持部分退款）
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
     * order_num    订单编号
     * order_status 订单状态（8：退款失败，9：退款审核中)
     **/   
	public function cateringRefundOrder($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'cateringRefundOrder','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data);
            $return_data    = $this->curl_post($this->url.'/catering/cateringRefundOrder/cateringRefundOrder',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
                     if($response_header=='8200')
                     { 
                       return $this->return_data(1,$return_data_array);   
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message'];
                        return $this->return_data(0,$msg);  
                     }  
                  }
                  else
                  {
                     $msg = 'API接口验证签名失败！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'API接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
               $msg = 'API接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}  
    
    
    

    /**
     *将unicode转为字符串(中文同样实用) 
     */
    function unicode_decode($name)
    {
      $json = '{"str":"'.$name.'"}';
      $arr = json_decode($json,true);
      if(empty($arr)) return '';
      return $arr['str'];
    }
    
    /**
     *返回值 
     * 
     */
    function return_data($code,$msg)
    { 
        $array = array('code'=>$code,'msg'=>$msg);
        //$data_json   = json_encode($array); 
        return  $array;
    }
    

    
    /**
    *验密
    *接口返回的base64  return_data字符串
    * 返回数据return_data+salt验证cipherText
    **/
    function data_decode($return_data,$return_cipherText)
    { 
       $cipherText  = hash("sha256", $return_data.$this->salt);
       if($return_cipherText == $cipherText)
        return  true;
       else
        return  false;
    }
    
    /**
    *解密
    *接口返回的base64  return_data字符串
    * 返回数据return_data+salt验证cipherText
    **/
    function decode_decode($return_data)
    {  
      $base64_decode = base64_decode($return_data);
      
      $data =  json_decode($base64_decode,true);
      if($data!='')
      {
        return $data;
      }
      else
      {
        return 0;
      } 
      
    }
    
    
    /**
    *解密2
    *接口返回的base64  return_data字符串
    * 返回数据return_data+salt验证cipherText
    **/
    function decode_decode2($return_data)
    {  
      $base64_decode = base64_decode($return_data);
      $data =  json_decode($base64_decode,true);
      if($data!='')
      {
        return $data;
      }
      else
      {
        return 0;
      } 
      
    }
    
    
        /**
    *加密
    *$data数组
    * 返回提交数据
    **/
    function data_encode($data)
    { 
        
        $data_json   = json_encode($data,JSON_UNESCAPED_UNICODE);
        $data_base64 = base64_encode($data_json);
        
        $cipherText  = hash("sha256",$data_base64.$this->salt);
        $post_data   = json_encode(array('cipherText'=>$cipherText,'data'=>$data_base64));
    
        return  $post_data;
    }
    
    
    
    //数组转对象
    function arrayToObject($e)
    {
        if( gettype($e)!='array' ) return;
        foreach($e as $k=>$v){
            if( gettype($v)=='array' || getType($v)=='object' )
                $e[$k]=(object)$this->arrayToObject($v);
        }
        return (object)$e;
    }
    

    //提交方法POST
    function curl_post($url,$post_data)
    {   
        $headers = array("Content-type:application/json;charset=utf-8", "Accept: application/json", "Cache-Control: no-cache","Pragma: no-cache");
       //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers); 
        //超时时间
        curl_setopt($curl, CURLOPT_TIMEOUT,300);
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
            return  'errno'.curl_error($curl);
           return 0;
        }
		else
		{
			//显示获得的数据
		   return $data;
		}
         
    }
    
    
    	
}



