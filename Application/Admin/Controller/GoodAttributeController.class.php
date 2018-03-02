<?php

namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page;
/**
 * 属性控制器
 * @author wangcheng
 */
class GoodAttributeController extends AdminController {

    public function _initialize() {
        $this->getMenu(2);
        parent::_initialize();
    }

    /**
     * 属性列表
     * @author wangcheng
     */
    public function index() {
//        $this->getMenu(2);
        //获取所有频道
        $channellist = M('Subdomain')->where("status >=1")->field("id,name")->select();
        $channelbrandlist = M('SubdomainBrand')->where("status >=1")->field("id,name,domainid")->select();
        foreach ($channellist as $k => &$v) {
            $v["isdomain"] = true;
            $att = M('good_attribute')->where("status >=0 and brandid=0 and domainid=" . $v["id"])->field("id,name,status")->select();
            $v["_"] = $att;
            foreach ($channelbrandlist as $k1 => $v1) {
                $v1["isbrand"] = true;
                $att = M('good_attribute')->where("status >=0 and brandid=" . $v1["id"] . " and domainid=" . $v["id"])->field("id,name,status")->select();
                $v1["_"] = $att;
                if ($v["id"] == $v1["domainid"]) {
                    $v["_"][] = $v1;
                }
            }
        }

        //// 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('tree', $channellist);
        $this->meta_title = '属性列表';
        $this->display();
    }

    /**
     * 显示左边菜单，进行权限控制
     * @author huajie <banhuajie@163.com>
     */
    protected function getMenu($ismenu = null){
        //获取动态分类
        $cate_auth  =   AuthGroupModel::getAuthCategories(UID); //获取当前用户所有的内容权限节点
        $cate_auth  =   $cate_auth == null ? array() : $cate_auth;
	$channel=   M('Subdomain')->where(array('status'=>1,'id'=>array('gt',1)))->field('id,name')->select();
	$cate       =   M('Category')->where(array('status'=>1,'ismenu'=>$ismenu))->field('id,domainid,title,pid,allow_publish')->order('pid,sort')->select();

        //没有权限的分类则不显示
        if(!IS_ROOT){
            foreach ($cate as $key=>$value){
                if(!in_array($value['id'], $cate_auth)){
                    unset($cate[$key]);
                }
            }
        }
        
        $cate           =   list_to_tree($cate);    //生成分类树
        //获取分类id
        $cate_id        =   I('param.cate_id');
        $this->cate_id  =   $cate_id;

        //是否展开分类
        $hide_cate = false;
        if(ACTION_NAME != 'recycle' && ACTION_NAME != 'draftbox' && ACTION_NAME != 'mydocument'){
            $hide_cate  =   true;
        }
	    $domainid = I("domainid");
        //生成每个分类的url
        foreach ($cate as $key=>&$value){
            $value['url']   =   'Goods/index?domainid='.$value['domainid'].'&cate_id='.$value['id'];
            if($cate_id == $value['id'] && $hide_cate){
                $value['current'] = true;
            }else{
                $value['current'] = false;
            }
            if(!empty($value['_child'])){
                $is_child = false;
                foreach ($value['_child'] as $ka=>&$va){
                    $va['url']      =   'Goods/index?domainid='.$va['domainid'].'&cate_id='.$va['id'];
                    if(!empty($va['_child'])){
                        foreach ($va['_child'] as $k=>&$v){
                            $v['url']   =   'Goods/index?domainid='.$v['domainid'].'&cate_id='.$v['id'];
                            $v['pid']   =   $va['id'];
                            $is_child = $v['id'] == $cate_id ? true : false;
                        }
                    }
                    //展开子分类的父分类
                    if($va['id'] == $cate_id || $is_child){
                        $is_child = false;
                        if($hide_cate){
                            $value['current']   =   true;
                            $va['current']      =   true;
                        }else{
                            $value['current']   =   false;
                            $va['current']      =   false;
                        }
                    }else{
                        $va['current']      =   false;
                    }
                }
            }
            foreach($channel as $ck=> &$cv){ 
                if($cv["id"]==$value["domainid"]){
                    //$cv['url']   =   'Goods/index?domainid='.$cv['id'];
                    if($value['current'] || $cv["id"]==$domainid ){
                    $cv["current"]=1;
                    } 
                    $cv["goodcat"][]=$value; 
                }
                //添加频道地址
                if(!isset($cv['url'])){
                    $cv['url']   =   'Goods/index?domainid='.$cv['id'];
                }
            }
        }

        $this->assign('nodes',      $channel);
        $this->assign('cate_id',    $this->cate_id);

        //商品搭配中的频道
        $pgdomainid = I("pgdomainid");
	    foreach($pgroupchannel as $ck=> &$cv){ 
		    $cv['url']   =   'ProductsGroup/index?pgdomainid='.$cv['id'];
		    if( $cv["id"]==$pgdomainid ){
			$cv["current"]=1;
		    }
	    }
//        $this->assign('productsgroupmenus',$pgroupchannel);

        //获取面包屑信息
        $nav = get_parent_category($cate_id);
        $this->assign('rightNav',   $nav);

        //获取回收站权限
        $this->assign('show_recycle', IS_ROOT || $this->checkRule('Admin/article/recycle'));
        //获取商品评论权限
        $this->assign('show_comment', IS_ROOT || $this->checkRule('Admin/Comment/index'));
        //获取商品搭配权限
//        $this->assign('show_productsgroup', IS_ROOT || $this->checkRule('Admin/ProductsGroup/index'));
        //获取草稿箱权限
        $this->assign('show_draftbox', C('OPEN_DRAFTBOX'));
        //获取审核列表权限
        $this->assign('show_examine', IS_ROOT || $this->checkRule('Admin/article/examine'));
    }
    function _after_insert($id) {
        $sub = I("sub");
        $types = I("types");
        if ($id && $sub) {
            $arr = explode("\n", $sub);
            $data = array();
            $time = time();
            foreach ($arr as $k => $v) {
                if ($v) {
                    $arr = array();
                    $arr["attributeid"] = $id;
                    $arr["name"] = $v;
                    $arr["create_time"] = $arr["update_time"] = $time;
                    $data[] = $arr;
                }
            }
            foreach ($data as $key => $value) {
                $re = M("GoodAttributeSub")->add($value);
            }
//            if($data){
//                $re = M("GoodAttributeSub")->addAll($data);
//            }
        }
    }

    /* 新增 */

    public function _before_add() {
        if (IS_POST) {
            
        } else {
            $domainid = I("get.domainid");
            $brandid = I("get.brandid");
            if (!$domainid)
                $this->error('请选择频道');
            $this->assign('domainid', $domainid);
            $this->assign('brandid', $brandid);
            $this->commondata();
            $this->meta_title = '新增商品属性';
        }
    }

    /* 编辑 */

    public function _after_edit(&$info) {
        if (IS_POST) {
            $sub = I("sub");
            if ($sub) {
                //更新或删除
                $time = time();
                foreach ($sub as $k => $v) {
                    $d = array();
                    if ($v) {
                        $d["update_time"] = $time;
                        $d["name"] = $v;
                        $d["id"] = $k;
                    } else {
                        $d["update_time"] = $time;
                        $d["status"] = 0;
                        $d["id"] = $k;
                    }
                    M("GoodAttributeSub")->save($d);
                }
            }
            $newsub = I("newsub");
            if ($newsub) {
                //新增
//                $newsub = I("newsub");
                $data = array();
                if ($newsub) {
                    foreach ($newsub as $k => $v) {
                        if ($v) {
                            $arr = array();
                            $arr["attributeid"] = $info["id"];
                            $arr["name"] = $v;
                            $arr["status"] = 1;
                            $arr["create_time"] = $arr["update_time"] = NOW_TIME;
                            $data[] = $arr;
                        }
                    }
                    foreach ($data as $key => $value) {
                        $re = M("GoodAttributeSub")->add($value);
                    }
//                    if($data){
//                        $re = M("GoodAttributeSub")->addAll($data);
//                    }
                }
            }
        } else {
            $map["attributeid"] = $info["id"];
            $map["status"] = 1;
            $sublist = M("GoodAttributeSub")->where($map)->select();
            $this->assign("sublist", $sublist);

            $this->commondata();
        }
    }

    /* 新增分类 */

    private function commondata() {
        //获取所有频道
        $channellist = M('Subdomain')->where("status >=1")->getField("id,name");
        $this->assign("channellist", $channellist);
        //获取所有品牌
        $channelbrandlist = M('SubdomainBrand')->where("status >=1")->getField("id,name");
        $this->assign("channelbrandlist", $channelbrandlist);
        $this->meta_title = '新增商品属性';
    }

    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function tree($tree = null) {
        $this->assign('tree', $tree);
        $this->display('tree');
    }

}
