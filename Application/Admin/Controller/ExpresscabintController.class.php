<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 1010422715@qq.com All rights reserved.
// +----------------------------------------------------------------------
// | author 烟消云散 <1010422715@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Admin\Model\AuthGroupModel;
use Think\Page;

/**
 * 货架管理
 * 
 */
class ExpresscabintController extends AdminController {

    private $_type = array(1 => '小', 2 => '中', 3 => '大',4=>'超大');
    private $_status = array(1 => '启用',0 => '停用',2=>'使用中',3=>'空闲中');

    /**
     * 货架管理
     */
    public function index() {
        /* 查询条件初始化 */
        $map = array('status' => 1);
        if (IS_GET) {
            $title = trim(I('get.title'));
            $map['name'] = array('like', "%{$title}%");
            $list = D("ExpressCabint")->lists($map);
        } else {
            $list = $this->lists('ExpressCabint', $map, 'id desc');
        }

        $this->assign('list', $list);
        // 记录当前列表页的cookie
//        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->meta_title = '货架管理';
        $this->display();
    }

    /* 编辑货架 */

    public function edit($id = null) {
        $cabint = D('ExpressCabint');
        if (IS_POST) { //提交表单
            if (false !== $cabint->update()) {
                $this->success('编辑成功！', U('index'));
            } else {
                $error = $cabint->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $info = $id ? $cabint->info($id) : '';
            $this->assign('info', $info);
            $this->meta_title = '编辑货架';
            $this->display('add');
        }
    }

    /* 新增分类 */

    public function add() {
        $cabint = D('ExpressCabint');
        if (IS_POST) { //提交表单
            if (false !== $cabint->update()) {
                $this->success('新增成功！', U('index'));
            } else {
                $error = $cabint->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $this->assign('info', null);
            $this->meta_title = '新增货架';
            $this->display();
        }
    }

    public function del() {
        if (IS_POST) {
            $ids = I('post.id');
            $order = M("ExpressCabint");

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    $order->where("id='$id'")->delete();
                }
            }
            $this->success("删除成功！");
        } else {
            $id = I('get.id');
            $db = M("ExpressCabint");
            $status = $db->where("id='$id'")->delete();
            if ($status) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /**
     * 增加货柜格
     */
    public function gridadd() {
        $model_grid = D("ExpressCabintGrid");
        if (IS_POST) {
            if (false !== $model_grid->update()) {
                $this->success('新增成功！', U('index'));
            } else {
                $error = $model_grid->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cabint_id = I('get.id');
            $list = D("ExpressCabint")->lists();
            $this->assign('cabint_id', $cabint_id);
            $this->assign('list', $list);
            $this->assign('type', $this->_type);
            $this->assign('status', $this->_status);
            $this->display('gridadd');
        }
    }

    /**
     * 更新货柜格
     */
    public function gridedit() {
        $model_grid = D("ExpressCabintGrid");
        if (IS_POST) {
            if (false !== $model_grid->update()) {
                $this->success('编辑成功！', U('gridlist'.'?id='.I('parent_id')));
            } else {
                $error = $model_grid->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cabint_id = I('get.id');
            $list = D("ExpressCabint")->lists();
            $id = I('get.gridid');
            $info = $model_grid->info($id);
            $this->assign('info', $info);
            $this->assign('cabint_id', $cabint_id);
            $this->assign('list', $list);
            $this->assign('type', $this->_type);
            $this->assign('status', $this->_status);
            $this->display('gridadd');
        }
    }

    /**
     * 查看货柜格子
     */
    public function gridlist() {

        $model_grid = D("ExpressCabintGrid");
        $map['prent_id'] = I('get.id');
        $list = $model_grid->lists($map);
        
        $this->assign('list',$list);
        $this->display();
    }

}
