<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | author 烟消云散 <1010422715@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台退货控制器
 * @author 烟消云散 <1010422715@qq.com>
 */
class ChangeController extends AdminController {

    /**
     * 退货管理
     * author 烟消云散 <1010422715@qq.com>
     */
    public function index() {
        /* 查询条件初始化 */
//        $a = M("refund_order")->where("total='null'")->delete();
//        $map = array('status' => 1);
        $map['refund_type'] = 2;
//        $map['refund_type'] = 2;
        $list = $this->lists('order_refund', $map, 'refund_id desc');
//        dump($list);
        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->meta_title = '退货管理';
        $this->display();
    }

    /**
     * 新增退货
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function add() {
        if (IS_POST) {
            $Config = D('change');
            $data = $Config->create();
            /* 新增时间并更新时间 */
            $shopid = $_POST["shopid"];
            $shoplist = M('shoplist')->where("id='$shopid'")->setField('status', '4');
            if ($data) {
                if ($Config->add()) {
                    S('DB_CONFIG_DATA', null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->assign('info', null);
            $this->display();
        }
    }

    /**
     * 编辑退货
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function edit($id = 0) {
        if (IS_POST) {
            $Form = D('OrderRefund');

            if ($_POST["refund_id"]) {
                $id = $_POST["refund_id"];
                $_POST['admin_time'] = NOW_TIME;
                $Form->create();

                $result = $Form->where("refund_id='$id'")->save();
                if ($result) {
                    //记录行为
                    action_log('update_change', 'change', $id, UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败' . $id);
                }
            } else {
                $this->error($Form->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('order_refund')->where(array('refund_id' => $id, 'refund_type' => 2))->find();
            if ($info['pic_info']) {
                if (strpos($info['pic_info'], ',')) {
                    $aa = explode(',', $info['pic_info']);
                    $bb = array();
                    foreach ($aa as $key => $val) {
                        $pics = M('picture')->where(array('id' => $val))->getField('path');
                        $bb[] = __PICURL__ . $pics;
                        $info['picss'] = $bb;
                    }
                } else {
                    $pics = M('picture')->where(array('id' => $info['pic_info']))->getField('path');
                    $info['picss'] = array(__PICURL__ . $pics);
                }
            }
//            dump($info);
//            $list = M('shoplist')->where("shopid='$id'")->select();

            if (false === $info) {
                $this->error('获取退货信息错误');
            }
            
            //$this->assign('list', $list);
            $this->assign('info', $info);
            $this->meta_title = '编辑退货';
            $this->display();
        }
    }

    /**
     * 同意退货
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function agree($id = 0) {
        if (IS_POST) {
            if ($_POST["id"]) {
                $id = $_POST["id"];
                $shopid = $_POST["shopid"];
                //判断状态
                $goods_refund_status = M('shoplist')->where("id='$shopid'")->getField('status');
                if($goods_refund_status == 3){
                	$this->error('该商品已同意退货，请勿重复操作！' . $id,Cookie('__forward__'));
                }
                //销量减1 库存加1
//                $sales = M('document_product')->where("id='$id'")->setDec('totalsales');
//                $stock = M('document_product')->where("id='$id'")->setInc('stock');

                /* 更新数据 */
                $Form = D('OrderRefund');
                $_POST['update_time'] = NOW_TIME;
                $Form->create();
                $Form->where("refund_id='{$id}'")->save();
                
                /* 编辑后更新商品反馈信息 */
                M('shoplist')->where("id='$shopid'")->setField('status', '3');
                //记录行为
                action_log('agree', 'change',$id, UID);
                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error('请选择要操作的数据',Cookie('__forward__'));
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('order_refund')->find($id);

            if (false === $info) {
                $this->error('获取退货信息错误');
            }
            
            $this->assign('info', $info);
            $this->meta_title = '编辑退货';
            $this->display();
        }
    }

    /**
     * 拒绝退货
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function refuse($id = 0) {
        if (IS_POST) {
            if ($_POST["id"]) {
                $id = $_POST["id"];
                $shopid = $_POST["shopid"];
                //判断状态
                $goods_refund_status = M('shoplist')->where("id='$shopid'")->getField('status');
                if($goods_refund_status == '-7'){
                	$this->error('该商品已拒绝退货，请勿重复操作！' . $id,Cookie('__forward__'));
                }
                
                $Form = D('OrderRefund');
                $Form->create();
                $result = $Form->where("refund_id='$id'")->save();
                
                /* 编辑后更新商品反馈信息 */
               M('shoplist')->where("id='$shopid'")->setField('status', '-7');
               
               //记录行为
               action_log('refuse', 'change', $id, UID);
               $this->success('更新成功', Cookie('__forward__'));
            } else {
               $this->error('请选择要操作的数据',Cookie('__forward__'));
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('order_refund')->find($id);
            //$detail = M('order_refund')->where("refund_id='$id'")->select();
            //$list = M('shoplist')->where("orderid='$id'")->select();

            if (false === $info) {
                $this->error('获取退货信息错误');
            }
            
           //$this->assign('list', $list);
            //$this->assign('detail', $detail);
            $this->assign('info', $info);

            $this->meta_title = '拒绝退货退货';
            $this->display();
        }
    }

    /**
     * 删除退货
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del() {
    	$order = M("order_refund");
        if (IS_POST) {
            $ids = I('post.id');
            if(empty($ids)){
            	$this->error("请选择要删除的数据！");
            }
            
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    $order->where("refund_id='{$id}'")->delete();
                }
            }
            $this->success("删除成功！");
        } else {
            $id = I('get.id');
            if(intval($id) == 0){
            	$this->error("请选择要删除的数据！");
            }
            $status = $order->where("refund_id='{$id}'")->delete();
            if ($status) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

}
