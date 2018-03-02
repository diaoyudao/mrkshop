<?php

/**
 * 购物流程/订单结算
 */

namespace Web\Controller;
use Api\Api\CheckoutApi;

class CartController extends HomeController {

    private $chkapi = '';

    protected function _initialize() {
        $this->chkapi = new CheckoutApi;
        parent::_initialize();
    }

    public function index() {
        $condition['is_system'] = 0;
        $cartlist = $this->chkapi->getCartList($condition);
        $usercart = $cartlist['type1']['cartlist'];
        $goodsType = $cartlist['type1']['goodsType'];
        $goodsNum = $cartlist['type1']['goodsNum'];
        $goodsTotal = $cartlist['type1']['goodsTotal'];
        $this->assign('usercart', $usercart);
        //猜你喜欢
        $goodsapi = new \Api\Api\GoodsApi();
        $con['position'] = array('in',array(8,9));
        $aboutproduct = $goodsapi->getDocument($con, 'id DESC', 7);
        $this->assign('aboutproduct', $aboutproduct);

        $this->assign('uid', is_login());
        $this->assign('count', $goodsType ? : 0);
        $this->assign('sum', $goodsNum);
        $this->assign('price', ncPriceFormat($goodsTotal));
        $this->assign('type1_count', $cartlist['type1']['goodsType'] ? : 0);  // 
        $this->assign('type2_count', $cartlist['type2']['goodsType'] ? : 0);  // 
        $this->assign('type3_count', $cartlist['type3']['goodsType'] ? : 0);  // 
        $this->assign('type4_count', $cartlist['type4']['goodsType'] ? : 0);  // 

        if (empty($cartlist['type1']['goodsType'])) {
            if ($cartlist['type2']['goodsType']) {
                $this->redirect(U("Cart/bonded"));
            } elseif ($cartlist['type3']['goodsType']) {
                $this->redirect(U("Cart/zhiyou"));
            } elseif ($cartlist['type4']['goodsType']) {
                $this->redirect(U("Cart/store"));
            }
        }
        $this->meta_title = '我的购物车';
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        if ($usercart) {
            $this->display('cart');
        } else {
            $this->display('cart.empty');
        }
    }

    /**
     * 保税商品
     */
    public function bonded() {

        $condition['is_system'] = 0;
        $cartlist = $this->chkapi->getCartList($condition);
        $usercart = $cartlist['type2']['cartlist'];
        $goodsType = $cartlist['type2']['goodsType'];
        $goodsNum = $cartlist['type2']['goodsNum'];
        $goodsTotal = $cartlist['type2']['goodsTotal'];
        $this->assign('usercart', $usercart);
        //猜你喜欢
        $aboutproduct = $this->getDocument(array(), 'id DESC', 7);
        $this->assign('aboutproduct', $aboutproduct);

        $this->assign('uid', is_login());
        $this->assign('count', $goodsType);
        $this->assign('sum', $goodsNum);
        $this->assign('price', ncPriceFormat($goodsTotal));
        $this->assign('type1_count', $cartlist['type1']['goodsType'] ? : 0);
        $this->assign('type2_count', $cartlist['type2']['goodsType'] ? : 0);
        $this->assign('type3_count', $cartlist['type3']['goodsType'] ? : 0);
        $this->assign('type4_count', $cartlist['type4']['goodsType'] ? : 0);

        if (empty($cartlist['type2']['goodsType'])) {
            if ($cartlist['type1']['goodsType']) {
                $this->redirect(U("Cart/index"));
            } elseif ($cartlist['type3']['goodsType']) {
                $this->redirect(U("Cart/zhiyou"));
            } elseif ($cartlist['type4']['goodsType']) {
                $this->redirect(U("Cart/store"));
            }
        }
        $this->meta_title = '我的购物车';
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        if ($usercart) {
            $this->display('cart.bonded');
        } else {
            $this->display('cart.empty');
        }
    }

    /**
     * 直邮商品
     */
    public function zhiyou() {

        $condition['is_system'] = 0;
        $cartlist = $this->chkapi->getCartList($condition);
        $usercart = $cartlist['type3']['cartlist'];
        $goodsType = $cartlist['type3']['goodsType'];
        $goodsNum = $cartlist['type3']['goodsNum'];
        $goodsTotal = $cartlist['type3']['goodsTotal'];
        $this->assign('usercart', $usercart);
        //猜你喜欢
        $aboutproduct = $this->getDocument(array(), 'id DESC', 7);
        $this->assign('aboutproduct', $aboutproduct);

        $this->assign('uid', is_login());
        $this->assign('count', $goodsType);
        $this->assign('sum', $goodsNum);
        $this->assign('price', ncPriceFormat($goodsTotal));
        $this->assign('type1_count', $cartlist['type1']['goodsType'] ? : 0);
        $this->assign('type2_count', $cartlist['type2']['goodsType'] ? : 0);
        $this->assign('type3_count', $cartlist['type3']['goodsType'] ? : 0);
        $this->assign('type4_count', $cartlist['type4']['goodsType'] ? : 0);
        if (empty($cartlist['type3']['goodsType'])) {
            if ($cartlist['type1']['goodsType']) {
                $this->redirect(U("Cart/index"));
            } elseif ($cartlist['type2']['goodsType']) {
                $this->redirect(U("Cart/bonded"));
            } elseif ($cartlist['type4']['goodsType']) {
                $this->redirect(U("Cart/store"));
            }
        }
        $this->meta_title = '我的购物车';

        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        if ($usercart) {
            $this->display('cart.zhiyou');
        } else {
            $this->display('cart.empty');
        }
    }

    /**
     * 门店商品
     */
    public function store() {

        $condition['is_system'] = 0;
        $cartlist = $this->chkapi->getCartList($condition);
        $usercart = $cartlist['type4']['cartlist'];
        $goodsType = $cartlist['type4']['goodsType'];
        $goodsNum = $cartlist['type4']['goodsNum'];
        $goodsTotal = $cartlist['type4']['goodsTotal'];
        $this->assign('usercart', $usercart);
        //猜你喜欢
        $aboutproduct = $this->getDocument(array(), 'id DESC', 7);
        $this->assign('aboutproduct', $aboutproduct);

        $this->assign('uid', is_login());
        $this->assign('count', $goodsType);
        $this->assign('sum', $goodsNum);
        $this->assign('price', ncPriceFormat($goodsTotal));
        $this->assign('type1_count', $cartlist['type1']['goodsType'] ? : 0);
        $this->assign('type2_count', $cartlist['type2']['goodsType'] ? : 0);
        $this->assign('type3_count', $cartlist['type3']['goodsType'] ? : 0);
        $this->assign('type4_count', $cartlist['type4']['goodsType'] ? : 0);
        $this->meta_title = '我的购物车';
        if (empty($cartlist['type4']['goodsType'])) {
            if ($cartlist['type1']['goodsType']) {
                $this->redirect(U("Cart/index"));
            } elseif ($cartlist['type2']['goodsType']) {
                $this->redirect(U("Cart/bonded"));
            } elseif ($cartlist['type3']['goodsType']) {
                $this->redirect(U("Cart/zhiyou"));
            }
        }

        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        if ($usercart) {
            $this->display('cart.store');
        } else {
            $this->display('cart.empty');
        }
    }

    /*
     * 商品数量+1
     */

    public function incNum() {
        $sort = I("sort");
        if (empty($sort)) {
            $this->ajaxReturn(array('status' => 0, 'msg' => '参数错误'));
        }
        $condition['sort'] = $sort;
        $num = I('num', 1);
        $result = $this->chkapi->incGoodsNum($condition, $num);
        if (isset($result['error'])) {
            $this->ajaxReturn($result);
        } elseif (isset($result['success'])) {
            $this->ajaxReturn($result);
        }
    }

    /*
      商品数量-1
     */

    public function decNum() {
        $sort = I('sort');
        if (empty($sort)) {
            $this->ajaxReturn(array('status' => 0, 'msg' => '参数错误'));
        }
        $condition['sort'] = $sort;
        $num = I('num', 1);
        $result = $this->chkapi->decGoodsNum($condition, $num);
        if (isset($result['error'])) {
            $this->ajaxReturn($result);
        } elseif (isset($result['success'])) {
            $this->ajaxReturn($result);
        }
    }

    /**
     * 添加商品到购物车
     */
    public function addItem() {

        $params['type'] = I('t', 0);    // 商品类型
        $params['price'] = I('price');  // 商品价格
        $params['goodsid'] = I('id');   // 商品ID
        $params['num'] = I('num', 1);   // 商品数量
        $params['pro'] = I('pro', 0);   // 是否促销
        $params['parameters'] = I('i', 0); // 属性
        $params['store_id'] = I('store_id', 0); // 店铺ID
        $params['is_system'] = I('is_system', 0);  // 下单方式
        $params['uid'] = cookie("store_member_id");
        $result = $this->chkapi->addCartItem($params);
        if (isset($result['error'])) {
            $this->ajaxReturn($result);
        } elseif (isset($result['success'])) {
            $this->ajaxReturn($result);
        }
    }

    /**
     * 删除购物车中的商品
     */
    public function delItem() {
        $sort = I('sort');
        $sort = explode(',', $sort);
        if (empty($sort)) {
            $this->ajaxReturn(array('status' => 0, 'msg' => '参数错误'));
        }
        $condition['sort'] = $sort;
        $result = $this->chkapi->delCartItem($condition);
        $this->ajaxReturn($result);
    }

    /**
     * 删除购物车中的商品
     */
    public function delItemheader() {
        $sort = I('sort');
        $sort = explode(',', $sort);
        if (empty($sort)) {
            $this->ajaxReturn(array('status' => 0, 'msg' => '参数错误'));
        }
        $condition['sort'] = $sort;
        $result = $this->chkapi->delCartItem($condition);
        $result = $this->get_cart_list();
        $_data['goods_type'] = $result['goods_type'];
        $_data['goods_count'] = $result['goods_count'];
        $_data['goods_total'] = $result['goods_total'];
        $this->ajaxReturn($_data);
    }

    /**
     * 异步获取购物车列表
     */
    public function ajax_cart() {
        $cart_list = $this->get_cart_list();
        $this->assign('cart_list', $cart_list); // 购物车列表
        $re = $this->fetch('Public/ajax_cart');
        $this->success($re);
    }

    /**
     * 商品详细页面ajax 对应的成交记录
     */
    public function ajaxlists($gid = 0, $callback = "") {
        $goodid = $gid ? $gid : I('gid');
        $map = array();
        $map['status'] = 3;
        $map["goodid"] = $goodid;
        $shopmodel = M("shoplist");
        $_POST['r'] = 10;
        $lists = $this->_ajaxlists($shopmodel, $map, 'id DESC', array(), 'id', 'getshoplist');
        if ($lists) {
            foreach ($lists as $k => $v) {
                $detail = S('cjjl_' . $v['id']);
                if (!$detail) {
                    $detail = $shopmodel->find($v['id']);

                    S('cjjl_' . $v['id'], $detail);
                }
                $lists[$k] = $detail;
            }
        }
        $this->assign('recordlist', $lists);
        $this->assign('gid', $goodid);
        $re = $this->fetch('Goods/goods.record');
        $back = $callback ? $callback : I("callback", "");
        if ($back == 'html') {
            return $re;
        } else {
            $this->success($re);
        }
    }

}
