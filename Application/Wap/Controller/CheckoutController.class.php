<?php

/**
 * 购物流程/订单结算
 */

namespace Wap\Controller;

use Wap\Controller\HomeController;
use Api\Api\CheckoutApi;

class CheckoutController extends HomeController {

    private $chkapi = '';

    protected function _initialize() {
        if (!is_login()) {
            $this->error("请先登录");
        }
        parent::_initialize();
        $this->chkapi = new CheckoutApi();
    }

    public function index() {
        
    }

    /**
     * 立即购买
     */
    public function buynow() {
        $post = I('post.');
        if (empty($post['id'])) {
            $this->error("参数错误，请重新购买");
        }
        if (empty($post['number'])) {
            $this->error("参数错误，请重新购买");
        }
        $parameters = explode(',', $post['parameters'][0]);
        $params['goods_id'] = $post['id'];
        $params['number'] = $post['number'];
        $params['proid'] = $post['proid'];
        $params['store_id'] = $post['store_id'];
        $params['parameters'] = $parameters;
//        params array('goods_id','number',proid,store_id,parameters); 
        $cartData = $this->chkapi->getOnlineBuynow($params);
//        dump($cartData);exit;
//        dump($cartData);
        if (isset($cartData['error'])) {
            $this->error($cartData['msg'], U('Cart/index'));  // 有错误，则返回购物车列表
        }
        
        $result = array();
        foreach ($cartData['goodsList'] as $k => $v) {
            $result[$v['warehouse']][$k] = $v;
        }

        $distribution = M("distribution")->where(array('type' => 0))->getField("id,title", true);
        $this->assign('distribution', $distribution);
        $this->assign('ordergoodslist', $result);

         // 计算每个发货仓商品重量/商品价格/运费
        $temp_data = array();
        foreach ($result as $key => $item) {
            $temp_data[$key]['weight'] = 0;         // 重量
            $temp_data[$key]['total'] = 0;          // 总价
            $temp_data[$key]['haiguan_rate'] = 0;   // 海关税
            $temp_data[$key]['shipping_fee'] = 0;   // 运费
            $temp_data[$key]['warehouse'] = $key;   // 运费模板ID
            foreach ($item as $k => $temp_goods) {
                $temp_data[$key]['weight'] += $temp_goods['weight'];
                $temp_data[$key]['total'] += $temp_goods['total'];
                $temp_data[$key]['haiguan_rate'] += $temp_goods['haiguan_rate'];
            }
        }
        $new_shipping = $this->calcshippingfee($temp_data);
        $this->assign('new_shipping', $new_shipping);

        $cartList = $cartData['goodsList'];
        $goodsTotal = $cartData['goodsTotal'];
        $goodsTotalWeight = ncPriceFormat($cartData['goodsTotalWeight']);  // 商品重量
        $goodsCount = $cartData['goodsCount'];
        $goodsType = $cartData['goodsType'];
        $orderType = $cartData['orderType'];
        $haiguan_rate_total = $cartData['haiguan_rate_total'];

        $address = M("transport")->where(array('uid' => is_login(), 'isdefault' => 1))->order("orderid desc")->find();
        if (empty($address)) {
            $address = M("transport")->where(array('uid' => is_login()))->order("id desc")->find();
        }
//        dump($address);
        $this->assign('address', $address);
        $shipping = $this->ajaxshipping(1);

        if (empty($shipping)) {
            $shipping_fee = 0;
        } else {
            if ($shipping['snum'] >= $goodsTotalWeight) {
                $shipping_fee = $shipping['sprice'];
            } else {
                $xfee = ($goodsTotalWeight - $shipping['snum']) * $shipping['xprice'];
                $shipping_fee = $shipping['sprice'] + $xfee;
            }
        }
        $shipping_fee = 0;
        foreach ($new_shipping as $value){
            $shipping_fee +=$value['shipping_fee'];
        }
        
        cookie("new_shipping", think_encrypt(serialize($new_shipping)));   // 分单包裹信息
        S("confirm_order",think_encrypt(serialize($cartList)));
        cookie("confirm_order", think_encrypt(serialize($cartList)));   // 商品信息
        cookie('goodsTotalWeight', $goodsTotalWeight);                   // 商品总重量
        cookie('goodsTotal', $goodsTotal);                   // 商品总金额
        //如果订单商品价格为０，则提示不充允许提交订单
        if ($goodsTotal <= 0) {
            $this->error("商品金额必须大于０。", U('Cart/index'));
        }

        $this->assign('orderType', $orderType);
        $this->assign('iscart', 0);
        $this->assign('goodsTotalWeight', $goodsTotalWeight);
        $this->assign('shoplist', $cartList);
        //计算提交的订单的商品运费 商品有运费根据配送方式的没运费不同
        //配送方式
        if ($goodsTotal > C('LOWWEST')) {   //商品金额小于免邮费额度
            $shipping_fee = 0;
        }
        $uid = is_login();
//        if (is_login()) {
//            $uid = session('user_auth.uid');
//            $map["uid"] = $uid;
//            $map["status"] = 1;
//            $address = M("transport")->where($map)->limit(10)->select();
//            $this->assign("address", $address);
//        }
        $tag = $this->ordersn(); //标识号
        $all = $goodsTotal + $shipping_fee + $haiguan_rate_total;     // 订单总金额
        $this->assign('all', ncPriceFormat($all));
        $this->assign('uid', $uid);

        $this->assign('count', $goodsType); //商品种类数量
        $this->assign('sum', $goodsCount); //商品总数量
        $this->assign('haiguan_rate_total', ncPriceFormat($haiguan_rate_total)); //海关税费

        $this->assign('tag', $tag);
        $this->assign('total', ncPriceFormat($goodsTotal));
        $this->assign('shipping_fee', ncPriceFormat($shipping_fee));
        $this->meta_title = "订单结算";
         if ($orderType == 4) {
            $this->display('store.buystep1');
        } else {
            $this->display('buystep1');
        }
    }

    /**
     * 提交选中的购物车的商品
     */
    public function buystep1() {
        $sort = I("sort");
        $num = I("num");
        if (empty($sort) || empty($num)) {
            $this->error("购物车空空的,赶紧前往首页选商品去!");
        }

        if (is_login()) {
            $condition['number'] = $num;
            $condition['sort'] = $sort;
            $cartData = $this->chkapi->getOnlineCartList($condition);  // 获取勾选中的商品
//            dump($cartData);
        } else {
            $this->error("请先登录网站", U('Member/login'));
            $count = $this->getCnt();
        }
        if (isset($cartData['error'])) {
            $this->error($cartData['msg'], U('Cart/index'));  // 有错误，则返回购物车列表
        }
        
        
        $result = array();
        foreach ($cartData['goodsList'] as $k => $v) {
            $result[$v['warehouse']][$k] = $v;
        }

        $distribution = M("distribution")->where(array('type' => 0))->getField("id,title", true);
        $this->assign('distribution', $distribution);
        $this->assign('ordergoodslist', $result);

        // 计算每个发货仓商品重量/商品价格/运费
        $temp_data = array();
        foreach ($result as $key => $item) {
            $temp_data[$key]['weight'] = 0;         // 重量
            $temp_data[$key]['total'] = 0;          // 总价
            $temp_data[$key]['haiguan_rate'] = 0;   // 海关税
            $temp_data[$key]['shipping_fee'] = 0;   // 运费
            $temp_data[$key]['warehouse'] = $key;   // 运费模板ID
            foreach ($item as $k => $temp_goods) {
                $temp_data[$key]['weight'] += $temp_goods['weight'];
                $temp_data[$key]['total'] += $temp_goods['total'];
                $temp_data[$key]['haiguan_rate'] += $temp_goods['haiguan_rate'];
            }
        }
        $new_shipping = $this->calcshippingfee($temp_data);
        $this->assign('new_shipping', $new_shipping);

        $cartList = $cartData['goodsList'];
        $goodsTotal = $cartData['goodsTotal'];
        $goodsTotalWeight = ncPriceFormat($cartData['goodsTotalWeight']);  // 商品重量
        $goodsCount = $cartData['goodsCount'];
        $goodsType = $cartData['goodsType'];
        $orderType = $cartData['orderType'];
        $haiguan_rate_total = $cartData['haiguan_rate_total'];

        $address = M("transport")->where(array('uid' => is_login(), 'isdefault' => 1))->order("orderid desc")->find();
        if (empty($address)) {
            $address = M("transport")->where(array('uid' => is_login()))->order("id desc")->find();
        }
//        dump($address);exit;
        $this->assign('address', $address);
        $shipping = $this->ajaxshipping(1);

        if (empty($shipping)) {
            $shipping_fee = 0;
        } else {
            if ($shipping['snum'] >= $goodsTotalWeight) {
                $shipping_fee = $shipping['sprice'];
            } else {
                $xfee = ($goodsTotalWeight - $shipping['snum']) * $shipping['xprice'];
                $shipping_fee = $shipping['sprice'] + $xfee;
            }
        }
        $shipping_fee = 0;
        foreach ($new_shipping as $value){
            $shipping_fee +=$value['shipping_fee'];
        }
        cookie("new_shipping", think_encrypt(serialize($new_shipping)));   // 分单包裹信息
        S("confirm_order",think_encrypt(serialize($cartList)));
        cookie("confirm_order", think_encrypt(serialize($cartList)));   // 商品信息
        cookie('goodsTotalWeight', $goodsTotalWeight);                   // 商品总重量
        cookie('goodsTotal', $goodsTotal);                   // 商品总金额
        //如果订单商品价格为０，则提示不充允许提交订单
        if ($goodsTotal <= 0) {
            $this->error("商品金额必须大于０。", U('Cart/index'));
        }

        $this->assign('orderType', $orderType);
        $this->assign('iscart', 1);
        $this->assign('goodsTotalWeight', $goodsTotalWeight);
        $this->assign('shoplist', $cartList);
        //计算提交的订单的商品运费 商品有运费根据配送方式的没运费不同
        //配送方式
//        if ($goodsTotal > C('LOWWEST')) {   //商品金额小于免邮费额度
//            $shipping_fee = 0;
//        }
//        $uid = 0;
//        if (is_login()) {
//            $uid = session('user_auth.uid');
//            $map["uid"] = $uid;
//            $map["status"] = 1;
//            $address = M("transport")->where($map)->limit(10)->select();
//            $this->assign("address", $address);
//        }
        $tag = $this->ordersn(); //标识号
        $all = $goodsTotal + $shipping_fee + $haiguan_rate_total;     // 订单总金额
        $this->assign('all', ncPriceFormat($all));
        $this->assign('uid', $uid);

        $this->assign('count', $goodsType); //商品种类数量
        $this->assign('sum', $goodsCount); //商品总数量
        $this->assign('haiguan_rate_total', ncPriceFormat($haiguan_rate_total)); //海关税费

        $this->assign('tag', $tag);
        $this->assign('total', ncPriceFormat($goodsTotal));
        $this->assign('shipping_fee', ncPriceFormat($shipping_fee));
        $this->meta_title = "订单结算";
         if ($orderType == 4) {
            $this->display('store.buystep1');
        } else {
            $this->display('buystep1');
        }
    }

    /**
     * 生成订单
     */
    public function createOrder() {
        if (!is_login()) {
            $this->error('请先登录，在购买商品', U('Member/login'));
        }
        $addressid = I("senderid");
        if (empty($addressid)) { // 检查收获地址
            $this->error('请检查收获地址是否填写');
        }
        $orderinfo = think_decrypt(S("confirm_order"));
        //清空cookie信息
//        cookie("confirm_order", null);
        $orderinfo = unserialize($orderinfo);
        if (empty($orderinfo)) {
            $this->error("请选择商品", U('Cart/index'));
            exit();
        }

        $params = array();
        foreach ($orderinfo as $key => $value) {
            $params['sort'][$value['sort']] = $value['sort'];
            $params['number'][$value['sort']] = $value['num'];
        }

        $newuser = 0;
        if (!is_login()) {//如果是为登录用户则判断电话然后帮用户注册 
            $userinfo = $this->createuserbyTel();
            if (!$userinfo) {
                $this->error("注册失败");
            }
            $uid = $userinfo['uid'];
            $senderid = $userinfo['senderid'];
            $newuser = $userinfo["newuser"];
        } else {
            $uid = session('user_auth.uid');
            $senderid = I("senderid");
        }
        $this->assign('newuser', $newuser);
        /* =======================保存订单信息及订单商品信息=================================== */
        if ($orderinfo) { //订单详细插入数据库表 shoplist
            $post = I('post.');
            $iscart = $post['iscart'];
            if ($post['orderType'] == 4) {  // 门店商品
                $post['backinfo'] = "等待门店配送";
            }
            if (empty($iscart)) {   // 立即购买
                $order_keys = array_keys($orderinfo);
                $sort = $order_keys[0];
                $parameters = explode('-', $orderinfo[$sort]['sort']);
                $buyparams['goods_id'] = $orderinfo[$sort]['goodid'];
                $buyparams['number'] = $orderinfo[$sort]['num'];
                $buyparams['proid'] = $orderinfo[$sort]['proid'];
                $buyparams['store_id'] = $orderinfo[$sort]['store_id'];
                $buyparams['parameters'] = $parameters;
                $params = $buyparams;
            }
            $post['order_from'] = 'wap';
            $result = $this->chkapi->createOrder($uid, $post, $params,$iscart);
            if (isset($result['error'])) {
                $this->error($result['msg'], $result['url']);
            } elseif ($result['success']) {
                S("confirm_order", '');   // 商品信息
                cookie("confirm_order", '');   // 商品信息
                cookie('goodsTotalWeight', '');                   // 商品总重量
                cookie('goodsTotal', '');
                $this->redirect($result['url']);
//                $this->redirect($result['url'], '', '', $result['msg']);
            }
            //跳转到订单支付页面
            exit();
        }
    }

    private function ordersn() {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        //$orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%04d%02d', rand(1000, 9999),rand(0,99));
        //2015/5/8 11:04 sheshanhu
//        $orderSn = $yCode[intval(date('Y')) - 2011]  .date('Ymd'). substr(time(), -3) . substr(microtime(), 2, 3) . sprintf('%04d%02d', rand(1000, 9999),rand(0,99));

        $orderSn = 'E' . date("Ymdhis") . sprintf('%01d', rand(0, 9));

        return $orderSn;
    }
    
    /**
     * 根据收货地址&发货仓计算运费
     * @param type $param
     */
    public function calcshippingfee($param) {
        $address_id = I('address_id', 0);
        $goodsTotalWeight = 0;
        foreach ($param as $key => &$value) {
            $goodsTotalWeight = $value['weight'];
            $shipping = $this->calcshipping($value, $address_id);
            if ($shipping) {
                if ($shipping['snum'] >= $goodsTotalWeight) {
                    $value['shipping_fee'] = $shipping['sprice'];
                } else {
                    $xfee = ($goodsTotalWeight - $shipping['snum']) * $shipping['xprice'];
                    $value['shipping_fee'] = $shipping['sprice'] + $xfee;
                }
                $value['org_shipping_fee'] = $value['shipping_fee'];
                if (C('LOWWEST') < $value['total']) {
                    $value['shipping_fee'] = 0;
                }
            }
        }
       
        return $param;
    }

    private function calcshipping($data, $address_id = 0) {
        $shippingModel = D("ShippingView");
        if ($address_id) {
            $condition['uid'] = is_login();
            $condition['id'] = $address_id;
            $address = M("transport")->where($condition)->order("isdefault desc")->find();
        } else {
            $address = M("transport")->where(array('uid' => is_login(), 'isdefault' => 1))->order("orderid desc")->find();
            if (empty($address)) {
                $address = M("transport")->where(array('uid' => is_login()))->order("id desc")->find();
            }
        }
        if ($address) {
            $map = array();
//            $map["status"] = 1;
            $map["shipping_id"] = $data['warehouse'];
            $map['area_name'] = array('like', '%' . $address['city'] . '%');
            $result = $shippingModel->where($map)->order('weight DESC')->find();  // 如果城市查寻不到
            if (empty($result)) {
                $map['area_name'] = array('like', '%' . $address['province'] . '%');
                $result = $shippingModel->where($map)->order('weight DESC')->find(); // 省市
                if (empty($result)) { //如果都查询不到 这返回全国统一设置
                    unset($map['area_name']);
                    $map['is_default'] = 1;
                    $result = $shippingModel->where($map)->order('weight DESC')->find();
                }
            }
        } else { // 最近没有购买记录
            $result = array();
        }

        return $result;
    }

    /**
     * 获取运费
     */
    public function ajaxshippingfee() {
        $param = think_decrypt(cookie("new_shipping"));
//        dump(unserialize($param));
       $shippingfee =  $this->calcshippingfee(unserialize($param));
        if ($shippingfee) {
            $data['disable'] = TRUE;
        } else {
            $data['disable'] = false;
        }
        $data['status'] = true;
        $money = 0;
        foreach ($shippingfee as $value){
            $money +=$value['shipping_fee'];
        }
        $data['fee'] = $shippingfee;
        $data['money']=  ncPriceFormat($money);
        cookie("new_shipping", think_encrypt(serialize($shippingfee)));   // 分单包裹信息
        $this->ajaxReturn($data);
    }
    

    public function getaddressinfo() {
        $addressid = I('addressid');
        $address = M("transport")->where(array('uid' => is_login(), 'id' => $addressid))->order("id desc")->find();
        $this->assign('address', $address);
        $re = $this->fetch('Checkout/checkout.addressinfo');
        if ($re) {
            $this->success($re);
        }
    }

    /**
     * 获取配送方式 根据不同的地址获取配送放肆
     * @param type $type
     */
    public function ajaxshipping($type = 0) {

        $address_id = I('address_id');
        $condition = array();
        if ($address_id) {
            $condition['id'] = $address_id;
        }
        $orderType = I('orderType');
        $condition['orderType'] = $orderType;
        $shipping = $this->chkapi->get_shipping($condition);

        $goodsTotalWeight = cookie('goodsTotalWeight');
        if ($shipping) {
            foreach ($shipping as &$value) {
                if ($value['snum'] >= $goodsTotalWeight) {
                    $value['money'] = $value['sprice'];
                } else {
                    $xfee = ($goodsTotalWeight - $value['snum']) * $value['xprice'];
                    $value['money'] = $value['sprice'] + $xfee;
                }

                if (C('LOWWEST') < cookie('goodsTotal')) {
                    $value['money'] = 0;
                }
            }
        }
        if ($type == 0) {
            $conf = C();
            if (isset($conf['LOWWEST'])) {
                $this->assign('lowwest', $conf['LOWWEST']); //满额免邮金额
            }
            $this->assign('distribution', $shipping); //配送方式
            $re = $this->fetch('Checkout/checkout.shipping');
            $data['info'] = $re;
            $data['status'] = true;
            if (C('LOWWEST') < cookie('goodsTotal')) {
                $data['money'] = 0.00;
            } else {
                $data['money'] = $shipping[0]['money'] ? : 0.00;
            }
            if ($shipping) {
                $data['disable'] = TRUE;
            } else {
                $data['disable'] = false;
            }
            $this->ajaxReturn($data);
        } else {
            return $shipping ? $shipping[0] : array();   // 获取默认运费
        }
    }

    /**
     * 检查收货地址
     */
    public function ajaxaddress() {
        $id = I('id', 0); // 如果为空 则新建 否则编辑 
        $address = M("transport");
        $map['uid'] = is_login();
        $map['id'] = $id;
        $info = $address->where($map)->find();
        if (empty($info)) {
            $info['status'] = -1;
        }
        $this->assign('addressinfo', $info);
        $re = $this->fetch('Checkout/checkout.address');
        if ($re) {
            $this->success($re);
        }
    }

    /**
     * 获取地址列表
     */
    public function ajaxaddresslist() {
        $uid = session('user_auth.uid');
        $map["uid"] = $uid;
        $map["status"] = 1;
        $address = M("transport")->where($map)->limit(10)->select();
        if (!empty($address)) {
            $addefult = 0;
            foreach ($address as $vad) {
                if ($vad['isdefault'] == 1) {
                    $addefult = 1;
                    break;
                }
            }
            if ($addefult == 0) {
                $address[0]['isdefault'] = 1;
            }
        }
        $this->assign("address", $address);
        $re = $this->fetch('Checkout/checkout.address_list');
        if ($re) {
            $this->success($re);
        } else {
            $this->error('加载地址失败');
        }
    }

    /*
     * *************************************************************
     * created date:2015/4/25 10:19
     * created author:sheshanhu
     * content:新增 修改地址
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    public function save() {
        if (is_login()) {
            $Transport = M("transport"); // 实例化transport对象
            $postinfo = I('post.');
            $data['id'] = $postinfo["id"];
            $data['phone'] = $postinfo["phone"];
            $data['realname'] = $postinfo["realname"];
            $data['isdefault'] = isset($postinfo["isdefault"]) ? 1 : 0;
            $data['province'] = $postinfo["province"];
            $data['city'] = $postinfo["city"];
            $data['area'] = isset($postinfo["area"]) ? $postinfo["area"] : '';
            $data['address'] = $postinfo["address"];
            $data['cellphone'] = $postinfo["cellphone"];
            $data['phone'] = $postinfo["phone"];
            $data['card_no'] = $postinfo["card_no"];
            $data['youbian'] = $postinfo["youbian"];

            if (empty($data['id'])) {
                unset($data['id']);
            }
            $Member = D("member");
            $uid = $Member->uid();
            $data['uid'] = $uid;
//$data['status'] = 0;
            $data['create_time'] = NOW_TIME;
            if (!isset($data['id'])) {
                $count = $Transport->where("uid=" . $uid)->count("*");
                if ($count >= 10) {
                    $this->error("最多只能添加10个收货地址哦");
                }
                $id = $Transport->add($data);
                if ($id) {
                    if ($data['isdefault'] == 1) {
                        $map = array();
                        $map["uid"] = $uid;
                        $map["id"] = array("neq", $id);
                        $updatedefault = $Transport->where($map)->setField("isdefault", 0);
                    }
                    $this->success('新增成功！', U("Member/address"));
                } else {
                    $this->error('新增失败！');
                }
            } else {
                $returninfo = $Transport->save($data);
                if ($returninfo) {
//清除其他默认地址
                    if ($data['isdefault'] == 1) {
                        $id = $data['id'];
                        $map = array();
                        $map["uid"] = $uid;
                        $map["id"] = array("neq", $id);
                        $updatedefault = $Transport->where($map)->setField("isdefault", 0);
                    }
                    $this->success('修改成功！', U("Member/address"));
                } else {
                    $this->error('修改失败！');
                }
            }
        } else {
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->redirect('Member/login');
        }
    }

}
