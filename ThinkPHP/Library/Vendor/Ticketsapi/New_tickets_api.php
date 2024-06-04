<?php
//set_time_limit(0);
class TicketApi {
	public $salt ="dLkumPfO";
	public $flag ="DJY-3653";
	public $url  ="http://124.90.43.59:3005";	
     
     
  	public function __construct($data)
    {
		$this->salt  = $data['salt'];
		$this->flag  = $data['flag'];
		$this->url   = $data['url']; 
	}
     
    /**
     * 获取产品 全部产品信息
     * 注: book_person_type  是否需要游客信息  
     * CONTACT_PERSON 只需要预订人信息
     * CONTACT_PERSON_AND_VISIT_PERSON：需要游客和预订人信息
     * 返回json
     * code： 0错误 1正确
     * msg： 错误返回错误原因  正确返回数据 
     */  
	public function getProduct_all()
    {
            // 临时设置最大内存占用为3G
            ini_set('memory_limit','3072M');    
            // 设置脚本最大执行时间 为0 永不过期
            set_time_limit(0);  
            $request_body   = array('method'=>'ALL','product_type'=>'TICKET_PRODUCT','page_num'=>1,'page_size'=>99);
            $request_header = array('method'=>'getProduct','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data,$this->salt);
            $return_data    = $this->curl_post($this->url.'/current/getProduct/getProduct',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     $response_body  =$return_data_array['response_body'];
                     if($response_header=='8200')
                     {
                          return $data = array('code'=>1,'msg'=>$response_body); 
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message']; 
                        return $data = array('code'=>0,'msg'=>$msg); 
                     }  
                  }
                  else
                  { 
                     return $data = array('code'=>0,'msg'=>'OTA接口验证签名错误！'); 
                  } 
                   
               }
               else
               { 
                 return $data = array('code'=>0,'msg'=>'OTA接口返回数据解析失败！'); 
               }
            }
            else
            { 
                return $data = array('code'=>0,'msg'=>'OTA接口链接请求超时！'); 
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
	public function getProduct_one($product_num)
    {
      
            $request_body   = array('method'=>'SINGLE',"product_num"=>$product_num,'product_type'=>'TICKET_PRODUCT','page_num'=>1,'page_size'=>20);
            //dump($request_body);
            $request_header = array('method'=>'getProduct','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
          
            //加密提交数据
            $post_data      = $this->data_encode($data,$this->salt);
            $return_data    = $this->curl_post($this->url.'/current/getProduct/getProduct',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               //dump($return_data);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     $response_body  =$return_data_array['response_body'];
                     if($response_header=='8200')
                     {
                          return $data = array('code'=>1,'msg'=>$response_body); 
                     }
                     else
                     {
                        $msg = $return_data_array['response_header']['message']; 
                        return $data = array('code'=>0,'msg'=>$msg); 
                     } 
                       
                  }
                  else
                  {
                     $msg = 'OTA接口验证签名错误！';
                     return $this->return_data(0,$msg);  
                  } 
                   
               }
               else
               {
                 $msg = 'OTA接口返回数据解析失败！';
                 return $this->return_data(0,$msg);  
               }
            }
            else
            {
                $msg = 'OTA接口链接失败！';
               return $this->return_data(0,$msg);
            }
	}
    
    
    
    /***************************景区POI***************************************************************************/
    
       /**
     * 3.8.	OTA主动获取全部景区POI信息接口
     * 返回json （景区基本信息）
     * code： 0错误 1正确
     * msg： 错误返回错误原因  正确返回数据 
     */  
	public function ticketGetPoi_all()
    {
      
            $request_body   = array('method'=>'ALL','poi_type'=>'SCENIC_CONSUME_TYPE','page_num'=>1,'page_size'=>99);
            $request_header = array('method'=>'ticketGetPoi','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data,$this->salt);
            $return_data    = $this->curl_post($this->url.'/current/ticketGetPoi/ticketGetPoi',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               //dump($return_data);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                    // dump($return_data_array);
                     $response_header=$return_data_array['response_header']['code'];
                     $response_body  =$return_data_array['response_body'];
                     if($response_header=='8200')
                     {
                       return $data = array('code'=>1,'msg'=>$response_body);  
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
     * 3.8.	OTA主动获取门票 单个 景区POI信息接口
     * poi_code  poi编码，method为SINGLE时必填
     * 返回json （景区基本信息）
     * code： 0错误 1正确
     * msg： 错误返回错误原因  正确返回数据 
     */  
	public function ticketGetPoi_one($poi_code)
    {
      
            $request_body   = array('method'=>'SINGLE',"poi_code"=>$poi_code,'poi_type'=>'SCENIC_CONSUME_TYPE','page_num'=>1,'page_size'=>20);
            $request_header = array('method'=>'ticketGetPoi','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data,$this->salt);
            $return_data    = $this->curl_post($this->url.'/current/ticketGetPoi/ticketGetPoi',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     $response_body  =$return_data_array['response_body'];
                     if($response_header=='8200')
                     {
                       return $data = array('code'=>1,'msg'=>$response_body);  
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
     * OTA下单（预订）请求的接口
     * 注: book_person_type  是否需要游客信息  
     * CONTACT_PERSON 只需要预订人信息
     * CONTACT_PERSON_AND_VISIT_PERSON：需要游客和预订人信息
     * visit_person_required_for_quantity
     * 每几个游客共享一个游客信息（如1，即每个游客都要填写游客信息，仅bookPersonType为CONTACT_PERSON_AND_VISI
     * 数组data 
     * data['distributor_order_num']      OTA的订单号，不同订单不能重复
     * data['operation_settlement_price'] OTA与平台的商品结算价
     * data['product_name']   产品名称
     * data['product_num']    产品编号
     * data['product_type']   产品类型（TICKET_PRODUCT:门票，COMBINATIONPRODUCT：组合产品）
     * data['purchase_count'] 购买数量
     * data['subscribe_start_date']           预订开始时间，即预订日期:2018-05-09
     * data['subscriber_identification_num']  预订人证件号码
     * data['subscriber_identification_type'] 预订人证件类型（ID_CARD：身份证），暂时只支持身份证，非必传信息
     * data['subscriber_name']      预订人姓名
     * data['subscriber_phone_num'] 预订人手机号码
     * data['total_price']          订单总价(元)
     * tourist[]  游客，只有当前产品bookPersonType为CONTACT_PERSON_AND_VISIT_PERSON时，才需要；
     * data['tourist[0][tourist_identification_num]']   游客信息，游客证件号码
     * data['tourist[0][tourist_identification_type]']  游客信息，游客证件类型（ID_CARD：身份证），暂时只支持身份证，非必传信息
     * data['tourist[0][tourist_name]']       游客信息，游客姓名
     * data['tourist[0][tourist_phone_num]']  游客信息，游客手机号
     * data['unit_price'] OTA售卖单价(元)
     * 返回json
     * code： 0错误 1正确
     * msg： 错误返回错误原因  正确返回数据
     * 
     * order_num     订单编号
     * order_status  订单状态 2：下单失败，4：待付款）
     */  
	public function takeOrder($data)
    {
      
            $request_body   = $data;
            $request_header = array('method'=>'takeOrder','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $t_data         = array('request_body'=>$request_body,'request_header'=>$request_header);
            //echo '<pre>';var_dump($t_data);
            //加密提交数据
            $post_data      = $this->data_encode($t_data,$this->salt);
            $return_data    = $this->curl_post($this->url.'/current/takeOrder/takeOrder',$post_data);
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
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     //echo '<pre>';var_dump($return_data_array);exit();
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
     * OTA支付请求（已支付订单验证）的接口  (接口返回速度慢)
     * data['order_num']   订单编号
     * data['payment_way'] 支付方式（PRECHARGE_PAYMENT：预充值支付），目前只支持预充值支付  
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
     * order_num 订单编号
     * order_status  当前订单状态（4：待支付，6：支付失败，7：已支付）
     * single_ticket_infos  array[object] 支付成功时的单票信息，支付成功并且底层为票务系统时返回
     * ticket_no         票务消费二维码
     * ticket_single_id  票务单票唯一编号
     * name        游客姓名，实名制时才返回
     * tel_phone   游客手机号，实名制且下单传了该字段时才返回
     * id_card_no  游客身份证
     **/  
  	public function verifyPaymentOrder($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'verifyPaymentOrder','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            // echo '<pre>';var_dump($data);
            //加密提交数据
            $post_data      = $this->data_encode($data,$this->salt);
            //echo '<pre>';var_dump($post_data);exit();
            $url = $this->url.'/current/verifyPaymentOrder/verifyPaymentOrder';
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
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
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
     * OTA取消订单的接口
     * data['order_num']   订单编号  
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
     * order_num 订单编号
     * order_status  当前订单状态：3已取消
     **/   
	public function cancelOrder($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'cancelOrder','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data,$this->salt);
            $return_data    = $this->curl_post($this->url.'/current/cancelOrder/cancelOrder',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
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
     * OTA请求（重）发入园凭证的接口
     * data['order_num']    订单编号  
     * data['phone_number'] 发送的手机号码，非必传，默认为
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
     * message_code 入园凭证（取票密码，组合产品为多个）
     **/   
	public function sendOrderEticket($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'sendOrderEticket','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data,$this->salt);
            $return_data    = $this->curl_post($this->url.'/current/sendOrderEticket/sendOrderEticket',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
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
     * OTA获取订单详细信息的接口
     * data['order_num']    订单编号  
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
     * order_status   订单状态
     * purchase_count 订单购买数量
     * order_num      订单编号
     * product_name   产品名称（主）
     ***** info  array[object] 订单信息
     * eticket_info     电子票信息（当底层为票务时，不返回此项，电子票信息在single_ticket_infos中列出）
     * eticket_status   电子票状态，0表示未发送，1表示已发送
     * consume_count    累计消费数量
     * product_name     产品名称（子）
     ***** single_ticket_infos   array[object] 单票信息集合，底层为票务时，返回此项
     * ticket_no          票务消费二维码
     * ticket_single_id   票务单票唯一编号
     * name               游客姓名，实名制时才返回
     * tel_phone          游客手机号，实名制且下单传了该字段时才返回
     * id_card_no         游客身份证号，实名制
     **/   
	public function getOrderInfo($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'getOrderInfo','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data,$this->salt);
            $return_data    = $this->curl_post($this->url.'/current/getOrderInfo/getOrderInfo',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
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
     * OTA退款申请的接口（异步请求）
     * data['order_num']        订单编号  
     * data['actually_refund']  退款金额
     * data['purchase_count']   订单购买数量
     * data['refund_charge']    退款手续费
     * data['refund_explain']   退款说明  非必填
     * data['refund_seq']       退款OTA流水号(唯一)
     * data['total_price']      订单总金额
     * data['unsubscribe_count']退订数量（目前只支持全部退款，不支持部分退款）
     * 返回json
     * code： 0错误 1正确
     * msg：  错误返回错误原因  正确返回数据array
     * order_num    订单编号
     * order_status 订单状态（8：退款失败，9：退款审核中)
     **/   
	public function refundOrder($data1)
    {
      
            $request_body   = $data1;
            $request_header = array('method'=>'refundOrder','ota_flag'=>"{$this->flag}",'version'=>'v0.0.1');
            $data           = array('request_body'=>$request_body,'request_header'=>$request_header);
            //加密提交数据
            $post_data      = $this->data_encode($data,$this->salt);
            $return_data    = $this->curl_post($this->url.'/current/refundOrder/refundOrder',$post_data);
            if($return_data != '0')
            {
               //json转换数组
               $return_data = json_decode($return_data,true);
               if($return_data!='')
               {
                  //验证
                  $data       = $return_data['data'];
                  $cipherText = $return_data['cipherText'];
                  $ischeck    = $this->data_decode($data,$cipherText,$this->salt);
                  if($ischeck)
                  {
                     //解密返回的数据
                     $data=base64_decode($data);
                     $return_data_array = json_decode($data,true);
                     $response_header=$return_data_array['response_header']['code'];
                     //echo '<pre>';var_dump($return_data_array);
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
     * OTA（异步请求返回方法）
     **/   
	public function ticket_asynchronous_return($isok,$api,$message,$salt)
    {
            //返回数据
            if($isok)
            {
                $response_body   = array("message"=>$message);
                $response_header = array("code"=>"8200","message"=>$message,"method"=>$api,'version'=>"v0.0.1");
                $data            = array("response_body"=>$response_body,"response_header"=>$response_header); 
            }
            else
            {
               $response_body   = array("message"=>$message);
               $response_header = array("code"=>"8500",'message'=>$message,"method"=>$api,"version"=>"v0.0.1");
               $data            = array("response_body"=>$response_body,"response_header"=>$response_header);
            }
            
           
            //加密提交数据
            return $post_data  = $this->data_encode2($data,$salt);
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
    function data_decode($return_data,$return_cipherText,$salt)
    { 
       $cipherText  = hash("sha256", $return_data.$salt);
       if($return_cipherText == $cipherText)
        return  true;
       else
        return  false;
    }
    
    /**
    *加密
    *$data数组
    * 返回提交数据
    **/
    function data_encode($data,$salt)
    { 
        $data_json   = json_encode($data);
        $data_base64 = base64_encode($data_json);
        $cipherText  = hash("sha256", $data_base64.$salt);
        $post_data   = json_encode(array('cipherText'=>$cipherText,'data'=>$data_base64));
        return  $post_data;
    }
    
    /**
    *加密2 (异步)
    *$data数组
    * 返回提交数据
    **/
    function data_encode2($data,$salt)
    { 
        //对象数组
        $data = $this->arrayToObject($data);
        
        $data_json   = json_encode($data,JSON_UNESCAPED_UNICODE);
        $data_base64 = base64_encode($data_json);
        $cipherText  = hash("sha256", $data_base64.$salt);
        $post_data   = json_encode(array('cipherText'=>$cipherText,'data'=>$data_base64));
        return  $post_data;
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



