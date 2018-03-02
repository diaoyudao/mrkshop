<?php

/**
 * @desc PC端总控制器
 * @author Caokoo
 */

namespace Wap\Controller;

use Core\Controller\CoreController;

class HomeController extends CoreController {

    protected $cartItems;
    private $_menus;
    protected $_customerInfo;

    /* 空操作，用于输出404页面 */

    public function _empty() {
        $this->redirect(U('Index/index'));
    }

    protected function _initialize() {
        parent::_initialize();
        //设置图片路径
        define("__PICURL__", __ROOT__ . "/Uploads/Picture/");
        // 设置上一步的地址
        Cookie('__referer__', $_SERVER['HTTP_REFERER']);
        Cookie('__forward__', $_SERVER['HTTP_REFERER']);
//        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
        if (!C('WEB_SITE_CLOSE')) {
            $this->show('站点已经关闭，请稍后访问~');exit;
            //$this->error('站点已经关闭，请稍后访问~');
        }


//        $denyControllers = array('order','address');
//        $pattern = '/'.implode('|', $denyControllers).'/i';
//        if(preg_match($pattern, CONTROLLER_NAME)){
//            if(!$this->customerId){
//                $this->redirect(url('member/login'));
//            }
//        }
//        $this->_initializeToCart();
        // 分类
        $category = $this->categorylist();
        $this->_helplist();
        $hotSearch = $this->getHotsearch();
        $cart_list = $this->get_cart_list();
//        dump($cart_list);
        $this->assign('cart_list', $cart_list); // 购物车列表
        $this->assign('hotSearch', $hotSearch);
        $this->assign('category_list', $category);
        $menus = $this->_initializeMenus(1);
        $this->assign('_menus', $menus);
        
        $share_arr['meta_title'] = $this->meta_title;
        $share_arr['meta_description'] = $this->meta_description;
        $share_arr['is_login'] = is_login();
        $share_arr['imgUrl'] = '';
        $this->assign("share_arr",$share_arr);
    }

    protected function _initializeToCart($flag = false) {
        if (!$this->cartItems || $flag) {
            $parameters['m'] = 'cart';
            $parameters['a'] = 'get_cart_items';
            $response = $this->query($parameters);
            $cartItems = array();
            if ($response['result'] == 'success' && $response['code'] == '0x0000') {
                $cartItems = $response['info'];
            }
            $cartItems['cartmum'] = $cartItems['cart_num'];
            $_carts = array();
            foreach ($cartItems['items'] as $key => $value) {
                $_carts[$value['cart_id']] = $value;
            }
            $cartItems['items'] = $_carts;
            $this->cartItems = $cartItems;
        }
        $this->assign('cart_items', $this->cartItems);
    }

    /**
     * 初始化导航菜单
     */
    protected function _initializeMenus($model = 1) {
//        $this->_menus = tycache('index_menus');
//        if (!$this->_menus) {
        $menus = $this->getNav(array('types' => 0, 'domainid' => $model));
//            tycache('index_menus', $menus);
//            $this->_menus = $menus;
//        }
        return $menus;
//        $this->assign('_menus', $menus);
    }

    /**
     * 获取导航
     * @param array | array map  查询条件
     * @author wangcheng
     * @return array|false
     */
    public function getNav($map = array()) {
        //$map["domainid"]=cookie("current_domainid");
        $map["status"] = 1;
        $lists = M("channel")->where($map)->field("id")->order("sort asc")->select();
        foreach ($lists as $k => $v) {
            $navdetail = array();
            if (!$navdetail) {
                $navdetail = M("channel")->find($v['id']);
                S('nav_' . $v['id'], $navdetail);
            }
            $lists[$k] = $navdetail;
        }
        return $lists;
    }

    function _helplist() {
        //查询所有帮助中心信息
        $cateid = "'shouhoufuwu','xinshourumen','guanyuwomen','zhifupeisongfangshi','bangzhuzhongxin'";
        $map = array();
        $map['ismenu'] = 3;
        $map['_string'] = "name in (" . $cateid . ")";
        $helpcatelist = $this->common_menulist($map, $cateid);
//        dump($helpcatelist);
        $this->assign("helpcatelist", $helpcatelist);
    }

    /*     * *
     * 无限极分类菜单调用
     */

    public function categorylist() {
        $field = 'id,name,pid,title,domainid,icon';
        $map["display"] = 1;
        $map["ismenu"] = 2;
        $categoryq = D('Category')->field($field)->order('sort asc')->where($map)->select();
        $catelist = $this->unlimitedForLevel($categoryq);
        //2015/8/18 16:44 有子分类的一级分类排在前面
        if (!empty($catelist)) {
            $hadchild = array();
            foreach ($catelist as $ckey => $catechild) {
                if (!empty($catechild['child'])) {
                    $hadchild[] = $catechild;
                    unset($catelist[$ckey]);
                }
            }
            $catelist = array_merge($hadchild, $catelist);
        }

        $domainmap = array(); //查询所有频道信息，优先级排序
        $domainmap["status"] = 1;
        $domainmap['id'] = array("NEQ", 1);
        $alldomaininfo = M("subdomain")->where($domainmap)->order("weight DESC")->getField("id,name,url,mark,meta_title,icon,keywords");
        $andomainid = array();
        foreach ($catelist as $kcate) {
            $andomainid[$kcate['domainid']][] = $kcate;
        }
        $recatelist = array();
        foreach ($alldomaininfo as $kdomainid => $kdomainidvalue) {
            if (isset($andomainid[$kdomainid])) {
                $kdomainidvalue['catelist'] = $andomainid[$kdomainid];
                $recatelist[] = $kdomainidvalue;
            }
        }
        return $recatelist;
    }

    /**
     * 无限制分类
     * @param type $cate
     * @param type $name
     * @param type $pid
     * @return type
     */
    public function unlimitedForLevel($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $key => $v) {
            //判断，如果$v['pid'] == $pid的则压入数组Child
            if ($v['pid'] == $pid) {
                //递归执行
                $v[$name] = self::unlimitedForLevel($cate, $name, $v['id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    /*     * *************************************************************
     * created date:2015/4/29 13:55
     * created author:sheshanhu
     * content:查询分类信息
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    public function common_menulist($map = array(), $categoryid) {
        $field = 'id,name,pid,title';
        //$map["domainid"] = cookie("current_domainid");
        $map["display"] = 1;
        $map["status"] = 1;
        if (!isset($map["ismenu"])) {
            $map["ismenu"] = 3;
        }
        $categoryq = D('Category')->field($field)->order('sort asc')->where($map)->select();

        if (!empty($categoryq)) {
            $levelid = array();
            foreach ($categoryq as $cate) {
                $levelid[] = $cate['id'];
            }
            $stringid = implode(',', $levelid);
            $map['_string'] = 'id in (' . $stringid . ') OR pid in (' . $stringid . ')';
            $categoryq = D('Category')->field($field)->order('sort asc')->where($map)->select();
        }

        $catelist = $this->unlimitedForLevel($categoryq);
        return $catelist;
    }

    /**
     * 短信验证码发送接口
     * @param int        $type    基本的查询条件
     * @author wangcheng
     * @return array|false
     * 返回true or false
     */
    public function sendPhoneMsg($type = 1, $tel = "") {
        if (!$tel) {
            return false;
        }
        $cmap["type"] = $type;
        $cmap["phone"] = $tel;
        $cmap["status"] = 1;
        $cmap["create_time"] = array("gt", time() - 60);
        $cCount = M("PhoneVerify")->where($cmap)->count();
        if ($cCount > 0) {
            return false;
        }
        $code = "";
        $arr = array();
        for ($i = 0; $i < 6; $i++) {
            $arr[$i] = rand(0, 9);
            $code .= (string) $arr[$i];
        }

        $appKey = C('message.AppKey');
        $appSecret = C('message.AppSecret');
        $nowTime = time();
        $checkSum = sha1($appSecret . $code . $nowTime);
        $url = "https://api.netease.im/sms/sendcode.action";
        $data['AppKey'] = $appKey;
        $data['CurTime'] = $nowTime;
        $data['CheckSum'] = $checkSum;
        $data['Nonce'] = $code;
        $data['Content-Type'] = 'application/x-www-form-urlencoded';
        $data['mobile'] = $tel;
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
                    'AppKey:' . $appKey,
                    'Nonce:' . $code,
                    'CurTime:' . $nowTime,
                    'CheckSum:' . $checkSum,
                ),
                'content' => 'mobile=' . $tel,
            ),
        );

        \Think\Log::write('post_data:\n' . var_export($options, true), 'DEBUG', '', C('LOG_PATH') . 'log_sms.log');
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $response = json_decode($response, true);
        \Think\Log::write('response_data2:\n' . var_export($response, true), 'DEBUG', '', C('LOG_PATH') . 'log_sms.log');

        if ($response['code'] != '200') {
            return false;
        }
        $map["type"] = $type;
        $map["phone"] = $tel;
        $result = M("PhoneVerify")->where($map)->delete();
        $data = array();
        $data["code"] = $response['obj'];
        $data["phone"] = $tel;
        $data["type"] = $type;
        $data["create_time"] = time();
        $data["update_time"] = time();
        $data["status"] = 1;
        $result = M("PhoneVerify")->add($data);
        if ($result) {
            return $data['code'];
        } else {
            return false;
        }
    }

    /*
     * 文章公共模块查询调用
     * return array
     */

    public function getArticle($map = array(), $order = "id desc", $limit = null) {
        $map['status'] = 1;
        $map['display'] = 1; //可见
        $map["ifcheck"] = 1; //已审核
        $map["model_id"] = 2; //文章模型
        if ($limit) {
            $lists = M("document")->where($map)->field("id")->order($order)->limit($limit)->select();
        } else {
            $lists = M("document")->where($map)->field("id")->order($order)->select();
        }
        $documentViewModel = D("ArticleView");
        foreach ($lists as $k => $v) {
            $article_detail = array();
            $article_detail = S('a_' . $v['id']);
            if (!$article_detail) {
                $article_detail = $documentViewModel->find($v['id']);
                if ($article_detail["cover_id"]) {
                    $arrmap["id"] = $article_detail["cover_id"];
                    $attinfo = array();
                    $attinfo = M("picture")->where($arrmap)->getField("id,path");
                    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                    foreach ($attinfo as $ckey => $cvalue) {
                        $attinfo[$ckey] = __PICURL__ . $article_detail['domainid'] . '/' . $cvalue;
                    }
                    $article_detail["pics_img"] = $attinfo;
                }
                //标签分析
                if (isset($article_detail['keywords']) && !empty($article_detail['keywords'])) {
                    $article_detail["tags"] = $this->analyist_tags($article_detail['keywords'], array(',', ' '));
                } else {
                    $article_detail["tags"] = array();
                }
                S('a_' . $v['id'], $article_detail);
            }
            $lists[$k] = $article_detail;
        }
        if ($limit == 1) {
            return $lists[0];
        }
        return $lists;
    }

    /*     * *************************************************************
     * created date:2015/4/29 13:55
     * created author:sheshanhu
     * content:标签内容分解
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    public function analyist_tags($keywords, $strt = array(" ")) {
        $return = array();
        if (!empty($keywords)) {
            $keywords2 = preg_replace("/[\s]+/is", " ", $keywords);
            foreach ($strt as $vb) {
                $keywords2 = str_replace($vb, " ", $keywords2);
            }
            $return = explode(" ", $keywords2);
        }
        /* if (count($return) > 1) {
          unset($return[array_search($keywords, $return) ]);
          } */
        return $return;
    }

    /*
     * 频道公共模块查询调用
     * return array
     */

    public function getDomain($map = array(), $order = "id desc", $limit = null) {
        $map['status'] = 1;
        if ($limit) {
            $lists = M("subdomain")->where($map)->field("id")->order($order)->limit($limit)->select();
        } else {
            $lists = M("subdomain")->where($map)->field("id")->order($order)->select();
        }
        $subdomain = M("subdomain");
        foreach ($lists as $k => $v) {
            $domain_detail = array();
            //$domain_detail = S('d_'.$v['id']);
            if (!$domain_detail) {
                $domain_detail = $subdomain->find($v['id']);
                S('d_' . $v['id'], $domain_detail);
            }
            $lists[$k] = $domain_detail;
        }
        if ($limit == 1) {
            return $lists ? $lists[0] : $lists;
        }
        return $lists;
    }

    /*     * 获取分类，并生成树形* */

    public function getCategory($map = array()) {
        $field = 'id,domainid,name,pid,title';
        //$map["domainid"]=cookie("current_domainid");
        $map["display"] = 1;
        $categoryq = D('Category')->field($field)->order('sort asc')->where($map)->select();
        $catelist = $this->unlimitedForLevel($categoryq);
        return $catelist;
    }

    /**
     * 分类查询 分类列表页面调用
     * @param int | string $id  查询分类名称或id
     * @author wangcheng
     * @return array|false
     */
    public function categoryinfo($id = 0) {
        /* 标识正确性检测 */
        $id = $id ? $id : I('get.id', 0);
        if (empty($id)) {
            $this->error('没有指定分类！');
        }
        /* 获取分类信息 */
        $category = D('Category')->info($id);
        if ($category && 1 == $category['status']) {
            switch ($category['display']) {
                case 0:
                    $this->error('该分类禁止显示！');
                    break;
                //TODO: 更多分类显示状态判断
                default:
                    return $category;
            }
        } else {
            $this->error('分类不存在或被禁用！');
        }
    }

    /*
     * 分类公共模块查询调用
     * return array
     */

    public function getCatalog($map = array(), $order = "id desc", $limit = null) {
        $map['status'] = 1;
        $map['display'] = 1; //可见
        $map["ifcheck"] = 1; //已审核
        if ($limit) {
            $lists = M("category")->where($map)->field("id")->order($order)->limit($limit)->select();
        } else {
            $lists = M("category")->where($map)->field("id")->order($order)->select();
        }
        $category = D("category");
        foreach ($lists as $k => $v) {
            $category_detail = array();
            $category_detail = S('c_' . $v['id']);
            if (!$category_detail) {
                $category_detail = $category->find($v['id']);
                S('c_' . $v['id'], $category_detail);
            }
            $lists[$k] = $category_detail;
        }
        if ($limit == 1) {
            return $lists ? $lists[0] : $lists;
        }
        return $lists;
    }

    /**
     * 获取所有品牌
     * @param array | array map  查询条件
     * @author wangcheng
     * @return array|false
     */
    public function getBrand($map = array(), $order = "id desc", $limit = null) {
        $map["status"] = 1;
        $brandModel = D("subdomain_brand");
        if (!empty($limit)) {
            $lists = $brandModel->where($map)->field("id")->order($order)->limit($limit)->select();
        } else {
            $lists = $brandModel->where($map)->field("id")->order($order)->select();
        }
        foreach ($lists as $k => $v) {
            $brand_detail = array();
            $brand_detail = S('pp_' . $v['id']);
            if (!$brand_detail) {
                $brand_detail = $brandModel->find($v['id']);
                if ($brand_detail["bgimg"] || $brand_detail['icon']) {
                    $arr = array();
                    if ($brand_detail['bgimg']) {
                        $arr[] = $brand_detail['bgimg'];
                    }
                    if ($brand_detail['icon']) {
                        $arr[] = $brand_detail['icon'];
                    }
                    $arrmap["id"] = array("in", $arr);
                    $attinfo = array();
                    $attinfo = M("picture")->where($arrmap)->getField("id,path");

                    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                    foreach ($attinfo as $ckey => $cvalue) {
                        $attinfo[$ckey] = __PICURL__ . $brand_detail['domainid'] . '/' . $cvalue;
                    }
                    $brand_detail["pics_img"] = $attinfo;
                }
                S('pp_' . $v['id'], $brand_detail);
            }
            $lists[$k] = $brand_detail;
        }
        if ($limit == 1) {
            return $lists[0];
        }
        return $lists;
    }

    /** 热门搜索热词* */
    public function getHotsearch() {
//        $str = M('config')->where('id="40"')->getField("value");
        $str = C('HOTSEARCHWORDS');
        $hotsearch = explode(",", $str);
        return $hotsearch;
    }

    /*
     * 产品公共模块查询调用
     * return array
     */

    public function getDocument($map = array(), $order = "id desc", $limit = null) {
        $map['status'] = 1;
        $map['display'] = 1;
        $map["ifcheck"] = 1;
        if (!isset($map["issales"])) {
            $map["issales"] = 1;
        } else {
            unset($map["issales"]);
        }
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
            $product_detail = S('p_' . $v['id']);
            if (!$product_detail) {
                $product_detail = $documentViewModel->find($v['id']);
                if ($product_detail["cover_id"] || $product_detail['pics']) {
                    $arr = array();
                    if ($product_detail['pics']) {
                        $arr = explode(",", $product_detail['pics']);
                    }
                    $arr[] = $product_detail["cover_id"];
                    $arrmap["id"] = array("in", $arr);

                    $attinfo = M("picture")->where($arrmap)->getField("id,path");


                   /*  if (isset($attinfo[$product_detail['cover_id']])) {
                        $url = __PICURL__ . $product_detail['domainid'] . '/' . $attinfo[$product_detail['cover_id']];
                        $newurl = $this->_image_thumb($url, 200, 200);
                        $attinfo[$product_detail['cover_id']] = $newurl;
                    } */

                    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                    foreach ($attinfo as $ckey => $cvalue) {
                        /* if ($ckey == $product_detail['cover_id'])
                            continue;
                        $attinfo[$ckey] = __PICURL__ . $product_detail['domainid'] . '/' . $cvalue; */
                    	$attinfo[$ckey] = __PICURL__ . $cvalue;
                    }
                    $product_detail["pics_img"] = $attinfo;
                }
                S('p_' . $v['id'], $product_detail);
            }
            $lists[$k] = $product_detail;
        }
        if ($limit == 1) {
            return $lists[0];
        }
        return $lists;
    }

    /*     * *************************************************************
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

    /*     * *************************************************************
     * created date:2015/6/26 10:38
     * created author:sheshanhu
     * content:图片缩略图生成及地址返回
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    function _image_thumb($url, $width, $height, $iscreted = false, $type = 'Picture', $rootpath = '') {
        // URL /yzjj/Uploads/Picture/2/2015-06-17/5581404d75cc4.jpg
        //对图片分析获取图片名称
        /* Array
          (
          [dirname] => /yzjj/Uploads/Picture/2/2015-06-17
          [basename] => 5581404d75cc4.jpg
          [extension] => jpg
          [filename] => 5581404d75cc4
          ) */

        $path_parts = pathinfo($url);
        $dirname = $path_parts['dirname'];
        $dirarray = explode('Uploads/' . $type . '/', $dirname);
        if (empty($rootpath)) {
            $uploadimage = C('PICTURE_UPLOAD'); //获取上传图片的文件夹地址
            $newdirname = $uploadimage['rootPath'] . $dirarray[1] . '/';
        } else {
            $newdirname = $rootpath . $dirarray[1] . '/';
        }
        $reurl = $newdirname . $path_parts['basename'];

        $newimagename = $path_parts['filename'] . '_' . $width . 'x' . $height . '.' . $path_parts['extension'];

        $newurl = $newdirname . $newimagename;

        if (is_file($reurl)) {
            //判断文件是否存在，如果存在则不生成，
            if (!is_file($newurl) || $iscreted) {
                $image = new \Think\Image();
                $image->open($reurl);
                // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
                $b = $image->thumb($width, $height)->save($newurl);
            }
        }

        //如果创建成功，图片存在则返回图片，如果不存在，则返回原地址。
        if (is_file($newurl)) {
            $newurl = $path_parts['dirname'] . '/' . $newimagename;
            return $newurl;
        } else {
            return $url;
        }
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
        $totalPages = $page->totalPages;
        $this->assign('_page', $p ? $p : '');

        $minpage = new \Think\Page($total, $listRows);
//        if ($total > $listRows) {
        $minpage->setConfig('theme', '%DOWN_PAGE% %UP_PAGE% %TOTAL_PAGE%');
//        }
        $minpage->rollPage = 5;
        $min_p = $minpage->min_page();

        $this->assign('_minpage', $min_p ? $min_p : '');

        $this->assign('_total', $total);
        $aPage = I("page", 1, "intval");
        $this->assign('p', $aPage);
        $options['limit'] = $page->firstRow . ',' . $page->listRows;
        $model->setProperty('options', $options);
        $list = $model->field($field)->select();
        $list['totalPages'] = $totalPages;
        return $list;
    }

    protected function _ajaxlists($model, $where = array(), $order = '', $base = array('status' => array('egt', 0)), $field = true, $callback) {
        $REQUEST = array_merge(I('post.'), I('get.'));
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
        $page = new \Think\AjaxPage($total, $listRows, $callback);
        if ($total > $listRows) {
            $page->setConfig('theme', '%first% %prePage% %upPage% %linkPage% %downPage% %nextPage% %end% %totalRow% %header% %nowPage%/%totalPage% 页 ');
        }
        $page->rollPage = 5;
        $p = $page->new_pc_page();
//        $p = $page->frontshow();
        $this->assign('_page', $p ? $p : '');
        $this->assign('_total', $total);
        $options['limit'] = $page->firstRow . ',' . $page->listRows;
        $model->setProperty('options', $options);
        $list = $model->field($field)->select();
        return $list;
    }

    /**
     * 获取历史浏览记录
     * @return array
     */
    public function get_history() {
        $history = cookie('history' . session('uid'));
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
                        //$attinfo[$ckey] = __PICURL__ . $val['domainid'] . '/' . $cvalue;
                    	$attinfo[$ckey] = __PICURL__ . $cvalue;
                    }
                    $history_list[$key]["pics_img"] = $attinfo;
                }
            }
        } else {
            $history_list = array();
        }
        return $history_list;
    }

    /*
     * * *************************************************************
     * created date:2015/5/21 11:41
     * created author:sheshanhu
     * content:对输入框中的特殊字符进行判断
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    public function int_num_validate($num) {
        $returnnum = $num;
        if (!preg_match("/[0-9]+/i", $num)) {
            $returnnum = 1;
        } elseif ($num <= 0) {
            $returnnum = 1;
        }
        return $returnnum;
    }

    /**
     * 获取购物车商品
     */
    public function get_cart_list() {
        if (is_login()) {
            $cart = D("Shopcart");
            $condition['is_system'] = 0;
            $usercart = $cart->getcart($condition);
            //　对订单中组合商品进行分析
            $productsgroupModel = D("ProductsGroup");
            foreach ($usercart as $key => &$value) {
                if ($value['type'] == 1) {
                    $groupproduct_detail = array();
                    $groupproduct_detail = S('pg_' . $value['goodid']);
                    if (empty($groupproduct_detail)) {
                        $groupproduct_detail = $productsgroupModel->find($value['goodid']);
                        //组合商品信息查询
                        $gmap = array();
                        $gmap['id'] = array('in', $groupproduct_detail['uniongood']);

                        $groupproduct_detail['goodsinfo'] = $this->getDocument($gmap);
                        $value['goodsinfo'] = $groupproduct_detail['goodsinfo'];
                        $value['price'] = $groupproduct_detail['price'];
                        S('pg_' . $value['goodid'], $groupproduct_detail);
                    } else {
                        $value['goodsinfo'] = $groupproduct_detail['goodsinfo'];
                        $value['price'] = $groupproduct_detail['price'];
                    }
                } else {
                    $domainid = M("document")->where(array('id' => $value["goodid"]))->getField("domainid");
                    $mark = M("subdomain")->where(array('id' => $domainid))->getField("mark");
                    $value["channelname"] = $mark;
                }
            }
            //登录用户的购物车在打开购物车时商品更新。
            $_SESSION['cart'] = $usercart;
            $count = $cart->getCntByuid(); /* 查询购物车中商品的种类 */
            $sum = $cart->getNumByuid(); /* 查询购物车中商品的个数 */
            $price = $cart->getPriceByuid(); /* 购物车中商品的总金额 */
        } else { // 未登录
            $uid = "";
            $count = $this->getCnt(); /* 查询购物车中商品的种类 */
            $sum = $this->getNum(); /* 查询购物车中商品的个数 */
            $price = $this->getPrice(); /* 购物车中商品的总金额 */
            $usercart = $_SESSION['cart'];
            //2015/6/19 16:51　对订单中组合商品进行分析
            $productsgroupModel = D("ProductsGroup");
            foreach ($usercart as $key => &$value) {
                if ($value['type'] == 1) {
                    $groupproduct_detail = array();
                    $groupproduct_detail = S('pg_' . $value['id']);
                    if (empty($groupproduct_detail)) {
                        $groupproduct_detail = $productsgroupModel->find($value['id']);

                        //组合商品信息查询
                        $gmap = array();
                        $gmap['id'] = array('in', $groupproduct_detail['uniongood']);
                        $groupproduct_detail['goodsinfo'] = $this->getDocument($gmap);
                        S('pg_' . $value['id'], $groupproduct_detail);
                    }
                    $value['goodsinfo'] = $groupproduct_detail['goodsinfo'];
                    $value['price'] = $groupproduct_detail['price'];
                } else {
                    $domainid = M("document")->where(array('id' => $value["id"]))->getField("domainid");
                    $mark = M("subdomain")->where(array('id' => $domainid))->getField("mark");
                    $value["channelname"] = $mark;
                }
                $value['goodid'] = $key;
            }
        }
        $cart_list['cart_list'] = $usercart;
        $cart_list['goods_type'] = $count;  //商品种类
        $cart_list['goods_count'] = $sum;   // 商品数量
        $cart_list['goods_total'] = $price; // 商品总金额
        return $cart_list;
    }

    /*
      购物车中商品的总金额
     */

    public function getPrice() {
        //数量为0，价钱为0
        if ($this->getCnt() == 0) {
            return 0;
        }
        $price = 0.00;
        $data = $_SESSION['cart'];
        foreach ($data as $item) {
            $price += $item['num'] * $item['price'];
        }
        return sprintf("%01.2f", $price);
    }

    /*
      获取单个商品
     */

    public function getItem($sort) {
        return $_SESSION['cart'][$sort];
    }

    /*
      查询购物车中商品的种类
     */

    public function getCnt() {
        return count($_SESSION['cart']);
    }

    /*
      查询购物车中商品的个数
     */
    /*
      查询购物车中商品的个数
     */

    public function getNum() {
        if ($this->getCnt() == 0) {
            //种数为0，个数也为0
            return 0;
        }
        $sum = 0;
        $data = $_SESSION['cart'];
        foreach ($data as $item) {
            $sum += $item['num'];
        }
        return $sum;
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
            } else if ($pro_info['status'] != 2) {
                return FALSE;
            }
            $pro_goods_info['end_time'] = $pro_info['end_time'];
            $pro_goods_info['xianshi_status'] = $pro_info['status'];
            $pro_goods_info['xianshi_explain'] = $pro_info['xianshi_explain'];
            return $pro_goods_info;
        }
        return FALSE;
    }

}
