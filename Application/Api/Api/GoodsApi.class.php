<?php

/**
 * 首页管理
 */

namespace Api\Api;

use Api\Api\Api;
use Think\Controller;
use Common\Model\TreeModel;

class GoodsApi extends Controller {

    public $model = '';
    public $totalPages = 0;

    /**
     * 构造方法，实例化操作模型
     */
    protected function _init() {
//        $this->model = new TreeModel();
        $this->model = new \Web\Controller\HomeController();
    }

    /**
     * 产品公共模块查询调用
     * @param type $map 查询条件
     * @param type $order   排序
     * @param type $limit  查询条数
     * @return string
     */
    public function getDocument($map = array(), $order = "id desc", $limit = null) {
        $map['status'] = 1;
        $map['stock'] = array('GT', 0);
        $map['display'] = 1;
        $map["ifcheck"] = 1;

        if (!isset($map["model_id"])) {
            $map["model_id"] = 5;
        }
        if ($limit) {
            $lists = D("DocumentView")->where($map)->field("id")->order($order)->limit($limit)->select();
        } else {
            $lists = D("DocumentView")->where($map)->field("id")->order($order)->select();
        }

        $documentViewModel = D("DocumentView");
        foreach ($lists as $k => $v) {
            $product_detail = array();
//            $product_detail = S('p_' . $v['id'], ''); // TODO： 去掉缓存
            if (!$product_detail) {
                $product_detail = $documentViewModel->find($v['id']);
                $product_detail['favor'] = 0;
                if (is_login()) {
                    $favor = M("favortable")->where(array('uid'=>  is_login(),'goodid'=>$product_detail['id']))->count("*");
                    if (!empty($favor)) {
                        $product_detail['favor'] = 1;
                    }
                }
                if(!$product_detail['org_price']){
                $product_detail['org_price'] = $product_detail['price'];
                }
                $_tempprice = $this->calcGoodsSpecPrice($product_detail);
                $product_detail['org_price'] = $product_detail['price'];
                $product_detail['price'] = $_tempprice['price'];
                $product_detail['show_price'] = $_tempprice['member_price'] ? : $product_detail['price'];
                $product_detail['marketprice'] = $_tempprice['marketprice'];
                $product_detail['member_price'] = $_tempprice['member_price'];
                $product_detail['spec_price'] = $_tempprice['spec_price'];
                $product_detail['spec_name'] = $_tempprice['spec_name'];
                $product_detail['spec_ids'] = $_tempprice['spec_ids'];
                if ($product_detail["cover_id"] || $product_detail['pics']) {
                    $arr = array();
                    if ($product_detail['pics']) {
                        $arr = explode(",", $product_detail['pics']);
                    }
                    $arr[] = $product_detail["cover_id"];
                    $arrmap["id"] = array("in", $arr);
                    $attinfo = M("picture")->where($arrmap)->getField("id,path");
                    
                    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                    foreach ($attinfo as $ckey => $cvalue) {
                        $attinfo[$ckey] = __PICURL__ . $cvalue;
                    }
                    
                    $product_detail["pics_img"] = $attinfo;
                }
                //S('p_' . $v['id'], $product_detail);
            }
            
            $lists[$k] = $product_detail;
        }
        if ($limit == 1) {
            if (empty($lists[0])) {
                return array();
            }
            $attr = $this->getAttr($lists[0]);  // 获取属性规格
            $_data = $lists[0];
            $_data['member_price'] = get_goods_MemberPirce($lists[0]);
            $_data['attribute'] = $attr['attribute'];
            $_data['goodattr'] = $attr['spec'];
//            dump($_data);
            return $_data;
        }
        return $lists;
    }

    /**
     * 根据商品信息计算商品规格价+商品基础价
     * @param type $goodsInfo
     * @return type
     */
    public function calcGoodsSpecPrice($goodsInfo) {
        $_data['price'] = $goodsInfo['price'];
        $_data['marketprice'] = $goodsInfo['marketprice'];
        $_data['member_price'] = get_goods_MemberPirce($goodsInfo);
        $_data['spec_name'] = '';
        $_data['spec_price'] = '';

        $attr = $this->getAttr($goodsInfo);  // 获取属性规格
        if ($attr['spec']) {
            $spec_price = 0;
            $spec_name = '';
            foreach ($attr['spec'] as $key => $value) {
                $spec_price += $value['sub'][0]['price'];
                $spec_name .= $value['sub'][0]['value'] . ' ';
                $spec_ids  .= $value['sub'][0]['id'].',';
            }
            $_data['price'] = ncPriceFormat($goodsInfo['price'] + $spec_price);
            $_data['marketprice'] = ncPriceFormat($goodsInfo['marketprice'] + $spec_price);
            $_data['member_price'] = ncPriceFormat(get_goods_MemberPirce($goodsInfo) + $spec_price);
            $_data['spec_name'] = $spec_name;
            $_data['spec_price'] = $spec_price;
            $_data['spec_ids'] = $spec_ids;
        }
        return $_data;
    }

    function getAttr($info) {//分别查询属性和规格
//    dump($info);
        //先查询自定义属性
        $alreadyattr_one = array(); //gid=" . $info["id"] . " and attributeid=0
        $alreadyattr_one = M("GoodAttr")->where(array('gid' => $info['id'], 'attributeid' => 0))->select();
        $arr = $arr2 = array();
        if ($alreadyattr_one) {
            foreach ($alreadyattr_one as $k => $v) {
                $arr2[$v["id"]] = array();
                $arr2[$v["id"]]["name"] = $v["keys"];
                $arr2[$v["id"]]["value"] = $v["value"];
            }
        }
        //再查询自定义属性里面的属性
        $condition['status'] =1;
        $condition['domainid'] = array('in',array($info['domainid'],1));
        $attribute = M("GoodAttribute")->where($condition)->getField("id,name,types,inputtypes", true);
        $alreadyattr = M("GoodAttr")->where("gid=" . $info["id"] . " and attributeid>0")->select();
        foreach ($alreadyattr as $k => $v) {
            if ($attribute[$v["attributeid"]]["types"] == 0) {//判断是属性
                $arr2[$v["id"]] = array();
                $arr2[$v["id"]]["name"] = $v["keys"];
                $arr2[$v["id"]]["value"] = $v["value"];
            } else { //判断是规格
                if (!isset($arr[$v['attributeid']])) {
                    $arr[$v['attributeid']] = array();
                    $arr[$v['attributeid']]["sub"] = array();
                }
                $arr[$v['attributeid']]['attr_type'] = $attribute[$v["attributeid"]]["types"];
                $arr[$v['attributeid']]['name'] = $attribute[$v["attributeid"]]["name"];
                $arr[$v['attributeid']]['sub'][] = array('value' => $v['value'], 'price' => $v['price'], 'id' => $v['id']);
            }
        }
//        $this->assign("contentgoodattr", $arr2);
        $_data['attribute'] = $arr2; // 属性
        $_data['spec'] = $arr;     // 规格
        return $_data;
    }

    /*
     * * *************************************************************
     * created date: 2015/6/19 15:12
     * created author:sheshanhu
     * content:根据商品组合编号查询组合信息
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    public function getproductsgroup($map = array(), $order = "id desc", $limit = null) {
        $map['status'] = 1;
        //$map["domainid"]= cookie("current_domainid");
        if ($limit) {
            $lists = M("ProductsGroup")->where($map)->field("id")->order($order)->limit($limit)->select();
        } else {
            $lists = M("ProductsGroup")->where($map)->field("id")->order($order)->select();
        }

        $productsgroupModel = D("ProductsGroup");
        foreach ($lists as $k => $v) {
            $product_detail = array();
            $product_detail = S('pg_' . $v['id']);
            if (!$product_detail) {
                $product_detail = $productsgroupModel->find($v['id']);
                //组合商品信息查询
                $gmap = array();
                $gmap['id'] = array('in', $product_detail['uniongood']);
                $product_detail['goodsinfo'] = $this->getDocument($gmap);
                S('pg_' . $v['id'], $product_detail);
            }
            $lists[$k] = $product_detail;
        }
        if ($limit == 1) {
            return $lists[0];
        }
        return $lists;
    }

    /**
     * 获取组合商品信息
     * @param type $id
     * @return type
     */
    public function get_pggood_info($id) {
        $row = M('ProductsGroup')->getbyId($id);
        return $row;
    }

    /**
     * 获取促销商品信息
     * @param type $goods_id
     */
    public function getpromotion_info($goods_id) {
        $xwhere['goods_id'] = $goods_id;
        $pro_goods_info = M("p_xianshi_goods")->where($xwhere)->find();
        if ($pro_goods_info) {
            $pro_info = M("p_xianshi")->where(array('xianshi_id' => $pro_goods_info['xianshi_id']))->find();
            if ($pro_info['end_time'] < NOW_TIME) {
                return FALSE;
            }
            if ($pro_info['status'] != 1) {
                return FALSE;
            }
            $pro_goods_info['end_time'] = $pro_info['end_time'];
            $pro_goods_info['xianshi_status'] = $pro_info['status'];
            $pro_goods_info['xianshi_explain'] = $pro_info['xianshi_explain'];
            return $pro_goods_info;
        }
        return FALSE;
    }

    public function getCategoodlist($map = array(), $order = "id desc", $bases = null) {
        //$map["domainid"]=cookie("current_domainid");
        //$map["pid"] = 0;
        $map["display"] = 1;
        $map['status'] = 1;
        $map["model_id"] = 5;
        $map["issales"] = 1;
        $documentViewModel = D("DocumentView");
        if ($bases) {
            $page = I('page', 1, 'int');
            $pageSize = $_POST['r'] ? : ($bases['pageSize'] ? : 10);
            $start = ($page - 1) * $pageSize;
            $limit = $pageSize;
            $lists = M('Document')->field('id')->where($map)->order($order)->limit("{$start},{$limit}")->select();
        } else {
            $lists = $this->_lists($documentViewModel, $map, $order, array(), "id");
        }
       /*  $collection = array();
        if (session('user_auth')) {//判断收藏
            $uid = session('user_auth,uid');
            $collection = F('collection' . $uid);
        } */
        
        foreach ($lists as $k => $v) {
            $lists[$k] = $this->getDocument(array("id" => $v["id"]), "id desc", 1);
        }
        return $lists;
    }

    /**
     * 获取历史浏览记录
     * @return array
     */
    public function get_history() {
        $history = cookie('history_list' . session('uid'));
        $count = count($history); //统计条数
        if ($count > 10) {
            $history_goodid = array_slice($history, $count - 10); //取最后10条记录
            $good_ids = implode(',', $history_goodid);
        } else {
            $good_ids = implode(',', $history);
        }
        if (!empty($good_ids)) {
            $map = array();
            $map['id'] = array('in', $good_ids);
            $documentViewModel = D("DocumentView");
            $history_list = $this->_lists($documentViewModel, $map);
            foreach ($history_list as $key => $val) {
                if ($val["cover_id"]) {
                    $arrmap['id'] = $val["cover_id"];
                    $attinfo = array();
                    $attinfo = M("picture")->where($arrmap)->getField("id,path");
                    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                    foreach ($attinfo as $ckey => $cvalue) {
                        $attinfo[$ckey] = __PICURL__ .$cvalue; // $val['domainid'] . '/' .
                    }
                    $history_list[$key]["pics_img"] = $attinfo;
                }
            }
        } else {
            $history_list = array();
        }
        return $history_list;
    }

    /**
     * 获取门店商品列表
     * @param type $condition array();
     * @param type $sort array();
     * @return array 商品列表
     */
    public function getStoreGoods_list($condition, $sort = "id DESC", $bases = null) {
        $goodsModel = new GoodsApi();
        if ($bases) {
            $page = I('page', 1, 'int');
            $pageSize = $_POST['r'] ? : ( $bases['pageSize'] ? : 10);
            $start = ($page - 1) * $pageSize;
            $limit = $pageSize;
            $goodslist = M('store_goods')->where($condition)->order($sort)->limit("{$start},{$limit}")->select();
        } else {
            $goodslist = $this->_lists("store_goods", $condition, $sort, array());
        }
        $goodsCount = M('store_goods')->where(array('store_id' => $condition['store_id']))->count('goods_num');
        $totalPages = ceil($goodsCount / $_POST['r']);
        $lists = array();
        foreach ($goodslist as $k => $v) {
            $lists[$k] = $this->getDocument(array("id" => $v["goods_id"]), "id desc", 1);
            $lists[$k]['stock_warning'] = $v['stock_warning'];  // 库存预警
            $lists[$k]['buy_price'] = $v['buy_price'];  // 采购价格
            $lists[$k]['goods_price'] = $v['goods_price'];  // 采购价格
            $lists[$k]['goods_marketprice'] = $v['goods_marketprice'];  // 采购价格
            $lists[$k]['store_id'] = $v['store_id'];    // 门店ID
            $lists[$k]['goods_num'] = $v['goods_num'];  //商品数量
            $lists[$k]['sales_num'] = $v['sales_num'];  //销售数量
            $lists[$k]['stock'] = $v['stock'];          // 门店库存
            $lists[$k]['goods_id'] = $v['goods_id'];          // 门店商品ID
            $lists[$k]['goods_name'] = $v['goods_name'];          // 门店商品名称
            $lists[$k]['storegoodsid'] = $v['id'];          // 门店ID
            $lists[$k]['storegoodsstatus'] = $v['status'];          // 门店状态
        }
//        dump($lists);
        $_data['goodslist'] = $lists;
        $_data['count'] = $goodsCount;
        $_data['totalPages'] = $totalPages;
        return $_data;
    }

    /**
     * 获取门店商品详情页
     * @param array $condition array('store_id'=>'','goods_id'=>'');
     * @return array 商品信息
     */
    public function getStoreGoodsDetail($condition) {
        $condition['status'] = 1;
        $storegoods = M('store_goods')->where($condition)->find();
        if (empty($storegoods)) {
            return array('error' => true, 'msg' => '门店商品不存在，或已下架');
        }
        $map['id'] = $storegoods['goods_id'];
        $map['domainid'] = $storegoods['domainid'];
        $goodsinfo = $this->getDocument($map, '', 1);
        if (empty($goodsinfo)) {
            return false;
        }
        $goodsinfo['stock_warning'] = $storegoods['stock_warning'];  // 库存预警
        $goodsinfo['buy_price'] = $storegoods['buy_price'];  // 采购价格
        $goodsinfo['store_id'] = $storegoods['store_id'];    // 门店ID
        $goodsinfo['goods_num'] = $storegoods['goods_num'];  //商品数量
        $goodsinfo['sales_num'] = $storegoods['sales_num'];  //销售数量
        $goodsinfo['stock'] = $storegoods['stock'];          // 门店库存
        $goodsinfo['goods_id'] = $storegoods['goods_id'];          // 门店库存
        return $goodsinfo;
    }

    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base    基本的查询条件
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *
     * @return array|false
     * 返回数据集
     */
    public function _lists($model, $where = array(), $order = '', $base = array('status' => array('egt', 0)), $field = true) {
        $options = array();
        $REQUEST = array_merge(I('post.'), I('get.')); //(array)I('request.');
        if (is_string($model)) {
            $model = M($model);
        }

        $OPT = new \ReflectionProperty($model, 'options');
        $OPT->setAccessible(true);

        $pk = $model->getPk();
        if ($order === null) {
            //order置空
        } else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array('desc', 'asc'))) {
            $options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
        } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
            $options['order'] = $pk . ' desc';
        } elseif ($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'], $REQUEST['_field']);
        $options['where'] = array_filter(array_merge((array) $base, /* $REQUEST, */ (array) $where), function($val) {
            if ($val === '' || $val === null) {
                return false;
            } else {
                return true;
            }
        });
        if (empty($options['where'])) {
            unset($options['where']);
        }
        $options = array_merge((array) $OPT->getValue($model), $options);
        $total = $model->where($options['where'])->count();

        if (isset($REQUEST['r'])) {
            $listRows = (int) $REQUEST['r'];
        } else {
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $page->rollPage = 5;
        $p = $page->new_pc_page();
//        $p =$page->frontshow();
        $this->assign('_page', $p ? $p : '');

        $minpage = new \Think\Page($total, $listRows);
//        if ($total > $listRows) {
        $minpage->setConfig('theme', '%DOWN_PAGE% %UP_PAGE% %TOTAL_PAGE%');
//        }
        $minpage->rollPage = 5;
        $min_p = $minpage->min_page();

        $this->assign('_minpage', $min_p ? $min_p : '');
        $this->assign('totalPages', $page->totalPages);
        $this->assign('_total', $total);
        $aPage = I("page", 1, "intval");
        $this->assign('p', $aPage);
        $options['limit'] = $page->firstRow . ',' . $page->listRows;
        $model->setProperty('options', $options);
        $list = $model->field($field)->select();
        return $list;
    }

}
