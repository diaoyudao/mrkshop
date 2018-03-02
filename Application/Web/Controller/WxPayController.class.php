<?php
namespace Web\Controller;

use Think\Controller;

class WxPayController extends Controller
{
    public $order_sn = '';//商户订单号,注意要唯一！
    public $order_amount = '';//订单金额
    public $subject = "订单支付";//标题
    public $return_url = '';//回调地址

    //在类初始化方法中，引入相关类库
    public function _initialize()
    {
        header("Content-type:text/html;charset=utf-8");
        vendor('Wxpay.WxPayConfig');
        vendor('Wxpay.WechatAuth');
        vendor('Wxpay.WechatJSAPI');
    }

    //判断是否是微信客户端打开
    private function is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    //获取二维码信息或者公众号支付
    public function dowxPay()
    {
        if (empty($this->order_sn) || empty($this->order_amount) || empty($this->subject)) {
            $this->error('缺少参数');
        }
        if (empty($this->return_url)) {
            $this->error('缺少回调地址');
        }
        //判断是否是客户端打开或者浏览器打开 
        if ($this->is_weixin()) {
            $code = I("code");
            $wxconfig =new \WxPayConfig();
            $appId = $wxconfig::WEB_APPID;//公众账号ID
            $appSecret = $wxconfig::WEB_MCHID;//商户号  
            if (!session("openid") && !$code) {
                $wechatAuth = new \WechatAuth($appId, $appSecret);
                $url = C('DOMAIN') . U("Payment/pay", array("paytype" => 2, "paycode" => "wxpay", "orderid" => $this->order_sn));// "http://tying.m1ju.com".$_SERVER['REQUEST_URI'];
                // snsapi_base 
                $url = $wechatAuth->get_authorize_url($url, '1', 'snsapi_base');//snsapi_userinfo
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
        $reinfo['order_amount'] = $info["order_amount"];
        return $reinfo;
    }


    //获取微信端支付号信息
    public function web_get_order_paynum($info)
    {
        vendor('Wxpay.WxPayApi');
        /*首先生成prepayid*/
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("订单" . $info["order_sn"]);//商品或支付单简要描述(必须填写)
        $input->SetAttach($info["order_sn"]);//附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据(不必填)
        $input->SetDetail($info['subject']);//商品名称明细列表(不必填)
        $input->SetOut_trade_no($info["order_sn"] . date('His'));//订单号(必须填写)
        $input->SetTotal_fee($info['order_amount'] * 100);//订单金额(必须填写)
        $input->SetTime_start(date("YmdHis"));//交易起始时间(不必填)
        $input->SetTime_expire(date("YmdHis", time() + 240 * 3600));//交易结束时间10分钟之内不必填)
        $input->SetGoods_tag($info['subject']);//商品标记(不必填)
        $input->SetNotify_url($info['return_url']);//回调URL(必须填写)
        if (session("openid") != "") {
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid(session("openid"));
        } else {
            $input->SetTrade_type("NATIVE");//交易类型(必须填写)
            $input->SetProduct_id($info['order_sn']);
        }
        //$input->SetProduct_id("123456789");//rade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
        $wxpayapi = new \WxPayApi();//获得订单的基本信息，包括prepayid
        $order = $wxpayapi->unifiedOrder($input);
        
         //记录日志
        \Think\Log::write(var_export($input, true), 'DEBUG', '', C('LOG_PATH') . 'log_input.log');
        \Think\Log::write(var_export($order, true), 'DEBUG', '', C('LOG_PATH') . 'log_order.log');
         //记录日志
        \Think\Log::write(var_export($info, true), 'DEBUG', '', C('LOG_PATH') . 'log_order.log');
        
        if (session("openid")) {
            vendor('Wxpay.WxPayJsApiPay');
            $jsapi = new \JsApiPay();
            $order["jsApiParameters"] = $jsapi->GetJsApiParameters($order);
        }
        return $order;
    }

    /**
     * 微信异步通知
     *
     * @author spjiang <spjiang@aliyun.com>
     */
    public function notify()
    {
        $file = dirname(__FILE__) . "/log_pay.txt";
        @file_put_contents($file, "wx_success start ------------------------" . var_export($_REQUEST, true) . "\r\n" . "end------------------------\r\n", FILE_APPEND);
        $_SESSION["WEB_APP_WXPAY"] = 1;
        $post_info = $_REQUEST;
        vendor('Wxpay.WxPayWebNotify');
        $notify = new \PayNotifyCallBack();
        $notify->Handle(true);
        if ($notify->GetReturn_code() == 'SUCCESS' && !checkorderstatus($post_info['orderid'])) {
            orderhandle($post_info['orderid'], 3);
        }
    }
}

?>