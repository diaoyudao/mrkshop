<?php
/**
 * Web service 支付组件驱动器
 * 支付组件抽象方法
 * @author Caokoo
 *
 */
namespace Common\Util\Payment;
abstract class Driver{
    //支付组件配置
    protected $config = array();
    //商品信息
    protected $productInfo = array();
    //用户信息
    protected $customerInfo = array();
    //订单信息
    protected $orderInfo = array();
    //物流信息
    protected $shippingInfo = array();
    /**
     * 配置项设置，可追加或删除
     * @warning 只能foreach,此处不能修改！！！
     * @param array $config
     * @return paymentabstract
     */
    public function setConfig($config){
        foreach ($config as $key=>$value){
            $this->config[$key] = $value;
        }
        return $this;
    }
    /**
     * 设置商品
     * @param array $productInfo
     */
    public function setProductInfo($productInfo){
        $this->productInfo = $productInfo;
        return $this;
    }
    /**
     * 设置用户信息
     * @param unknown $customerInfo
     */
    public function setCustomerInfo($customerInfo){
        $this->customerInfo = $customerInfo;
        return $this;
    }
    /**
     * 设置运费信息
     * @param unknown $shippingInfo
     */
    public function setShippingInfo($shippingInfo){
        $this->shippingInfo = $shippingInfo;
        return $this;
    }
    
    public function setOrderInfo($orderInfo){
        $this->orderInfo = $orderInfo;
        return $this;
    }
    
    public function getCode($button_attr = ''){
        if (strtoupper($this->config['gateway_method']) == 'POST') $str = '<form action="' . $this->config['gateway_url'] . '" method="POST">';
        else $str = '<form action="' . $this->config['gateway_url'] . '" method="GET" target="_blank">';
        $prepare_data = $this->getpreparedata();
        foreach ($prepare_data as $key => $value) $str .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
        $str .= '<input type="submit" ' . $button_attr . ' />';
        $str .= '</form>';
        return $str;
    }
    protected function get_verify($url,$time_out = "60") {
        $urlarr     = parse_url($url);
        $errno      = "";
        $errstr     = "";
        $transports = "";
        if($urlarr["scheme"] == "https") {
            $transports = "ssl://";
            $urlarr["port"] = "443";
        } else {
            $transports = "tcp://";
            $urlarr["port"] = "80";
        }
        $fp=@fsockopen($transports . $urlarr['host'],$urlarr['port'],$errno,$errstr,$time_out);
        if(!$fp) {
            die("ERROR: $errno - $errstr<br />\n");
        } else {
            fputs($fp, "POST ".$urlarr["path"]." HTTP/1.1\r\n");
            fputs($fp, "Host: ".$urlarr["host"]."\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: ".strlen($urlarr["query"])."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $urlarr["query"] . "\r\n\r\n");
            while(!feof($fp)) {
                $info[]=@fgets($fp, 1024);
            }
            fclose($fp);
            $info = implode(",",$info);
            return $info;
        }
    }
    /**
     * 返回参数接收
     * GET方式
     */
    abstract public function receive();
    /**
     * 返回参数接收
     * POST方式
     */
    abstract public function notify();
    /**
     * 返回结果
     * @param  $result
     */
    abstract public function response($result);
    /**
     * 预处理数据获取
     */
    abstract public function getPrepareData();
    
}