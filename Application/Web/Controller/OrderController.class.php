<?php

/**
 * 会员中心
 */

namespace Web\Controller;

use Web\Controller\HomeController;
use Api\Api\OrderApi;

class OrderController extends HomeController {

    protected function _initialize() {
        parent::_initialize();
        if (!$this->customerId) {
            $this->redirect(url('member/login'));
        }
    }

    /**
     * 订单列表
     */
    public function index() {
        $orderapi = new OrderApi();
        $orderid = I('ordersn');
        if (!empty($orderid)) {
            $conditon["orderid"] = array("like", '%' . $orderid . '%');
            $issearch = 1;
            $this->assign('orderid', $orderid);
        }
        $state_type = I('state_type');
        switch ($state_type) {
            case 'state_new': // 待付款
                $conditon["status"] = -1;
                $conditon["ispay"] = 1;
                break;
            case 'state_pay': // 待发货
                $conditon["status"] = 1;
                break;
            case 'state_send': // 待收货
                $conditon["status"] = 2;
                break;
            case 'state_success': // 已完成
                $conditon["status"] = array('EGT', 3);
                break;
            case 'state_noeval': // 待评价
                $conditon["status"] = array('EGT', 3);
                $conditon['iscomment'] = 0;
                break;
            case 'state_eval': // 已评价
                $conditon["status"] = array('EGT', 3);
                $conditon['iscomment'] = 1;
                break;
            case 'state_cancel': // 已取消
                $conditon["status"] = -2;
                break;
            default:
                $state_type = 'all';
                break;
        }
        $this->assign('state_type', $state_type);
        $conditon["uid"] = is_login();
        $list = $orderapi->getOrderList($conditon);
        $uid = is_login();
        $OrderStautsCount = $orderapi->getOrderStautsCount($uid);

        $this->assign('ordernum', $OrderStautsCount);
        $this->assign('orderlist', $list); // 赋值数据集
        $this->meta_title = '订单管理';
        $this->display();
    }

    /**
     * 订单详情页
     */
    public function details() {
        if (!is_login()) {
            $this->error("您还没有登录", U("Member/login"));
        }
        $id = I('get.orderid'); //获取id
        $orderapi = new OrderApi();
        $condition['orderid'] = $id;
        $orderdetail = $orderapi->getOrderDetail($condition);
        //dump($orderdetail);exit;
        $this->assign('orderdetail', $orderdetail);
        $typeCom = M("order")->where("orderid='$id'")->getField("tool");
        $typeNu = M("order")->where("orderid='$id'")->getField("toolid");
        if (isset($typeCom) && $typeNu) {
            $retData = $this->getkuaidi($typeCom, $typeNu);
        } else {
            $retData = "";
        }
        $this->assign('kuaidata', $retData); // 物流信息
        $this->meta_title = '订单详情';
        $this->display('order.detail');
    }

    /**
     * 确认收货
     * @param type $id
     */
    public function order_receive() {
        if (IS_GET) {
            $orderapi = new OrderApi();
            $orderid = I('orderid');
            $condition['tag'] = $orderid;
            $order_info = $orderapi->getOrderInfo($condition);
            if (empty($order_info)) {
                $this->error('订单不存在，或参数错误');
            }
            $result = $orderapi->changeOrderStatus('order_receive', $order_info);
            if (isset($result['error'])) {
                $this->error($result['msg'], Cookie('__forward__'));
            } else {
                $this->success($result['msg']);
            }
        }
    }

    /**
     * 取消订单
     */
    public function cancel_order() {

        $orderapi = new OrderApi();
        $ordersn = I('id');
        $orderid = I('orderid');
        $condition['tag'] = $ordersn;
        $condition['orderid'] = $orderid;
        $order_info = $orderapi->getOrderInfo($condition);
        if (empty($order_info)) {
            $this->error('订单不存在，或参数错误');
        }
        $result = $orderapi->changeOrderStatus('order_cancel', $order_info);
        if (isset($result['error'])) {
            $this->error($result['msg'], Cookie('__forward__'));
        } else {
            $this->success($result['msg']);
        }
    }

    /**
     * 获取物流信息
     * @param type $typeCom 物流公司
     * @param type $typeNu  物流单号
     * @return type result  物流跟踪信息
     */
    public function getkuaidi($typeCom, $typeNu) {

        //$typeCom = $_GET["com"];//快递公司
        //$typeNu = $_GET["nu"];  //快递单号
        //echo $typeCom.'<br/>' ;
        //echo $typeNu ;

        $AppKey = C('100KEY'); //请将XXXXXX替换成您在http://kuaidi100.com/app/reg.html申请到的KEY
        $url = 'http://api.kuaidi100.com/api?id=' . $AppKey . '&com=' . $typeCom . '&nu=' . $typeNu . '&show=2&muti=1&order=asc';

        //请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
        $powered = '查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 ';


        //优先使用curl模式发送数据
        if (function_exists('curl_init') == 1) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            $get_content = curl_exec($curl);
            curl_close($curl);
        } else {
            Vendor("Snoopy.Snoopy");
            $snoopy = new \Vendor\Snoopy\Snoopy();
            $snoopy->referer = 'http://www.google.com/'; //伪装来源
            $snoopy->fetch($url);
            $get_content = $snoopy->results;
        }
        return $get_content;
    }

}
