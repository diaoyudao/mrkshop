<?php

/**
 * 商品列表/详情
 */

namespace Wap\Controller;

use Api\Api\GoodsApi;

class GoodsController extends HomeController {

    private $goodsapi = '';

    protected function _initialize() {
        parent::_initialize();
        $this->goodsapi = new GoodsApi();
    }

    /**
     * 商品详情页
     */
    public function index() {
        $this->display('goods');
    }

    /**
     * 商品列表
     */
    public function lists($is_zhiyou = 0) {
        $_POST['r'] = I('r') ? : 12;
        $is_ajax = I('ajax') ? 1 : 0;
        if ($is_ajax) {
            $_POST['p'] = I('page');
        }
        $cateid = I("id", 0);
        $domainid = I("domainid", 0);
        $brandid = I('brandid', 0);
        $seopid = 0;

        $price = I('get.pz', '');    //条件筛选 价格
        //获取所有频道
        $map = array();
        $map["id"] = array("gt", 1);
        $domainlist = $this->getDomain($map);
        $domainlist[0] = array('id' => 0, 'name' => '全部');
        $this->assign("domainlist", $domainlist);
//        dump($domainlist);
        $this->assign("domainid", $domainid);

        $this->assign('is_ajax', $is_ajax);
        // 获取当前频道
        $subdomain = M("subdomain")->where(array('id' => $domainid))->find();
        $this->assign("subdomain", $subdomain);
        //调用品牌
        $brandlist = $this->getBrand();
        $brandlist[0] = array('id' => 0, 'title' => '全部', 'name' => '全部');
        $this->assign('brandlist', $brandlist);

        $this->assign('brand', M('subdomain_brand')->where(array('id' => $brandid))->find());
        $this->assign('page', I('p'));

        //获取商品类别及子分类
        $map = array();
        $map["ismenu"] = 2;
        if ($domainid)
            $map["domainid"] = $domainid;
        $categorytop = $this->getCategory($map);
        $this->assign("categorysub", $categorytop);
        $map = array();
        if ($cateid) {
            //设置列表顶部分类
            $this->assign('categoryid', $cateid);
            $category = array();
            $category = $this->categoryinfo($cateid);
            $pid = $category["pid"];
            if ($category["pid"] == 0) {
                $pid = $category["id"];
                $map = array();
                $map["ismenu"] = 2;
                $map["pid"] = $category['id'];
                $currentcatlist = $this->getCatalog($map);
            } else {
                $seopid = $category["pid"];
                $map = array();
                $map["ismenu"] = 2;
                $map["pid"] = $category['pid'];
                $pcategory = $this->categoryinfo($category['pid']);
                $this->assign("pcategory", $pcategory);
                $currentcatlist = $this->getCatalog($map);
            }
            $this->assign("categorypid", $pid);
            $this->assign("currentcategory", $category);
        } else {
            $this->assign('categoryid', 0);
            $this->assign("categorypid", 0);
            $this->assign("currentcategory", array("id" => 0));
            $currentcatlist = array();
        }

//        dump($categorytop);
        //获取子级分类 ids
        if ($currentcatlist) {
            $cids = array();
            $cids[] = $id;
            foreach ($currentcatlist as $k => $vs) {
                $cids[] = $vs['id'];
            }
        }

        //处理排序
        $key = I('get.order', 1);
        $sort = I('get.sort', 0);
        $ssort = 'asc'; //默认排序
        if ($sort == 1) {
            $ssort = 'desc';
        } else {
            $ssort = 'asc';
        }
        if ($key) {
            if ($key == "1") {//最新
                $listsort = "id " . $ssort;
            } else if ($key == "2") {//销量
                $listsort = "sales " . $ssort;
            } else if ($key == "3") {//热门
                $listsort = "ishot " . $ssort;
            } else if ($key == "4") {//价格
                $listsort = "price " . $ssort;
            }
        }
        if ($ssort == "desc") {
            $see = 0;
            $sortvalue = 'up';
        } else {
            $see = 1;
            $sortvalue = 'down';
        }
        $keyword = I("keywords", "");
        $keyword = htmlspecialchars($keyword);
        $minganci = C("MGSEARCH");
        $minganciarr = explode(",", $minganci);
        $keyword = str_replace($minganciarr, "", $keyword);
        if ($keyword) {
            $map["title"] = array("like", "%" . $keyword . "%");
            $this->assign("keywords", $keyword);
        }
        $stringcx = 'domainid=' . $domainid . '&id=' . $cateid . '&brandid=' . $brandid . '&keywords=' . $keyword;


        $this->assign('stringcx', $stringcx);
        $this->assign('see', $see);
        $this->assign('order', $key);
        $this->assign('value', $sortvalue);

        //返回查询参数
        $_filter = array();
        !empty($price) ? $_filter['pz'] = $price : '';
        $_filter['type'] = I('type') ? intval(I('type')) : 2;
        $_filter['domainid'] = $domainid;
        $_filter['brandid'] = $brandid;
        $_filter['keywords'] = $keyword;
        $_filter['order'] = $key;
        $_filter['sort'] = $sort;
        $this->assign('_filter', $_filter);

//        dump($_filter);
        if (isset($price) && $price) {
            list($price1, $price2) = explode('-', $price);
            $price1 = intval($price1);
            $price2 = intval($price2);
            if (!$price2) {
                $map['price'] = array('gt', $price1);
            } else {
                $map['price'] = array('between', array("{$price1}", "{$price2}"));
            }
        }

        if ($keyword) {
            $uid = session('uid');
            $historykeywords = cookie('historyWord' . $uid);
            $historykeywords[] = $keyword;
            Cookie('historyWord' . $uid, array_unique($historykeywords));
        }

        //商品列表
        if ($domainid) {
            $map["domainid"] = $domainid;
        }
        if ($brandid) {
            $map["brandid"] = $brandid;
        }
        if ($is_zhiyou) {
            $map['product_type'] = 3;
        } else {
            $product_type = I('type') ? intval(I('type')) : 1;
            $map['product_type'] = $product_type;
        }
        unset($map['ismenu']);
        $lists = $this->goodsapi->getCategoodlist($map, $listsort, I('ajax'));
//        dump($lists);

        $this->assign('totalPages', $this->goodsapi->totalPages);
        unset($lists['totalPages']);
        //对商品的收藏状态进行判断
//        if (is_login()) {
//            $Member = D("Member");
//            $uid = $Member->uid();
//            $fav = D("favortable");
//            $favor = $fav->getfavor(1);
//            Cookie('favor' . $uid, $favor);
//            //$favor = Cookie('favor'.$uid);
//            if (!empty($favor)) {
//                foreach ($lists as $key => $value) {
//                    if (in_array($value['id'], $favor)) {
//                        $lists[$key]['favor'] = 1;
//                    }
//                }
//            }
//        }
        if (I('ajax')) {
            $this->assign("lists", $lists);
            $re = $this->fetch('Goods/lists.ajax');
            exit($re);
        }
//        dump($lists);
        $this->assign("lists", $lists);

        // 价格区间
        $price_list = array(0 => '全部', '1' => '0-99', '2' => '100-299', '3' => '300-499', '4' => '500-899', '5' => '900-1999', '6' => '2000-2499', '7' => '2499以上');
        $this->assign('price_list', $price_list);
        $this->meta_title = $subdomain['name'] ? : '商品列表';
        if ($is_zhiyou) {
            $this->display('lists.zhiyou');
        } else {
            $this->display('lists');
        }
    }

    /**
     * 店铺精选商品
     */
    public function storegoods() {
        $_POST['r'] = I('r') ? : 12;
        $domainid = I("domainid", 0);
        $brandid = I('brandid', 0);
        $seopid = 0;

        $price = I('get.pz', '');    //条件筛选 价格
        //获取所有频道
        $map = array();
        $map["id"] = array("gt", 1);
        $domainlist = $this->getDomain($map);
        $domainlist[0] = array('id' => 0, 'name' => '全部');
        $this->assign("domainlist", $domainlist);
//        dump($domainlist);
        $this->assign("domainid", $domainid);

        // 获取当前频道
        $subdomain = M("subdomain")->where(array('id' => $domainid))->find();
        $this->assign("subdomain", $subdomain);
        //调用品牌
        $brandlist = $this->getBrand();
        $brandlist[0] = array('id' => 0, 'title' => '全部', 'name' => '全部');
        $this->assign('brandlist', $brandlist);

        $this->assign('brand', M('subdomain_brand')->where(array('id' => $brandid))->find());
        $this->assign('page', I('p'));

        //获取商品类别及子分类
        $map = array();
        $map["ismenu"] = 2;
        if ($domainid) {
            $map["domainid"] = $domainid;
        }
        $categorytop = $this->getCategory($map);
        $this->assign("categorysub", $categorytop);
        $brandlist = $this->getBrand($map);
        $this->assign('brandlist', $brandlist);
        $this->assign('brandid', $brandid);
        $this->assign('page', I('p'));

        //处理排序
        $key = I('get.order', 1);
        $sort = I('get.sort', 0);
        $ssort = 'asc'; //默认排序
        if ($sort == 1) {
            $ssort = 'desc';
        } else {
            $ssort = 'asc';
        }
        if ($key) {
            if ($key == "1") {//最新
                $listsort = "id " . $ssort;
            } else if ($key == "2") {//销量
                $listsort = "sales_num " . $ssort;
            } else if ($key == "3") {//热门
                $listsort = "ishot " . $ssort;
            } else if ($key == "4") {//价格
                $listsort = "buy_price " . $ssort;
            }
        }
        if ($ssort == "desc") {
            $see = 0;
            $sortvalue = 'up';
        } else {
            $see = 1;
            $sortvalue = 'down';
        }

        $keyword = I("keywords", "");
        $keyword = htmlspecialchars($keyword);
        $minganci = C("MGSEARCH");
        $minganciarr = explode(",", $minganci);
        $keyword = str_replace($minganciarr, "", $keyword);
        if ($keyword) {
            $map["goods_name"] = array("like", "%" . $keyword . "%");
            $this->assign("keywords", $keyword);
        }

        $stringcx = 'domainid=' . $domainid . '&id=' . $category_id . '&brandid=' . $brandid . '&keywords=' . $keyword;

        $this->assign('stringcx', $stringcx);
        $this->assign('see', $see);
        $this->assign('order', $key);
        $this->assign('value', $sortvalue);

        $store_id = session('memberinfo.member_agent_id');  // 店铺ID
        //返回查询参数
        $_filter = array();
        !empty($price) ? $_filter['pz'] = $price : '';
        $_filter['type'] = I('type') ? intval(I('type')) : 2;
        $_filter['domainid'] = $domainid;
        $_filter['brandid'] = $brandid;
        $_filter['store_id'] = $store_id;
        $_filter['keywords'] = $keyword;
        $_filter['order'] = $key;
        $_filter['sort'] = $sort;
        $this->assign('_filter', $_filter);

//        dump($_filter);

        $condition['store_id'] = $store_id;
        $this->assign('store_id', $store_id);
        if (isset($price) && $price) {
            list($price1, $price2) = explode('-', $price);
            $price1 = intval($price1);
            $price2 = intval($price2);
            if (!$price2) {
                $condition['goods_price'] = array('gt', $price1);
            } else {
                $condition['goods_price'] = array('between', array("{$price1}", "{$price2}"));
            }
        }

        if ($domainid) {
            $condition['domainid'] = $domainid;
        }
        if ($brandid) {
            $condition['brandid'] = $brandid;
        }
        if ($category_id) {
            $condition['category_id'] = $category_id;
        }
        $condition['stock'] = array('Gt', 0);
        $bases = array(1);
        $lists = $this->goodsapi->getStoreGoods_list($condition, $listsort, $bases);
//        dump($lists);
        $this->assign("lists", $lists['goodslist']);
        $totalPages = $lists['totalPages'];
        $this->assign('totalPages', $totalPages);

        // 价格区间
        $price_list = array(0 => '全部', '1' => '0-99', '2' => '100-299', '3' => '300-499', '4' => '500-899', '5' => '900-1999', '6' => '2000-2499', '7' => '2499以上');
        $this->assign('price_list', $price_list);
        if (I('ajax')) {
            $this->display('lists.store.ajax');
        } else {
            $this->display('lists.store');
        }
    }

    /**
     * 商品详情页
     */
    public function detail() {
        $id = I("id");
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

        $goodsapi = new GoodsApi();
        $condition['id'] = $id;
        $condition['domainid'] = $channelinfo['id'];
//        $condition['issales'] = 1;
        $info = $goodsapi->getDocument($condition, "id desc", 1);
//        $info = $this->getDocument(array("id" => $id, 'domainid' => $channelinfo['id'], 'issales' => 2), "id desc", 1);
        if (!$info) {
            $this->error('未找到相关产品');
        }
        $this->assign("contentgoodattr", $info['attribute']);
        //获取面包屑导航信息
        $info["cateinfo"] = $this->getCatalog(array("id" => $info["category_id"]), "", 1);
        if ($info["cateinfo"]["pid"] != 0) {
            $info["cateinfo"]["p"] = $this->getCatalog(array("id" => $info["pid"]), "", 1);
        }
        $this->assign('info', $info);
        $this->meta_title = $info["title"];
        if ($info["meta_title"]) {
            $this->meta_title = $info["meta_title"];
        }
        $this->meta_keyword = $info["title"];
        if ($info['meta_keyword']) {
            $this->meta_keyword = $info['meta_keyword'];
        }
        $this->meta_description = "这是" . $info["title"] . "的详细介绍页面,购买与咨询请联系商城客服.";
        if ($info['meta_description']) {
            $this->meta_description = $info['meta_description'];
        }
        
        $share_arr['meta_title'] = $this->meta_title;
        $share_arr['meta_description'] = $this->meta_description;
        $share_arr['is_login'] = is_login();
        $share_arr['imgUrl'] = $info['pics_img'][$info['cover_id']];
        $this->assign("share_arr",$share_arr);
        
//        dump($info);
        // 是否是促销商品
        $promotion = $this->goodsapi->getpromotion_info($id);
        $this->assign("promotion", $promotion);

        //获取热门产品
        $hotmap = array();
        $hotmap["domainid"] = $info["domainid"];
        $hotmap["ishot"] = 1;
        $hotmap['id'] = array("neq", $info["id"]);
        $hotgoodlist = $goodsapi->getDocument($hotmap, "id desc", 3);
        $this->assign("hotgoodlist", $hotgoodlist);
        //获取销量排行商品
        $salesmap = array();
//        $salesmap["domainid"] = $domainid;
        $salesmap["product_type"] = 1;
//        $salesmap["category_id"] = array("in", $cids);
        $salesgoodlist = $goodsapi->getDocument($salesmap, 'totalsales desc', 5);
        $this->assign("salesgoodlist", $salesgoodlist);

        //获取推荐产品
        $remap = array();
        $remap["domainid"] = $info["domainid"];
        $remap['id'] = array("neq", $info["id"]);
        $remap[0] = "position & 4 = 4"; //列表页推荐数字为2
        $tuijian = $goodsapi->getDocument($remap, 'update_time desc', 5);
        $this->assign("tuijian", $tuijian);
        // 看了又看
        $history_list = $goodsapi->get_history();
        $this->assign("history_list", $history_list);
//        dump($history_list);
        //用户评论
        $aboutcommentlist = R('Comment/ajaxlists', array('gid' => $info['id'], 'callback' => 'html'));
        $this->assign('aboutcommentlist', $aboutcommentlist);

        //购买记录
        $recordlist = R('Cart/ajaxlists', array('gid' => $info['id'], 'callback' => 'html'));
        $this->assign('recordlist', $recordlist);


        //设置最近浏览Cookie
        M("document")->where(array('id' => $id))->setInc('view');
        $uid = session('uid');
        $good_id = cookie('history' . $uid);
        $good_id[] = $id;
        Cookie('history' . $uid, array_unique($good_id));
        if (I("active") == "pomotion") {
            $this->display("goods.active");
        } else {
            $this->display('goods');
        }
    }

    /**
     * 店铺商品详情也
     */
    public function storedetail() {
        $id = I("id");
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
        $store_id = I('store_id');
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
        $this->assign("contentgoodattr", $info['attribute']);
        //获取面包屑导航信息
        $info["cateinfo"] = $this->getCatalog(array("id" => $info["category_id"]), "", 1);
        if ($info["cateinfo"]["pid"] != 0) {
            $info["cateinfo"]["p"] = $this->getCatalog(array("id" => $info["pid"]), "", 1);
        }
        $this->assign('info', $info);
        $this->assign('store_id', $info['store_id']);
        $this->meta_title = '门店精选-' . $info["title"];
        if ($info["meta_title"]) {
            $this->meta_title = $info["meta_title"];
        }
        $this->meta_keyword = $info["title"];
        if ($info['meta_keyword']) {
            $this->meta_keyword = $info['meta_keyword'];
        }
        $this->meta_description = "这是" . $info["title"] . "的详细介绍页面,购买与咨询请联系商城客服.";
        if ($info['meta_description']) {
            $this->meta_description = $info['meta_description'];
        }

        $share_arr['meta_title'] = $this->meta_title;
        $share_arr['meta_description'] = $this->meta_description;
        $share_arr['is_login'] = is_login();
        $share_arr['imgUrl'] = $info['pics_img'][$info['cover_id']];
        $this->assign("share_arr",$share_arr);
        // 是否是促销商品
//        $promotion = $this->getpromotion_info($id);
//        $this->assign("promotion", $promotion);
        //获取热门产品
        $hotmap = array();
        $hotmap["domainid"] = $info["domainid"];
        $hotmap["ishot"] = 1;
        $hotmap["product_type"] = $product_type;
        $hotmap['id'] = array("neq", $info["id"]);
        $hotgoodlist = $goodsapi->getDocument($hotmap, "id desc", 3);
        $this->assign("hotgoodlist", $hotgoodlist);
        //获取销量排行商品
        $salesmap = array();
//        $salesmap["domainid"] = $domainid;
        $salesmap["product_type"] = $product_type;
//        $salesmap["category_id"] = array("in", $cids);
        $salesgoodlist = $goodsapi->getDocument($salesmap, 'totalsales desc', 5);
        $this->assign("salesgoodlist", $salesgoodlist);

        // 看了又看
        $history_list = $goodsapi->get_history();
        $this->assign("history_list", $history_list);
//        dump($history_list);
        //用户评论
        $aboutcommentlist = R('Comment/ajaxlists', array('gid' => $info['id'], 'callback' => 'html'));
        $this->assign('aboutcommentlist', $aboutcommentlist);

        //购买记录
        $recordlist = R('Cart/ajaxlists', array('gid' => $info['id'], 'callback' => 'html'));
        $this->assign('recordlist', $recordlist);

        /* 更新浏览数 */
        M("document")->where(array('id' => $id))->setInc('view');
        //设置最近浏览Cookie
        $uid = session('uid');
        $good_id = cookie('history' . $uid);
        $good_id[] = $id;
        Cookie('history' . $uid, array_unique($good_id));
        if (I("store_id")) {
            $this->display("goods.store");
        } else {
            $this->redirect(U('Goods/lists'));
        }
    }

    /**
     * 商品评论
     */
    public function comments() {
        $this->display();
    }

    /**
     * 直邮商品
     */
    public function zhiyou() {

        $this->lists(1);
    }

    /**
     * 限时折扣
     */
    public function xianshi() {

        $_POST['r'] = I('r') ? : 20;
        $cateid = I("id", 0);
        $domainid = I("domainid", 0);
        $brandid = I('brand', 0);
        $seopid = 0;
        //获取所有频道
        $map = array();
        $map["id"] = array("gt", 1);
        $domainlist = $this->getDomain($map);
        $this->assign("domainlist", $domainlist);
        $this->assign("domainid", $domainid);
        // 获取当前频道
        $subdomain = M("subdomain")->where(array('id' => $domainid))->find();
        $this->assign("subdomain", $subdomain);
        //调用品牌
        $map = array();
        if ($domainid)
            $map["domainid"] = $domainid;

        $brandlist = $this->getBrand($map);
        $this->assign('brandlist', $brandlist);
        $this->assign('brandid', $brandid);
        $this->assign('page', I('p'));

        //获取商品类别及子分类
        $map = array();
        $map["ismenu"] = 2;
        if ($domainid)
            $map["domainid"] = $domainid;
        $categorytop = $this->getCategory($map);
        $this->assign("categorysub", $categorytop);
        $map = array();
        if ($cateid) {
            //设置列表顶部分类
            $this->assign('categoryid', $cateid);
            $category = array();
            $category = $this->categoryinfo($cateid);
            $pid = $category["pid"];
            if ($category["pid"] == 0) {
                $pid = $category["id"];
                $map = array();
                $map["ismenu"] = 2;
                $map["pid"] = $category['id'];
                $currentcatlist = $this->getCatalog($map);
            } else {
                $seopid = $category["pid"];
                $map = array();
                $map["ismenu"] = 2;
                $map["pid"] = $category['pid'];
                $pcategory = $this->categoryinfo($category['pid']);
                $this->assign("pcategory", $pcategory);
                $currentcatlist = $this->getCatalog($map);
            }
            $this->assign("categorypid", $pid);
            $this->assign("currentcategory", $category);
        } else {
            $this->assign('categoryid', 0);
            $this->assign("categorypid", 0);
            $this->assign("currentcategory", array("id" => 0));
            $currentcatlist = array();
        }


//        dump($categorytop);
        //获取子级分类 ids
        if ($currentcatlist) {
            $cids = array();
            $cids[] = $id;
            foreach ($currentcatlist as $k => $vs) {
                $cids[] = $vs['id'];
            }
        }

        //处理排序
        $key = I('get.order', 1);
        $sort = I('get.sort', 0);
        $ssort = 'asc'; //默认排序
        if ($sort == 1) {
            $ssort = 'desc';
        } else {
            $ssort = 'asc';
        }
        if ($key) {
            if ($key == "1") {//最新
                $listsort = "id " . $ssort;
            } else if ($key == "2") {//销量
                $listsort = "sales " . $ssort;
            } else if ($key == "3") {//热门
                $listsort = "ishot " . $ssort;
            } else if ($key == "4") {//价格
                $listsort = "price " . $ssort;
            }
        }
        if ($ssort == "desc") {
            $see = 0;
            $sortvalue = 'up';
        } else {
            $see = 1;
            $sortvalue = 'down';
        }
        $keyword = I("get.keywords", "");
        $keyword = htmlspecialchars($keyword);
        $minganci = C("MGSEARCH");
        $minganciarr = explode(",", $minganci);
        $keyword = str_replace($minganciarr, "", $keyword);
        if ($keyword) {
            $map["title"] = array("like", "%" . $keyword . "%");
            $this->assign("keywords", $keyword);
        }
        $stringcx = 'domainid=' . $domainid . '&id=' . $cateid . '&brand=' . $brandid . '&keywords=' . $keyword;

        $this->assign('stringcx', $stringcx);
        $this->assign('see', $see);
        $this->assign('order', $key);
        $this->assign('value', $sortvalue);

        $where['status'] = 1;
        $where['end_time'] = array('gt', NOW_TIME);
        $xianshi = M("p_xianshi")->where($where)->limit(1)->find();
        if (empty($xianshi)) {
            $this->error('活动已经结束，尽情期待...', U("index/index"));
        }
        $ptime['day'] = date("d", $xianshi['end_time'] - NOW_TIME);
        $ptime['hour'] = date("H", $xianshi['end_time'] - NOW_TIME);
        $ptime['point'] = date("i", $xianshi['end_time'] - NOW_TIME);
        $ptime['times'] = date("s", $xianshi['end_time'] - NOW_TIME);

        $this->assign("ptime", $ptime);
        $map['xianshi_id'] = $xianshi['xianshi_id'];
        I('domainid') && $map['domainid'] = I('domainid');
//        $xianshi_goods = M("p_xianshi_goods")->where($map)->select();
        $xianshi_model = M("p_xianshi_goods");
        $xianshi_goods = $this->_lists($xianshi_model, $map, 1, array(), "xianshi_goods_id");
        foreach ($xianshi_goods as $k => $v) {
            $xianshi_goods[$k] = $xianshi_model->where(array("xianshi_goods_id" => $v["xianshi_goods_id"]))->find();
            $xianshi_goods[$k]['goods_image'] = M("document")->where(array('id'=>$xianshi_goods[$k]['goods_id']))->getField('cover_id');
        }
        $empty = '没有活动商品';
        $this->assign('empty', $empty);
        unset($xianshi_goods['totalPages']);
        $this->assign("xianshi_goods_list", $xianshi_goods);

//        dump($xianshi_goods);
        $this->assign("xianshi", $xianshi);
        $indexapi = new \Api\Api\IndexApi();
        $newgoodslist = $indexapi->new_goods();
        $this->assign('newgoodslist', $newgoodslist);
//        dump($xianshi_goods);
        $this->display('goods.xianshi');
    }

    /*
     * 列表页根据分类及 
     * return array 
     */

    private function getCategoodlist($map = array(), $order = "id desc") {
        //$map["domainid"]=cookie("current_domainid");
        $map["pid"] = 0;
        $map["display"] = 1;
        $map['status'] = 1;
        $map["model_id"] = 5;
        $map["issales"] = 1;
        $documentViewModel = D("DocumentView");

        $lists = $this->_lists($documentViewModel, $map, $order, array(), "id");
        $collection = array();
        if (session('user_auth')) {//判断收藏
            $uid = session('user_auth,uid');
            $collection = F('collection' . $uid);
        }
        $totalPages = $lists['totalPages'];
        unset($lists['totalpages']);
        foreach ($lists as $k => $v) {
            $lists[$k] = $this->getDocument(array("id" => $v["id"]), "id desc", 1);
        }
        $lists['totalPages'] = $totalPages;
        return $lists;
    }

    public function search() {
        $uid = session('uid');
        $historykeywords = cookie('historyWord' . $uid);
        $this->assign('historykeywords', $historykeywords);
        $hotSearch = $this->getHotsearch();
        $this->assign('hotSearch', $hotSearch);
        $this->display();
    }

}
