<?php

/**
 * 订单管理
 */

namespace Api\Api;

use Api\Api\Api;
use Common\Model\TreeModel;
use Api\Api\GoodsApi;

class OrderApi extends Api {

    private $status = 0;   //订单状态
    private $uid = 0;   // 用户ID

    /**
     * 构造方法，实例化操作模型
     */

    protected function _init() {
        $this->model = new TreeModel();
    }

    /**
     * 更改订单状态
     * @param type $status_type  订单状态
     * @param type $order_info  订单信息
     * @return type 返回处理结果
     */
    public function changeOrderStatus($status_type, $order_info) {
        $this->uid = is_login();
        try {
            switch ($status_type) {
                case "order_cancel": // 取消订单
                    $result = $this->_cancelOrder($order_info);
                    break;
                case "order_refund": // 退货订单
                    $result = $this->_refundOrder($order_info);
                    break;
                case "order_pay":   // 付款订单
                    $result = $this->_payOrder($order_info);
                    break;
                case "order_deliver":   // 发货订单
                    $result = $this->_deliverOrder($order_info);
                    break;
                case "order_receive": // 收货订单
                    $result = $this->_receiveOrder($order_info);
                    break;
                case "order_comment": // 评论订单
                    $result = $this->_commentsOrder($order_info);  // 订单商品
                    break;
                case "order_delete": // 删除订单
                    // TODO：
                    break;
                default:
                    return array('error' => true, 'msg' => '非法操作');
                    break;
            }
        } catch (Exception $e) {
            return array('error' => true, 'msg' => '操作失败' . $e);
        }
        return $result;
    }

    /**
     * 确认收货
     * @param type $order_info 订单信息
     * @return type 处理结果
     */
    private function _receiveOrder($order_info) {
        if (session('memberinfo.member_type') == 2 && $order_info['order_type'] == 1) {
            $orderid = $order_info["orderid"];
            // 订单确认收货的时间（完成时间）和是否发表评论的标识
            $data['status'] = 3;
            $data['complete_time'] = NOW_TIME;
            $result = M('order')->where(array('id' => $order_info['id']))->save($data);
            //根据订单id获取购物清单,设置商品状态为已完成.，status=3
            $id = $order_info['id'];
            $goodslist = M("shoplist")->where("orderid='$id'")->select();

            foreach ($goodslist as $k => $val) {
                //获取购物清单数据表产品id，字段id
                $byid = $val["id"];
                M("shoplist")->where("id='$byid'")->setField(array("status" => "3", "iscomment" => 0));
                // 如果用户类型为2 则是门店购买商品，把订单商品添加到门店采购的商品表里面
//                $this->addStoreGoods($val);
                $this->updateStoreGoods($val);
            }
            if ($result) {
                M("order_delivery")->where(array('order_id' => $id))->data(array('status' => 3, 'update_time' => NOW_TIME))->save();
                //记录行为
                // 增加会员消费积分
                M("member")->where(array('uid' => $order_info['uid']))->setInc('points', floor($order_info['pricetotal']));
                 // 更改提成日志状态
                M('affiliate_log')->where(array('order_sn'=>$orderid))->setField('status',3);
                action_log('update_order', 'order', $orderid, UID);
                $this->addOrderLog($id, array('msg' => $order_info['msg'] ? : '确认收货成功', 'status' => 3, 'roleid' => is_login(), 'uid' => $order_info['uid']));
                return array('success' => true, 'msg' => $order_info['msg'] ? : '确认收货成功，您可以返回评价商品', 'url' => Cookie('__forward__'));
            } else {
                return array('error' => true, 'msg' => '确认收货失败' . $orderid);
            }
        } else {  // 非门店收获
            $orderid = $order_info["orderid"];
            // 订单确认收货的时间（完成时间）和是否发表评论的标识
            $data['status'] = 3;
            $data['complete_time'] = NOW_TIME;
            $result = M('order')->where(array('id' => $order_info['id']))->save($data);
            // 增加会员消费积分
            M("member")->where(array('uid' => $order_info['uid']))->setInc('points', floor($order_info['pricetotal']));
            //根据订单id获取购物清单,设置商品状态为已完成.，status=3
            $id = $order_info['id'];
            $goodslist = M("shoplist")->where("orderid='$id'")->select();

            foreach ($goodslist as $k => $val) {
                //获取购物清单数据表产品id，字段id
                $byid = $val["id"];
                M("shoplist")->where("id='$byid'")->setField(array("status" => "3", "iscomment" => 0));
            }
            if ($result) {
                
                M("order_delivery")->where(array('order_id' => $id))->data(array('status' => 3, 'update_time' => NOW_TIME))->save();
                
                //记录行为
                action_log('update_order', 'order', $orderid, UID);
                 // 更改提成日志状态
                M('affiliate_log')->where(array('order_sn'=>$orderid))->setField('status',3);
                $this->addOrderLog($id, array('msg' => $order_info['msg'] ? : '确认收货成功', 'status' => 3, 'roleid' => is_login(), 'uid' => $order_info['uid']));
                return array('success' => true, 'msg' => $order_info['msg'] ? : '确认收货成功，您可以返回评价商品', 'url' => Cookie('__forward__'));
            } else {
                return array('error' => true, 'msg' => '确认收货失败' . $orderid);
            }
        }
    }

    /**
     * 付款订单
     * @param type $order_info
     */
    private function _payOrder($order_info) {
        $data['status'] = 1;
        $data['payment_time'] = NOW_TIME;
        $result = M('order')->where(array('id' => $order_info['id']))->save($data);
        if ($result) {
             M("order_delivery")->where(array('order_id' => $order_info['id']))->data(array('status' => 1, 'update_time' => NOW_TIME))->save();
            //记录行为
            action_log('update_order', 'order', $order_info['orderid'], UID);
            $this->addOrderLog($order_info['id'], array('msg' => $order_info['msg'] ? : '订单付款成功', 'status' => 1, 'roleid' => is_login(), 'uid' => $order_info['uid']));
            return array('success' => true, 'msg' => $order_info['msg'] ? : '订单付款成功', 'url' => Cookie('__forward__'));
        } else {
            return array('error' => true, 'msg' => '订单付款失败' . $order_info['orderid']);
        }
    }

    /**
     * 发货订单
     * @param type $order_info
     */
    private function _deliverOrder($order_info) {
        $data['ispay'] = 4;
        $data['send_time'] = NOW_TIME;
        $result = M('order')->where(array('id' => $order_info['id']))->save($data);
        if ($result) {
            //记录行为
            action_log('update_order', 'order', $order_info['orderid'], UID);
            $this->addOrderLog($order_info['id'], array('msg' => $order_info['msg'] ? : '订单发货成功', 'status' => 1, 'roleid' => is_login(), 'uid' => $order_info['uid']));
            return array('success' => true, 'msg' => $order_info['msg'] ? : '订单发货成功', 'url' => Cookie('__forward__'));
        } else {
            return array('error' => true, 'msg' => '订单发货失败' . $order_info['orderid']);
        }
    }

    /**
     *  评价订单商品
     * @param type $ordergoods  订单商品
     * @return type
     */
    private function _commentsOrder($ordergoods) {
        M('shoplist')->where(array('id' => $ordergoods['id']))->setField("iscomment", "2"); // 更改订单商品评论状态
        M('document')->where(array('id' => $ordergoods['goodid']))->setInc('comment', 1);  // 商品评论数量加+

        $sum = M('shoplist')->where('orderid=' . $ordergoods['orderid'])->count();  // 获取订单商品数量
        $iscomment = M('shoplist')->where(array('orderid' => $ordergoods['orderid'], 'iscomment' => array('gt', 0)))->count();  // 获取已经评论的商品数量
        if ($sum == $iscomment) {
            $res = M('order')->where(array('id' => $ordergoods['orderid']))->setField('iscomment', 1);
        }
        $this->addOrderLog($ordergoods['orderid'], array('msg' => $ordergoods['msg'] ? : '您评论了订单中的商品', 'status' => 4));

        return array('success' => true, 'msg' => '您评论订单商品成功');
    }

    /**
     * 退货退款订单
     * @param type $ordergoods
     * @return type
     */
    private function _refundOrder($ordergoods) {
        $refunddata = array(
            'order_id' => $ordergoods['id'],
            'order_sn' => $ordergoods['tag'],
            'uid' => $ordergoods['uid'],
            'user_name' => get_username($ordergoods['uid']),
            'refund_type' => 2,
            'return_type' => 2,
            'refund_state' => 0,
            'refund_sn' => $this->makeOrderSn(NOW_TIME . $ordergoods['id']), // 生成退款单号
            'refund_amount' => $ordergoods['total'], //$order_info['total'], // 商品金额
            'order_goods_type' => $ordergoods['order_type'],
            'goods_num' => $ordergoods['goods_num'],
            'goods_id' => $ordergoods['goods_id'],
            'order_goods_id' => $ordergoods['order_goods_id'],
            'pic_info' => $ordergoods['pics'],
            'reason_id' => $ordergoods['reason_id'],
            'reason_info' => $ordergoods['reason_info'],
            'buyer_message' => $ordergoods['buyer_message'],
            'store_id' => $ordergoods['store_id'],
            'add_time' => NOW_TIME,
        );
        $ref = $this->CreateOrderRefund($refunddata);
        $res = M('order')->where(array('id' => $ordergoods['id']))->save(array('refund_status' => 1));             // 订单已退回
        $re = M('shoplist')->where(array('id' => $ordergoods['order_goods_id']))->save(array('status' => 3, 'uid' => $refunddata['uid'], 'roleid' => is_login()));  // 退货状态
        $this->addOrderLog($ordergoods['id'], array('msg' => get_username() . '退货了订单商品', 'status' => 5, 'uid' => $refunddata['uid'], 'roleid' => is_login()));  //
        $this->addOrderLog($ordergoods['id'], array('msg' => '系统生成了退货单号：' . $refunddata['refund_sn'] . '，请注意查看', 'status' => 5));
        if ($ref) {
            return array('success' => true, 'msg' => '订单退货成功,并生成了退货单号');
        } else {
            return array('error' => true, 'msg' => '订单退货失败2');
        }
    }

    /**
     * 取消订单
     * @param type $order_info
     */
    private function _cancelOrder($order_info) {

        if ($order_info['ispay'] == 1 && $order_info['status'] == -1) {  // 未付款，直接取消订单
            $data['status'] = -2;
            $data['cancel_time'] = NOW_TIME;
            $res = M('order')->where(array('id' => $order_info['id']))->save($data);
            $msg = $order_info['msg'] ? : get_username() . '取消了订单';
            $this->addOrderLog($order_info['id'], array('msg' => $msg, 'status' => -2));
            // 库存还原，销量还原
            $this->orgin_goods_stock($order_info);
            if ($res) {
                return array('success' => true, 'msg' => '订单取消成功');
            } else {
                return array('error' => true, 'msg' => '订单取消失败');
            }
        } elseif ($order_info['status'] == 1) {  // 已付款，生成退款单，未发货
            $data['status'] = -3;
            $data['cancel_time'] = NOW_TIME;
            $re = M('order')->where(array('id' => $order_info['id']))->save($data);
            $refunddata = array(
                'order_id' => $order_info['id'],
                'store_id' => $order_info['store_id'],
                'order_sn' => $order_info['tag'],
                'uid' => $order_info['uid'],
                'user_name' => get_username($order_info['uid']),
                'refund_type' => 1,
                'refund_state' => 0,
                'refund_sn' => $this->makePaySn(NOW_TIME . $order_info['id']), // 生成退款单号
                'refund_amount' => $order_info['pricetotal'], //$order_info['total'], // 商品金额
                'order_goods_type' => $order_info['order_type'],
                'add_time' => NOW_TIME,
            );
            $ref = $this->CreateOrderRefund($refunddata);

            // 库存还原，销量还原
            $this->orgin_goods_stock($order_info);


            $this->addOrderLog($order_info['id'], array('msg' => get_username() . '取消了订单', 'status' => -2));
            $this->addOrderLog($order_info['id'], array('msg' => '系统生成了退款单号：' . $refunddata['refund_sn'] . '，请注意查看', 'status' => -2));
            if ($re && $ref) {
                return array('success' => true, 'msg' => '订单取消成功,并生成了退款单号');
            } else {
                return array('error' => true, 'msg' => '订单取消失败2');
            }
        } else {
            return array('error' => true, 'msg' => '非法操作');
        }
    }

    /**
     * 还原订单商品库存
     * @param type $order_info
     */
    public function orgin_goods_stock($order_info) {
        $ordergooods = M('shoplist')->where(array('orderid' => $order_info['id']))->select();
        foreach ($ordergooods as $key => $value) {
            // 修改库存
            if ($value['proid']) {
                $re = M('p_xianshi_goods')->where("goods_id=" . $value["goodid"] . " and xianshi_id = '" . $value["proid"] . "'")->setInc('xianshi_stock', $value["num"]);
            }
            if ($value['store_id'] > 0) {   // 门店iD大于0  则修改 门店商品库存/销量
                $re = M('store_goods')->where(array("goods_id" => $value["goodid"], 'store_id' => $value['store_id']))->setInc('stock', $value["num"]);   // 增加库存
                $re = M('store_goods')->where(array("goods_id" => $value["goodid"], 'store_id' => $value['store_id']))->setDec('sales_num', $value["num"]);   // 减少销量
            } else {    // 否则修改 平台商品库存销量
                $re = M('document_product')->where("id=" . $value["goodid"])->setInc('stock', $value["num"]);
                //记录销售数量 并修改缓存
                $re = M('document')->where("id=" . $value["goodid"])->setDec('sales', $value["num"]);
                $re = M('document_product')->where("id=" . $value["goodid"])->setDec('totalsales', $value["num"]);
            }
        }
        return $re;
    }

    /**
     * 添加订单信息到门店商品表
     * @param array $goodsInfo  订单商品信息
     * @return array bool
     */
    public function addStoreGoods($goodsInfo) {
        if (empty($goodsInfo)) {
            return false;
        }
        $storegoodsModel = M('store_goods');
        $goods = $storegoodsModel->where(array('store_id' => $goodsInfo['uid'], 'goods_id' => $goodsInfo['goodid']))->find();
        $goodsData['store_id'] = $goodsInfo['uid'];
        $goodsData['domainid'] = $goodsInfo['domainid'];
        $goodsData['category_id'] = $goodsInfo['category_id'];
        $goodsData['brandid'] = M("document")->where(array('id' => $goodsInfo['goodid']))->getField('brandid'); //$goodsInfo['brandid'];
        $goodsData['goods_name'] = M("document")->where(array('id' => $goodsInfo['goodid']))->getField('title'); //$goodsInfo['brandid'];
        $goodsData['goods_id'] = $goodsInfo['goodid'];
        $goodsData['buy_price'] = $goodsInfo['price'];
        $goodsData['goods_price'] = $goodsInfo['orgin_price'];
        $goodsData['product_type'] = $goodsInfo['cart_type'];
        $goodsData['goods_marketprice'] = get_good_yprice($goodsInfo['goodid']);
        $goodsData['is_show'] = 1;
        $goodsData['create_time'] = NOW_TIME;
        if ($goods) { // 编辑
            $result = $storegoodsModel->where(array('store_id' => $goodsInfo['uid'], 'goods_id' => $goodsInfo['goodid']))->save($goodsData);
        } else {    // 新增
            $result = $storegoodsModel->data($goodsData)->add();
        }
        $this->addOrderLog($goodsInfo['orderid'], array('uid' => $goodsInfo['uid'], 'roleid' => is_login(), 'msg' => '采购商品 数量为：' . $goodsInfo['num']));
        return $result;
    }

    /**
     * 更新门店商品库存
     * @param type $goodsInfo
     */
    public function updateStoreGoods($goodsInfo) {
        $storegoodsModel = M('store_goods');
        $goods = $storegoodsModel->where(array('store_id' => $goodsInfo['uid'], 'goods_id' => $goodsInfo['goodid']))->find();
        if ($goods) { // 已经采购 则在原来的采购中增加采购数量
            $goodsData['goods_num'] = $goods['goods_num'] + $goodsInfo['num'];
            $goodsData['stock'] = $goods['goods_num'] + $goodsInfo['num'];
        } else {
            $goodsData['goods_num'] = $goodsInfo['num'];
            $goodsData['stock'] = $goodsInfo['num'];
        }
        if ($goods) { // 编辑
            $result = $storegoodsModel->where(array('store_id' => $goodsInfo['uid'], 'goods_id' => $goodsInfo['goodid']))->save($goodsData);
        } else {    // 新增
            $result = $storegoodsModel->data($goodsData)->add();
        }
        $this->addOrderLog($goodsInfo['orderid'], array('uid' => $goodsInfo['uid'], 'roleid' => is_login(), 'msg' => '采购商品 数量为：' . $goodsInfo['num'] . '采购成功'));
        return $result;
    }

    /**
     * 添加订单评论记录
     * @param type $comments_data
     * @return type
     */
    public function addOrderComments($comments_data) {
        $comments = M('comment');
        $result = $comments->data($comments_data)->add();
        return $result;
    }

    /**
     * 自动更新订单订单状态 1、自动取消 2、自动完成 3、自动评价
     */
    public function autoDealOrderStatus() {
        // 自动取消订单
        $this->_autoCancelOrder();
        // 自动完成订单
        $this->_autoCompleOrder();
        // 自动评价订单
        $this->_autoCommentOrder();
    }

    /**
     * 自动取消订单
     */
    private function _autoCancelOrder() {
        // 自动取消订单
        $day = $this->_getMaxDay('order_cancel');
        $times = NOW_TIME - $day * 24 * 60 * 60;
        $condition['create_time'] = array("LT", $times);
        $condition['status'] = -1;
        $condition['ispay'] = 1;
        $orderlist = $this->_getOrderlist($condition);
        if (empty($orderlist)) {
            return false;
        }
        foreach ($orderlist as $k => $val) {
            $val['msg'] = '超过' . $day . '天未付款系统自动取消了订单';
            $this->changeOrderStatus('order_cancel', $val);
        }
    }

    /**
     * 自动完成订单
     */
    private function _autoCompleOrder() {
        // 自动确认收货
        $day = 1;
        $this->_getMaxDay('order_receive');
        $times = NOW_TIME - $day * 24 * 60 * 60;
        $condition['send_time'] = array("LT", $times);  // 发货时间大于7天自动收货
        $condition['status'] = 2; // 已发货
        $condition['ispay'] = 2;    // 已支付
        $orderlist = $this->_getOrderlist($condition);
        if (empty($orderlist)) {
            return false;
        }
        foreach ($orderlist as $k => $val) {
            $val['msg'] = '超过' . $day . '天未收货，系统自动完成了订单';
            $this->changeOrderStatus('order_receive', $val);
        }
    }

    /**
     * 自动评价订单
     */
    private function _autoCommentOrder() {
        // 自动评论订单
        $day = $this->_getMaxDay('order_comment');
        $times = NOW_TIME - $day * 24 * 60 * 60;
//        echo date("Y-m-d H:i:s",1462965630);
        $condition['complete_time'] = array("LT", $times);      // 完成时间大于7天自动评价
        $condition['status'] = 3; // 已完成
        $condition['iscomment'] = 0;    // 待评价
        $orderlist = $this->_getOrderlist($condition);
        if (empty($orderlist)) {
            return false;
        }
        foreach ($orderlist as $k => $val) {
            $goodslist = M('shoplist')->where(array('orderid' => $val['id']))->select();
            foreach ($goodslist as $key => $value) {
                $value['msg'] = '系统自动评价了订单';
//                $value['orderid'] = $val['id'];
                $this->changeOrderStatus('order_comment', $value);
                $comments_data['content'] = "这个产品不错，下次还在你家商城购买。";
                $comments_data['category_id'] = $value['category_id'];
                $comments_data['domainid'] = $value['domainid'];
                $comments_data['score'] = 5;
                $comments_data['goodscore'] = 5;
                $comments_data['servicescore'] = 5;
                $comments_data['deliveryscore'] = 5;
                $comments_data['status'] = 2;
                $comments_data['brandid'] = 0;
                $comments_data['tag'] = $val['tag'];
                $comments_data['orderid'] = $value['orderid'];
                $comments_data['goodid'] = $value['goodid'];
                $comments_data['create_time'] = NOW_TIME;
                $comments_data['uid'] = is_login();
                $this->addOrderComments($comments_data);
//                $this->_commentsOrder($value);
            }
        }
    }

    private function _getOrderlist($condition) {
        $orderlist = M('order')->where($condition)->select();
        return $orderlist;
    }

    public function _getMaxDay($day_type = 'all') {
        $max_data = array(
            'order_cancel' => C('ORDER_AUTO_CANCEL') ? : 7, //自动取消订单时间
            'order_receive' => C('ORDER_AUTO_COMPLE') ? : 7, //自动完成订单
            'order_comment' => C('ORDER_AUTO_COMMENT') ? : 7, //自动评价订单
        );
        if ($day_type == 'all') {
            return $max_data; //返回所有
        }
        if (intval($max_data[$day_type]) < 1) {
            $max_data[$day_type] = 1; //最小的值设置为1
        }
        return $max_data[$day_type];
    }

    /**
     * 获取订单列表
     * @param type $conditon
     * @return type
     */
    public function getRefundOrderList($conditon,$sort='refund_id DESC', $bases = '') {
        $goodsModel = new GoodsApi();
        if ($bases) {
            $page = I('page', 1, 'int');
            $pageSize = $bases['pageSize'] ? : 10;
            $start = ($page - 1) * $pageSize;
            $limit = $pageSize;
            $list = M('order_refund')->where($conditon)->order($sort)->limit("{$start},{$limit}")->select();
        } else {
            $ordermodel = new \Web\Controller\HomeController();
            $list = $ordermodel->_lists("order_refund", $conditon, 'refund_id DESC', array());
        }
        if (empty($list)) {
            return array(); // array('error' => false, 'msg' => '订单不存在');
        }
        $map = array();
        //商品属性表
        $attributeid = 6; //产品规格属性ID
        $documentViewModel = D("DocumentView");
        $ordergoods = M("shoplist"); //订单商品表
        foreach ($list as $n => $val) {
            //2015/4/22 16:07 查询出订单商品规格
            if ($val['goods_id']) {
                $orderproducts = $ordergoods->where(array('orderid' => $val['order_id'], 'goodid' => $val['goods_id'],'id'=>$val['order_goods_id']))->select();
            } else {
                $orderproducts = $ordergoods->where(array('orderid' => $val['order_id']))->select();
            }
            if (!empty($orderproducts)) {
                //$ordersum++;
            }
            foreach ($orderproducts as $keyid => $value) {
                // SELECT * FROM yzj_good_attr WHERE gid=65 AND attributeid=6;
                $productid = $value['goodid'];
                $map['gid'] = $productid;
                $map['attributeid'] = $attributeid;
                $goodattr = M('good_attr')->where($map)->select();
                if (!empty($goodattr)) {
                    $orderproducts[$keyid]['goodattr'] = $goodattr[0]['value'];
                }
                //商品图片
                $product_detail = $documentViewModel->find($value['goodid']);
                if ($product_detail["cover_id"]) {
                    $arrmap['id'] = $product_detail["cover_id"];
                    $attinfo = array();
                    $attinfo = M("picture")->where($arrmap)->getField("id,path");

                    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                    foreach ($attinfo as $ckey => $cvalue) {
                        $attinfo[$ckey] = __PICURL__ .  $cvalue; //$product_detail['domainid'] . '/' .
                    }

                    $product_detail["pics_img"] = $attinfo;
                }

                $orderproducts[$keyid]['cover_id'] = $product_detail["cover_id"];
                $orderproducts[$keyid]['pics_img'] = $product_detail["pics_img"];
                $orderproducts[$keyid]['pics'] = $product_detail["pics"];
                $orderproducts[$keyid]['channelname'] = $product_detail["channelname"];
                //生成缩略图
                $url = $orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']];
                $newurl = $goodsModel->_image_thumb($url, 100, 100);
                $orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']] = $newurl;
            }
            $list[$n]['handle'] = $this->get_order_handle($val); // 获取订单操作权限
            $list[$n]['orderStatus'] = $this->get_order_status($val['status'], $val['ispay']);
            $list[$n]['goodslist'] = $orderproducts;
            //商品数量
            $list[$n]['productnum'] = count($orderproducts);
        }
        return $list;
    }

    /**
     * 获取退货订单详情
     * @param type $condition
     */
    public function getRefundOrderDetail($condition) {
        $goodsModel = new GoodsApi();
        $order = M("order_refund");
//        $condition['uid'] = is_login();
        $detail = $order->where($condition)->select();  // 订单详情
        if (empty($detail)) {
            return array('error' => true, 'msg' => '数据不存在，或参数错误');
        }
        $attributeid = 6; //产品规格属性ID
        $documentViewModel = D("DocumentView");
        if (!empty($detail)) {
            $ordergoods = M("shoplist");
            foreach ($detail as $n => $val) {
                if (!is_array($detail[$n]['id'])) {
                    $detail[$n]['oldid'] = $detail[$n]['id'];
                }
//                $orderproducts = $list->where('orderid=\'' . $val['id'] . '\'')->order('groupid DESC,price DESC')->select();
                if ($val['goods_id']) {
                    $orderproducts = $ordergoods->where(array('orderid' => $val['order_id'], 'goodid' => $val['goods_id'],'id'=>$val['order_goods_id']))->select();
                } else {
                    $orderproducts = $ordergoods->where(array('orderid' => $val['order_id']))->select();
                }
                foreach ($orderproducts as $keyid => $value) {
                    // SELECT * FROM yzj_good_attr WHERE gid=65 AND attributeid=6;
                    $productid = $value['goodid'];
                    $map['gid'] = $productid;
                    $map['attributeid'] = $attributeid;
                    $goodattr = M('good_attr')->where($map)->select();
                    if (!empty($goodattr)) {
                        $orderproducts[$keyid]['goodattr'] = $goodattr[0]['value'];
                    }
                    //商品图片
                    $product_detail = $documentViewModel->find($value['goodid']);
                    if ($product_detail["cover_id"]) {
                        $arrmap['id'] = $product_detail["cover_id"];
                        $attinfo = array();
                        $attinfo = M("picture")->where($arrmap)->getField("id,path");

                        //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                        foreach ($attinfo as $ckey => $cvalue) {
                            $attinfo[$ckey] = __PICURL__ .  $cvalue; //$product_detail['domainid'] . '/' .
                        }
                        $product_detail["pics_img"] = $attinfo;
                    }
                    $orderproducts[$keyid]['cover_id'] = $product_detail["cover_id"];
                    $orderproducts[$keyid]['pics_img'] = $product_detail["pics_img"];
                    $orderproducts[$keyid]['pics'] = $product_detail["pics"];
                    $orderproducts[$keyid]['channelname'] = $product_detail["channelname"];
                    //生成缩略图
                    $url = $orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']];
                    $newurl = $goodsModel->_image_thumb($url, 100, 100);
                    $orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']] = $newurl;
                }
                $detail[$n]['goodslist'] = $orderproducts;
                //商品数量
                $detail[$n]['productnum'] = count($orderproducts);
            }

            // 收货人信息
            $addressid = $detail[0]['addressid'];
            //$order->where("orderid='$id'")->getField("addressid");
            $address = M("order_address")->find($addressid);
            $detail[$n]['address'] = $address;
            //获取订单配送方式 物流信息
            $distribution = $detail[0]['distribution'];
            $shipping = M("distribution")->find($distribution);
            $shipping['shipping_code'] = $detail[0]['toolid'] ? : '物流单号'; // 物流单号
            $shipping['shipping_name'] = $detail[0]['tool'] ? : $shipping['title']; // 物流公司
            $detail[$n]['shipping'] = $shipping;
            $detail[$n]['shipway'] = $shipping ? '快递' : '上门自提';
        } else {
            return array('error' => true, 'msg' => '订单信息不存在，或参数错误');
        }
        if (!empty($detail)) {
            //订单状态
            $orderinfo = $detail[0];

            // 订单状态
            if ($orderinfo['refund_state'] < 3) {
                $current_step = 'processing';   // 处理中
                $status_txt = '处理中';
            } elseif ($orderinfo['refund_state'] == 3) {
                $current_step = 'hP';           // 已处理
                $status_txt = '已处理';
            }
            
            
            switch ($orderinfo['refund_state']) {
                case -2:
                    $status_txt = '拒绝';
                    break;
                case 0:
                    $status_txt = '申请状态';
                    break;
                case 1:
                    $status_txt = '同意退货';
                    break;
                case 2:
                    $status_txt = '管理员已处理';
                    break;
                case 3:
                    $status_txt = '已完成';
                    break;

                default:
                    break;
            }

            $orderinfo['current_step'] = $current_step;  // 第几步骤
            $orderinfo['statusstring'] = $status_txt;   // 订单状态
            //如果订单是银行转账，显示银行转账信息根据用户ID和订单ID查询
            $map = array();
            $map['orderid'] = $orderinfo['oldid'];
            $order_bank = M("order_bank");
            $bankinfo = $order_bank->where($map)->limit(1)->select();
            if (!empty($bankinfo)) {//银行转账汇款信息
                $bank = M("bank");
                $bankfo = $bank->find($bankinfo[0]['bankid']);
                $bankinfo[0]['bankstring'] = array();
                if (!empty($bankfo)) {
                    $bankinfo[0]['bankstring'] = $bankfo;
                }
            }
            $orderinfo['bankinfo'] = $bankinfo[0];

            //发票信息
            if (!empty($orderinfo['invoice'])) {
                $serinvoice = $orderinfo['invoice'];
                $invoice = unserialize($serinvoice);
                $orderinfo['bankinfo'] = $invoice;
            }

            //对组合商品重新设计数组结构
            $goodsgroup = array();
            foreach ($orderinfo['id'] as $okey => $ovalue) {
                if ($ovalue['groupid'] > 0) {
                    $goodsgroup[$ovalue['groupid']][] = $ovalue;
                }
                if ($ovalue['groupid'] > 0 && $ovalue['price'] == 0) {
                    unset($orderinfo['id'][$okey]);
                }
            }

            foreach ($orderinfo['id'] as $pkey => $pvalue) {
                if ($pvalue['groupid'] > 0) {
                    $orderinfo['id'][$pkey]['goodsinfo'] = $goodsgroup[$pvalue['groupid']];
                }
            }
        }
        return $orderinfo;
    }

    /**
     * 获取订单信息
     * @param type $conditon
     * @param type $order_goods
     * @return type
     */
    public function getOrderInfo($conditon, $order_goods = null) {
        $orderid = $conditon['orderid'];
        unset($conditon['orderid']);
        $orderInfo = M('order')->where($conditon)->find();
        if ($order_goods) {
            $ordergoods = M('shoplist')->where('orderid=\'' . $orderid . '\'')->order('groupid DESC,price DESC')->select();
            $orderInfo['ordergoods'] = $ordergoods;
        }
        return $orderInfo;
    }

    /**
     * 获取订单列表
     * @param type $conditon 查询条件
     * @param type $sort   排序
     * @param type $is_address   地址
     * @param type $bases   其他参数手机浏览
     * @return type
     */
    public function getOrderList($conditon, $sort = "id DESC", $is_address = 0, $bases = '') {
        $goodsModel = new GoodsApi();
        if ($bases) {
            $page = I('page', 1, 'int');
            $pageSize = $bases['pageSize'] ? : 10;
            $start = ($page - 1) * $pageSize;
            $limit = $pageSize;
            $list = M('order')->where($conditon)->order($sort)->limit("{$start},{$limit}")->select();
        } else {
            $ordermodel = new \Web\Controller\HomeController();
            $list = $ordermodel->_lists("order", $conditon, $sort, array());
        }
        
        $map = array();
        //商品属性表
        $attributeid = 6; //产品规格属性ID
        $documentViewModel = D("DocumentView");
        $ordergoods = M("shoplist"); //订单商品表
        foreach ($list as $n => $val) {
            //2015/4/22 16:07 查询出订单商品规格
            $orderproducts = $ordergoods->where('orderid=\'' . $val['id'] . '\'')->select();
            if (!empty($orderproducts)) {
                //$ordersum++;
            }
            foreach ($orderproducts as $keyid => $value) {
                // SELECT * FROM yzj_good_attr WHERE gid=65 AND attributeid=6;
                $productid = $value['goodid'];
                $map['gid'] = $productid;
                $map['attributeid'] = $attributeid;
                $goodattr = M('good_attr')->where($map)->select();
                if (!empty($goodattr)) {
                    $orderproducts[$keyid]['goodattr'] = $goodattr[0]['value'];
                }
                //商品图片
                $product_detail = $documentViewModel->find($value['goodid']);
                if ($product_detail["cover_id"]) {
                    $arrmap['id'] = $product_detail["cover_id"];
                    $attinfo = array();
                    $attinfo = M("picture")->where($arrmap)->getField("id,path");

                    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                    foreach ($attinfo as $ckey => $cvalue) {
                        $attinfo[$ckey] = __PICURL__ . $cvalue; // $product_detail['domainid'] . '/' .
                    }

                    $product_detail["pics_img"] = $attinfo;
                }
                //判断该商品是否已申请了售后
                $refund_id = M('order_refund')->where("order_id='{$val['id']}' AND order_goods_id='{$value['id']}'")->getField('refund_id');
                if($refund_id){
                	$orderproducts[$keyid]['refund_id'] = $refund_id;
                }
                else{
                	$orderproducts[$keyid]['refund_id'] = '';
                }
                $orderproducts[$keyid]['cover_id'] = $product_detail["cover_id"];
                $orderproducts[$keyid]['pics_img'] = $product_detail["pics_img"];
                $orderproducts[$keyid]['pics'] = $product_detail["pics"];
                $orderproducts[$keyid]['channelname'] = $product_detail["channelname"];
                //生成缩略图
                $url = $orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']];
                $newurl = $goodsModel->_image_thumb($url, 100, 100);
                $orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']] = $newurl;
            }
            $list[$n]['handle'] = $this->get_order_handle($val); // 获取订单操作权限
            $list[$n]['orderStatus'] = $this->get_order_status($val['status'], $val['ispay']);
            $list[$n]['goodslist'] = $orderproducts;
            $list[$n]['detail_url'] = U('Order/details', array('orderid' => $val['orderid']));
            $list[$n]['balanceString'] = $this->balanceString($val);
            //商品数量
            $list[$n]['productnum'] = count($orderproducts);
            if ($is_address) {
                $address = '';
//                echo $val['id'].'/';
                $list[$n]['address'] = M("order_address")->where(array('orderid' => $val['id']))->find();
//                echo $address['id'];
//                $list[$n]['address'] = $address;
            }
        }
        return $list;
    }

    /**
     * 获取订单详情
     * @param type $condition
     */
    public function getOrderDetail($condition) {
        $goodsModel = new GoodsApi();
        $order = D("order");
        //$detail = $order->where($condition)->select();  // 订单详情
        $detail = $order->where($condition)->find();  // 订单详情
        $attributeid = 6; //产品规格属性ID
        $documentViewModel = D("DocumentView");
        if (!empty($detail)) {
        	$list = M("shoplist");

        	if (!is_array($detail['id'])) {
        		$detail['oldid'] = $detail['id'];
        	}
        	$orderproducts = $list->where('orderid=\'' . $detail['id'] . '\'')->order('groupid DESC,price DESC')->select();
        	foreach ($orderproducts as $keyid => $value) {
        		// SELECT * FROM yzj_good_attr WHERE gid=65 AND attributeid=6;
        		$productid = $value['goodid'];
        		$map['gid'] = $productid;
        		$map['attributeid'] = $attributeid;
        		$goodattr = M('good_attr')->where($map)->select();
        		if (!empty($goodattr)) {
        			$orderproducts[$keyid]['goodattr'] = $goodattr[0]['value'];
        		}
        		//商品图片
        		$product_detail = $documentViewModel->find($value['goodid']);
        		if ($product_detail["cover_id"]) {
        			$arrmap['id'] = $product_detail["cover_id"];
        			$attinfo = array();
        			$attinfo = M("picture")->where($arrmap)->getField("id,path");
        	
        			//2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
        			foreach ($attinfo as $ckey => $cvalue) {
        				$attinfo[$ckey] = __PICURL__ . $cvalue; //                            $attinfo[$ckey] = __PICURL__ . $cvalue;
        			}
        			$product_detail["pics_img"] = $attinfo;
        		}
        		$orderproducts[$keyid]['cover_id'] = $product_detail["cover_id"];
        		$orderproducts[$keyid]['pics_img'] = $product_detail["pics_img"];
        		$orderproducts[$keyid]['pics'] = $product_detail["pics"];
        		$orderproducts[$keyid]['channelname'] = $product_detail["channelname"];
        		//生成缩略图
        		$url = $orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']];
        		$newurl = $goodsModel->_image_thumb($url, 100, 100);
        		$orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']] = $newurl;
        	}
        	$detail['goodslist'] = $orderproducts;
        	//商品数量
        	$detail['productnum'] = count($orderproducts);
        	
        	
        /*
            $list = M("shoplist");
            foreach ($detail as $n => $val) {
                if (!is_array($detail[$n]['id'])) {
                    $detail[$n]['oldid'] = $detail[$n]['id'];
                }
                $orderproducts = $list->where('orderid=\'' . $val['id'] . '\'')->order('groupid DESC,price DESC')->select();
                foreach ($orderproducts as $keyid => $value) {
                    // SELECT * FROM yzj_good_attr WHERE gid=65 AND attributeid=6;
                    $productid = $value['goodid'];
                    $map['gid'] = $productid;
                    $map['attributeid'] = $attributeid;
                    $goodattr = M('good_attr')->where($map)->select();
                    if (!empty($goodattr)) {
                        $orderproducts[$keyid]['goodattr'] = $goodattr[0]['value'];
                    }
                    //商品图片
                    $product_detail = $documentViewModel->find($value['goodid']);
                    if ($product_detail["cover_id"]) {
                        $arrmap['id'] = $product_detail["cover_id"];
                        $attinfo = array();
                        $attinfo = M("picture")->where($arrmap)->getField("id,path");

                        //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                        foreach ($attinfo as $ckey => $cvalue) {
                            $attinfo[$ckey] = __PICURL__ . $cvalue; //                            $attinfo[$ckey] = __PICURL__ . $cvalue;
                        }
                        $product_detail["pics_img"] = $attinfo;
                    }
                    $orderproducts[$keyid]['cover_id'] = $product_detail["cover_id"];
                    $orderproducts[$keyid]['pics_img'] = $product_detail["pics_img"];
                    $orderproducts[$keyid]['pics'] = $product_detail["pics"];
                    $orderproducts[$keyid]['channelname'] = $product_detail["channelname"];
                    //生成缩略图
                    $url = $orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']];
                    $newurl = $goodsModel->_image_thumb($url, 100, 100);
                    $orderproducts[$keyid]['pics_img'][$orderproducts[$keyid]['cover_id']] = $newurl;
                }
                $detail[$n]['goodslist'] = $orderproducts;
                //商品数量
                $detail[$n]['productnum'] = count($orderproducts);
            }
            // 收货人信息
//            $addressid = $detail[0]['addressid'];
            //$order->where("orderid='$id'")->getField("addressid");
            $address = M("order_address")->where(array('orderid' => $detail[$n]['id']))->find();
//            dump($address);
            $detail[$n]['address'] = $address;
            //获取订单配送方式 物流信息
            
            $shippings = M("order_delivery")->where(array('order_id'=>$detail[0]['id']))->select();
//            dump($shippings);
            $distribution = $detail[0]['distribution'];
            $shipping = M("distribution")->find($distribution);
            $shipping['shipping_code'] = $detail[0]['toolid'] ? : '物流单号'; // 物流单号
            $shipping['shipping_name'] = $detail[0]['tool'] ? : $shipping['title']; // 物流公司
            $detail[$n]['shipping'] = $shipping;
            $detail[$n]['deliverys'] = $shippings;
            $detail[$n]['shipway'] = $shipping ? '快递' : '上门自提';
         */
        	// 收货人信息
        	$address = M("order_address")->where(array('orderid' => $detail['id']))->find();
        	$detail['address'] = $address;
        	
        	//获取订单配送方式 物流信息
        	$shippings = M("order_delivery")->where(array('order_id'=>$detail['id']))->find();
        	$distribution = $detail['distribution'];
        	$shipping = M("distribution")->find($distribution);
        	$shipping['shipping_code'] = $detail['toolid'] ? : '物流单号'; // 物流单号
        	$shipping['shipping_name'] = $detail['tool'] ? : $shipping['title']; // 物流公司
        	$detail['shipping'] = $shipping;
        	$detail['deliverys'] = $shippings;
        	$detail['shipway'] = $shipping ? '快递' : '上门自提';
        	
        	//订单状态
        	$status = $detail['status'];
        	$ispay = $detail['ispay'];
        	
        	// 订单状态
        	$orderStatus = $this->get_order_status($status, $ispay);
        	$detail['current_step'] = $orderStatus['current_step'];  // 第几步骤
        	$detail['statusstring'] = $orderStatus['status_txt'];   // 订单状态
        	//订单支付方式
        	$paymentstring = $this->get_payment($ispay);
        	$detail['paymentstring'] = $paymentstring;
        	//如果订单是银行转账，显示银行转账信息根据用户ID和订单ID查询
        	$map = array();
        	$map['orderid'] = $detail['oldid'];
        	$order_bank = M("order_bank");
        	//$bankinfo = $order_bank->where($map)->limit(1)->select();
        	$bankinfo = $order_bank->where($map)->limit(1)->find();
        	if (!empty($bankinfo)) {//银行转账汇款信息
        		$bank = M("bank");
        		$bankfo = $bank->find($bankinfo['bankid']);
        		$bankinfo['bankstring'] = array();
        		if (!empty($bankfo)) {
        			$bankinfo['bankstring'] = $bankfo;
        		}
        	}
        	$detail['bankinfo'] = $bankinfo;
        	
        	//发票信息
        	if (!empty($detail['invoice'])) {
        		$serinvoice = $detail['invoice'];
        		$invoice = unserialize($serinvoice);
        		$detail['bankinfo'] = $invoice;
        	}
        	
        	//对组合商品重新设计数组结构
        	$goodsgroup = array();
        	foreach ($detail['id'] as $okey => $ovalue) {
        		if ($ovalue['groupid'] > 0) {
        			$goodsgroup[$ovalue['groupid']][] = $ovalue;
        		}
        		if ($ovalue['groupid'] > 0 && $ovalue['price'] == 0) {
        			unset($detail['id'][$okey]);
        		}
        	}
        	
        	foreach ($detail['id'] as $pkey => $pvalue) {
        		if ($pvalue['groupid'] > 0) {
        			$detail['id'][$pkey]['goodsinfo'] = $goodsgroup[$pvalue['groupid']];
        		}
        	}
        	
        	
        } else {
            return array('error' => true, 'msg' => '订单信息不存在，或参数错误');
        }
        return $detail;
        
        /*if (!empty($detail)) {
            //订单状态
            $orderinfo = $detail[0];
            $status = $orderinfo['status'];
            $ispay = $orderinfo['ispay'];

            // 订单状态
            $orderStatus = $this->get_order_status($status, $ispay);
            $orderinfo['current_step'] = $orderStatus['current_step'];  // 第几步骤
            $orderinfo['statusstring'] = $orderStatus['status_txt'];   // 订单状态
            //订单支付方式
            $paymentstring = $this->get_payment($ispay);
            $orderinfo['paymentstring'] = $paymentstring;
            //如果订单是银行转账，显示银行转账信息根据用户ID和订单ID查询
            $map = array();
            $map['orderid'] = $orderinfo['oldid'];
            $order_bank = M("order_bank");
            $bankinfo = $order_bank->where($map)->limit(1)->select();
            if (!empty($bankinfo)) {//银行转账汇款信息
                $bank = M("bank");
                $bankfo = $bank->find($bankinfo[0]['bankid']);
                $bankinfo[0]['bankstring'] = array();
                if (!empty($bankfo)) {
                    $bankinfo[0]['bankstring'] = $bankfo;
                }
            }
            $orderinfo['bankinfo'] = $bankinfo[0];

            //发票信息
            if (!empty($orderinfo['invoice'])) {
                $serinvoice = $orderinfo['invoice'];
                $invoice = unserialize($serinvoice);
                $orderinfo['bankinfo'] = $invoice;
            }

            //对组合商品重新设计数组结构
            $goodsgroup = array();
            foreach ($orderinfo['id'] as $okey => $ovalue) {
                if ($ovalue['groupid'] > 0) {
                    $goodsgroup[$ovalue['groupid']][] = $ovalue;
                }
                if ($ovalue['groupid'] > 0 && $ovalue['price'] == 0) {
                    unset($orderinfo['id'][$okey]);
                }
            }

            foreach ($orderinfo['id'] as $pkey => $pvalue) {
                if ($pvalue['groupid'] > 0) {
                    $orderinfo['id'][$pkey]['goodsinfo'] = $goodsgroup[$pvalue['groupid']];
                }
            }
         }
        return $orderinfo;*/
    }

    /**
     * 获取用户的订单各个状态的数量
     * @parms type $uid
     * @return type
     */
    public function getOrderStautsCount($uid) {

        /* 待支付 */
        $data['nopaynum'] = M("order")->where("uid={$uid} and status=-1")->count();
        /* 待发货 */
        $data['paynum'] = M("order")->where("uid='$uid' and status=1")->count();
        /* 待收货 */
        $data['shipnum'] = M("order")->where("uid='$uid' and status='2'")->count();
        //未完成订单（待支付，待发货，待确认）
        //已完成
        $data['completenum'] = M("order")->where("uid='$uid' and status='3'")->count();
        //已取消
        $data['cancelordernum'] = M("order")->where("uid='$uid' and status='-2'")->count();
        // 待评价
        $data['noevalnum'] = M('order')->where(array('uid' => $uid, 'status' => 3, 'iscomment' => 0))->count();
        // 已评价
        $data['evalnum'] = M('order')->where(array('uid' => $uid, 'status' => 3, 'iscomment' => 1))->count();
        //订单总数量
        $data['ordersum'] = M('order')->where('uid=' . $uid)->count();
        return $data;
    }

    /**
     * 生成退货退款单
     * @param type $data
     */
    public function CreateOrderRefund($data) {
        $order_refund = M('order_refund');
        $result = $order_refund->data($data)->add();
        return $result;
    }

    /**
     * 订单日志
     * @param type $orderid
     * @param type $data
     */
    public function addOrderLog($orderid, $data) {
        $order_log = M("order_log");
        $order_log->create();
        $order_log->order_id = $orderid;
        $order_log->log_msg = $data['msg'];
        $order_log->log_time = NOW_TIME;
        $order_log->log_role = $data['roleid'] ? : $this->uid;
        $order_log->log_user = $data['uid'] ? : $this->uid;
        $order_log->log_orderstate = $this->status ? : $data['status'];
        $order_log->add();
    }

    /**
     * 获取订单日志
     * @param type $orderid
     */
    public function getOrderLog($orderid) {
        $log_list = M("order_log")->where(array('order_id' => $orderid))->order("log_id desc")->select();
        return $log_list;
    }

    /**
     * 获取订单操作
     * @param type $orderinfo
     * @return string
     */
    public function get_order_handle($orderinfo) {
        $status = $orderinfo['status'];
        $handle = '';
        if ($status == 2) {
            $href1 = U('Order/order_receive', array('orderid' => $orderinfo['orderid']));
            $handle = "<a class='pj confirm' data-msg='请收到商品后，再确认收货，否则可能钱货两空！' href='$href1'>确认收货</a>";
        }
        $pay = $orderinfo['ispay'];
        if ($status == 1 && $pay == 2) { // 已经付款 等待发货
            $hrefqx = U('Order/cancel_order', array('id' => $orderinfo['orderid'], 'orderid' => $orderinfo['id']));
            //$handle = "<a class='qx confirm' data-msg='确定要取消订单吗？' href=" . $hrefqx . ">取消订单</a>";
            $handle = "<a class='qx' href='javascript:;'>已付款</a>";
            $handle .= "<a class='qx' href='javascript:;'>等待商家发货</a>";
        }
        if ($pay == 1 && $status == -1) {
            $hrefp = U('Payment/index', array('id' => $orderinfo['orderid']));
            $hrefqx = U('Order/cancel_order', array('id' => $orderinfo['tag'], 'orderid' => $orderinfo['id']));
            $handle = "<a class='pj' href=" . $hrefp . ">立即付款</a>";
            $handle .= "<a class='qx confirm' data-msg='确定要取消订单吗？' href=" . $hrefqx . ">取消订单</a>";
        }

        if ($pay == 3 && $status == -1) {  // 货到付款
            $hrefp = U('Payment/index', array('id' => $orderinfo['orderid']));
//            $handle = "<a class='pj' href=" . 'javascript:;;' . ">货到付款</a>";
//            $handle .="<a class='qx confirm' data-msg='确定要取消订单？' href='javascript: alert(123)'>取消订单</a>";
//            echo "<dd><a class='pay_btn' href='$hrefp'>前去支付</a> </dd>";
//            $hrefp = U('Center/bank', array('id' => $orderinfo['orderid']));
//            echo "<dd><a class='pay_btn' href='$hrefp'>转账信息</a> </dd>";
        }
        if ($status == 3) {  // 交易完成 ，可以评价了
//            $handle = '<a class="sc confirm" data-msg="确定要删除订单吗？" href="javascript:;">删除订单</a>';
            $href1 = U('Comment/index',array('ordersn'=>$orderinfo['orderid'])) . '#' . $orderinfo['orderid'];
            $handle = "<a class='pj' href=" . $href1 . ">立即评价</a>";
            if ($orderinfo['refund_status'] < 1) {
//                $handle .= "<a class='sc' href=" . $href1 . ">申请售后</a>";
            }
        }
        if ($status == 3 && $orderinfo['iscomment'] == 1) {  // 交易完成 ，可以评价了
//            $handle = '<a class="sc confirm" data-msg="确定要删除订单吗？" href="javascript:;">删除订单</a>';
            $href1 = U('Comment/index') . '#' . $orderinfo['orderid'];
//            $href1 = U('Comment/index',array('orderid'=>$orderinfo['orderid'])).'#'.$orderinfo['orderid'];
            $handle = ' <a href="javascript:;" style="background-color: gray;cursor:not-allowed" class="pj">已评价</a>';
        }
        return $handle;
    }

    /**
     * 获取剩余时间
     * @param type $orderinfo
     */
    public function balanceString($orderinfo) {
        $strtime = '';
        if ($orderinfo['status'] == -1) { // 待付款还剩时间
            $day = $this->_getMaxDay('order_cancel');
            $times = $day * 24 * 60 * 60;
            $time = $orderinfo['create_time'] + $times - NOW_TIME;
            if ($time >= 3600) {
                $strtime .= intval($time / 3600) . '小时';
                $time = $time % 3600;
            } else {
                $strtime .= '';
            }
            if ($time >= 60) {
                $strtime .= intval($time / 60) . '分钟';
                $time = $time % 60;
            } else {
                $strtime .= '';
            }
        }
        if ($orderinfo['status'] == 2) { // 自动收货
            $day = $this->_getMaxDay('order_receive');
            $times = $day * 24 * 60 * 60;
            $time = $orderinfo['create_time'] + $times - NOW_TIME;
            if ($time >= 3600) {
                $strtime .= intval($time / 3600) . '小时';
                $time = $time % 3600;
            } else {
                $strtime .= '';
            }
            if ($time >= 60) {
                $strtime .= intval($time / 60) . '分钟';
                $time = $time % 60;
            } else {
                $strtime .= '';
            }
        }
        if ($orderinfo['status'] == 3 && $orderinfo['iscomment'] == 0) { // 自动评论
            $day = $this->_getMaxDay('order_comment');
            $times = $day * 24 * 60 * 60;
            $time = $orderinfo['create_time'] + $times - NOW_TIME;
            if ($time >= 3600) {
                $strtime .= intval($time / 3600) . '小时';
                $time = $time % 3600;
            } else {
                $strtime .= '';
            }
            if ($time >= 60) {
                $strtime .= intval($time / 60) . '分钟';
                $time = $time % 60;
            } else {
                $strtime .= '';
            }
        }
        return $strtime;
    }

    /**
     *  获取支付方式
     * @param type $ispay
     * @return string
     */
    public function get_payment($ispay) {
        if ($ispay == 1) {
            $paymentstring = "在线支付";
        }
        if ($ispay == 2) {
            $paymentstring = "在线支付";
        }
        if ($ispay == 3) {
            $paymentstring = "货到付款";
        }
        if ($ispay == 4) {
            $paymentstring = "货到付款";
        }
        if ($ispay == 5) {
            $paymentstring = "银行转账汇款";
        }

        return $paymentstring;
    }

    /**
     * 获取订单状态 /第几步骤
     * @param type $status // -1：待付款  1：已付款 2：待收货 3： 已完成
     * @param type $ispay
     */
    public function get_order_status($status, $ispay) {
        $s1 = 'sP';  // 拍下商品   confirmGoods=>cG / stayPayment =>sP  / sendGoods => sG   finished
        $s2 = 'sP';  // 待付款
        $s3 = '';  // 待发货
        $s4 = '';  // 确认收货
        $s5 = '';  // 已完成
        $statusarray = array();
        if ($status == -1 and $ispay == 1) {
            $s1 = 'sP';
            $status_txt = '待付款';
        }
        if ($status == -1 and $ispay == 4) {
            $s1 = 'sP';
        }
        if ($status == -1 and $ispay == 5) {
            $s2 = 'sP';
        }
        if ($status == 1) {
            $s3 = 'sG';
        }

        if ($status == 2) {
            $s4 = 'cG';
            $current_step = $s4;
        } elseif ($status == 3) {
            $s5 = 'finished';
            $current_step = $s5;
        } elseif ($status == -2) {
            $current_step = 'cancel';
        } else {
            $current_step = (($s5 ? $s5 : $s4) ? $s4 : $s3) ? $s3 : $s2;
        }

        // 订单状态string
        if ($status == -1 and $ispay == 1) {
            $statusstring = "等待支付";
        }
        if ($status == -1 and $ispay == 3) {
            $current_step = 'sG';
            $statusstring = "货到付款，等待发货";
        }
        if ($status == -1 and $ispay == 2) {
            $statusstring = "线上支付完成";
        }
        if (($status == -1 and $ispay == 4) or ( $status == 1 and $ispay == 4)) {
            $current_step = 'cG';
            $statusstring = "已发货等待收货";
        }
        if ($status == 1) {
            $statusstring = "等待发货";
        }
        if ($status == 2) {
            $statusstring = "已发货";
        }
        if ($status == 3 && $ispay == 4) {
            $statusstring = "已收货确认收款";
        }
        if (($status == 3 && $ispay == 2) or ( $status == 3 && $ispay == 5)) {
            $statusstring = "交易成功";
        } elseif ($status == 3 && $ispay == 1) {
            $statusstring = "交易成功";
        }
//        if ($status == 4) {
//            $statusstring = "申请取消订单";
//        }
//        if ($status == 5) {
//            $statusstring = "取消订单被拒绝";
//        }
        if ($status == -2) {
            $statusstring = "订单已取消";
        }
        $result['current_step'] = $current_step;
        $result['status_txt'] = $statusstring;
        return $result;
    }

    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    public function makePaySn($member_id) {
        return mt_rand(10, 99)
                . sprintf('%010d', time() - 946656000)
                . sprintf('%03d', (float) microtime() * 1000)
                . sprintf('%03d', (int) $member_id % 1000);
    }

    /**
     * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
     * 生成订单编号(年取1位 + $pay_id取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @param $pay_id 支付表自增ID
     * @return string
     */
    public function makeOrderSn($pay_id) {
        //记录生成子订单的个数，如果生成多个子订单，该值会累加
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num ++;
        }
        return (date('y', time()) % 9 + 1) . sprintf('%013d', $pay_id) . sprintf('%02d', $num);
    }

    /**
     * affiliate log
     * 更新或添加商品提成日志
     * @param type $data arrray(log_id,'order_id','order_sn','goods_id','goods_price','member_id','member_name',money,affiliate,status,business_id);
     * @param type $condition 修改条件 如果为空则添加 否者更新
     * @return type
     */
    public function affiliate_log($data, $condition = null) {
        $affiliate_log = M('affiliate_log');
        if (!empty($condition)) {
            $result = $affiliate_log->where($condition)->save($data);
        } else {
            $data['status'] = 0;
            $data['add_time'] = NOW_TIME;
            $result = $affiliate_log->data($data)->add();
        }
        return $result;
    }

    /**
     * 获取订单提成日志明显
     * @param type $condition
     */
    public function affiliate_log_list($condition) {
//        $lists = M('affiliate_log')->where($condition)->select();
        $affiliatemodel = new \Web\Controller\HomeController();
        $lists = $affiliatemodel->_lists("affiliate_log", $condition, 'log_id DESC', array());
        if (empty($lists)) {
            return array();
        }
        return $lists;
    }

    /**
     * 获取订单提成单
     * @param type $condition
     */
    public function affiliate_bill_list($condition) {
//        $lists = M('affiliate_bill')->where($condition)->select();
        $affiliatemodel = new \Web\Controller\HomeController();
        $lists = $affiliatemodel->_lists("affiliate_bill", $condition, 'ob_create_date DESC', array());
        return $lists;
    }

}
