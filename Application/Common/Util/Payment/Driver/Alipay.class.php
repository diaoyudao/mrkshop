<?php
/**
 * 支付宝支付
 */
namespace Common\Util\Payment\Driver;
use Common\Util\Payment\Driver;
use Common\Util\Payment\Driver\Alipay\Helper;
class Alipay extends Driver{
    public function __construct($config = array()) {
        if (!empty($config)) $this->setConfig($config);
        if ($this->config['service_type']==1) $this->config['service'] = 'trade_create_by_buyer';
        elseif($this->config['service_type']==2) $this->config['service'] = 'create_direct_pay_by_user';
        else $this->config['service'] = 'create_partner_trade_by_buyer';
        $this->config['gateway_url'] = 'https://www.alipay.com/cooperate/gateway.do?_input_charset='.C('DEFAULT_CHARSET');
        $this->config['gateway_method'] = 'POST';
        $alipayConfig = C('ALIPAY_CONFIG');
        $this->config['notify_url'] = $alipayConfig['notify_url'];
        $this->config['return_url'] = $alipayConfig['return_url'];
    }
    
    public function getPrepareData() {
        $prepare_data['service'] = $this->config['service'];
        $prepare_data['payment_type'] = '1';
        $prepare_data['seller_email'] = $this->config['alipay_account'];
        $prepare_data['partner'] = $this->config['alipay_partner'];
        $prepare_data['_input_charset'] = C('DEFAULT_CHARSET');
        $prepare_data['notify_url'] = $this->config['notify_url'];
        $prepare_data['return_url'] = $this->config['return_url'];
    
        // 商品信息
        $prepare_data['subject'] = $this->productInfo['name'];
        $prepare_data['price'] = $this->productInfo['price'];
        if (array_key_exists('url', $this->productInfo)) $prepare_data['show_url'] = $this->productInfo['url'];
        $prepare_data['body'] = $this->productInfo['body'];
    
        //订单信息
        $prepare_data['out_trade_no'] = $this->orderInfo['id'];
        $prepare_data['quantity'] = $this->orderInfo['quantity'];
    
        // 物流信息
        if($this->config['service'] == 'create_partner_trade_by_buyer' || $this->config['service'] == 'trade_create_by_buyer') {
            $prepare_data['logistics_type'] = 'EXPRESS';
            $prepare_data['logistics_fee'] = '0.00';
            $prepare_data['logistics_payment'] = 'SELLER_PAY';
        }
        //买家信息
        $prepare_data['buyer_email'] = $this->orderInfo['buyer_email'];
    
        $prepare_data = Helper::arg_sort($prepare_data);
        // 数字签名
        $prepare_data['sign'] = Helper::build_mysign($prepare_data,$this->config['alipay_key'],'MD5');
        return $prepare_data;
    }
    
    /**
     * GET接收数据
     * 状态码说明  （0 交易完成 1 交易失败 2 交易超时 3 交易处理中 4 交易未支付5交易取消6交易发生错误）
     */
    public function receive() {
        $receive_sign = $_GET['sign'];
        $receive_data = $this->filterParameter($_GET);
        $receive_data = Helper::arg_sort($receive_data);
        if ($receive_data) {
            $verify_result = $this->get_verify('http://notify.alipay.com/trade/notify_query.do?partner=' . $this->config['alipay_partner'] . '&notify_id=' . $receive_data['notify_id']);
            if (preg_match('/true$/i', $verify_result))
            {
                $sign = '';
                $sign = Helper::build_mysign($receive_data,$this->config['alipay_key'],'MD5');
                if ($sign != $receive_sign)
                {
                    error_log(date('m-d H:i:s',NOW_TIME).'| GET: signature is bad |'."\r\n", 3, CACHE_PATH.'pay_error_log.php');
                    E('无效签名');
                    return false;
                }
                else
                {
                    $return_data['increment_id'] = $receive_data['out_trade_no'];
                    $return_data['payment_total'] = $receive_data['total_fee'];
                    $return_data['paymented_at'] = strtotime($receive_data['notify_time']);
                    $return_data['payment_method'] = 'online';
                    $return_data['payment_code'] = 'alipay';
                    $return_data['payment_trade_no'] = $receive_data['trade_no'];
                    switch ($receive_data['trade_status'])
                    {
                        case 'WAIT_BUYER_PAY': $return_data['order_status'] = 'pending_payment'; break;
                        case 'WAIT_SELLER_SEND_GOODS': $return_data['order_status'] = 'pending_shipment'; break;
                        case 'WAIT_BUYER_CONFIRM_GOODS': $return_data['order_status'] = 'shiped'; break;
                        case 'TRADE_CLOSED': $return_data['order_status'] = 'closed'; break;
                        case 'TRADE_FINISHED': $return_data['order_status'] = 'complete'; break;
                        case 'TRADE_SUCCESS': $return_data['order_status'] = 'pending_shipment'; break;
                        default:
                            $return_data['order_status'] = 'pending_payment';
                    }
                    return $return_data;
                }
    
            }
            else
            {
                error_log(date('m-d H:i:s',NOW_TIME).'| GET: illegality notice : flase |'."\r\n", 3, CACHE_PATH.'pay_error_log.php');
                E('无效返回');
                return false;
            }
        } else {
            	
            error_log(date('m-d H:i:s',NOW_TIME).'| GET: no return |'."\r\n", 3, CACHE_PATH.'pay_error_log.php');
            E('无效返回');
            return false;
        }
    }
    
    /**
     * POST接收数据
     * 状态码说明  （0 交易完成 1 交易失败 2 交易超时 3 交易处理中 4 交易未支付 5交易取消6交易发生错误）
     */
    public function notify() {
        $receive_sign = $_POST['sign'];
        $receive_data = $this->filterParameter($_POST);
        $receive_data = Helper::arg_sort($receive_data);
        if ($receive_data) {
            $verify_result = $this->get_verify('http://notify.alipay.com/trade/notify_query.do?service=notify_verify&partner=' . $this->config['alipay_partner'] . '&notify_id=' . $receive_data['notify_id']);
            if (preg_match('/true$/i', $verify_result))
            {
                $sign = '';
                $sign = Helper::build_mysign($receive_data,$this->config['alipay_key'],'MD5');
                if ($sign != $receive_sign)
                {
                    error_log(date('m-d H:i:s',NOW_TIME).'| POST: signature is bad |'."\r\n", 3, CACHE_PATH.'pay_error_log.php');
                    return false;
                }
                else
                {
                    $return_data['increment_id'] = $receive_data['out_trade_no'];
                    $return_data['payment_total'] = $receive_data['total_fee'];
                    $return_data['paymented_at'] = strtotime($receive_data['notify_time']);
                    $return_data['payment_method'] = 'online';
                    $return_data['payment_code'] = 'alipay';
                    $return_data['payment_trade_no'] = $receive_data['trade_no'];
                    switch ($receive_data['trade_status'])
                    {
                        case 'WAIT_BUYER_PAY': $return_data['order_status'] = 'pending_payment'; break;
                        case 'WAIT_SELLER_SEND_GOODS': $return_data['order_status'] = 'pending_shipment'; break;
                        case 'WAIT_BUYER_CONFIRM_GOODS': $return_data['order_status'] = 'shiped'; break;
                        case 'TRADE_CLOSED': $return_data['order_status'] = 'closed'; break;
                        case 'TRADE_FINISHED': $return_data['order_status'] = 'complete'; break;
                        case 'TRADE_SUCCESS': $return_data['order_status'] = 'pending_shipment'; break;
                        default:
                            $return_data['order_status'] = 'pending_payment';
                    }
                    return $return_data;
                }
    
            }
            else
            {
                error_log(date('m-d H:i:s',NOW_TIME).'|  POST: illegality notice : flase |'."\r\n", 3, CACHE_PATH.'pay_error_log.php');
                return false;
            }
        } else {
            	
            error_log(date('m-d H:i:s',NOW_TIME).'|  POST: no post return |'."\r\n", 3, CACHE_PATH.'pay_error_log.php');
            return false;
        }
    }
     
    /**
     * 相应服务器应答状态
     * @param $result
     */
    public function response($result) {
        if (FALSE == $result) echo 'fail';
        else echo 'success';
    }
    
    /**
     * 返回字符过滤
     * @param $parameter
     */
    private function filterParameter($parameter)
    {
        $para = array();
        foreach ($parameter as $key => $value)
        {
            if ('sign' == $key || 'sign_type' == $key || '' == $value || 'm' == $key  || 'a' == $key  || 'c' == $key   || 'code' == $key ) continue;
            else $para[$key] = $value;
        }
        return $para;
    }
    
}