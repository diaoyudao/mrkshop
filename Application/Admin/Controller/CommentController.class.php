<?php

namespace Admin\Controller;

use Admin\Model\AuthGroupModel;
use Think\Page;

/**
 * 后台商品评论-问题控制器
 * @author wangcheng <253490851@qq.com>
 */
class CommentController extends AdminController {

    function index() {
        $map = array();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $model = D(CONTROLLER_NAME);
        $arr = array();
//        $map['status'] =1;
        if (!empty($model)) {
            $list = $this->lists($model, $map, 'id DESC');
            if (method_exists($this, '_after_list')) {
                $this->_after_list($list);
            }
            $this->assign('list', $list);
        }

        $this->display();
    }

    /**
     * 查看详情
     */
    public function detail() {
        if (IS_POST) {
            $id = I("id");
            $ifcheck = I('ifcheck');
            $res = M("comment")->where(array('id' => $id))->setField(array('ifcheck' => $ifcheck));
            if ($res) {
                $this->success('更新成功', U("Comment/index"));
            } else {
                $this->error('更新失败', U("Comment/index"));
            }
        } else {
            $id = I("id");
            $info = M("comment")->where(array('id' => $id))->find();
            if ($info['pics']) {
                if (strpos($info['pics'], ',')) {
                    $aa = explode(',', $info['pics']);
                    $bb = array();
                    foreach ($aa as $key => $val) {
                        $pics = M('picture')->where(array('id' => $val))->getField('path');
                        $bb[] = __PICURL__ . $pics;
                        $info['picss'] = $bb;
                    }
                } else {
                    $pics = M('picture')->where(array('id' => $info['pics']))->getField('path');
                    $info['picss'] = array(__PICURL__ . $pics);
                }
            }
            $this->assign("info", $info);
            $this->display();
        }
    }

    /**
     * 问题列表
     * @author wangcheng <253490851@qq.com>
     */
    public function _filter(&$map) {
        $map['status'] = array('gt', -1);
        $cate_id = I("cate_id");
        $domainid = I("domainid");
        if ($cate_id) {
            $map['category_id'] = $cate_id;
            //by wangcheng 循环找出子分类的id
            $cate_ids_array = get_stemma($cate_id, M("category"), 'id');
            if ($cate_ids_array) {
                $cate_ids = array();
                foreach ($cate_ids_array as $k => $v) {
                    $cate_ids[] = $v["id"];
                }
                $cate_ids[] = $cate_id;
                $map['category_id'] = array("in", $cate_ids);
            }
            $this->assign("cate_id", $cate_id);
        }
        if ($domainid) {
            $map['domainid'] = $domainid;
        }
        $map['pid'] = I('pid', 0);
        if ($map['pid']) { // 子文档列表忽略分类
            unset($map['category_id']);
        }
        $this->getMenu(2);
    }

    /**
     * 添加问题
     * @author wangcheng <253490851@qq.com>
     */
    public function _before_add() {
        if (IS_POST) {
            //记录行为 
        } else {
            $cate_id = I("cate_id");
            $info["category_id"] = $cate_id;
            $cateinfo = get_category($cate_id);
            $info["domainid"] = $cateinfo["domainid"];
            //$info["brandid"] = $cateinfo["brandid"]; 
            $this->assign('info', $info);
            //获取指定分类下的百科词条
            $map = array();
            $map["domainid"] = $info["domainid"];
            $map["status"] = 1;
            $tags = M("tags")->where($map)->getField("id,keywords");
            $this->assign('tags', $tags);

            //查询频道下的所有品牌
            $channelbrandlist = M('SubdomainBrand')->where("status >=1 and domainid=" . $info["domainid"])->getField("id,name");
            $this->assign("channelbrandlist", $channelbrandlist);
            $this->assign("brandid", 0);

            $this->getMenu(2);
            $this->meta_title = '新增商品评论';
        }
    }

    /**
     * 添加问题
     * @author wangcheng <253490851@qq.com>
     */
    public function _before_edit(&$info) {
        if (IS_POST) {
            
        } else {
            
        }
        $id = I('get.id', '');
        if (!empty($id)) {
            $Comment = M("Comment");
            $data = $Comment->find($id);

            //查询频道下的所有品牌
            $channelbrandlist = M('SubdomainBrand')->where("status >=1 and domainid=" . $data["domainid"])->getField("id,name");
            $this->assign("channelbrandlist", $channelbrandlist);
            $this->assign("brandid", $data["brandid"]);
        }
    }

    /**
     * 编辑问题
     * @author wangcheng <253490851@qq.com>
     */
    public function _after_edit(&$info) {
        if (IS_POST) {
            //记录行为--后期扩展
            action_log('update_comment', 'comment', $data['id'], UID);
            clearcache('pl_' . $info['id']);
        } else {
            //获取指定分类下的百科词条
            $map = array();
            $map["domainid"] = $info["domainid"];
            $map["status"] = 1;
            $tags = M("tags")->where($map)->getField("id,keywords");
            $this->assign('tags', $tags);
            $this->getMenu(2);
            $this->meta_title = '编辑问题';
        }
    }

    protected function getMenu($ismenu = null) {
        //获取动态分类
        $cate_auth = AuthGroupModel::getAuthCategories(UID); //获取当前用户所有的内容权限节点
        $cate_auth = $cate_auth == null ? array() : $cate_auth;
        $channel = M('Subdomain')->where(array('status' => 1, 'id' => array('gt', 1)))->field('id,name')->select();
        $cate = M('Category')->where(array('status' => 1, 'ismenu' => $ismenu))->field('id,domainid,title,pid,allow_publish')->order('pid,sort')->select();
        //没有权限的分类则不显示
        if (!IS_ROOT) {
            foreach ($cate as $key => $value) {
                if (!in_array($value['id'], $cate_auth)) {
                    unset($cate[$key]);
                }
            }
        }

        $cate = list_to_tree($cate);    //生成分类树
        //获取分类id
        $cate_id = I('param.cate_id');
        $this->cate_id = $cate_id;

        //是否展开分类
        $hide_cate = false;
        if (ACTION_NAME != 'recycle' && ACTION_NAME != 'draftbox' && ACTION_NAME != 'mydocument') {
            $hide_cate = true;
        }
        $domainid = I("domainid");
        //生成每个分类的url
        foreach ($cate as $key => &$value) {
            $value['url'] = 'Goods/index?domainid=' . $value['domainid'] . '&cate_id=' . $value['id'];
            if ($cate_id == $value['id'] && $hide_cate) {
                $value['current'] = true;
            } else {
                $value['current'] = false;
            }
            if (!empty($value['_child'])) {
                $is_child = false;
                foreach ($value['_child'] as $ka => &$va) {
                    $va['url'] = 'Goods/index?domainid=' . $va['domainid'] . '&cate_id=' . $va['id'];
                    if (!empty($va['_child'])) {
                        foreach ($va['_child'] as $k => &$v) {
                            $v['url'] = 'Goods/index?domainid=' . $v['domainid'] . '&cate_id=' . $v['id'];
                            $v['pid'] = $va['id'];
                            $is_child = $v['id'] == $cate_id ? true : false;
                        }
                    }
                    //展开子分类的父分类
                    if ($va['id'] == $cate_id || $is_child) {
                        $is_child = false;
                        if ($hide_cate) {
                            $value['current'] = true;
                            $va['current'] = true;
                        } else {
                            $value['current'] = false;
                            $va['current'] = false;
                        }
                    } else {
                        $va['current'] = false;
                    }
                }
            }
            foreach ($channel as $ck => &$cv) {
                if ($cv["id"] == $value["domainid"]) {
                    //$cv['url']   =   'Goods/index?domainid='.$cv['id'];
                    if ($value['current'] || $cv["id"] == $domainid) {
                        $cv["current"] = 1;
                    }
                    $cv["goodcat"][] = $value;
                }
                //添加频道地址
                if (!isset($cv['url'])) {
                    $cv['url'] = 'Goods/index?domainid=' . $cv['id'];
                }
            }
        }

        $this->assign('nodes', $channel);
        $this->assign('cate_id', $this->cate_id);

        //商品搭配中的频道
        $pgdomainid = I("pgdomainid");
        foreach ($pgroupchannel as $ck => &$cv) {
            $cv['url'] = 'ProductsGroup/index?pgdomainid=' . $cv['id'];
            if ($cv["id"] == $pgdomainid) {
                $cv["current"] = 1;
            }
        }
//        $this->assign('productsgroupmenus',$pgroupchannel);
        //获取面包屑信息
        $nav = get_parent_category($cate_id);
        $this->assign('rightNav', $nav);

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

    /**
     * 显示左边菜单，进行权限控制
     * @author huajie <banhuajie@163.com>
     */
    protected function getMenu22($ismenu = null) {
        //获取动态分类
        $cate_auth = AuthGroupModel::getAuthCategories(UID); //获取当前用户所有的内容权限节点
        $cate_auth = $cate_auth == null ? array() : $cate_auth;
        $channel = M('Subdomain')->where(array('status' => 1, 'id' => array('gt', 1)))->field('id,name')->select();
        $cate = M('Category')->where(array('status' => 1, 'ismenu' => $ismenu))->field('id,domainid,title,pid,allow_publish')->order('pid,sort')->select();

        //没有权限的分类则不显示
        if (!IS_ROOT) {
            foreach ($cate as $key => $value) {
                if (!in_array($value['id'], $cate_auth)) {
                    unset($cate[$key]);
                }
            }
        }

        $cate = list_to_tree($cate);    //生成分类树
        //获取分类id
        $cate_id = I('param.cate_id');
        $this->cate_id = $cate_id;

        //是否展开分类
        $hide_cate = false;
        if (ACTION_NAME != 'recycle' && ACTION_NAME != 'draftbox' && ACTION_NAME != 'mydocument') {
            $hide_cate = true;
        }
        $domainid = I("domainid");
        //生成每个分类的url
        foreach ($cate as $key => &$value) {
//            $value['url']   =   'Goods/index?cate_id='.$value['id'];
            $value['url'] = 'Goods/index?domainid=' . $value['domainid'] . '&cate_id=' . $value['id'];
            if ($cate_id == $value['id'] && $hide_cate) {
                $value['current'] = true;
            } else {
                $value['current'] = false;
            }
            if (!empty($value['_child'])) {
                $is_child = false;
                foreach ($value['_child'] as $ka => &$va) {
//                    $va['url']      =   'Goods/index?cate_id='.$va['id'];
//                    if(!empty($va['_child'])){
//                        foreach ($va['_child'] as $k=>&$v){
//                            $v['url']   =   'Goods/index?cate_id='.$v['id'];
//                            $v['pid']   =   $va['id'];
//                            $is_child = $v['id'] == $cate_id ? true : false;
//                        }
//                    }
                    $va['url'] = 'Goods/index?domainid=' . $va['domainid'] . '&cate_id=' . $va['id'];
                    if (!empty($va['_child'])) {
                        foreach ($va['_child'] as $k => &$v) {
                            $v['url'] = 'Goods/index?domainid=' . $v['domainid'] . '&cate_id=' . $v['id'];
                            $v['pid'] = $va['id'];
                            $is_child = $v['id'] == $cate_id ? true : false;
                        }
                    }
                    //展开子分类的父分类
                    if ($va['id'] == $cate_id || $is_child) {
                        $is_child = false;
                        if ($hide_cate) {
                            $value['current'] = true;
                            $va['current'] = true;
                        } else {
                            $value['current'] = false;
                            $va['current'] = false;
                        }
                    } else {
                        $va['current'] = false;
                    }
                }
            }
            foreach ($channel as $ck => &$cv) {
                if ($cv["id"] == $value["domainid"]) {
                    //$cv['url']   =   'Goods/index?domainid='.$cv['id'];
                    if ($value['current'] || $cv["id"] == $domainid) {
                        $cv["current"] = 1;
                    }
                    $cv["goodcat"][] = $value;
                }
                if (!isset($cv['url'])) {
                    $cv['url'] = 'Goods/index?domainid=' . $cv['id'];
                }
            }
        }
        $this->assign('nodes', $channel);
        $this->assign('cate_id', $this->cate_id);

        //商品搭配中的频道
        $pgdomainid = I("pgdomainid");
        foreach ($pgroupchannel as $ck => &$cv) {
            $cv['url'] = 'ProductsGroup/index?pgdomainid=' . $cv['id'];
            if ($cv["id"] == $pgdomainid) {
                $cv["current"] = 1;
            }
        }
        $this->assign('productsgroupmenus', $pgroupchannel);

        //获取面包屑信息
        $nav = get_parent_category($cate_id);
        $this->assign('rightNav', $nav);

        //获取回收站权限
        $this->assign('show_recycle', IS_ROOT || $this->checkRule('Admin/article/recycle'));
        //获取商品评论权限
        $this->assign('show_comment', IS_ROOT || $this->checkRule('Admin/Comment/index'));
        //获取商品搭配权限
        $this->assign('show_productsgroup', IS_ROOT || $this->checkRule('Admin/ProductsGroup/index'));
        //获取草稿箱权限
        $this->assign('show_draftbox', C('OPEN_DRAFTBOX'));
        //获取审核列表权限
        $this->assign('show_examine', IS_ROOT || $this->checkRule('Admin/article/examine'));
    }

    public function searchgoods() {
        $cate_id = I("cate_id");
        $info = get_category($cate_id);
        $keywords = I("keywords");
        if ($keywords && $info) {
            $map["status"] = $info["status"];
            $map["domainid"] = $info["domainid"];
            $map["title"] = array("like", '%' . $keywords . '%');
            $uniongood = M("document")->where($map)->field("id,title")->limit(0, 20)->order("id desc")->select();
            $this->success($uniongood);
        }
        $this->error(array());
    }

    /**
     * 审核问题
     * @author wangcheng <253490851@qq.com>
     */
    function audit() {
        $id = I('id', 0);
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $ifcheck = I('ifcheck', 0);
        $map = array('id' => array('in', $id));
        $map['status'] = 1;
        $model = M("Comment");
        $list = $model->where($map)->setField('ifcheck', $ifcheck);
        //获取商品ID
        $cgoods = $model->find($id);
        $goodid = $cgoods['goodid'];
        clearcache('pl_' . $info['id']);
        if ($list !== false) {
            if ($ifcheck == 1) {//审核通过，数量加1
                M('document')->where("id='$goodid'")->setInc('comment');
            } elseif ($ifcheck == 0) {
                M('document')->where("id='$goodid'")->setDec('comment');
            }
            $this->success('审核成功');
        } else {
            $this->error('审核失败！');
        }
    }

    /*     * *************************************************************
     * created date:2015/5/8 15:17 
     * created author:sheshanhu
     * content:删除用户提交的评论
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    public function del() {
        if (IS_GET) {
            $id = I('get.ids');
            $document = D('Comment');
            if ($document->where("id='$id'")->setField("status", -1)) {
                clearcache("pl_" . $ids);
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
        if (IS_POST) {
            $ids = I('post.id');
            $document = M("Comment");
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    $document->where("id='$id'")->setField("status", -1);
                }
            }
            $this->success("删除成功！");
        }
    }

    /*     * *************************************************************
     * created date:2015/5/8 15:17 
     * created author:sheshanhu
     * content:删除用户提交的评论
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

//    public function setStatus($model='Comment'){
//        return parent::setStatus('Comment');
//    }
    /**
     * 设置一条或者多条数据的状态
     */
    public function setStatus($Model = CONTROLLER_NAME) {

        $ids = I('request.ids');
        $ifcheck = I('request.ifcheck');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }

        $map['id'] = array('in', $ids);
        switch ($ifcheck) {
            case -1 :
                  $data = array('status' => $ifcheck);
//                $this->delete($Model, $map, array('success' => '删除成功', 'error' => '删除失败'));
                $this->editRow($Model, $data, $map, array('success' => '删除成功', 'error' => '删除失败'));
                break;
            case 0 :
//                $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                $data = array('ifcheck' => $ifcheck);
                $this->editRow($Model, $data, $map, array('success' => '禁用成功', 'error' => '禁用失败'));
                break;
            case 1 :
                $data = array('ifcheck' => $ifcheck);
                $this->editRow($Model, $data, $map, array('success' => '启用成功', 'error' => '启用失败'));
                //$this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 设置状态
     * @author wangcheng <253490851@qq.com>
     */
    function _before_setStatus() {
        $ids = I('request.ids');
        if ($ids) {
            clearcache($ids, 'pl_');
        }
    }

}
