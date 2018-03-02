<?php

/**
 * 代理管理
 */

namespace Web\Controller;

use Web\Controller\HomeController;
use Api\Api\GoodsApi;
use Api\Api\MemberApi;
use Api\Api\CheckoutApi;
use Api\Api\OrderApi;

class AgentController extends HomeController {

    private $memberapi = '';
    private $goodsapi = '';
    private $orderapi = '';
    private $chkapi = '';

    protected function _initialize() {
        parent::_initialize();
        $this->memberapi = new MemberApi();
        $this->goodsapi = new GoodsApi();
        $this->chkapi = new CheckoutApi();
        $this->orderapi = new OrderApi();
        if (!$this->customerId) {
            $this->redirect(url('member/login'));
        }
        $this->memberinfo();
    }

    /**
     * 代理管理首页
     */
    public function index() {
        $this->display();
    }

    /**
     * 订单管理
     */
    public function orderlist() {
        $conditon["store_id"] = is_login();
        $tag = I('tag');
        if ($tag) {
            $conditon['tag'] = array('like', "$tag%");
        }
        $status = I('status');
        if ($status) {
            $conditon['status'] = $status;
        }

        $day = I('day');
        switch ($day) {
            case 'day0': // 昨天
                $starttime = strtotime(date("Y-m-d", NOW_TIME)) - 24 * 60 * 60;
                $endtime = strtotime(date("Y-m-d", NOW_TIME));
                $conditon['create_time'] = array('between', array($starttime, $endtime));
                break;
            case 'day1':    // 今天
                $starttime = strtotime(date("Y-m-d", NOW_TIME));
                $endtime = $starttime + 24 * 60 * 60;
                $conditon['create_time'] = array('between', array($starttime, $endtime));

                break;
            case 'day3':   // 近三天
                $starttime = NOW_TIME - 24 * 60 * 60 * 3;
                $endtime = NOW_TIME;
                $conditon['create_time'] = array('between', array($starttime, $endtime));

                break;
            case 'day30':   // 近一个月
                $starttime = NOW_TIME - 24 * 60 * 60 * 30;
                $endtime = NOW_TIME;
                $conditon['create_time'] = array('between', array($starttime, $endtime));
                break;
            default:
                break;
        }

        $list = $this->orderapi->getOrderList($conditon, '', 1);
//        $OrderStautsCount = $this->orderapi->getOrderStautsCount();
//        $this->assign('ordernum', $OrderStautsCount);
        $this->assign('orderlist', $list); // 赋值数据集
        $this->assign('day', $day); // 赋值数据集
        $orderstatus = array(
            array('status' => -3, 'txt' => '售后订单'),
            array('status' => -2, 'txt' => '已取消订单'),
            array('status' => -1, 'txt' => '待付款订单'),
            array('status' => 1, 'txt' => '待发货订单'),
            array('status' => 2, 'txt' => '待收货订单'),
            array('status' => 3, 'txt' => '已完成订单'),
        );
        $this->assign('orderstatus', $orderstatus); // 赋值数据集
        $this->meta_title = '订单管理';
        $this->display();
    }

    /**
     * 订单详情
     */
    public function orderdetail($order_sn) {

        $condition['orderid'] = $order_sn;
        $orderdetail = $this->orderapi->getOrderDetail($condition);
        $this->assign('orderdetail', $orderdetail);
//        dump($orderdetail);
        $orderlog = $this->orderapi->getOrderLog($orderdetail['id']);
        $this->assign('orderlog', $orderlog);
        // 操作按钮
        // 取消订单  、去付款 、去发货、确认收货
        $this->assign('handle', $this->store_order_handle($orderdetail));

        $this->meta_title = '订单详情';
        $this->display();
    }

    public function store_order_handle($order) {
        if ($order['store_id'] != is_login()) {
            return '';
        }
        $handle = '';
        if ($order['status'] == -1 && $order['ispay'] == 3) { // 刚下单
            $handle.="<a class='confirm' data-msg='确定要取消订单吗？' href=" . U('Agent/ordercancel', array('order_sn' => $order['orderid'])) . ">取消订单</a>";
            $handle.="<a class='confirm' data-msg='确定要去付款吗？' href=" . U('Agent/orderpay', array('order_sn' => $order['orderid'])) . ">去付款</a>";
            $handle.="<a class='confirm' data-msg='确定要去发货吗？' href=" . U('Agent/ordership', array('order_sn' => $order['orderid'])) . ">去发货</a>";
        }
        if ($order['status'] == -1 && $order['ispay'] == 4) { // 已发货
            $handle.="<a class='confirm' data-msg='确定要取消订单吗？' href=" . U('Agent/ordercancel', array('order_sn' => $order['orderid'])) . ">取消订单</a>";
//            $handle.="<a class='confirm' data-msg='确定要去付款吗？' href=" . U('Agent/orderpay', array('order_sn' => $order['orderid'])) . ">去付款</a>";
//            $handle.="<a class='confirm' data-msg='确定要去发货吗？' href=" . U('Agent/ordership', array('order_sn' => $order['orderid'])) . ">去发货</a>";
            $handle.="<a class='confirm' data-msg='确定收货吗？' href=" . U('Agent/orderreceive', array('order_sn' => $order['orderid'])) . ">确认收货</a>";
        }
        if ($order['status'] == 1 && $order['ispay'] == 3) {  // 已付款
//            $handle.="<a class='confirm' data-msg='确定要退款吗？' href=" . U('Agent/orderrefund', array('order_sn' => $order['orderid'])) . ">要退款</a>";
            $handle.="<a class='confirm' data-msg='确定要去发货吗？' href=" . U('Agent/ordership', array('order_sn' => $order['orderid'])) . ">去发货</a>";
            $handle.="<a class='confirm' data-msg='确定收货吗？' href=" . U('Agent/orderreceive', array('order_sn' => $order['orderid'])) . ">确认收货</a>";
        }
        if ($order['status'] == 1 && $order['ispay'] == 4) {  // 已付款
//            $handle.="<a class='confirm' data-msg='确定要退款吗？' href=" . U('Agent/orderrefund', array('order_sn' => $order['orderid'])) . ">要退款</a>";
//            $handle.="<a class='confirm' data-msg='确定要去发货吗？' href=" . U('Agent/ordership', array('order_sn' => $order['orderid'])) . ">去发货</a>";
            $handle.="<a class='confirm' data-msg='确定收货吗？' href=" . U('Agent/orderreceive', array('order_sn' => $order['orderid'])) . ">确认收货</a>";
        }
        if ($order['status'] == 3 && $order['ispay'] == 4) {  // 已发货
//            $handle.="<a class='confirm' data-msg='确定要退货吗？' href=" . U('Agent/orderrefund', array('order_sn' => $order['orderid'])) . ">要退货</a>";
            $handle.="<a class='confirm' data-msg='确定收到货款？' href=" . U('Agent/orderpayok', array('order_sn' => $order['orderid'])) . ">收到货款</a>";
//            $handle.="<a class='confirm' data-msg='确定收货吗？' href=" . U('Agent/orderreceive', array('order_sn' => $order['orderid'])) . ">确认收货</a>";
        }
        if ($order['status'] == -2) {  // 已取消
//            $handle.="<a href=" . U('Agent/orderorigin', array('order_sn' => $order['orderid'])) . ">还原订单</a>";
            $handle.="<a class='confirm' data-msg='订单已取消' href='Javascript:;;'>订单已取消</a>";
        }

        if ($order['status'] == 3 && $order['ispay'] == 5) {
            $handle.="<a class='confirm' data-msg='订单已完成' href='javascript:;;'>订单已完成</a>";
        } else {
//            $handle.="<a class='confirm' data-msg='确定要一键完成订单吗？' href=" . U('Agent/orderover', array('order_sn' => $order['orderid'])) . ">一键完成订单</a>";
        }

        return $handle;
    }

    /**
     * 取消订单
     */
    public function ordercancel() {

        $ordersn = I('order_sn');
        $orderid = I('order_sn');
        $condition['tag'] = $ordersn;
        $condition['orderid'] = $orderid;
        $order_info = $this->orderapi->getOrderInfo($condition);
        if (empty($order_info)) {
            $this->error('订单不存在，或参数错误');
        }
        $result = $this->orderapi->changeOrderStatus('order_cancel', $order_info);
        if (isset($result['error'])) {
            $this->error($result['msg'], Cookie('__forward__'));
        } else {
            $data['msg'] = '门店取消了订单' . get_username(is_login());
            $data['uid'] = $order_info['uid'];
            $data['roleid'] = is_login();
            $data['status'] = -2;
            $this->orderapi->addOrderLog($order_info['id'], $data);
            $this->success($result['msg']);
        }
    }

    /**
     * 付款
     */
    public function orderpay() {
        if (IS_POST) {
            $orderid = I('order_sn');
            $condition['tag'] = $orderid;
            $condition['orderid'] = $orderid;
            $condition['status'] = -1;
            $order_info = $this->orderapi->getOrderInfo($condition);
            if (empty($order_info)) {
                $this->error('订单不存在，或参数错误');
            }
            $order_info['msg'] = I('log_msg', '');
            $result = $this->orderapi->changeOrderStatus('order_pay', $order_info);
            if (isset($result['error'])) {
                $this->error($result['msg'], Cookie('__forward__'));
            } else {
                M('order')->where(array('orderid' => $orderid))->save(array('assistant' => I('log_user')));
                $this->success($result['msg'], U('Agent/orderdetail', array('order_sn' => $order_info['orderid'])));
//                $this->redirect(U('Agent/orderdetail'), array('order_sn' => $order_info['orderid']));
            }
        } else {
            $orderid = I('order_sn');
            $condition['tag'] = $orderid;
            $condition['orderid'] = $orderid;
            $order_info = $this->orderapi->getOrderInfo($condition);
            $this->assign('orderdetail', $order_info);
            $this->meta_title = '付款';
            $this->display();
        }
    }

    /**
     * 确认收到货款
     */
    public function orderpayok() {
        $orderid = I('order_sn');
        $condition['tag'] = $orderid;
        $condition['orderid'] = $orderid;
        $condition['status'] = 3;
        $condition['ispay'] = 4;
        $order_info = $this->orderapi->getOrderInfo($condition);
        if (empty($order_info)) {
            $this->error('订单不存在，或参数错误');
        }
        $data['ispay'] = 5;
        $data['complete_time'] = NOW_TIME;
        $res = M('order')->where(array('id' => $order_info['id']))->save($data);
        $msg = $order_info['msg'] ? : get_username() . '完成了订单';
        $log['uid'] = $order_info['uid'];
        $log['roleid'] = is_login();
        $log['msg'] = $msg;
        $log['status'] = 5;
        $this->orderapi->addOrderLog($order_info['id'], $log);
        $this->success($log['msg']);
    }

    /**
     * 发货
     */
    public function ordership() {
        if (IS_POST) {
            $orderid = I('order_sn');
            $condition['tag'] = $orderid;
            $condition['orderid'] = $orderid;
//            $condition['status'] = 1;
            $order_info = $this->orderapi->getOrderInfo($condition);
            if (empty($order_info)) {
                $this->error('订单不存在，或参数错误');
            }
            $order_info['msg'] = I('log_msg', '');
            $result = $this->orderapi->changeOrderStatus('order_deliver', $order_info);
            if (isset($result['error'])) {
                $this->error($result['msg'], Cookie('__forward__'));
            } else {
                $da = array('assistant' => I('log_user'),
                    'send_name' => I('send_name'),
                    'send_contact' => I('send_contact'));
                M('order')->where(array('orderid' => $orderid))->save($da);

                $data['msg'] = '门店已发货-' . $da['send_name'] . '/' . $da['send_contact'];
                $data['uid'] = $order_info['uid'];
                $data['roleid'] = is_login();
                $data['status'] = 2;
                $this->orderapi->addOrderLog($order_info['id'], $data);
                $this->success($result['msg'], U('Agent/orderdetail', array('order_sn' => $order_info['tag'])));
//                $this->redirect(U('Agent/orderdetail'), array('order_sn' => $order_info['orderid']));
//                $this->success($result['msg']);
            }
        } else {
            $orderid = I('order_sn');
            $condition['tag'] = $orderid;
            $condition['orderid'] = $orderid;
            $order_info = $this->orderapi->getOrderInfo($condition);
            $this->assign('orderdetail', $order_info);
            $this->meta_title = '发货';
            $this->display();
        }
    }

    /*
     * 退款
     */

    public function orderrefund() {
        $ordersn = I('order_sn');
        $orderid = I('order_sn');
        $condition['tag'] = $ordersn;
        $condition['orderid'] = $orderid;
        $order_info = $this->orderapi->getOrderInfo($condition);
        if (empty($order_info)) {
            $this->error('订单不存在，或参数错误');
        }
        $result = $this->orderapi->changeOrderStatus('order_cancel', $order_info);
        if (isset($result['error'])) {
            $this->error($result['msg'], Cookie('__forward__'));
        } else {
            $data['msg'] = '门店申请退款订单' . get_username(is_login());
            $data['uid'] = $order_info['uid'];
            $data['roleid'] = is_login();
            $data['status'] = -2;
            $this->orderapi->addOrderLog($order_info['id'], $data);
            $this->success($result['msg']);
        }
    }

    /**
     * 确认收货
     */
    public function orderreceive() {
        if (IS_GET) {
            $orderapi = new OrderApi();
            $order_sn = I('order_sn');
            $condition['tag'] = $order_sn;
//            $condition['status'] = 2;
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
     * 一键完成订单
     */
    public function orderover() {

        // TODO:
    }

    /**
     * 售后管理
     */
    public function refundmanage() {
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
        $conditon['store_id'] = is_login();
        $result = $this->orderapi->getRefundOrderList($conditon);
        $this->assign('state_type', $state_type);
        $ordersum = M("order_refund")->where(array('store_id' => $conditon['store_id']))->count();    //所有
        $evalnum = M('order_refund')->where(array('store_id' => $conditon['store_id'], 'refund_state' => 3,))->count();  // 已完成
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

    public function dealrefund() {
        $refund_id = I('refund_id');
        if (empty($refund_id)) {
            $this->error("参数错误");
        }
        $refundmodel = M("order_refund");
        $refundinfo = $refundmodel->where(array('refund_id' => $refund_id))->find();
        if ($refundinfo) {
            $data['refund_state'] = 3;
            $data['admin_time'] = NOW_TIME;
            $data['admin_message'] = '门店已处理退款申请';
            $res = $refundmodel->where(array('refund_id' => $refund_id))->save($data);
            if ($res) {
                $data['msg'] = '门店已处理退款申请';
                $data['uid'] = $refundinfo['uid'];
                $data['roleid'] = is_login();
                $data['status'] = 5;
                $this->orderapi->addOrderLog($refundinfo['order_id'], $data);
            }
            $this->success('操作成功');
        } else {
            $this->error('数据不存在');
        }
    }

    /**
     * 商品管理
     */
    public function goodsmanage() {
        $goodsmodel = new GoodsApi();
        $condition['store_id'] = is_login();
        $goods_name = I('goods_name');
        if ($goods_name) {
            $condition['goods_name'] = array('like', "%{$goods_name}%");
        }
        $domainid = I('domainid');
        if ($domainid) {
            $condition['domainid'] = $domainid;
        }
        $data = $goodsmodel->getStoreGoods_list($condition);
        $map["id"] = array("gt", 1);
        $domainlist = $this->getDomain($map);
        $this->assign("domainlist", $domainlist);
        $this->assign("domainid", $domainid);
//        dump($data['goodslist']);
        $goodsCount = $data['count'];
        $this->assign('count', $goodsCount);
        $this->assign('list', $data['goodslist']);
        $this->meta_title = '商品管理';
        $this->display();
    }

    // 设置库存预警
    public function setgoodsstock() {
        $stock = I("stock");
        $storegoodsid = I("storegoodsid");
        if ($stock && $storegoodsid) {
            $res = M("store_goods")->where(array('id' => $storegoodsid))->setField('stock_warning', $stock);
            $this->success('设置成功');
        } else {
            $this->error('参数错误');
        }
    }

    /**
     * 会员管理
     */
    public function membermanage() {

        $condition['member_agent_id'] = is_login();
        $mobile = I('mobile');
        if ($mobile) {
            $condition['mobile'] = array('like', "$mobile%");
        }
        $memberData = $this->memberapi->getMemberlist($condition);
        $this->assign('memberData', $memberData);
//        dump($memberData);
        $this->meta_title = "会员管理";
        $this->display();
    }

    /**
     * 结算管理
     */
    public function billmanage() {
        $type = I('type');
        $this->assign('type', $type);

        $total = M('affiliate_bill')->where(array('ob_store_id' => is_login()))->sum('ob_pay_total');
        $this->assign('total', $total? : 0.00);
        if ($type == 'log') {
            $ob_no = I('ob_no');
            if ($ob_no) {
                $condition['ob_no'] = $ob_no;
            }
            $condition['business_id'] = is_login();
            $lists = $this->orderapi->affiliate_log_list($condition);
            $this->assign('lists', $lists);
            $this->meta_title = "提成明细";
            $this->display('bill.log');
        } else {
            $condition['ob_store_id'] = is_login();
            $lists = $this->orderapi->affiliate_bill_list($condition);
            $this->assign('lists', $lists);
            $this->meta_title = "结算单";
            $this->display("bill.list");
        }
    }

    /**
     * 生成结算单
     */
    public function create_bill() {
        $store_id = is_login();
        $bool = $this->create_affiliate_statis($store_id);
        if ($bool) {
            $this->success("生成结算单成功");
        } else {
            $this->error("生成结算单失败");
        }
    }

    //手动生成结算单
    public function create_affiliate_statis($store_id) {
        $condition = array('status' => 3, 'business_id' => $store_id);
        $fields = " SUM(money) as moneny,MAX(add_time) as max_time, MIN(add_time) as min_time,business_id as store_id,rem_code ";
        $result = M('affiliate_log')->field($fields)->where($condition)->group('business_id')->select();
        if (empty($result)) {
          $this->error("对不起，您还没有可提成的订单！");
        }
        $bool = $this->_create_affiliate_bill($result);
        return $bool;
    }

    public function _create_affiliate_bill($affiliate_arr) {

        $data = array();
        foreach ($affiliate_arr as $item) {

            $data['ob_result_totals'] = $item['moneny'];
            $data['ob_pay_total'] = $item['moneny'];
            $data['rem_code'] = $item['rem_code'];
            $data['ob_store_id'] = $item['store_id'];
            //$data['ob_start_date'] = $item['min_time'];
            //$data['ob_end_date'] = $item['max_time'];
            $data['ob_start_date'] = M('affiliate_bill')->order('ob_create_date DESC')->limit(1)->getField('ob_create_date');//获取上一条记录的时间
            $data['ob_end_date'] = NOW_TIME;
            $data['ob_create_date'] = NOW_TIME;
            $data['os_month'] = date('Ym', NOW_TIME);
            //$data['ob_no'] = date('Ymd', NOW_TIME) . rand(10, 99);
            $data['ob_no'] = date('Ym', NOW_TIME).substr(NOW_TIME, 6);
            $data['ob_state'] = 1;
            $bool = M('affiliate_bill')->add($data);
            if (!$bool) {
                return false;
            }
            //$this->_update_affiliate_log(array('business_id' => $item['store_id'], 'status' => 2), array('status' => 3, 'ob_no' => $data['ob_no']));
            $this->_update_affiliate_log(array('business_id' => $item['store_id'], 'status' => 3), array('status' => 2, 'ob_no' => $data['ob_no']));
        }
        
        return $bool;
    }

    public function _update_affiliate_log($condition, $data) {
        return M('affiliate_log')->where($condition)->save($data);
    }

    /**
     * 门店资料
     */
    public function storeinfo() {

        $uid = is_login();
        // 获取会员基本信息
        $memberInfo = $this->memberapi->getMemberInfo(array('uid' => $uid));
//        dump($memberInfo);
        if ($memberInfo['member_type'] != 3) {
            $bankinfo = M('member_bank')->where(array('member_id' => $uid))->find();
            $this->assign('bankinfo', $bankinfo);
        }
//dump($bankinfo);
        $this->assign('memberInfo', $memberInfo);

        $this->display();
    }

    /* ========================手动下单 start==================================* */
    /*
     * 选择会员
     */

    public function order_step1() {

        if (IS_AJAX) {
            $member_id = I('member_id');
            if ($member_id) {
                cookie('store_member_id', $member_id);
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        } else {
            $condition['member_agent_id'] = is_login();
            $moble = I("mobile", '');
            if ($moble) {
                $condition['mobile'] = array('like', $moble . '%');
            }

            $memberData = $this->memberapi->getMemberlist($condition);
            $this->assign('memberData', $memberData['memberlist']);
            $this->assign('moble', $moble);
            $this->meta_title = "选择会员";
            $this->display('order.step1');
        }
    }

    /**
     * 选择商品
     */
    public function order_step2() {
        $store_id = session('memberinfo.uid');  // 店铺ID
        $condition['store_id'] = $store_id;
        $condition['stock'] = array('Gt', 0);
        if (I('goods_name')) {
            $condition['goods_name'] = array('like', '%' . I('goods_name') . '%');
        }
        $lists = $this->goodsapi->getStoreGoods_list($condition, "id desc");
//        dump($lists['goodslist']);
        $this->assign("lists", $lists['goodslist']);
        $this->meta_title = "选择商品";
        $this->display('order.step2');
    }

    public function delItem() {
        $sort = I('sort');
        $bool = M("shopcart")->where("sort='$sort'")->delete();
        if ($bool) {
            $result = array('success' => true, 'msg' => '处理成功', 'status' => 1);
        } else {
            $result = array('success' => true, 'msg' => '处理失败', 'status' => 0);
        }
        $this->ajaxReturn($result);
    }

    public function goods_detail() {
        $id = I("goods_id");
        if (empty($id)) {
            $this->error('参数错误');
        }
        $channelname = I("channelname");
        if (!$channelname) {
            $domainid = M("document")->where(array("id" => $id))->getField('domainid');
            $channelinfo = M("subdomain")->where(array("id" => $domainid))->find();
            $channelname = $channelinfo['mark'];
        } else {
            $channelinfo = M("subdomain")->where(array("mark" => $channelname))->find();
        }

        if (!$channelinfo) {
            $this->error("未找到相关产品");
        }
        $this->assign("domaindinfo", $channelinfo);

        /* 获取详细信息 */
        $product_type = I('product_type', 1);
        $store_id = is_login();
        $goodsapi = new GoodsApi();
        $condition['goods_id'] = $id;
        $condition['store_id'] = $store_id;
        $condition['domainid'] = $channelinfo['id'];
//        $condition['issales'] = 1;

        $info = $goodsapi->getStoreGoodsDetail($condition);
        //$info = $goodsapi->getDocument($condition, "id desc", 1);
        if (!$info) {
            $this->error('未找到相关产品');
        }
        $info['category_name'] = get_category_name($info['category_id']);
        $this->assign('info', $info);
//        dump($info);exit;
        $this->assign("contentgoodattr", $info['attribute']);
        $re = $this->fetch('Agent/order.goodsinfo');
        if ($info) {
            $this->success($re);
        } else {
            $this->error('商品信息不存在');
        }
    }

    public function cartlist() {
        $condition['is_system'] = 1;
        $condition['uid'] = cookie("store_member_id");
        $cartlist = $this->chkapi->getCartList($condition);
//        dump($cartlist);exit;
        $usercart = $cartlist['type4']['cartlist'];
        $goodsType = $cartlist['type4']['goodsType'];
        $goodsNum = $cartlist['type4']['goodsNum'];
        $goodsTotal = $cartlist['type4']['goodsTotal'];
        $this->assign('usercart', $usercart);

        $this->assign('uid', is_login());
        $this->assign('count', $goodsType ? : 0);
        $this->assign('sum', $goodsNum);
        $this->assign('price', ncPriceFormat($goodsTotal));
        $this->assign('type1_count', $cartlist['type1']['goodsType'] ? : 0);  //
        $this->assign('type2_count', $cartlist['type2']['goodsType'] ? : 0);  //
        $this->assign('type3_count', $cartlist['type3']['goodsType'] ? : 0);  //
        $this->assign('type4_count', $cartlist['type4']['goodsType'] ? : 0);  //
        $re = $this->fetch('Agent/order.cartlist');
        $this->success($re);
    }

    public function store_createOrder() {

        $sort = I("sort");
        $num = I("num");
        if (is_login()) {
            $condition['number'] = $num;
            $condition['sort'] = $sort;
            $condition['uid'] = cookie('store_member_id');
            $cartData = $this->chkapi->getOnlineCartList($condition);  // 获取勾选中的商品
//            dump($cartData);
//            exit;
        } else {
            $this->error("请先登录网站", U('Member/login'));
        }
        if (isset($cartData['error'])) {
            $this->error($cartData['msg'], U('Agent/order_step2'));  // 有错误，则返回购物车列表
        }

        $orderinfo = $cartData['goodsList'];
        $goodsTotal = $cartData['goodsTotal'];
        $goodsTotalWeight = $cartData['goodsTotalWeight'];

        cookie('goodsTotalWeight', $goodsTotalWeight);
        //如果订单商品价格为０，则提示不充允许提交订单
        if ($goodsTotal <= 0) {
            $this->error("商品金额必须大于０。", U('Agent/order_step2'));
        }

        $post['tag'] = $tag = $this->chkapi->ordersn(); //标识号
        $post['shipping_fee'] = 0;
        $post['order_message'] = '';
        $post['store_order'] = get_username() . '门店下单';
        $uid = cookie('store_member_id');
        $params = array();
        foreach ($orderinfo as $value) {
            $params['sort'][$value['sort']] = $value['sort'];
            $params['number'][$value['sort']] = $value['num'];
        }
        $params['uid'] = $uid;
        if ($orderinfo) { //订单详细插入数据库表 shoplist
            $result = $this->chkapi->createOrder($uid, $post, $params);
            if (isset($result['error'])) {
                $this->error($result['msg'], $result['url']);
            } elseif ($result['success']) {
                cookie('store_member_id', '');
                cookie('goodsTotalWeight', '');
                $this->redirect(U("Agent/order_step3", array('orderid' => $result['orderid'])));
//                $this->redirect($result['url'], '', '', $result['msg']);
            }
            //跳转到订单支付页面
            exit();
        }
    }

    /**
     * 付款
     */
    public function order_step3() {
        if (!is_login()) {
            $this->error("您还没有登录", U("Member/login"));
        }
        if (IS_POST) {

            $orderapi = new \Api\Api\OrderApi();
            $orderid = I('orderid');
            $condition['tag'] = $orderid;
            $order_info = $orderapi->getOrderInfo($condition);
            if (empty($order_info)) {
                $this->error('订单不存在，或参数错误');
            }


            $data['shipway'] = '门店自提';
            $data['backinfo'] = I('payment');
            $data['pricetotal'] = I('order_total', 0);

            M("order")->where($condition)->save($data);

            $result = $orderapi->changeOrderStatus('order_receive', $order_info);
            if (isset($result['error'])) {
                $this->error($result['msg'], Cookie('__forward__'));
            } else {
                $this->success($result['msg'], U("Agent/order_step4"));
            }
        } else {
            $id = I('get.orderid'); //获取id
            $orderapi = new \Api\Api\OrderApi();
            $condition['orderid'] = $id;
            $condition['status'] = -1;
            $orderdetail = $orderapi->getOrderDetail($condition);
//        dump($orderdetail);
            if ($orderdetail['error'] == true) {
                $this->error("订单信息不存在");
            }
            $this->assign('orderdetail', $orderdetail);

            //支付方式
            $payment = get_site_payment();
//        dump($payment);
            $this->assign('paymentlist', $payment); //支付方式
            $this->meta_title = "确认订单";
            $this->display('order.step3');
        }
    }

    /**
     * 完成
     */
    public function order_step4() {
        $this->meta_title = "下单完成";
        $this->display('order.step4');
    }

    /*     * **==================手动下单end===============================*** */

    /**
     * 统计
     */
    public function statics() {
        $map = array();
        $order = "sales desc ";
        $goods_list = $this->goodsapi->getDocument($map, $order, 15);
//        dump($goods_list);
        //构造横轴数据
        for ($i = 1; $i <= 15; $i++) {
            //横轴
            $stat_arr['xAxis']['categories'][] = $i;
        }
        $stat_arr['title'] = '商品销量排行TOP15 ';
        $stat_arr['yAxis'] = '销量';
        $stat_arr['series'][0]['name'] = '销量';
        $stat_arr['series'][0]['data'] = array();
        for ($i = 0; $i < 15; $i++) {
            $stat_arr['series'][0]['data'][] = array('name' => strval($goods_list[$i]['title']), 'y' => floatval($goods_list[$i]['sales']));
        }
        $stat_arr['legend']['enabled'] = false;
        $stat_json = getStatData_Bar2D($stat_arr);
        $this->assign('stat_json', $stat_json);
        $this->meta_title = "商品销量统计";
        $this->display();
    }

    public function memberinfo() {

        if (!S('memberinfo_' . is_login())) {
            $memberinfo = $this->memberapi->getMemberInfo(array('uid' => is_login()));
            S('memberinfo_' . is_login(), $memberinfo);
        }
//        dump(S('memberinfo_' . is_login()));
        $total = M('affiliate_bill')->where(array('ob_store_id' => is_login()))->sum('ob_pay_total');
        $total_log = M('affiliate_log')->where(array('business_id' => is_login(), 'status' => 3))->sum('money');
        $this->assign('total', $total ? : 0.00);
        $this->assign('total_log', $total_log ? : 0.00);
        $this->assign('memberinfo', S('memberinfo_' . is_login()));
    }

    /**
     * 会员统计
     */
    public function statmember() {
        //新增总数数组
        $count_arr = array('up' => 0, 'curr' => 0);
        $where = array();
        $field = ' count(*) as allnum ';

        $cur_year = date('Y', NOW_TIME);
        $cur_month = date('m', NOW_TIME);
        $stime = strtotime($cur_year . '-' . $cur_month . "-01 -1 month");
        $etime = getMonthLastDay($cur_year, $cur_month) + 86400 - 1;
        //总计的查询时间
        $count_arr['seartime'] = strtotime($cur_year . '-' . $cur_month . "-01") . '|' . $etime;

        $up_month = date('m', $stime);
        $curr_month = date('m', $etime);
        //计算横轴的最大量（由于每个月的天数不同）
        $up_dayofmonth = date('t', $stime);
        $curr_dayofmonth = date('t', $etime);
        $x_max = $up_dayofmonth > $curr_dayofmonth ? $up_dayofmonth : $curr_dayofmonth;

        //构造横轴数据
        for ($i = 1; $i <= $x_max; $i++) {
            //统计图数据
            $up_arr[$i] = 0;
            $curr_arr[$i] = 0;
            //统计表数据
            $currlist_arr[$i]['timetext'] = $i;
            //方便搜索会员列表，计算开始时间和结束时间
            $currlist_arr[$i]['stime'] = strtotime($cur_year . '-' . $cur_month . "-01") + ($i - 1) * 86400;
            $currlist_arr[$i]['etime'] = $currlist_arr[$i]['stime'] + 86400 - 1;

            $uplist_arr[$i]['val'] = 0;
            $currlist_arr[$i]['val'] = 0;
            //横轴
            $stat_arr['xAxis']['categories'][] = $i;
        }
        $where['um.reg_time'] = array('between', array($stime, $etime), '');
        $field .= ',MONTH(FROM_UNIXTIME(um.reg_time)) as monthval,day(FROM_UNIXTIME(um.reg_time)) as dayval ';
        $where['um.is_admin'] = 0;
        $where['member_agent_id'] = is_login();
        $memberlist = M('ucenter_member')->alias('um')->field($field)
                ->join('LEFT JOIN __MEMBER__  m ON um.id = m.uid')->where($where)->group('monthval,dayval')
                ->select();

//        $memberlist = M('ucenter_member')->field($field)->where($where)->select();
        if ($memberlist) {
            foreach ($memberlist as $k => $v) {
                if ($up_month == $v['monthval']) {
                    $up_arr[$v['dayval']] = intval($v['allnum']);
                    $uplist_arr[$v['dayval']]['val'] = intval($v['allnum']);
                    $count_arr['up'] += intval($v['allnum']);
                }
                if ($curr_month == $v['monthval']) {
                    $curr_arr[$v['dayval']] = intval($v['allnum']);
                    $currlist_arr[$v['dayval']]['val'] = intval($v['allnum']);
                    $count_arr['curr'] += intval($v['allnum']);
                }
            }
        }

        $stat_arr['series'][0]['name'] = '上月';
        $stat_arr['series'][0]['data'] = array_values($up_arr);
        $stat_arr['series'][1]['name'] = '本月';
        $stat_arr['series'][1]['data'] = array_values($curr_arr);
        $stat_arr['title'] = '新增会员统计';
        $stat_arr['yAxis'] = '新增会员数';
        $stat_json = getStatData_Column2D($stat_arr);
        $this->assign('stat_json', $stat_json);
        $this->meta_title = "会员统计";
        $this->display('statics');
    }

    // 品类统计
    public function statclasses() {
        $where = array();
        $field = ' count(*) as allnum,domainid ';
        $goodslist = M("document")->field($field)->where($where)->group('domainid')->select();
//        $memberlist = $model->getNewStoreStatList($where, $field, 0, '', 0, 'grade_id');
//        $sd_list = $model->getStoreDegree();
        $sd_list = $this->getDomain();
        //处理数组数据
        if (!empty($goodslist)) {
            foreach ($goodslist as $k => $v) {
                $goodslist[$k]['p_name'] = $sd_list[$v['domainid']]['name'];
                $goodslist[$k]['allnum'] = intval($v['allnum']);
            }
        }
        $data = array(
            'title' => '热销品类占比统计',
            'name' => '商品个数',
            'label_show' => true,
            'series' => $goodslist
        );
        $stat_json = getStatData_Pie($data);
        $this->assign('stat_json', $stat_json);
        $this->meta_title = "品类统计";
        $this->display('statics');
    }

    /**
     * 订单统计
     */
    public function statorder() {
        //新增总数数组
        $count_arr = array('up' => 0, 'curr' => 0);
        $where = array();
        $field = ' count(*) as allnum ';

        $cur_year = date('Y', NOW_TIME);
        $cur_month = date('m', NOW_TIME);
        $stime = strtotime($cur_year . '-' . $cur_month . "-01 -1 month");
        $etime = getMonthLastDay($cur_year, $cur_month) + 86400 - 1;
        //总计的查询时间
        $count_arr['seartime'] = strtotime($cur_year . '-' . $cur_month . "-01") . '|' . $etime;

        $up_month = date('m', $stime);
        $curr_month = date('m', $etime);
        //计算横轴的最大量（由于每个月的天数不同）
        $up_dayofmonth = date('t', $stime);
        $curr_dayofmonth = date('t', $etime);
        $x_max = $up_dayofmonth > $curr_dayofmonth ? $up_dayofmonth : $curr_dayofmonth;

        //构造横轴数据
        for ($i = 1; $i <= $x_max; $i++) {
            //统计图数据
            $up_arr[$i] = 0;
            $curr_arr[$i] = 0;
            //统计表数据
            $currlist_arr[$i]['timetext'] = $i;
            //方便搜索会员列表，计算开始时间和结束时间
            $currlist_arr[$i]['stime'] = strtotime($cur_year . '-' . $cur_month . "-01") + ($i - 1) * 86400;
            $currlist_arr[$i]['etime'] = $currlist_arr[$i]['stime'] + 86400 - 1;

            $uplist_arr[$i]['val'] = 0;
            $currlist_arr[$i]['val'] = 0;
            //横轴
            $stat_arr['xAxis']['categories'][] = $i;
        }
        $where['create_time'] = array('between', array($stime, $etime));
        $field .= ',MONTH(FROM_UNIXTIME(create_time)) as monthval,day(FROM_UNIXTIME(create_time)) as dayval ';
//        $memberlist = $model->statByMember($where, $field, 0, '', 'monthval,dayval');
//        $memberlist = $this->memberapi->getMemberlist($where);
        $memberlist = M('order')->field($field)->where($where)->select();

//        dump($memberlist);

        if ($memberlist) {
            foreach ($memberlist as $k => $v) {


                if ($up_month == $v['monthval']) {
                    $up_arr[$v['dayval']] = intval($v['allnum']);
                    $uplist_arr[$v['dayval']]['val'] = intval($v['allnum']);
                    $count_arr['up'] += intval($v['allnum']);
                }
                if ($curr_month == $v['monthval']) {
                    $curr_arr[$v['dayval']] = intval($v['allnum']);
                    $currlist_arr[$v['dayval']]['val'] = intval($v['allnum']);
                    $count_arr['curr'] += intval($v['allnum']);
                }
            }
        }
        $stat_arr['series'][0]['name'] = '上月';
        $stat_arr['series'][0]['data'] = array_values($up_arr);
        $stat_arr['series'][1]['name'] = '本月';
        $stat_arr['series'][1]['data'] = array_values($curr_arr);
        $stat_arr['title'] = '新增会员统计';
        $stat_arr['yAxis'] = '新增会员数';

        $stat_json = getStatData_LineLabels($stat_arr);

        $this->assign('stat_json', $stat_json);
        $this->meta_title = "订单统计";
        $this->display('statics');
    }

}
