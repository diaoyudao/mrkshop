<?php
/**
 * 全局控制器
 * @author Caokoo
 */
namespace Core\Controller;
use Think\Controller;
use Common\Util\Validate;
class CoreController extends Controller{
    //Web Service 回调对象
    protected $_response;
    //用户Id
    protected $customerId;
    //商家ID
    protected $merchantId;
    
    protected function _initialize(){
        header('Content-Type:text/html;Charset=UTF-8');
        $this->customerId = session('uid');
        $this->merchantId = session('merchantId');
        if(APP_DEBUG){
            C('SHOW_PAGE_TRACE',true);
        }
//        $this->renderCustomer();
    }
    
    /**
     * customer 渲染
     */
    protected function renderCustomer(){
        $customer = array();
        if($this->customerId){
            $parameters['m'] = 'customer';
            $parameters['a'] = 'get_customer_info';
            $response = $this->query($parameters);
            if($response['result'] == 'success' && $response['code']== '0x0000'){
                $customer = $response['info'];
            }
        }
        $this->assign('customer',$customer);
    }
    
    /**
     * API 执行
     * @param array $parameters
     */
    protected function query($parameters,$debug=false){
        if(!$this->_response){
            $this->_response = new \Common\Util\Response();
        }
        if($debug){
            return $this->_response->debug()->put($parameters);
        }
        return $this->_response->put($parameters);
    }
    
    /**
     * 获取当前登录用户信息
     */
    protected function getCustomerInfo(){
        $customerInfo = array();
        if(!$this->customerId){
            return $customerInfo;
        }
        $parameters['m'] = 'customer';
        $parameters['a'] = 'get_customer_info';
        $response = $this->query($parameters);
        if($response['result']!='success' || $response['code']!='0x0000'){
            return $customerInfo;
        }
        return $response['info'];
    }
    
    /**
     * Action跳转(URL重定向） 支持指定模块和延时跳转
     * @access protected
     * @param string $url 跳转的URL表达式
     * @param array $params 其它URL参数
     * @param integer $delay 延时跳转的时间 单位为秒
     * @param string $msg 跳转提示信息
     * @return void
     */
    protected function redirect($url,$params=array(),$delay=0,$msg='') {
        redirect($url,$delay,$msg);
    }
    
    /**
     * 
     * @param Array $options
     * $options['mobile']
     * $options['message']
     * $options['function']
     * @param string $mobile mobile
     */
    protected function sendMobileMessage($options,$mobile=null){
        if($mobile){
            $options['mobile'] = $mobile;
        }
        if(!isset($options['mobile'])){
            return false;
        }
        if(!isset($options['message'])){
            return false;
        }
        if(!isset($options['function'])){
            return false;
        }
        if(!Validate::mobi($options['mobile'])){
            return false;
        }
        extract($options);
        $mobileHelper = new \Common\Util\Mobile();
        return $mobileHelper->$function();
    }
}