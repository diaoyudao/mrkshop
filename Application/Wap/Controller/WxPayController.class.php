<?php

namespace Wap\Controller;

use Think\Controller;

class WxPayController extends Controller {

    public $order_sn = ''; //商户订单号,注意要唯一！
    public $order_amount = ''; //订单金额
    public $subject = "订单支付"; //标题
    public $return_url = ''; //回调地址

    //在类初始化方法中，引入相关类库

    public function _initialize() {
        header("Content-type:text/html;charset=utf-8");
        $this->notify_url = SITE_URL . U('Wap/Payment/wx_notify'); //微信异步通知页面//"http://tying.m1ju.com/Home/Pay/wx_success.html";// 
        $this->return_url_success = SITE_URL . U('Wap/Payment/wx_success', array('orderid' => $this->order_sn, 'order_amount' => $this->order_amount)); //SITE_URL . "Wap/Pay/over";//wap端支付成功后跳转页面
        $this->return_url_error = SITE_URL . U('Wap/Pay/wx_error', array('orderid' => $this->order_sn, 'order_amount' => $this->order_amount)); //SITE_URL . "Wap/Pay/overerror";//wap端支付失败后跳转页面
        vendor('Wxpay.WxPayConfig');
        vendor('Wxpay.WechatAuth');
        vendor('Wxpay.WechatJSAPI');
    }

    //判断是否是微信客户端打开
    private function is_weixin() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    //获取二维码信息或者公众号支付
    public function dowxPay() {
        if (empty($this->order_sn) || empty($this->order_amount) || empty($this->subject)) {
            $this->error('缺少参数');
        }
        if (empty($this->return_url)) {
            $this->error('缺少回调地址');
        }
        //判断是否是客户端打开或者浏览器打开 
        if ($this->is_weixin()) {
            $code = I("code");
            $wxconfig = new \WxPayConfig();
            $appId = $wxconfig::WEB_APPID; //公众账号ID
            $appSecret = $wxconfig::APPSECRET; //商户号  
            \Think\Log::write('code:' . "{$appId}--{$appSecret}---{$code}----" . session('openid'), 'DEBUG', '', C('LOG_PATH') .date('Y-m-d', NOW_TIME). '/log_snsapi.log');
            if (!session("openid") && !$code) {
                $wechatAuth = new \WechatAuth($appId, $appSecret);
//                $url = C('DOMAIN') . U("Payment/pay", array("paytype" => 2, "paycode" => "wxpay", "orderid" => $this->order_sn));
                
                $url = str_replace('com/', 'com', WAP_SITE_URL) . U("Payment/pay", array("paytype" => 2, "paycode" => "wxpay", "orderid" => $this->order_sn));
                // snsapi_base 
                \Think\Log::write('url1:' . var_export($url, true), 'DEBUG', '', C('LOG_PATH') . 'log_snsapi.log');
                $url = $wechatAuth->get_authorize_url($url, '1', 'snsapi_base'); //snsapi_userinfo
                \Think\Log::write('url2:' . var_export($url, true), 'DEBUG', '', C('LOG_PATH') . 'log_input.log');
                redirect($url);
                exit;
            }
            if (!session("openid") && $code) {
                $wechatAuth = new \WechatAuth($appId, $appSecret);
                $wechat_access_token = $wechatAuth->get_access_token($code);
                $openid = $wechat_access_token['openid'];
                session("openid", $openid);
            }
        } else {
            session("openid", null);
        }
        $info = array(
            'return_url' => $this->return_url,
            'order_sn' => $this->order_sn,
            'order_amount' => $this->order_amount,
            'subject' => $this->subject
        );
        $order = $this->web_get_order_paynum($info);
        $reinfo['img_url'] = C('DOMAIN') . "/qrcode.php?data=" . urlencode($order["code_url"]);
        $reinfo['return_url'] = $info["return_url"];
        $reinfo['order_sn'] = $info["order_sn"];
        $reinfo['openid'] = session("openid");
        $reinfo['order_amount'] = $info["order_amount"];
        $reinfo['jsApiParameters'] = $order["jsApiParameters"];
        $reinfo['success_page'] = WAP_SITE_URL . U('Wap/Payment/wx_success', array("orderid" => $this->order_sn, "order_amount" => $this->order_amount)); //SITE_URL . "Wap/Pay/over";//wap端支付成功后跳转页面
        $reinfo['error_page'] = WAP_SITE_URL . U('Wap/Payment/wx_error', array("orderid" => $this->order_sn, "order_amount" => $this->order_amount)); //SITE_URL . "Wap/Pay/overerror";//wap端支付失败后跳转页面

        return $reinfo;
    }

    //获取微信端支付号信息
    public function web_get_order_paynum($info) {
        if (session("openid")) {
            vendor('Wxpay.WxPayJsApiPay');
        }
        vendor('Wxpay.WxPayApi');
        /* 首先生成prepayid */
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($info['subject'] . $info["order_sn"]); //商品或支付单简要描述(必须填写)
        $input->SetAttach($info["order_sn"]); //附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据(不必填)
        $input->SetDetail($info['subject']); //商品名称明细列表(不必填)
        $input->SetOut_trade_no($info["order_sn"] . date('His')); //订单号(必须填写)
        $input->SetTotal_fee($info['order_amount'] * 100); //订单金额(必须填写)
        $input->SetTime_start(date("YmdHis")); //交易起始时间(不必填)
        $input->SetTime_expire(date("YmdHis", time() + 240 * 3600)); //交易结束时间10分钟之内不必填)
        $input->SetGoods_tag($info['subject']); //商品标记(不必填)
        $input->SetNotify_url($info['return_url']); //回调URL(必须填写)
        if (session("openid") != "") {
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid(session("openid"));
        } else {
            $input->SetTrade_type("NATIVE"); //交易类型(必须填写)
            $input->SetProduct_id($info['order_sn']);
        }
        //$input->SetProduct_id("123456789");//rade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
        $wxpayapi = new \WxPayApi(); //获得订单的基本信息，包括prepayid
        $order = $wxpayapi->unifiedOrder($input);

        //记录日志
        \Think\Log::write(var_export($input, true), 'DEBUG', '', C('LOG_PATH') . date('Y-m-d', NOW_TIME).'/log_input.log');
        \Think\Log::write('order:' . var_export($order, true), 'DEBUG', '', C('LOG_PATH') .date('Y-m-d', NOW_TIME). '/log_order.log');
        //记录日志
        \Think\Log::write('post:' . var_export($info, true), 'DEBUG', '', C('LOG_PATH') . date('Y-m-d', NOW_TIME).'/log_order.log');

        if (session("openid")) {
            $jsapi = new \JsApiPay();
            $order["jsApiParameters"] = $jsapi->GetJsApiParameters($order);
        }
        return $order;
    }

}

?>