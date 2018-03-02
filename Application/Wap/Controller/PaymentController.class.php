<?php

/**
 * 支付
 */

namespace Wap\Controller;

use Wap\Controller\HomeController;

class PaymentController extends HomeController {

    protected function _initialize() {
        parent::_initialize();
    }

    /**
     * 去支付
     */
    public function index() {
        if (IS_POST) {
//             $sysconfig = api('Config/lists');
            $paytype = I('post.PayType'); //支付类型
            $payname = I('post.onlinepaymentname');
            $orderid = I('id');
            //更新订单支付方式
            $order = D("order");
            $pay = M("pay");
        } else {
            $id = I("get.id");
            if (empty($id)) {
                $this->error('参数错误，非法访问', U('index/index'));
                die();
            }
            /* uid调用 */
            $user = session('user_auth');
            $uid = $user['uid'];
            $score = get_score($uid);
            /* 积分兑换 */
            $ratio = $score / C('RATIO');
            $this->assign('ratio', $ratio);
            $this->meta_title = '支付订单';
            //在此之前goods1的业务订单已经生成，状态为等待支付
            $order = D("order");
            if (empty($order)) {
                $this->error('参数错误,没有查询到订单', U('index/index'));
                die();
            }
            $this->assign('codeid', $id);

            $order_total = $order->where("orderid='$id'")->getField('pricetotal');
            $this->assign('order_total', $order_total);
            //支付方式
            $payment = get_site_payment();
            $this->assign('paymentlist', $payment); //支付方式
            $this->display('index');
        }
    }

    /**
     * 去付款
     */
    public function pay() {
//         $sysconfig = api('Config/lists');

        // 去付款
        $orderid = I('orderid');
        $payid = I('payid');
        $paycode = I('paycode');
        $paymentinfo = M('payment')->where(array('paycode' => $paycode))->find();
        if (empty($paymentinfo)) {
            $this->error('支付方式不存在，请联系客服' . C('CONTACT'), Cookie('__forward__'));
        }
        $where['orderid'] = $orderid;
        $where['status'] = -1;
        $orderinfo = M('order')->where($where)->find();
        if (empty($orderinfo)) {
            $this->error('订单不存在，请联系客服' . C('CONTACT'), U("Order/index"));
        }
        $order_total = $orderinfo['pricetotal'];
        if ($order_total == 0) {
            $this->error('订单金额为0，不需支付' . C('CONTACT'),U('Order/index'));
        }
        $title = trim($title, ',');
        $description = trim($description, ',');
        $good_title = $this->msubstr($title, 0, 40);
        $good_description = $this->msubstr($description, 0, 100);
        $body = C('SITENAME') . $good_description; //商品描述
        $title = C('SITENAME') . $good_title; //设置商品名称 
        $order_no = $orderid;
        switch ($paycode) {
            case 'alipay':
                $paylist["email"] = C('ALIPAYEMAIL');
                $paylist["key"] = C("ALIPAYKEY");
                $paylist["partner"] = C("ALIPAYPARTNER");
                $paylist["notify_url"] = "http://".C('BASE_SITE_URL').U("Payment/notify", array('apitype' => 'alipay', 'method' => 'notify'));
                $paylist["return_url"] = "http://".C('BASE_SITE_URL').U("Payment/notify", array('apitype' => 'alipay', 'method' => 'return'));
                $pay = new \Think\Pay($paycode, $paylist);
                $vo = new \Think\Pay\PayVo();
                $vo->setBody($body)
                        ->setFee($order_total) //支付金额
                        ->setOrderNo($order_no)//订单号
                        ->setTitle($title)//设置商品名称
                        ->setCallback(U('Wap/Payment/success'))/* 设置支付完成后的后续操作接口 */
                        ->setUrl(U("Wap/Payment/over")) /* 设置支付完成后的跳转地址 */
                        ->setParam(array('order_id' => $order_no));
                ob_clean();
                echo $pay->buildRequestForm($vo);
                exit;
                break;
            case 'wxpay':
//                 $this->error('其他支付请联系客服，请耐心等待！' . C('CONTACT'), Cookie('__forward__'));
                //调用微信在线接口
                $_SESSION["WEB_APP_WXPAY"] = 1;
                // 微信支付
                $wxpay = new WxPayController();
                $wxpay->return_url = WAP_SITE_URL."Payment/wx_notify.html"; // C('DOMAIN') . U("Web/Payment/wx_notify");
                $wxpay->order_sn = $order_no;
                $wxpay->order_amount = $order_total;
                $wxpay->subject = $title;
                $reinfo = $wxpay->dowxPay();
                $this->assign("orderid", $orderid);
                $this->assign("order_total", $order_total);
                $this->assign("orderinfo", $reinfo);
                \Think\Log::write('orderinfo'.var_export($reinfo, true), 'DEBUG', '', C('LOG_PATH') . date('Y-m-d', NOW_TIME). '/log_snsapi.log');
                $this->display('wxbuy');
                exit;
                break;

            default:
                $this->error('其他支付请联系客服，请耐心等待！' . C('CONTACT'), Cookie('__forward__'));
                break;
        }
    }

    /**
     * 微信支付异步通知
     */
    public function wx_notify() {
        //记录日志
        \Think\Log::write(var_export($_REQUEST, true), 'DEBUG', '', C('LOG_PATH') .date('Y-m-d', NOW_TIME). '/log_pay.log');
        $_SESSION["WEB_APP_WXPAY"] = 1;
        $post_info = $_REQUEST;
        vendor('Wxpay.WxPayWebNotify');
        $notify = new \PayNotifyCallBack();
        $notify->Handle(true);
        if ($notify->GetReturn_code() == 'SUCCESS' && !checkorderstatus($post_info['orderid'])) {
            orderhandle($post_info['orderid'], $post_info['transaction_id'], 3);
        }
        exit;


//        vendor('Wxpay.WxPayWebNotify');
//        //记录日志
//        \Think\Log::write(var_export($_REQUEST, true), 'DEBUG', '', C('LOG_PATH') . 'log_pay.log');
//        $_SESSION["WEB_APP_WXPAY"] = 1;
//        $notify = new \PayNotifyCallBack();
//        $notify->Handle(true);
//        exit(1);
    }
    
    /**
     * 微信支付成功
     */
    public function wx_success() {
        $orderid = I('orderid');
        $order_amount = I('order_amount');
        $this->assign('orderid', $orderid);
        $this->assign('order_amount', $order_amount);
        $this->display('success');
    }

    /**
     * 微信支付失败
     */
    public function wx_error() {
        $orderid = I('orderid');
        $order_amount = I('order_amount');
        $this->assign('orderid', $orderid);
        $this->assign('order_amount', $order_amount);
        $this->display('error');
    }

    // 查看订单状态
    public function check_order_status() {
        $orderId = I('orderId');
        $amt = I('amt');
        if (!$orderId || !$amt) {
            $this->error('参数错误');
        }
        $status = M('order')->where(array('tag' => $orderId))->getField('status');
        if ($status >= 1) {
            $this->success('支付成功', U('Order/index'));
        }
        $this->error('等待支付');
    }

    //  支付宝
    public function over() {
        $this->meta_title = '支付成功';
        $postinfo = I('get.');

        $this->assign('orderid', $postinfo['orderid']);
        $this->assign('money', $postinfo['money']);
        $this->display('success');
    }

    /**
     * 支付结果返回
     */
    public function notify() {
        $apitype = I('get.apitype');
        if(empty($apitype)){
        	E("非法操作！");die();
        }
        $config = array();
        if($apitype == 'alipay'){
        	$config = array(
        			'email' => C('ALIPAYEMAIL'),
        			'key' => C("ALIPAYKEY"),
        			'partner' => C("ALIPAYPARTNER"),
        			'cacert' => getcwd() . '\\cacert.pem'
        	);
        }
        
        $pay = new \Think\Pay($apitype, C('PAYMENT.' . $apitype));
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            exit('Access Denied');
        }
        //验证
        if ($pay->verifyNotify($notify)) {
            //获取订单信息
            $info = $pay->getInfo();
            if ($info['status']) {
                $payinfo = M("Pay")->field(true)->where(array('out_trade_no' => $info['out_trade_no']))->find();
                if ($payinfo['status'] == 1 && $payinfo['callback']) {
                    session("pay_verify", true);
                    //修改支付状态
                    M("Pay")->where(array('out_trade_no' => $info['out_trade_no']))->setField(array('status' => '2', 'update_time' => time()));
                    //设置订单为已经支付,状态为已提交
                    M('Order')->where(array('tag' => $info['out_trade_no']))->setField(array('status' => '1', 'ispay' => '2','payment_time'=>time()));
//                     $check = R($payinfo['callback'], array('money' => $info['money'], 'param' => unserialize($payinfo['param'])));
                    //if ($check !== false) {
                    //    M("Pay")->where(array('out_trade_no' => $info['out_trade_no']))->setField(array('update_time' => time(), 'status' => 1));
                    //}
                }
                if (I('get.method') == "return") {
                    $this->success('支付成功',U("Order/detail",array("orderid"=>$info['out_trade_no'])) );
//                     redirect($payinfo['url']);
                } else {
                    $pay->notifySuccess();
                }
            } else {
                $this->error("支付失败！");
            }
        } else {
            E("Access Denied");
        }
    }

    /* 支付宝订单成功返回地址 */

    public function success($param) {
        if (session("pay_verify") == true) {
            session("pay_verify", null);
            //处理goods1业务订单、改名good1业务订单状态
            $data = array();
            $data = array('status' => '2', 'update_time' => time());
            M("pay")->where(array('out_trade_no' => $param['order_id']))->setField($data);
            $data = array();
            $data = array('status' => '1', 'ispay' => '2',); //设置订单为已经支付,状态为已提交
            M('order')->where(array('tag' => $param['order_id']))->setField($data);
            //// 配置邮件提醒
            //$uid=M("pay")->where(array('out_trade_no' => $param['order_id']))->getField('uid');
            //$mail=get_email($uid);//获取会员邮箱
            //$title="支付提醒";
            //$content="您在<a href=\"".C('DAMAIN')."\" target='_blank'>".C('SITENAME').'</a>支付了订单，交易号'.$param['order_id'];
            //if( C('MAIL_PASSWORD'))
            //
    	    //{
            //SendMail($mail,$title,$content);
            //}
        } else {
            E("Access Denied");
        }
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @static
     * @access public
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符
     * @return string
     */
    public function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
        if (function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice . '...' : $slice;
    }

}
