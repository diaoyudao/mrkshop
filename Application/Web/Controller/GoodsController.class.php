<?php

/**
 * 商品列表/详情
 */

namespace Web\Controller;

use Web\Controller\HomeController;
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
        $_POST['r'] = I('r') ? : 20;//默认显示条数
        $cateid = I("id", 0);//分类ID
        $domainid = I("domainid", 0);//频道id
        $brandid = I('brand', 0);//品牌ID
        $pid = I('pid',0);//顶级分类ID
        $seopid = 0;
        $page = I('p',1);
        
        //获取所有频道
        $map = array();
        $map["id"] = array("gt", 1);
        $domainlist = $this->getDomain($map);
        $this->assign("domainlist", $domainlist);
        $this->assign("domainid", $domainid);
        $this->assign("categoryid", $cateid);
        $this->assign("pid", $pid);
        
        // 获取当前频道
        $subdomain = M("subdomain")->where(array('id' => $domainid))->find();
        $this->assign("subdomain", $subdomain);
        
        //调用品牌
        $brandlist = $this->getBrand();
        $this->assign('brandlist', $brandlist);
        $this->assign('brandid', $brandid);
        $this->assign('page', $page);

        //获取商品类别及子分类
        $map = array();
        $map["ismenu"] = 2;
        if ($domainid){
            $map["domainid"] = $domainid;
        }
        $categorytop = $this->getCategory($map);
        $this->assign("categorysub", $categorytop);
        
        $cate_map = array();
        $currentcatlist = array();
        
        if ($cateid) {
            //设置列表顶部分类
            $category = array();
            $category = $this->categoryinfo($cateid);
            
            if ($category["pid"] == 0) {
                $cate_map["ismenu"] = 2;
                $cate_map["pid"] = $category["pid"];
                $currentcatlist = $this->getCatalog($cate_map);
            } else {
                $seopid = $category["pid"];
                $cate_map["ismenu"] = 2;
                $cate_map["pid"] = $category['pid'];
                $pcategory = $this->categoryinfo($category['pid']);
                $this->assign("pcategory", $pcategory);
                $currentcatlist = $this->getCatalog($cate_map);
            }
            $this->assign("categorypid", $category["pid"]);
            $this->assign("currentcategory", $category);
        }

        //获取子级分类 ids
        if ($currentcatlist) {
            $cids = array();
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

        if ($keyword) {
            $uid = session('uid');
            $historykeywords = cookie('historyWord' . $uid);
            $historykeywords[] = $keyword;
            Cookie('historyWord' . $uid, array_unique($historykeywords));
//        dump(cookie('historyWord'.$uid));
        }



        //商品列表
        if ($is_zhiyou) {
            $map['product_type'] = 3;
        } else {
            $product_type = I('type') ? intval(I('type')) : 1;
            $map['product_type'] = $product_type;
        }
			
        unset($map['ismenu']);
        if($pid > 0){
        	$map['pid'] = $pid;
        }
        if($cateid > 0){
        	$map['category_id'] = $cateid;
        }
           
        //dump($map);
        $lists = $this->goodsapi->getCategoodlist($map, $listsort);
        $this->assign("lists", $lists);

        //获取热门产品
        $hotmap = array();
        //$hotmap["domainid"]= $domainid;
        $hotmap["ishot"] = 1;
        if (!empty($cids)) {
            $hotmap["category_id"] = array("in", $cids);
        }
        $hotgoodlist = $this->goodsapi->getDocument($hotmap, 'update_time desc', 5);
        $this->assign("hotgoodlist", $hotgoodlist);

        //获取销量排行商品
        $salesmap = array();
        //$hotmap["domainid"]= $domainid;
        if (!empty($cids)) {
            $salesmap["category_id"] = array("in", $cids);
        }
        
        $salesgoodlist = $this->goodsapi->getDocument($salesmap, 'totalsales desc', 5);
        $this->assign("salesgoodlist", $salesgoodlist);

        
        //猜你喜欢
        $goodsapi = new \Api\Api\GoodsApi();
        $con['position'] = array('in',array(8,9));
        $aboutproduct = $goodsapi->getDocument($con, 'id DESC', 7);
        $this->assign('aboutproduct', $aboutproduct);
//        dump($salesgoodlist);
        $this->assign('pagesizes', $_POST['r']);
        if (1 == C('IP_TONGJI')) {
            $id = "lists";
            $record = IpLookup("", 2, $id);
        }

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
        $_POST['r'] = I('r') ? : 20;
        $domainid = I('domainid', 0);
        $brandid = I('brand', 0);
        $category_id = I('id', 0);
        //获取所有频道
        $map = array();
        $map["id"] = array("gt", 1);
        $domainlist = $this->getDomain($map);
        $this->assign("domainlist", $domainlist);
        $this->assign("domainid", $domainid);
        $this->assign("categoryid", $cateid);
        $this->assign("pid", I('pid'));
        // 获取当前频道
        $subdomain = M("subdomain")->where(array('id' => $domainid))->find();
        $this->assign("subdomain", $subdomain);
        //调用品牌
        $map = array();
        if ($domainid) {
            $map["domainid"] = $domainid;
        }

        //获取商品类别及子分类
        $map = array();
        $map["ismenu"] = 2;
        if ($domainid) {
            $map["domainid"] = $domainid;
        }
        $categorytop = $this->getCategory($map);
        $this->assign("categorysub", $categorytop);
        $brandlist = $this->getBrand();
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

        $stringcx = 'domainid=' . $domainid . '&id=' . $category_id . '&brand=' . $brandid . '&keywords=' . $keyword;

        $this->assign('stringcx', $stringcx);
        $this->assign('see', $see);
        $this->assign('order', $key);
        $this->assign('value', $sortvalue);

        $store_id = session('memberinfo.member_agent_id');  // 店铺ID
        $condition['store_id'] = $store_id;
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
        $condition['status'] =1;
        $lists = $this->goodsapi->getStoreGoods_list($condition, $listsort);
//        dump($lists);
        $this->assign("lists", $lists['goodslist']);





        //获取热门产品
        $hotmap = array();
        //$hotmap["domainid"]= $domainid;
        $hotmap["ishot"] = 1;
        if (!empty($cids)) {
            $hotmap["category_id"] = array("in", $cids);
        }
        $hotgoodlist = $this->getDocument($hotmap, 'update_time desc', 5);
        $this->assign("hotgoodlist", $hotgoodlist);

        //猜你喜欢
        $goodsapi = new \Api\Api\GoodsApi();
        $con['position'] = array('in',array(8,9));
        $aboutproduct = $goodsapi->getDocument($con, 'id DESC', 7);
        $this->assign('aboutproduct', $aboutproduct);
        
        //获取销量排行商品
        $salesmap = array();
        //$hotmap["domainid"]= $domainid;
        if (!empty($cids)) {
            $salesmap["category_id"] = array("in", $cids);
        }
        $salesgoodlist = $this->getDocument($salesmap, 'totalsales desc', 5);
        $this->assign("salesgoodlist", $salesgoodlist);






        $this->assign('pagesizes', $_POST['r']);
        $this->display('lists.store');
    }

    /**
     * 商品详情页
     */
    public function detail() {
        $id = $goods_id = I("id");
        
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
//        dump($info);
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

        // 是否是促销商品
        $promotion = $goodsapi->getpromotion_info($id);
//        dump($promotion);
        $this->assign("promotion", $promotion);
        
//        dump($promotion);

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
//        $salesmap["category_id"] = array("in", $cids);
        $salesgoodlist = $goodsapi->getDocument($salesmap, 'totalsales desc', 5);
        $this->assign("salesgoodlist", $salesgoodlist);

        //获取推荐产品
        $remap = array();
        $remap["domainid"] = $info["domainid"];
        $remap['id'] = array("neq", $info["id"]);
//        $remap[0] = "position & 4 = 4"; //列表页推荐数字为2
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
        
         $map = array();
        $map['status'] = 3;
        $map["goodid"] = $info['id'];
        $goodsrecord = M("shoplist")->where($map)->count();
        $this->assign('recordcount', $goodsrecord);
        
        $goodscollect = M("favortable")->where(array('goodid'=>$info['id']))->count();
        $this->assign('goodscollect', $goodscollect);
        // 统计商品详情页
        if (1 == C('IP_TONGJI')) {
            $id = "detail";
            $record = IpLookup("", 3, $id);
        }

        //设置最近浏览Cookie
        M("document")->where(array('id' => $goods_id))->setInc('view');
        $uid = session('uid');
        $good_id = cookie('history_list' . $uid);
        $good_id[] = $goods_id;
        Cookie('history_list' . $uid, array_unique($good_id));
        if ($promotion) {
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
        if ($info['error']) {
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
         $map = array();
        $map['status'] = 3;
        $map["goodid"] = $info['id'];
        $goodsrecord = M("shoplist")->where($map)->count();
        $this->assign('recordcount', $goodsrecord);
        
        $goodscollect = M("favortable")->where(array('goodid'=>$info['id']))->count();
        $this->assign('goodscollect', $goodscollect);

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
        $this->assign("xianshi_goods_list", $xianshi_goods);
        $this->assign("xianshi", $xianshi);

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
        foreach ($lists as $k => $v) {
            $lists[$k] = $this->getDocument(array("id" => $v["id"]), "id desc", 1);
        }
        return $lists;
    }

}
