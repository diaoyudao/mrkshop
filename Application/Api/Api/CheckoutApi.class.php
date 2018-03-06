<?php

/**
 * 购物流程接口
 */

namespace Api\Api;

use Common\Model\ShopcartModel;

class CheckoutApi extends Api {

    private $tag = '';     // 订单号
    private $orderid = '';     // 订单ID
    private $status = 0;   //订单状态
    private $uid = 0;   // 用户ID
    private $senderid = 0; // 收货地址
    private $goodsapi = '';
    private $orderapi = '';

    /**
     * 构造方法，实例化操作模型
     */
    protected function _init() {
        $this->model = new ShopcartModel();
        $this->goodsapi = new GoodsApi();
        $this->orderapi = new OrderApi();
    }

    /**
     * 加入商品到购物车
     * @param array $params
     * @return array
     */
    public function addCartItem($params) {
        $goodsid = $params['goodsid'];  // 如果商品ID不存在 则返回false
        $price = $params['price'];      // 价格
        $type = $params['type'] ? : 0;  // 商品类型 {普通商品、组合商品}
        $pro = $params['pro'] ? : 0;    // 促销ID
        $num = $params['num'] ? int_num_validate($params['num']) : 1;    // 对数量进行判断，非数字的字符都设置数量为
        $parameters = $params['parameters'] ? : ''; // 属性ID
        $store_id = $params['store_id'];    // 门店id
        $this->uid = $params['uid'] ? : is_login();
        $is_system = $params['is_system'] ? : 0;
        //根据传值ID查询商品是否存在
        $_tmpdata = $this->getGoodsPirce($goodsid, $num, $type, $pro, $store_id, $is_system);
        if ($_tmpdata['error']) {
            return array('error' => true, 'msg' => $_tmpdata['msg'], 'status' => 0);
        }
        $price = $_tmpdata['price'];
        $promsg = $_tmpdata['promsg'];
        $cart_type = $_tmpdata['cart_type'];
        $goodsInfo = $_tmpdata['goodsInfo'];

        if ($parameters) {//根据接收的参数获取对于的选项 ，如果存在规格则查询当前规格，否则读取默认规格
            $attrid = explode(",", $parameters);
            sort($attrid);
            $map["id"] = array("in", $attrid);
            $parameters = M("good_attr")->where($map)->getField("id,value");
            $sort = $goodsid . '_' . $pro . (implode("-", $attrid)) . '-' . $store_id; // 追加促销ID 追加门店ID
            $parameters = implode(" ", $parameters);

            //根据商品属性查询商品增量价格，重新计算
            //属性格式27910826-10830 商品ID，属性值
            if (!empty($sort)) {
                $parame = str_replace('-' . $store_id, '', str_replace('_' . $pro, '', str_replace($goodsid, '', $sort)));
                $paramestr = explode('-', $parame);
                $map = array();
                $map['gid'] = $goodsid;
                $map['id'] = array('in', $paramestr);
                $sumprice = M("goodAttr")->where($map)->sum("price");
                $price = $price + $sumprice;
            }
        } else {
            $sumprice = $goodsInfo['spec_price'] ? : 0;
            if ($type == 0) {
                $_paramer = array_filter(explode(',', $goodsInfo['spec_ids']));
                sort($_paramer);

                $sort = $sort = $goodsid . '_' . $pro . (implode("-", $_paramer)) . '-' . $store_id;
            } elseif ($type == 1) {
                $sort = $goodsid . '_' . $pro . 'pg';
            }
            $parameters = $goodsInfo['spec_name'] ? : "";
            $price = $price + $sumprice;
        }
        $exsit = $this->saveSession($goodsInfo, $goodsid, $store_id, $price, $num, $sort, $type, $parameters, $pro, $promsg, $cart_type, $is_system);
        if ($exsit['error']) {
            return array('error' => true, 'msg' => $exsit['msg'], 'status' => $exsit['status']);
        }
        if (is_login()) {
            $table = D("shopcart");
            $uid = $this->uid;
            $data = $this->cartData($goodsid, $store_id, $price, $num, $sort, $type, $parameters, $pro, $promsg, $cart_type, $is_system);
            $datanum = M("shopcart")->where("goodid='$goodsid' and uid='$uid' and sort = '$sort' ")->getField("num");
            $goods_stock = M('document_product')->where(array('id' => $goodsid))->getField('stock');
            if ($goods_stock < $datanum) {
                return array('error' => true, 'msg' => '商品库存不足', 'status' => 0);
            }
            if ($datanum) {
                $exsit = "1";
                $data['num'] = $datanum + $num;
                $table->where("goodid='$goodsid' and uid='$uid' and sort = '$sort'")->save($data);
            } else {
                $data['num'] = $num;
                $table->add($data);
                $exsit = "0";
            }
        }
        //  获取购物车商品金额
        $_cart = $this->getCartList();
        $goodsType = $_cart['type' . $cart_type]['goodsType'];
        $goodsNum = $_cart['type' . $cart_type]['goodsNum'];
        $goodsTotal = $_cart['type' . $cart_type]['goodsTotal'];
        $goodsTotalNum = $_cart['goodsTotalNum'];
        return array('success' => true, 'goodsType' => $goodsType, 'goodsNum' => $goodsNum, 'goodsTotal' => $goodsTotal, 'goodsTotalNum' => $goodsTotalNum, 'msg' => '已添加到购物车', 'exsit' => $exsit, 'status' => 1);
    }

    /**
     * 删除购物车的商品
     * @param type $condition array('sort'=>sort)
     * @param type $condition
     * @return type
     */
    public function delCartItem($condition) {
        $sortarr = $condition['sort'];  // 批量删除
        $uid = is_login();
        if ($uid) {
            $cart = D("Shopcart");
            foreach ($sortarr as $key => $sort) {
                $cart_type = $cart->where("sort='$sort' and uid='$uid'")->getField('cart_type');
                $bool = $cart->where("sort='$sort' and uid='$uid'")->delete();
                unset($_SESSION['cart'][$sort]);
            }
            if ($bool) {
                //  获取购物车信息
                $_cart = $this->getCartList();
                $goodsType = $_cart['type' . $cart_type]['goodsType'] ? : 0;
                $goodsNum = $_cart['type' . $cart_type]['goodsNum'] ? : 0;
                $goodsTotal = $_cart['type' . $cart_type]['goodsTotal'] ? : 0;
                $goodsTotalNum = $_cart['goodsTotalNum'];
                return array('success' => true, 'goodsType' => $goodsType, 'goodsNum' => $goodsNum, 'goodsTotal' => $goodsTotal, 'goodsTotalNum' => $goodsTotalNum, 'msg' => '处理成功', 'status' => 1);
            }
        } else {
            foreach ($sortarr as $key => $sort) {
                $cart_type = $_SESSION['cart'][$sort]['cart_type'];  // 根据删除的类型去该类型购物车下的信息
                unset($_SESSION['cart'][$sort]);
            }
            //  获取购物车信息
            $_cart = $this->getCartList();
            $goodsType = $_cart['type' . $cart_type]['goodsType'] ? : 0;
            $goodsNum = $_cart['type' . $cart_type]['goodsNum'] ? : 0;
            $goodsTotal = $_cart['type' . $cart_type]['goodsTotal'] ? : 0;
            $goodsTotalNum = $_cart['goodsTotalNum'];
            return array('success' => true, 'goodsType' => $goodsType, 'goodsNum' => $goodsNum, 'goodsTotal' => $goodsTotal, 'goodsTotalNum' => $goodsTotalNum, 'msg' => '处理成功', 'status' => 1);
        }
    }

    /**
     * 增加商品数量 ++1
     * @param type $condition
     * @param type $num 数量
     * @return type
     */
    public function incGoodsNum($condition, $num = 1) {
        $sort = $condition['sort'];
        $uid = is_login();
        if (empty($sort) || $num <= 0) {
            return array('error' => true, 'status' => 0, 'msg' => '参数错误');
        }
        if ($uid) {
            $cart = D("Shopcart");
            $cart_type = $cart->where("sort='$sort' and uid='$uid'")->getField('cart_type');
            $datanum = $cart->where("sort='$sort' and uid='$uid'")->getField('num');

            $goodsid = $cart->where("sort='$sort' and uid='$uid'")->getField('goodid');
            $goods_stock = M('document_product')->where(array('id' => $goodsid))->getField('stock');
            if ($goods_stock - 1 < $datanum) {
                return array('error' => true, 'msg' => '商品库存不足', 'status' => 0);
            }
            $result = $cart->inc($sort, $num);
            if ($result) {
                //  获取购物车信息
                $_cart = $this->getCartList();
                $goodsType = $_cart['type' . $cart_type]['goodsType'];
                $goodsNum = $_cart['type' . $cart_type]['goodsNum'];
                $goodsTotal = $_cart['type' . $cart_type]['goodsTotal'];
                $goodsitemTotal = $cart->getTotalbysort($sort);
                $goodsTotalNum = $_cart['goodsTotalNum'];
                return array('success' => true, 'goodsType' => $goodsType, 'goodsitemTotal' => $goodsitemTotal, 'goodsNum' => $goodsNum, 'goodsTotal' => $goodsTotal, 'goodsTotalNum' => $goodsTotalNum, 'msg' => '处理成功', 'status' => 1);
            }
        } else {
            if (isset($_SESSION['cart'][$sort])) {
                if ($num > 0) {
                    $_SESSION['cart'][$sort]['num'] += $num;
                } else {
                    $_SESSION['cart'][$sort]['num'] +=1;
                }
                $cart_type = $_SESSION['cart'][$sort]['cart_type'];
            }
            //  获取购物车信息
            $_cart = $this->getCartList();
            $goodsType = $_cart['type' . $cart_type]['goodsType'];
            $goodsNum = $_cart['type' . $cart_type]['goodsNum'];
            $goodsTotal = $_cart['type' . $cart_type]['goodsTotal'];
            $goodsitemTotal = $_cart['type' . $cart_type]['cartlist'][$sort]['goodsItemTotal'];
            $goodsTotalNum = $_cart['goodsTotalNum'];
            return array('success' => true, 'goodsType' => $goodsType, 'goodsitemTotal' => $goodsitemTotal, 'goodsNum' => $goodsNum, 'goodsTotal' => $goodsTotal, 'goodsTotalNum' => $goodsTotalNum, 'msg' => '处理成功', 'status' => 1);
        }
    }

    /**
     * 减少商品数量 --1
     * @param type $condition
     * @param type $num 数量 默认1
     */
    public function decGoodsNum($condition, $num = 1) {
        $uid = is_login();
        $sort = $condition['sort'];
        if (empty($sort) || $num <= 0) {
            return array('error' => true, 'status' => 0, 'msg' => '参数错误');
        }
        if ($uid) {
            $cart = D("Shopcart");
            $cart_type = $cart->where("sort='$sort' and uid='$uid'")->getField('cart_type');
            $result = $cart->dec($sort, $num);
            if ($result) {
                //  获取购物车信息
                $_cart = $this->getCartList();
                $goodsType = $_cart['type' . $cart_type]['goodsType'];
                $goodsNum = $_cart['type' . $cart_type]['goodsNum'];
                $goodsTotal = $_cart['type' . $cart_type]['goodsTotal'];
                $goodsitemTotal = $cart->getTotalbysort($sort);
                $goodsTotalNum = $_cart['goodsTotalNum'];
                return array('success' => true, 'goodsType' => $goodsType, 'goodsitemTotal' => $goodsitemTotal, 'goodsNum' => $goodsNum, 'goodsTotal' => $goodsTotal, 'goodsTotalNum' => $goodsTotalNum, 'msg' => '处理成功', 'status' => 1);
            }
        } else {
            if (isset($_SESSION['cart'][$sort]) && $_SESSION['cart'][$sort]['num'] > $num) {
                $_SESSION['cart'][$sort]['num'] -= $num;
            }
            $cart_type = $_SESSION['cart'][$sort]['cart_type'];
            //如果减少后，数量为0，则把这个商品删掉
            if ($_SESSION['cart'][$sort]['num'] < 1) {
                unset($_SESSION['cart'][$sort]);
            }
            //  获取购物车信息
            $_cart = $this->getCartList();
            $goodsType = $_cart['type' . $cart_type]['goodsType'];
            $goodsNum = $_cart['type' . $cart_type]['goodsNum'];
            $goodsTotal = $_cart['type' . $cart_type]['goodsTotal'];
            $goodsitemTotal = $_cart['type' . $cart_type]['cartlist'][$sort]['goodsItemTotal'];
            $goodsTotalNum = $_cart['goodsTotalNum'];
            return array('success' => true, 'goodsType' => $goodsType, 'goodsitemTotal' => $goodsitemTotal, 'goodsNum' => $goodsNum, 'goodsTotal' => $goodsTotal, 'goodsTotalNum' => $goodsTotalNum, 'msg' => '处理成功', 'status' => 1);
        }
    }

    /**
     * 获取购物车商品
     * @param type $condition array('cart_type'=>1/2/3,'uid'=>1)
     * @return type $cartlist array();
     */
    public function getCartList($condition = array()) {
        if (is_login()) {
            $cart = D("Shopcart");
            if (!$condition['is_system']) {
                $condition['is_system'] = 0;
            }
            $cartlist = $cart->getcart($condition);
            //　对购物车商品分析
            $cartlist = $this->cartListGoodsdone($cartlist);

            //对商品的收藏状态进行判断
//            $Member = D("Member");
            $uid = is_login();

            $fav = D("favortable");
            $favor = $fav->getfavor(1);
            Cookie('favor' . $uid, $favor);
            if (!empty($favor)) {
                foreach ($cartlist as $bkey => $bvalue) {
                    if ($bvalue['type'] == 1 || in_array($bvalue['goodid'], $favor)) {
                        $cartlist[$bkey]['favor'] = 1;
                    } else {
                        $cartlist[$bkey]['favor'] = 0;
                    }
                }
            }
            // 对购物车进行分析
            $_data = $this->cartListdone($cartlist);
        } else { // 未登录
            $cartlist = $_SESSION['cart'];
            $cartlist = $this->cartListGoodsdone($cartlist);
            $_data = $this->cartListdone($cartlist);
        }
        return $_data;
    }

    /**
     * 对购物车进行分析
     * @param type $cartlist
     */
    public function cartListdone($cartlist) {
        $_tempcart = array();
        foreach ($cartlist as $k => $val) {
            switch ($val['cart_type']) {
                case 1: // 普通商品
                    $_tempcart['type1']['cartlist'][$k] = $val;
                    break;
                case 2: // 保税商品
                    $_tempcart['type2']['cartlist'][$k] = $val;
                    break;
                case 3: // 直邮商品
                    $_tempcart['type3']['cartlist'][$k] = $val;
                    break;
                case 4: // 直邮商品
                    $_tempcart['type4']['cartlist'][$k] = $val;
                    break;
                default:
                    $_tempcart = array();
                    break;
            }
        }
        $goodsTotalNum = 0;
        foreach ($_tempcart as $key => $value) {
            $goodsTotal = 0;
            $goodsNum = 0;
            $goodsType = 0;
            foreach ($_tempcart[$key]['cartlist'] as $k => $val) {
                $goodsTotal += $val['num'] * $val['price'];
                $_tempcart[$key]['cartlist'][$k]['goodsItemTotal'] = $val['num'] * $val['price'];
                $goodsNum += $val['num'];
                $goodsType++;
            }
            $goodsTotalNum += $goodsNum;
            $_tempcart[$key]['goodsTotal'] = $goodsTotal ? : 0;   // 购物车中商品的总金额
            $_tempcart[$key]['goodsNum'] = $goodsNum ? : 0;       // 查询购物车中商品的个数
            $_tempcart[$key]['goodsType'] = $goodsType ? : 0;     // 查询购物车中商品的种类
        }
        $_tempcart['goodsTotalNum'] = $goodsTotalNum ? : 0;     // 求购物车总共的商品数量
        return $_tempcart;
    }

    /**
     * 购物车商品分析
     * @param type $cartlist
     */
    public function cartListGoodsdone($cartlist) {
        $goodsModel = new GoodsApi();
        //　对订单中组合商品进行分析
        $productsgroupModel = D("ProductsGroup");
        foreach ($cartlist as $key => &$value) {
            if ($value['type'] == 1) { // TODO: 组合商品
                $groupproduct_detail = S('pg_' . $value['goodid']);
                if (empty($groupproduct_detail)) {
                    $groupproduct_detail = $productsgroupModel->find($value['goodid']);
                    //组合商品信息查询
                    $gmap = array();
                    $gmap['id'] = array('in', $groupproduct_detail['uniongood']);

                    $groupproduct_detail['goodsinfo'] = $goodsModel->getDocument($gmap);
                    $value['goodsinfo'] = $groupproduct_detail['goodsinfo'];
                    $value['price'] = $groupproduct_detail['price'];
                    S('pg_' . $value['goodid'], $groupproduct_detail);
                } else {
                    $value['goodsinfo'] = $groupproduct_detail['goodsinfo'];
                    $value['price'] = $groupproduct_detail['price'];
                }
            } else {
                $goodsinfo = $this->goodsapi->getDocument(array('id' => $value["goodid"]), '', 1);
                $domainid = $goodsinfo['domainid']; // M("document")->where(array('id' => $value["goodid"]))->getField("domainid");
                $mark = M("subdomain")->where(array('id' => $domainid))->getField("mark");
                $value["channelname"] = $mark;
                $value['handle_status'] = true;
                if ($value['price'] <= 0) {
                    $value['handle_status'] = false;
                }
                if ($goodsinfo) {
                    if ($goodsinfo['stock'] < $value['num']) {
                        $value['handle_status'] = false;
                    }
                } else {
                    $value['handle_status'] = false;
                }
            }
        }

        return $cartlist;
    }

    /**
     * 立即购买商品
     * @param type $params array('goods_id','number',proid,store_id,parameters);
     * @return type
     */
    public function getOnlineBuynow($params) {
        if (empty($params)) {
            return array('error' => false, 'msg' => '参数错误'); // 参数为空 返回false
        }

        $goods_id = $params['goods_id'];
        // 取最新商品数据
        $data = array();
        $goodsTotal = 0;  // 商品总金额
        $goodsCount = 0;  // 商品数量
        $goodsType = 0;   // 商品种类
        $discount_amount = 0; // 抵扣金额
        $haiguan_rate_total = 0; // 海关税总额
        $orderType = 1;         // 订单类型  默认普通订单
        $store_id = 0;          // 订单店铺  默认平台
        $goodsTotalWeight = 0;  // 商品总重量
        $discount_price = 0;
        $goodsinfo = $this->goodsapi->getDocument(array('id' => $goods_id), 'id desc', 1);

        $haiguan_rate = $goodsinfo['haiguan_rate'];
        if ($goodsinfo['product_type'] == 3) {     // 直邮
            $orderType = 3;
        } elseif ($goodsinfo['product_type'] == 2) {    // 保税
            $orderType = 2;
        } else {
            $orderType = $goodsinfo['product_type'];
        }
        if ($goodsinfo['stock'] < $params['number']) {
            return array('error' => true, 'msg' => '商品库存不足，或已下架');
        }
        $orgin_price = $goodsinfo['org_price'];
        $member_price = get_goods_MemberPirce($goodsinfo);

        if ($params['store_id']) {    // 门店商品
            $orderType = 4;
            $store_goods = M('store_goods')->where(array('goods_id' => $goods_id, 'store_id' => $params['store_id'], 'stock' => array("gt", 0), 'status' => 1))->find();
            if (empty($store_goods)) {  // 如果门店商品不存在，
                return array('error' => false, 'msg' => '门店商品库存不足，或已下架');
            } elseif ($params['number'] > $store_goods['stock']) {
                return array('error' => false, 'msg' => '门店商品库存不足');
            } else {
                if (!empty($params['parameters'])) { // 如果有商品属性  属性格式 279_110826-10830 商品ID，促销id，属性值 门店ID
//                $parame = $params['parameters'];
//                $paramestr = explode('-', $parame);
                    $paramestr = $params['parameters'];
                    $map = array();
                    $map['gid'] = $goods_id;
                    $map['id'] = array('in', $paramestr);
                    $sumprice = M("goodAttr")->where($map)->sum("price");
//                if ($params['proid']) {  // 有促销活动，则原价
//                    $price = $orgin_price + $sumprice - $discount_price;
//                } else {   // 没有促销活动，则会员价
//                    $price = $member_price + $sumprice - $discount_price;
//                }
                    $parameters = M("goodAttr")->where($map)->getField('id,value');
                    $parameters = implode(" ", $parameters);
                } else {
                    $sumprice = 0; // 规格价格
                    $parameters = '';
                }

                if ($params['proid'] > 0) { // 促销商品
                    $promotion = $this->goodsapi->getpromotion_info($goods_id); // 促销信息
                    if ($promotion) {
                        $pro_num = $promotion['xianshi_stock'];
                        $pro_price = $promotion['xianshi_price'];
                        $pro_msg = $promotion['xianshi_title'];
                        if ($pro_num >= $params['number']) {// 大于促销数量 则可以
                            $price = $pro_price ? : $orgin_price;
                            $discount_price = $orgin_price > $pro_price ? ($orgin_price - $pro_price) : 0;  // 优惠金额

                            $price = $orgin_price + $sumprice - $discount_price;
                        } else {
                            // continue; // 否则不能够吗该商品
                            return array('error' => true, 'msg' => '促销商品库存不足');
                        }
                    }
                } else {  // 非促销商品
                    $price = $member_price + $sumprice - $discount_price;
                }
            }
        } else {  // 平台商品
            if (!empty($params['parameters'])) { // 如果有商品属性  属性格式 279_110826-10830 商品ID，促销id，属性值 门店ID
//                $parame = $params['parameters'];
//                $paramestr = explode('-', $parame);
                $paramestr = $params['parameters'];
                $map = array();
                $map['gid'] = $goods_id;
                $map['id'] = array('in', $paramestr);
                $sumprice = M("goodAttr")->where($map)->sum("price");
//                if ($params['proid']) {  // 有促销活动，则原价
//                    $price = $orgin_price + $sumprice - $discount_price;
//                } else {   // 没有促销活动，则会员价
//                    $price = $member_price + $sumprice - $discount_price;
//                }
                $parameters = M("goodAttr")->where($map)->getField('id,value');
                $parameters = implode(" ", $parameters);
            } else {
                $sumprice = 0; // 规格价格
                $parameters = '';
            }

            if ($params['proid'] > 0) { // 促销商品
                $promotion = $this->goodsapi->getpromotion_info($goods_id); // 促销信息
                if ($promotion) {
                    $pro_num = $promotion['xianshi_stock'];
                    $pro_price = $promotion['xianshi_price'];
                    $pro_msg = $promotion['xianshi_title'];
                    if ($pro_num >= $params['number']) {// 大于促销数量 则可以
                        $price = $pro_price ? : $orgin_price;
                        $discount_price = $orgin_price > $pro_price ? ($orgin_price - $pro_price) : 0;  // 优惠金额

                        $price = $orgin_price + $sumprice - $discount_price;
                    } else {
                        // continue; // 否则不能够吗该商品
                        return array('error' => true, 'msg' => '促销商品库存不足');
                    }
                }
            } else {  // 非促销商品
                $price = $member_price + $sumprice - $discount_price;
            }
        }

        $sort = $goods_id . '_' . $params['proid'] . (implode("-", $params['parameters'])) . '-' . $params['store_id']; // 追加促销ID 追加门店ID

        $member_discount_price = $orgin_price - $member_price;
        $data[$sort]['affilite_a'] = $goodsinfo['affilite_a'] ? : 0;  // 代理提成
        $data[$sort]['affilite_b'] = $goodsinfo['affilite_b'] ? : 0;  // 门店提成
        $data[$sort]['type'] = 0;  //商品类型
        $data[$sort]['price'] = ncPriceFormat($price);  //商品类型
        $data[$sort]['discount_price'] = $discount_price;  //抵扣金额
        $data[$sort]['orgin_price'] = $orgin_price;  //初始价格
        $data[$sort]['member_price'] = $member_price;  //会员价
        $data[$sort]['member_discount_price'] = $member_discount_price;  //会员价抵扣
        $data[$sort]['pro_price'] = $pro_price;  // 促销价格
        $data[$sort]['goodid'] = $params['goods_id'];
        $data[$sort]['store_id'] = $params['store_id'];
        $data[$sort]['num'] = $params['number'];
        $data[$sort]['sort'] = implode('-', $params['parameters']);
        $data[$sort]['parameters'] = $parameters;
        $data[$sort]['cart_type'] = $orderType;   // 购物车类型
        $data[$sort]['promsg'] = $pro_msg;
        $data[$sort]['proid'] = $params['proid'];
        $data[$sort]['uid'] = $params['uid'] ? : is_login();
        $data[$sort]['weight'] = $goodsinfo['weight'] * $data[$sort]['num'];

        $data[$sort]['warehouse'] = $goodsinfo['warehouse'];
        $data[$sort]['category_id'] = $goodsinfo['category_id'];
        $data[$sort]['domainid'] = $goodsinfo['domainid'];
//        $data[$sort]['category_id'] = $goodsinfo['category_id'];
        
        //商品设置的会员价
        $data[$sort]['member_level_price'] = $goodsinfo['member_level_price'];
             
        $data[$sort]['haiguan_rate'] = ncPriceFormat($haiguan_rate * $data[$sort]['num'] * $data[$sort]['price']);
        $data[$sort]["total"] = ncPriceFormat($data[$sort]['price'] * $data[$sort]['num']); //  sprintf("%01.2f", $price[$v] * $num[$v]);
        $orderType = $data[$sort]['cart_type'];
        $store_id = $data[$sort]['store_id'];
        $goodsCount +=$data[$sort]['num'];
        $goodsTotal +=$data[$sort]['total'];
        if (!$data[$sort]['store_id']) {
            $haiguan_rate_total +=$data[$sort]['haiguan_rate'];
        }
        $discount_amount +=$data[$sort]['discount_price'];
        $goodsTotalWeight +=$data[$sort]['weight'];

        $checklist['goodsTotal'] = $goodsTotal;             // 商品总金额
        $checklist['goodsTotalWeight'] = $goodsTotalWeight; // 商品总重量
        $checklist['goodsCount'] = $goodsCount;             // 商品数量
        $checklist['goodsType'] = $goodsType;               // 商品重量
        $checklist['orderType'] = $orderType;               // 订单类型
        $checklist['store_id'] = $store_id;                 // 门店ID
        $checklist['discount_amount'] = ncPriceFormat($discount_amount);    // 折扣金额
        $checklist['haiguan_rate_total'] = ncPriceFormat($haiguan_rate_total);  // h海关费
        $checklist['goodsList'] = $data;
        return $checklist;
    }

    // 2.结算商品
    /**
     * 结算商品
     * @param type $params array('sort','number');
     * @return type 返回结算商品的列表
     */
    public function getOnlineCartList($params) {
        if (empty($params)) {
            return array('error' => false, 'msg' => '参数错误'); // 参数为空 返回false
        }
        $number = $params['number'];
        $where['sort'] = array('in', $params['sort']);
        if ($params['uid']) {
            $where['uid'] = $params['uid'];
        }
        $cart = D("Shopcart");
        $cartlist = $cart->getcart($where);
        if (empty($cartlist)) {
            return array('error' => false, 'msg' => '没有查询到商品'); // 如果没有查询到购物车商品返回 false
        }
        $goodsModel = new GoodsApi();
        // 取最新商品数据
        $data = array();
        $goodsTotal = 0;  // 商品总金额
        $goodsCount = 0;  // 商品数量
        $goodsType = 0;   // 商品种类
        $discount_amount = 0; // 抵扣金额
        $haiguan_rate_total = 0; // 海关税总额
        $orderType = 1;         // 订单类型  默认普通订单
        $store_id = 0;          // 订单店铺  默认平台
        $goodsTotalWeight = 0;  // 商品总重量
        foreach ($cartlist as $k => $value) {
            //根据购物车中商品及类型ID查询出当前商品的价格，不能使用购物车中商品价格。
            //原因：如果用户商品加入购物车，则后台对商品价格进行修改，这里商品价格不一致。因此商品价格需要时时查询
            //如果商品缓存中没有，则查询数据库。
            $goodsid = $value['goodid'];
            $discount_price = 0;
            $pro_num = 0;
            $pro_price = 0;
            $pro_msg = '';
            $price = 0;
            $orgin_price = 0;
            $member_price = 0;
            $weight = 0;
            if ($value['type'] == 1) {  // TODO:组合商品
                $goodsinfo = $goodsModel->get_pggood_info($goodsid);
                $orgin_price = $goodsinfo['price'];
                $haiguan_rate = $goodsinfo['haiguan_rate'];
            } else {
                if ($value['store_id'] > 0) {
                    $store_goods = M('store_goods')->where(array('goods_id' => $goodsid, 'store_id' => $value['store_id'], 'stock' => array("gt", 0), 'status' => 1))->find();
                    if (empty($store_goods)) {  // 如果门店商品不存在，
                        return array('error' => false, 'msg' => '门店商品库存不足，或已下架');
                    } elseif ($value['num'] > $store_goods['stock']) {
                        return array('error' => false, 'msg' => '门店商品库存不足');
                    }
                }
                $goodsinfo = $goodsModel->getDocument(array('id' => $goodsid), 'id desc', 1);
                if (empty($goodsinfo)) {
                    return array('error' => false, 'msg' => '商品不存在，或已下架');
                } elseif ($value['num'] > $goodsinfo['stock']) {
                    return array('error' => false, 'msg' => '商品库存不足');
                }
                $orgin_price = $goodsinfo['price'];
                if ($value['is_system']) {
                    $member_price = $goodsinfo['price'];
                } else {
                    $member_price = $goodsinfo['member_price'];
                }
                $haiguan_rate = $goodsinfo['haiguan_rate'];
                $weight = $goodsinfo['weight'];
            }
            //如果当前商品价格查询出来为0,商品不允许购买，自动从结算页面中把商品去除掉
            if ($member_price <= 0) {
                continue;
            }

            $promotion = $goodsModel->getpromotion_info($goodsid); // 促销信息
            if ($value['proid'] > 0) { // 大于零获取促销商品信息
                $promotion = $goodsModel->getpromotion_info($goodsid); // 促销信息
                if ($promotion) {
                    $pro_num = $promotion['xianshi_stock'];
                    $pro_price = $promotion['xianshi_price'];
                    $pro_msg = $promotion['xianshi_title'];
                    if ($pro_num >= $value['num']) {// 大于促销数量 则可以
                        $price = $pro_price ? : $orgin_price;
                        $discount_price = $orgin_price > $pro_price ? ($orgin_price - $pro_price) : 0;  // 优惠金额
                    } else {
                        // continue; // 否则不能够吗该商品
                        return array('error' => false, 'msg' => '促销商品库存不足');
                    }
                }
            }
            if (!empty($value['parameters'])) { // 如果有商品属性  属性格式 279_110826-10830 商品ID，促销id，属性值 门店ID
                $parame = str_replace('-' . $value['store_id'], '', str_replace('_' . $value['proid'], '', str_replace($goodsid, '', $value['sort'])));
                $paramestr = explode('-', $parame);
                $map = array();
                $map['gid'] = $goodsid;
                $map['id'] = array('in', $paramestr);
                $sumprice = M("goodAttr")->where($map)->sum("price");
                if ($value['proid']) {  // 有促销活动，则原价
                    $price = $orgin_price + $sumprice - $discount_price;   // 80  100  -20
                } else {   // 没有促销活动，则会员价
                    $price = $member_price + $sumprice - $discount_price;
                }
            } else {
                if ($value['proid']) {  // 有促销活动，则原价
                    $price = $orgin_price - $discount_price;
                } else {   // 没有促销活动，则会员价
                    $price = $member_price - $discount_price;
                }
//                $price = $member_price - $discount_price;
            }
            $member_discount_price = $orgin_price - $member_price;
            $data[$value['sort']]['affilite_a'] = $goodsinfo['affilite_a'] ? : 0;  // 代理提成
            $data[$value['sort']]['affilite_b'] = $goodsinfo['affilite_b'] ? : 0;  // 门店提成
            $data[$value['sort']]['type'] = $value['type'];  //商品类型
            $data[$value['sort']]['price'] = $price;  //商品类型
            $data[$value['sort']]['discount_price'] = $discount_price;  //抵扣金额
            $data[$value['sort']]['orgin_price'] = $orgin_price;  //初始价格
            $data[$value['sort']]['member_price'] = $member_price;  //会员价
            $data[$value['sort']]['member_discount_price'] = $member_discount_price;  //会员价抵扣
            $data[$value['sort']]['pro_price'] = $pro_price;  // 促销价格
            $data[$value['sort']]['goodid'] = $value['goodid'];
            $data[$value['sort']]['store_id'] = $value['store_id'];
            $data[$value['sort']]['num'] = $number[$value['sort']];
            $data[$value['sort']]['sort'] = $value['sort'];
            $data[$value['sort']]['parameters'] = $value['parameters'];
            $data[$value['sort']]['cart_type'] = $value['cart_type'];   // 购物车类型
            $data[$value['sort']]['promsg'] = $value['promsg'];
            $data[$value['sort']]['proid'] = $value['proid'];
            $data[$value['sort']]['uid'] = $value['uid'];
            $data[$value['sort']]['weight'] = $weight * $data[$value['sort']]['num'];

            $data[$value['sort']]['warehouse'] = $goodsinfo['warehouse'];
            $data[$value['sort']]['category_id'] = $goodsinfo['category_id'];
            $data[$value['sort']]['domainid'] = $goodsinfo['domainid'];
            $data[$value['sort']]['category_id'] = $goodsinfo['category_id'];
            //商品设置的会员价
            $data[$value['sort']]['member_level_price'] = $goodsinfo['member_level_price'];
            
            $data[$value['sort']]['haiguan_rate'] = ncPriceFormat($haiguan_rate * $data[$value['sort']]['num'] * $data[$value['sort']]['price']);
            $data[$value['sort']]["total"] = ncPriceFormat($data[$value['sort']]['price'] * $data[$value['sort']]['num']); //  sprintf("%01.2f", $price[$v] * $num[$v]);
            $orderType = $data[$value['sort']]['cart_type'];
            $store_id = $data[$value['sort']]['store_id'];
            $goodsCount +=$data[$value['sort']]['num'];
            $goodsTotal +=$data[$value['sort']]['total'];
            $haiguan_rate_total +=$data[$value['sort']]['haiguan_rate'];
            $discount_amount +=$data[$value['sort']]['discount_price'];
            $goodsTotalWeight +=$data[$value['sort']]['weight'];
            $goodsType++;
        }

        $checklist['goodsTotal'] = $goodsTotal;             // 商品总金额
        $checklist['goodsTotalWeight'] = $goodsTotalWeight; // 商品总重量
        $checklist['goodsCount'] = $goodsCount;             // 商品数量
        $checklist['goodsType'] = $goodsType;               // 商品重量
        $checklist['orderType'] = $orderType;               // 订单类型
        $checklist['store_id'] = $store_id;                 // 门店ID
        $checklist['discount_amount'] = ncPriceFormat($discount_amount);    // 折扣金额
        $checklist['haiguan_rate_total'] = ncPriceFormat($haiguan_rate_total);  // h海关费
        $checklist['goodsList'] = $data;
        return $checklist;
    }

    /**
     * 创建订单
     * @param type $uid 用户ID
     * @param type $post 提交信息
     * @param type $params 购物车信息
     * @param type $iscart 是否是购物车 0 立即购买  1 购物车
     * @return type
     */
    public function createOrder($uid, $post, $params, $iscart = 1) {
        
        $tag = $post['tag'];
        $senderid = $post['senderid'];
        if ($iscart) {
            $temp_orderinfo = $this->getOnlineCartList($params); // 临时订单信息
        } else {
            $temp_orderinfo = $this->getOnlineBuynow($params);
        }
        //dump($temp_orderinfo);exit;
        $goodsList = $temp_orderinfo['goodsList'];
        $goodsTotal = $temp_orderinfo['goodsTotal'];
        $goodsCount = $temp_orderinfo['goodsCount'];
        $goodsType = $temp_orderinfo['goodsType'];
        $orderType = $temp_orderinfo['orderType'];
        $store_id = $temp_orderinfo['store_id'];
        $discount_amount = $temp_orderinfo['discount_amount'];
        $haiguan_rate_total = $temp_orderinfo['haiguan_rate_total'];
        $memberinfo = M('member')->where(array('uid' => $uid))->find(); // 获取会员信息
        $member_agent_id = $memberinfo['member_agent_id'];
        foreach ($goodsList as $key => &$value) {
            if ($value['type'] == 0) { // 普通商品
                $value['status'] = 1;
                $value['tag'] = $tag;
                $value['groupid'] = 0;
                $value['iscomment'] = 0;
                $value['create_time'] = NOW_TIME;
                $value['data'] = serialize($value);
            } else {
                // TODO: 组合商品
            }

            // 提成
            $affiliate['order_id'] = '0';
            $affiliate['order_sn'] = $tag;
            $affiliate['goods_id'] = $value['goodid'];
            $affiliate['goods_num'] = $value['num'];
            $affiliate['goods_name'] = get_good_name($value['goodid']) . '-' . $value['parameters'];
            $affiliate['goods_price'] = $value['member_price'];
            $affiliate['member_id'] = $uid;
            $affiliate['member_name'] = get_username($uid);
            $affiliate['rem_code'] = $memberinfo['code'];
            $affiliate['business_id'] = $member_agent_id;
            
            //获取该会员等级对应的商品价格
            if($value['member_level_price']){
            	$member_level_price = @unserialize($value['member_level_price']);
            	if(!empty($member_level_price)){
            		$memeber_price = $member_level_price[$memberinfo['member_level_id']];
            		//获取上级代理的会员等级及对应的商品价格
            		if(!empty($member_agent_id)){
            			$member_parents = M('member')->field('member_type,member_level_id,member_agent_id')->where(array('uid' => $member_agent_id))->find();//上级代理的类型和等级
            			$member_parent_price = $member_level_price[$member_parents['member_level_id']];//上级会员的等级价
            			$member_parent_type = $member_parents['member_type'];
            			
            			// 计算提成
            			if($member_parent_price > 0 && ($memeber_price-$member_parent_price) > 0){//判断提成金额是否正确
            				$affiliate['money'] = $memeber_price-$member_parent_price;//会员价格 - 上级代理价格
            				$this->orderapi->affiliate_log($affiliate);
            				//判断该会员是否为终端消费者，如果是区域代理商也要提成
            				if($member_parents['member_type'] == 2 && $member_parents['member_agent_id'] > 0){
            					$member_agent_parents = M('member')->field('member_type,member_level_id,member_agent_id')->where(array('uid' => $member_parents['member_agent_id']))->find();//上级代理的类型和等级
            					$member_agent_parents_price = $member_level_price[$member_agent_parents['member_level_id']];//上级会员的等级价
            					if($member_agent_parents['member_type'] == 1 && $member_agent_parents_price > 0 && ($member_parent_price - $member_agent_parents_price > 0)){
            						$affiliate['business_id'] = $member_parents['member_agent_id'];
            						$affiliate['money'] = $member_parent_price - $member_agent_parents_price;//会员价格 - 上级代理价格
            						$this->orderapi->affiliate_log($affiliate);
            					}
            				}
            			}
            		}

            		/* // 提成 门店购买商品 代理会员提成
            		if ($memberinfo['member_type'] == 2 && !empty($member_agent_id)) {
            			$affiliate['affiliate'] = $value['affilite_a'];
            			 $affiliate['money'] = $value['member_price'] * $value['affilite_a'] * $value['num'];
            			$this->orderapi->affiliate_log($affiliate);
            		}
            		 
            		// 提成 普通会员购买 门店会员提成
            		if ($memberinfo['member_type'] == 3 && !empty($member_agent_id)) {
            			$affiliate['affiliate'] = $value['affilite_b'];
            			 $affiliate['money'] = $value['member_price'] * $value['affilite_b'] * $value['num'];
            			$this->orderapi->affiliate_log($affiliate);
            		} */
            	}
            }

            $_goodsdata[] = $value;
            if ($iscart) {
                //删除购物车中已经下单的物品
                if (!session('user_auth')) {
                    if (isset($_SESSION['cart'][$value['sort']])) {
                        unset($_SESSION['cart'][$value['sort']]);
                    }
                } else {
                    //删除购物车中已经下单的物品
                    $re = M("shopcart")->where("uid=" . $uid . " and sort='" . $value["sort"] . "'")->delete();
                }
            }

            // 修改库存
            if ($value['proid']) {
                $re = M('p_xianshi_goods')->where("goods_id=" . $value["goodid"] . " and xianshi_id = '" . $value["proid"] . "'")->setDec('xianshi_stock', $value["num"]);
            }

            if ($value['store_id'] > 0) {   // 门店iD大于0  则修改 门店商品库存/销量
                $re = M('store_goods')->where(array("goods_id" => $value["goodid"], 'store_id' => $value['store_id']))->setDec('stock', $value["num"]);   // 减少库存
                $re = M('store_goods')->where(array("goods_id" => $value["goodid"], 'store_id' => $value['store_id']))->setInc('sales_num', $value["num"]);   // 增加销量
            } else {    // 否则修改 平台商品库存销量
                $re = M('document_product')->where("id=" . $value["goodid"])->setDec('stock', $value["num"]);
                //记录销售数量 并修改缓存
                $re = M('document')->where("id=" . $value["goodid"])->setInc('sales', $value["num"]);
                $re = M('document_product')->where("id=" . $value["goodid"])->setInc('totalsales', $value["num"]);
            }
//            if (S('p_' . $value['goodid'])) {
//                $detail = S('p_' . $value['goodid']);
//                $detail['sales'] = $detail['sales'] + $value["num"];
//                S('p_' . $value['goodid'], $detail);
//            }
        }
        //保存订单商品信息
        //echo $re = M("Shoplist")->addAll($_goodsdata);
        foreach ($_goodsdata as $key => $value) {
            $ress = M("Shoplist")->add($value);
        }
        $order = D('order');
        $orderid = $order->where("tag='$tag'")->getField('id');
        if ($orderid) { // 订单已经存在 则跳转到支付页面
            return array('error' => false, 'msg' => "订单已经存在 则跳转到支付页面", 'url' => U('Payment/index', array('id' => $tag)));
        }

        $this->uid = $uid;
        $this->tag = $tag;
        // 添加订单记录
        $result = $this->addOrder($post, $store_id, $goodsTotal, $orderType, $haiguan_rate_total, $discount_amount);
        $address_data = M('transport')->where(array('id' => $senderid))->find();
        $address_data['orderid'] = $result; // 订单ID
        unset($address_data['id']);
        $address_id = M('order_address')->data($address_data)->add();
        $this->senderid = $address_id;    // 收货地址ID
        $re = M("order")->where(array('id' => $result))->setField('addressid', $address_id);
        $this->orderapi->affiliate_log(array('order_id' => $result), array('order_sn' => $tag));

        // 生成初始发货单
        $tempdeliverylist = think_decrypt(cookie("new_shipping"));
        $deliverylist = unserialize($tempdeliverylist);
        foreach ($deliverylist as $key => $item) {
            $delivery['order_sn'] = $tag;
            $delivery['order_id'] = $result;
            $delivery['uid'] = $uid;
            $delivery['address_id'] = $address_id;
            $delivery['shipping_fee'] = $item['shipping_fee'];
            $delivery['warehouse'] = $item['warehouse'];
            $delivery['weight'] = $item['weight'];
            $delivery['total'] = $item['total'];
            $delivery['add_time'] = NOW_TIME;
            M("order_delivery")->data($delivery)->add();
        }
        cookie("new_shipping", '');   // 清空cookie
        if ($result) {
            return array('success' => true, 'msg' => '订单提交成功', 'orderid' => $tag, 'url' => U('Payment/index', array('id' => $tag)));
        } else {
            return array('error' => true, 'msg' => '订单提交失败', 'url' => U('Cart/index'));
        }
    }

    /**
     *  添加订单信息
     * @param type $post
     * @param type $store_id  // 门店ID
     * @param type $goodsTotal  // 商品金额
     * @param type $orderType  // 订单类型
     * @param type $haiguan_rate_total 海关费
     * @param type $discount_amount  优惠金额
     * @return type $orderid  订单 ID
     */
    public function addOrder($post, $store_id, $goodsTotal, $orderType, $haiguan_rate_total, $discount_amount) {
        //dump($post);exit;
        //计算提交的订单的商品运费 商品有运费根据配送方式的没运费不同
        if ($post['shipping_fee'] == 0 && isset($post['shipping_fee'])) {
            $shipmoney = $post['shipping_fee'];
        } else {
            $shipping = M('shipping_extend')->where(array('id' => $post['distribution']))->find();
            $goodsTotalWeight = cookie('goodsTotalWeight');
            if ($shipping['snum'] >= $goodsTotalWeight) {
                $shipmoney = $shipping['sprice'];
            } else {
                $yxfee = ($goodsTotalWeight - $shipping['snum']) * $shipping['xprice'];
                $shipmoney = $shipping['sprice'] + $yxfee;
            }
        }

//        if ($goodsTotal < C('LOWWEST')) {
            $trans_fee = $post['shipping_fee'];        // 运费
//        } else {
//            $trans_fee = 0;
//        }
        //计算提交的积分兑换
        if ($post['score']) {
            $score = $post['score'];
            //读取配置，1000积分兑换1元
            $points_amount = $score / C('RATIO');   // 积分金额
            $data['score'] = $score;
            $re = M("member")->where("uid='$this->uid'")->setField('score', 0);
        } else {
            $points_amount = 0;
        }
        //计算提交的优惠券
        $couponcode = $post['couponcode'];
        //计算提交的订单的费用（含运费）
        $xfee = $goodsTotal + $trans_fee - $points_amount;
        //计算优惠券可使用的金额,home/common/function
        $couponfee = 0; // 优惠券金额
        if ($couponcode) {
            $couponfee = get_fcoupon_fee($couponcode, $xfee);    // 优惠券金额
        }

//        $senderid = $this->senderid; // 收货地址ID
        $senderid = $post['senderid']; // 收货地址ID
        $data['codeid'] = $couponcode;
        $data['order_from'] = $post['order_from'];  // 订单来源
        $data['codemoney'] = $couponfee;
        $data['addressid'] = $senderid;
        $data['total'] = $goodsTotal;    // 商品金额
        $data['create_time'] = time();
        $data['shipprice'] = $trans_fee;
         $data['tool'] = $shipping['shipping_title'] ? : $post['store_order'];
        $data['shipway'] = $shipping['shipping_title'] ? : $post['store_order'];
        $data['distribution'] = $shipping['shipping_id'] ? : 0;
        $data['weight'] = $goodsTotalWeight;
        
        //计算提交的订单的总费用
        $orderTotal = $goodsTotal + $trans_fee + $haiguan_rate_total - $points_amount - $couponfee; // 订单总额 = 商品金额 + 运费 + 海关税费 - 积分金额 -优惠券金额
        $data['pricetotal'] = $orderTotal;
        $data['orderid'] = $this->tag;
        $data['tag'] = $this->tag;
        $data['uid'] = $this->uid;
        $data['order_type'] = $orderType;
        $data['store_id'] = $store_id;
        $data['haiguan_rate_total'] = $haiguan_rate_total;
        $data['discount_amount'] = $discount_amount;
        $data['message'] = $post['order_message']; // 订单留言
        //修改订单状态为用户已提交
        //订单频道
        // $data['domainid']=cookie("current_domainid");
        //发票信息保存，首先判断是否开启发票功能，如查开启则获取发票：发票抬头invoiceup，发票名称invoicename，发票内容invoicecontent,数据序列化为保存在order表invoice字段中
        $conf = C();
        $ishave = $post['ishave'];
        if (isset($conf['HADINVOICE']) && $conf['HADINVOICE'] == 1 && !empty($ishave)) {
            $invoicecontent = $conf['INVOICECONTENT']; //发票内容
            $contentarray = $this->parse_config_attr_info($invoicecontent);
            $invoiceup = I("invoiceup");
            $invoiceupstring = '';
            if ($invoiceup == 0) {
                $invoiceupstring = '个人';
            } elseif ($invoiceup == 1) {
                $invoiceupstring = '企业';
            }
            $invoicename = I("invoicename");
            $invoicecontent = I("invoicecontent");
            $invoicecontentstring = isset($contentarray[$invoicecontent]) ? $contentarray[$invoicecontent] : '';

            $invoice = array();
            $invoice['invoiceup'] = $invoiceupstring;
            $invoice['invoicename'] = $invoicename;
            $invoice['invoicecontent'] = $invoicecontentstring;

            $data['invoice'] = serialize($invoice);
        }
        //根据订单id保存对应的费用数据
        $data['backinfo'] = $post['backinfo'] ? : '等待支付';
        if ($post['orderType'] == 4) {
            $data['ispay'] = "3";
        } else {
            $data['ispay'] = "1";
        }


        $data['status'] = "-1"; //待支付
        $orderid = D('order')->add($data);

        $this->orderid = $orderid;
        $res = M("shoplist")->where("tag='$this->tag'")->setField('orderid', $orderid);
        if (session('memberinfo.member_type') == 2) {
            $orderapi = new OrderApi();
            $buy_goodslist = M('Shoplist')->where(array('orderid' => $orderid))->select();
            foreach ($buy_goodslist as $key => $value) {
                if (session('memberinfo.member_type') == 2 && $value['cart_type'] == 1) { // 是门店 并且是购买的普通商品 就加入到门店商品表里面
                    $orderapi->addStoreGoods($value);   // 购买的商品添加到门店的商品里面
                }
            }
        }
        $this->status = $data['status'];
        $this->addOrderLog($orderid, array('msg' => get_username() . '用户提交了订单'));
        if ($orderid) {
            $this->addPay($orderTotal, $points_amount, $goodsTotal, $trans_fee, $couponfee);
        }
        return $orderid;
    }

    /**
     * 生成支付信息
     * @param type $uid
     * @param type $tag
     * @param type $orderTotal
     * @param type $points_amount
     * @param type $goodsTotal
     * @param type $trans_fee
     * @param type $couponfee
     * @param type $senderid
     */
    public function addPay($orderTotal, $points_amount, $goodsTotal, $trans_fee, $couponfee) {
        // 生成支付信息
        $pay = M("pay");
        $pay->create();
        $pay->money = $orderTotal;
        $pay->ratio = $points_amount;
        $pay->total = $goodsTotal;
        $pay->out_trade_no = $this->tag;
        $pay->yunfee = $trans_fee;
        $pay->coupon = $couponfee ? : 0;
        $pay->uid = $this->uid;
        $pay->addressid = $this->senderid;
        $pay->create_time = NOW_TIME;
        $pay->type = 1; //货到付款
        $pay->status = 1;
        $pay->add();
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
        $order_log->log_role = $this->uid ? : $data['roleid'];
        $order_log->log_user = $this->uid ? : $data['uid'];
        $order_log->log_orderstate = $this->status ? : $data['status'];
        $order_log->add();
    }

    public function ordersn() {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        //$orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%04d%02d', rand(1000, 9999),rand(0,99));
        //2015/5/8 11:04 sheshanhu
//        $orderSn = $yCode[intval(date('Y')) - 2011]  .date('Ymd'). substr(time(), -3) . substr(microtime(), 2, 3) . sprintf('%04d%02d', rand(1000, 9999),rand(0,99));

        $orderSn = 'E' . date("Ymdhis") . sprintf('%01d', rand(0, 9));

        return $orderSn;
    }

    /**
     * 格式化数据
     * @param type $string
     * @return type
     */
    public function parse_config_attr_info($string) {
        $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
        if (strpos($string, ':')) {
            $value = array();
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }

    /**
     * 获取购物车属性
     * @param type $id  购物车ID
     * @param type $store_id  店铺ID
     * @param type $price 价格
     * @param type $num 数量
     * @param type $sort 排序
     * @param type $type 类型 0 普通商品 1组合商品
     * @param type $parameters 参数
     * @param type $pro 是否促销 促销ID
     * @param type $promsg 促销参数
     * @param type $cart_type 购物车类型 1普通商品，2跨境商品 3 直邮商品
     * @param type $is_system 下单方式  0自动下单  1手动下单
     * @return type
     */
    public function cartData($id, $store_id, $price, $num, $sort, $type, $parameters, $pro, $promsg, $cart_type = 2, $is_system = 0) {
        $item['uid'] = $this->uid;
        $item['goodid'] = $id;
        $item['store_id'] = $store_id;
        $item['price'] = $price;
        $item['num'] = $num;
        $item['sort'] = $sort;
        $item['type'] = $type;
        $item['parameters'] = $parameters;
        $item['proid'] = $pro;
        $item['promsg'] = $promsg;
        $item['cart_type'] = $cart_type;
        $item['create_time'] = NOW_TIME;
        $item['is_system'] = $is_system;
        return $item;
    }

    /**
     * 保存购物车商品到session
     * @param type $goodsInfo
     * @param type $goodsid
     * @param type $price
     * @param type $num
     * @param type $sort
     * @param type $type
     * @param type $parameters
     * @param type $pro
     * @param type $promsg
     * @param type $cart_type
     * @param type $is_system
     * @return string
     */
    private function saveSession($goodsInfo, $goodsid, $store_id, $price, $num, $sort, $type, $parameters, $pro, $promsg, $cart_type, $is_system) {

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        if (isset($_SESSION['cart'][$sort])) {
            if ($type == 0) {
                $goodnum = $_SESSION['cart'][$sort]['num'] + $num;
                if ($goodnum > $goodsInfo['stock']) { //判断库存
                    return array('error' => TRUE, 'msg' => '库存不足，请联系客服。', 'status' => 0, 'url');
                }
            }
            $_SESSION['cart'][$sort]['num'] += $num;
            $num = $_SESSION['cart'][$sort]['num'];
            $item = $this->cartData($goodsid, $store_id, $price, $num, $sort, $type, $parameters, $pro, $promsg, $cart_type, $is_system);
            $_SESSION['cart'][$sort] = $item;
            $exsit = $_SESSION['cart'][$sort] ? 1 : 0;
        } else {
            if ($type == 0) {
                if ($num > $goodsInfo['stock']) {   //判断库存
                    return array('error' => TRUE, 'msg' => '库存不足，请联系客服。', 'status' => 0, 'url');
                }
            }
            $item = $this->cartData($goodsid, $store_id, $price, $num, $sort, $type, $parameters, $pro, $promsg, $cart_type, $is_system);
            $_SESSION['cart'][$sort] = $item;
            $exsit = "0";
        }
        return $exsit;
    }

    /**
     * 获取当前商品的价格
     * @param type $goodsid 商品ID
     * @param type $num 购买数量
     * @param type $type 商品类型
     * @param type $pro 促销ID
     * @param type $store_id 门店ID
     * @return type array();
     */
    private function getGoodsPirce($goodsid, $num, $type = 0, $pro = 0, $store_id = 0, $is_system = 0) {
        $goodsModel = new GoodsApi();
        //根据传值ID查询商品是否存在
        if ($type == 0) {   // 普通商品
            $map = array();
            $map['id'] = $goodsid;
            $goodsInfo = $goodsModel->getDocument($map, 'id desc', 1);
        } elseif ($type == 1) { // TODO组合商品
            $map = array();
            $map['id'] = $goodsid;
            $goodsInfo = $goodsModel->getproductsgroup($map);
        }
        if (!empty($goodsInfo)) {
            if ($is_system) {
                $price = $goodsInfo['price'];  // 商品价格
            } else {
                $price = $goodsInfo['member_price'];  // 商品会员价格
            }
            $promsg = '';
            $cart_type = 1;
            if ($store_id > 0) {
                $cart_type = 4;
            } else {
                $cart_type = $goodsInfo["product_type"];
            }
//            if ($store_id == 0) {
//                $cart_type = 1;
//            } elseif ($store_id > 0 && $goodsInfo['product_type'] == 1) {
//                $cart_type = 4;
//            } elseif ($goodsInfo['product_type'] == 2) {
//                $cart_type = 2;
//            } elseif ($goodsInfo['product_type'] == 3) {
//                $cart_type = 3;
//            }
            if ($pro) {
                $promotion = $goodsModel->getpromotion_info($goodsid); // 促销信息
                if ($promotion) {
                    if ($promotion['xianshi_stock'] < $num) {
                        return array('error' => true, 'msg' => '促销商品库存不足');
                    }
                    $price = $promotion['xianshi_price'];
                    $promsg = $promotion['xianshi_title'];
                }
            }
        } else {
            return array('error' => true, 'msg' => '商品不存在');
        }

        if ($store_id > 0) {
            $store_goods = M('store_goods')->where(array('goods_id' => $goodsid, 'store_id' => $store_id, 'stock' => array("gt", 0), 'status' => 1))->find();
            if (empty($store_goods)) {  // 如果门店商品不存在，
                return array('error' => false, 'msg' => '门店商品库存不足，或已下架');
            } elseif ($num > $store_goods['stock']) {
                return array('error' => false, 'msg' => '门店商品库存不足');
            }
        }

        $_data['price'] = $price;
        $_data['cart_type'] = $cart_type;
        $_data['promsg'] = $promsg;
        $_data['goodsInfo'] = $goodsInfo;
        return $_data;
    }

    /**
     * 获取配送方式
     * @param type $condition array('id'=>address_id)
     * @return 返回配送列表
     */
    public function get_shipping($condition = array()) {
        $shippingModel = D("ShippingView");
        if ($condition['orderType'] == 4) {
            $shippingids = M('distribution')->where(array('type' => 1, 'status' => 1))->getField('id', TRUE);
            $map['shipping_id'] = array('in', $shippingids);
            $result = $shippingModel->where($map)->order('weight DESC')->select(); // 省市
        } else {
            if ($condition) {
                unset($condition['orderType']);
                $condition['uid'] = is_login();
                $address = M("transport")->where($condition)->order("isdefault desc")->find();
            } else {
                $address = M("transport")->where(array('uid' => is_login(), 'isdefault' => 1))->order("orderid desc")->find();
                if (empty($address)) {
                    $address = M("transport")->where(array('uid' => is_login()))->order("id desc")->find();
                }
            }
            if ($address) {
                $map = array();
                $map["status"] = 1;
                $map['area_name'] = array('like', '%' . $address['city'] . '%');
                $result = $shippingModel->where($map)->order('weight DESC')->select();  // 如果城市查寻不到
                if (empty($result)) {
                    $map['area_name'] = array('like', '%' . $address['province'] . '%');
                    $result = $shippingModel->where($map)->order('weight DESC')->select(); // 省市
                }
            } else { // 最近没有购买记录
                $result = array();
            }
        }
        return $result;
    }

}
