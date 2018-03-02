<?php

/**
 * 建议投诉
 */

namespace Web\Controller;

use Web\Controller\HomeController;
use Api\Api\OrderApi;

class MessageController extends HomeController {

    private $refund_resion = array(
        1 => array('id' => 1, 'resion' => '不想要了'),
        2 => array('id' => 2, 'resion' => '商品已损坏'),
        3 => array('id' => 3, 'resion' => '购买其他商品'),
        4 => array('id' => 4, 'resion' => '价格太贵'),
    );

    protected function _initialize() {
        parent::_initialize();
         if (!$this->customerId) {
                $this->redirect(url('member/login'));
            }
        $refund_resion = $this->refund_resion;
        $this->assign('refund_resion', $refund_resion);
    }

    public function index() {
        $condition['uid'] = is_login();
        $lists = M("message")->where($condition)->order('id desc')->select();
        $this->assign('lists', $lists); // 赋值数据集
        $this->meta_title = "建议投诉";
        $this->display();
    }

    public function add() {
        if (!IS_POST) {
            $this->meta_title = '新增建议投诉';
            $this->display();
        } else {
            $post = I('post.');
            $post['uid'] = is_login();
            $post['create_time'] = NOW_TIME;
            $post['update_time'] = NOW_TIME;
            $res = M('Message')->add($post);
            if ($res) {
                $this->success('提交成功', U('Message/index'));
            } else {
                $this->error('保存失败');
            }
        }
    }

    public function update() {
        if (!IS_POST) {
            $id = I('id');
            if (empty($id)) {
                $this->error('非法访问');
            }
            $condition['id'] = $id;
            $detail = M("message")->where($condition)->order('id desc')->find();
            $this->meta_title = '修改建议投诉';
            $this->assign('detail', $detail);
            $this->display('add');
        } else {
            $post = I('post.');
            $id = $post['id'];
            $post['uid'] = is_login();
            $post['update_time'] = NOW_TIME;
            $res = M('Message')->where(array('id' => $id))->save($post);
            if ($res) {
                $this->success('提交成功', U('Message/index'));
            } else {
                $this->error('保存失败');
            }
        }
    }

    /**
     * 详情
     */
    public function detail() {

        $id = I('id');
        if (empty($id)) {
            $this->error('非法访问');
        }
        $condition['id'] = $id;
        $detail = M("message")->where($condition)->order('id desc')->find();
        $reply = M('reply')->where(array("messageid" => $id))->select();
        $this->assign('reply', $reply);
        $this->assign('detail', $detail);
        $this->display('detail');
    }

}
