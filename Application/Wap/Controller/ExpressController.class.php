<?php

/**
 * 快件管理
 */

namespace Web\Controller;

use Web\Controller\HomeController;

class ExpressController extends HomeController {

    private $_type = array(1 => '小', 2 => '中', 3 => '大', 4 => '超大');
    private $_status = array(
        array('k' => 0, 'val' => '停用'),
        array('k' => 1, 'val' => '启用'),
        array('k' => 2, 'val' => '使用中'),
        array('k' => 3, 'val' => '空闲中'),);
    private $_status_val = array(0 => '停用', 1 => '启用', 2 => '使用中', 3 => '空闲中');

    protected function _initialize() {
        parent::_initialize();
    }

    /**
     * 快件管理首页
     */
    public function index() {

        $uid = is_login();

        $count = M("express_cabint")->where("member_id='$uid'")->count();
        $this->assign('count', $count);
        $this->meta_title = "快件管理";
        $this->display();
    }

    /**
     * 货架管理
     */
    public function express() {
        $uid = is_login();
        $map['member_id'] = $uid;
        $express = D("ExpressCabint");
        $lists = $express->lists($map);

//        dump($this->_status);
        $this->assign('type_list', $this->_type);   // 可用型号
        $this->assign('status_list', $this->_status); // 可用状态
        $this->assign('lists', $lists);
        $this->meta_title = "货架管理";
        $this->display();
    }

    /**
     * 编辑货架
     */
    public function edit_express() {
        $id = I('id', 0); // 如果为空 则新建 否则编辑 
        $express = D("ExpressCabint");
        $info = $express->info($id);
        if (empty($info)) {
            $info['status'] = -1;
        }
        $this->assign('expressinfo', $info);
        $this->assign('type_list', $this->_type);   // 可用型号
        $this->assign('status_list', $this->_status); // 可用状态
        $re = $this->fetch('Express/express_edit');
        if ($re) {
            $this->success($re);
        }
        exit;
    }

    /**
     * 所有货架格子列表
     */
    public function express_grid_list() {
        $page_size = $REQUEST['r'] = 10;
        $pages = I('p') ? : 1;
        $where = array();
        $_list = M('express_cabint')->alias('c')->field('g.*,c.name express_name,c.alias express_alias, c.type express_type,c.id express_id')
                        ->join('RIGHT JOIN __EXPRESS_CABINT_GRID__  g ON c.id = g.parent_id')->where($where)
                        ->page("$pages,$page_size")->select();

        $total = M('express_cabint')->alias('c')->field('g.*,c.name express_name,c.alias express_alias, c.type express_type,c.id express_id')->
                        join('RIGHT JOIN __EXPRESS_CABINT_GRID__  g ON c.id = g.parent_id')->where($where)->count();

        if (isset($REQUEST['r'])) {
            $listRows = (int) $REQUEST['r'];
        } else {
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        foreach ($_list as $key => $value) {
            $_list[$key]['type_txt'] = $this->_type[$value['type']];
            $_list[$key]['status_txt'] = $this->_status_val[$value['status']];
        }
        $page->rollPage = 5;
        $p = $page->new_pc_page();
//        $p =$page->frontshow(); 
        $this->assign('_page', $p ? $p : '');
        $this->assign('lists', $_list);
        $this->display();
    }

    /**
     * 编辑货架格子
     */
    public function express_grid() {
        $uid = is_login();
        $map['member_id'] = $uid;
        $express = D("ExpressCabint");
        $expresslist = $express->lists($map);
        $this->assign('express_list', $expresslist);
        $this->assign('type_list', $this->_type);   // 可用型号
        $this->assign('status_list', $this->_status); // 可用状态
        $re = $this->fetch('Express/express_grid');
        $this->success($re);
    }

//    货架记录
    public function express_record() {

        $this->display();
    }

    /**
     * 货架使用明细
     */
    public function express_log() {
        $page_size = $REQUEST['r'] = 10;
        $pages = I('p') ? : 1;
        $where = array();
        $lists = M('express_cabint_grid_log')->alias('l')->field('l.*,g.grid_name,g.alias grid_alias, g.type grid_type,g.id grids_id')
                        ->join('LEFT JOIN __EXPRESS_CABINT_GRID__  g ON l.grid_id = g.id')->where($where)
                        ->page("$pages,$page_size")->select();
        $total = M('express_cabint_grid_log')->alias('l')->field('l.*,g.grid_name,g.alias grid_alias, g.type grid_type,g.id grids_id')
                        ->join('LEFT JOIN __EXPRESS_CABINT_GRID__  g ON l.grid_id = g.id')->where($where)>count();

        if (isset($REQUEST['r'])) {
            $listRows = (int) $REQUEST['r'];
        } else {
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        foreach ($lists as $key => $value) {
            $lists[$key]['type_txt'] = $this->_type[$value['grid_type']];
            $lists[$key]['status_txt'] = $this->_status_val[$value['status']];
        }
        $page->rollPage = 5;
        $p = $page->new_pc_page();
//        $p =$page->frontshow(); 
        $this->assign('_page', $p ? $p : '');
        $this->assign('lists', $lists);
        $this->display();
    }

    /**
     * 新增货架
     */
    public function add() {
        $cabint = D('ExpressCabint');
        if (IS_POST) { //提交表单
            $_POST['member_id'] = is_login();
            if (false !== $cabint->update()) {
                $this->success('新增成功！', U('express/express'));
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

    /**
     * 新建货架格子
     */
    public function gridadd() {
        $model_grid = D("ExpressCabintGrid");
        if (IS_POST) {
            $grid_count = I('grid_count');
            $res = 0;
            unset($_POST['grid_count']);
            if ($grid_count > 1) {
                for ($i = 1; $i <= $grid_count; $i++) {
                    $temp = sprintf("%03d", $i);
                    $_POST['alias'] = I('parent_id') . I('alias') . $temp;
                    $_POST['grid_name'] .=$temp;
                    $res = $model_grid->update();
                    $res++;
                    $temp = '';
                }
            } else {
                $_POST['alias'] = I('parent_id') . I('alias');
                $_POST['grid_name'] .= sprintf("%02d", $i);
                $res = $model_grid->update();
            }
            if ($res) {
                $this->success('新增成功！', U('express/express'));
            } else {
                $error = $model_grid->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $this->error('参数错误');
        }
    }

}
