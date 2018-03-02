<?php

/**
 * 订单退货/退款
 */

namespace Web\Controller;

use Web\Controller\HomeController;
use Api\Api\OrderApi;

class RefundController extends HomeController {

    private $refund_resion = array(
        1 => array('id' => 1, 'resion' => '不想要了'),
        2 => array('id' => 2, 'resion' => '商品已损坏'),
        3 => array('id' => 3, 'resion' => '购买其他商品'),
        4 => array('id' => 4, 'resion' => '价格太贵'),
    );

    protected function _initialize() {
        parent::_initialize();
         if (!$this->customerId) {
                $this->redirect(url('member/login'));
            }
        $refund_resion = $this->refund_resion;
        $this->assign('refund_resion', $refund_resion);
    }

    public function index() {
        $state_type = I('state_type');
        switch ($state_type) {
            case 'state_sucess': // 已完成
                $conditon["refund_state"] = 3;
                break;
            case 'state_new': // 待处理
                $conditon["refund_state"] = array('LT', 3);
//                $conditon['iscomment'] = 0;
                break;
            case 'state_eval': // 已评价
                $conditon["refund_state"] = array('EGT', 3);
//                $conditon['iscomment'] = 1;
                break;
            default:
                $state_type = 'all';
                break;
        }
        $orderid = I('ordersn');
        if (!empty($orderid)) {
            $conditon["refund_sn"] = array("like", '%' . $orderid . '%');
            $this->assign('orderid', $orderid);
        }
        $conditon['uid'] = is_login();
        $orderapi = new OrderApi();

        $result = $orderapi->getRefundOrderList($conditon);
        $this->assign('state_type', $state_type);
        $ordersum = M("order_refund")->where(array('uid' => $conditon['uid']))->count();    //所有
        $evalnum = M('order_refund')->where(array('uid' => $conditon['uid'], 'refund_state' => 3,))->count();  // 已完成
        $noevalnum = $ordersum - $evalnum;  // 处理中
        $this->assign('ordersum', $ordersum);
        $this->assign('noevalnum', $noevalnum);
        $this->assign('evalnum', $evalnum);
        $this->assign('orderlist', $result); // 赋值数据集
        $this->meta_title = "售后管理";
        if (isset($result['error'])) {
            $this->error($result['msg'], Cookie('__forward__'));
        }
        $this->display();
    }

    public function add() {
        if (!IS_POST) {
            $this->error('非法访问');
        }
        $post = I('post.');
        if (empty($post['goods_num']) || empty($post['order_id'])) {
            $this->error('参数错误');
        }
        $orderapi = new OrderApi();
        $condition['id'] = $post['order_id'];
        $order_info = $orderapi->getOrderInfo($condition);
        if (empty($order_info)) {
            $this->error('订单不存在，或参数错误');
        }
        $_ordergoods = M('shoplist')->where(array('orderid' => $post['order_id'], 'goodid' => $post['goodid'],'id'=>$post['id']))->find();
        if (empty($_ordergoods)) {
            $this->error("订单商品信息不存在");
        }
        // 退款信息
        $ordergoods['id'] = $_ordergoods['orderid'];
        $ordergoods['tag'] = $_ordergoods['tag'];
        $ordergoods['uid'] = $_ordergoods['uid'];
        $ordergoods['total'] = $_ordergoods['total'];
        $ordergoods['goods_id'] = $_ordergoods['goodid'];
        $ordergoods['order_goods_id'] = $_ordergoods['id'];
        $ordergoods['order_type'] = $_ordergoods['cart_type'];
        $ordergoods['goods_num'] = $post['goods_num'] > $_ordergoods['num'] ? $_ordergoods['num'] : intval($post['goods_num']);
        $ordergoods['pics'] = $post['pics'];
        $ordergoods['reason_id'] = $post['resion_id'];
        $ordergoods['reason_info'] = $this->refund_resion[$post['resion_id']]['resion'];
        $ordergoods['buyer_message'] = $post['buyer_message'];
        $ordergoods['store_id'] = $order_info['store_id'];
        $result = $orderapi->changeOrderStatus('order_refund', $ordergoods);
        if (isset($result['error'])) {
            $this->error($result['msg'], Cookie('__forward__'));
        } else {
            $this->success($result['msg'], U('Refund/index'));
        }
    }

    /**
     * 申请售后
     */
    public function apply_refund() {


        $requstdata = I('get.');
        if (empty($requstdata)) {
            $this->error('非法访问');
        }
        $orderapi = new OrderApi();
        $condition['id'] = $requstdata['orderid'];
        $orderdetail = $orderapi->getOrderDetail($condition);
        //dump($orderdetail);
        $this->assign('orderdetail', $orderdetail);
        if (isset($orderdetail['error'])) {
            $this->error($orderdetail['msg'], Cookie('__forward__'));
        } else {
//            $this->success($orderdetail['msg'], U('Refund/index'));
            $this->display();
        }
    }

    /**
     * 退款/退货详情
     */
    public function detail() {

        $refund_id = I('id');
        if (empty($refund_id)) {
            $this->error('非法访问');
        }
        $condition['refund_id'] = $refund_id;
        $orderapi = new OrderApi();
        $orderdetail = $orderapi->getRefundOrderDetail($condition);
        $this->assign('orderdetail', $orderdetail);
        if (isset($orderdetail['error'])) {
            $this->error($orderdetail['msg'], Cookie('__forward__'));
        } else {
//            $this->success($orderdetail['msg'], U('Refund/index'));
            $this->display('refund.detail');
        }
    }

}
