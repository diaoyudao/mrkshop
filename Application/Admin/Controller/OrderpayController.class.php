<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | author 烟消云散 <1010422715@qq.com>
// +----------------------------------------------------------------------

/**
 * 已付款 待发货管理
 */

namespace Admin\Controller;
use Api\Api\OrderApi;
/**
 * 后台订单控制器
 * @author 烟消云散 <1010422715@qq.com>
 */
class OrderPayController extends AdminController {

    /**
     * 订单管理
     * author 烟消云散 <1010422715@qq.com>
     */
    public function index() {
        /* 查询条件初始化 */
        $map = array();
        $map = array('status' => array('in', array(1)));

        $_POST['r'] = 20;
        $orderid = I("orderid");
        if($orderid){
            $map['orderid'] = array('like','%'.$orderid.'%');
        }
        if ( isset($_GET['time-start']) ) {
            $map['create_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['create_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        if (isset($_GET['uid'])) {
            $map['uid'] = I('uid');
        }
        $list = $this->lists('order', $map, 'id desc');

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->meta_title = '订单管理';
        $this->display();
    }

    /**
     * 新增订单
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function add() {
        if (IS_POST) {
            $Config = D('order');
            $data = $Config->create();
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
            $this->display('edit');
        }
    }

    /**
     * 编辑订单
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function edit($id = 0) {
        if (IS_POST) {
            $Form = D('order');
            $user = session('user_auth');
            $uid = $user['uid'];
            if ($_POST["id"]) {
                $Form->create();
                $id = $_POST["id"];
                $Form->update_time = NOW_TIME;
                $Form->assistant = $uid;

                $result = $Form->where("id='$id'")->save();
                if ($result) {
                    //记录行为
                    action_log('update_order', 'order', $data['id'], UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败55' . $id);
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('order')->find($id);
            $detail = M('order')->where("id='$id'")->select();
            $list = M('shoplist')->where("orderid='$id'")->select();

            $documentViewModel = D("Document");
            foreach ($list as $k => $v) {
                $product_detail = array();
                $product_detail = S('p_' . $v['goodid']);
                if (!$product_detail) {
                    $product_detail = $documentViewModel->find($v['goodid']);
                    if ($product_detail["cover_id"] || $product_detail['pics']) {
                        $arr = array();
                        if ($product_detail['pics']) {
                            $arr = explode(",", $product_detail['pics']);
                        }
                        $arr[] = $product_detail["cover_id"];
                        $arrmap["id"] = array("in", $arr);
                        $product_detail["pics_img"] = M("picture")->where($arrmap)->getField("id,path");
                        ;
                    }
                    S('p_' . $v['goodid'], $product_detail);
                }
                $list[$k] = array_merge($product_detail, $v);
            }

            //对组合商品重新设计数组结构
            $goodsgroup = array();
            foreach ($list as $okey => $ovalue) {
                if ($ovalue['groupid'] > 0) {
                    $goodsgroup[$ovalue['groupid']][] = $ovalue;
                }
                if ($ovalue['groupid'] > 0 && $ovalue['price'] == 0) {
                    unset($list[$okey]);
                }
            }
            foreach ($list as $pkey => $pvalue) {
                if ($pvalue['groupid'] > 0) {
                    $list[$pkey]['goodsinfo'] = $goodsgroup[$pvalue['groupid']];
                }
            }
            //对组合商品重新设计数组结构END
            //<td align="center">{$vo.id|get_good_price}</td>

            $addressid = M('order')->where("id='$id'")->getField("addressid");
            $address = M('transport')->where("id='$addressid'")->select();
            $this->assign('alist', $address);
            if (false === $info) {
                $this->error('获取订单信息错误');
            }
            $this->assign('list', $list);
            $this->assign('detail', $detail);
            $this->assign('info', $info);
            $this->assign('a', $orderid);
            $this->meta_title = '编辑订单';
            $this->display();
        }
    }

    /**
     * 订单发货
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function send($id = 0) {
        if (IS_POST) {
            $Form = D('order');
            $user = session('user_auth');
            $uid = $user['uid'];
            if ($_POST["id"]) {
                $id = $_POST["id"];
                $toolid = I('toolid');  // 物流单号
                if(empty($toolid)){
                    $this->error('快递单号不能为空！');
                }
                $status = 2;
                $Form->create();
                $user = session('user_auth');
                $uid = $user['uid'];
                $Form->assistant = $uid;
                $Form->update_time = NOW_TIME;
                $Form->status = $status;
                $Form->toolid = $toolid;
                $Form->send_name = I('send_name');
                $Form->send_contact = I('send_contact');
                $Form->send_address = I('send_address');
                $Form->send_time = NOW_TIME; //2015/5/8 14:13 sheshanhu 订单发货状态操作时间
                $orderid = M('order')->where("id='$id'")->getField("orderid");
                $result = $Form->where("id='$id'")->save();

                //根据订单id获取购物清单
                $del = M("shoplist")->where("orderid='$id'")->select();

                foreach ($del as $k => $val) {
                    //获取购物清单数据表产品id，字段id
                    $byid = $val["id"];
                    $goodid = $val["goodid"];
                    //销量加1 库存减1
                    $sales = M('document_product')->where("id='$goodid'")->setInc('totalsales');
                    $stock = M('document_product')->where("id='$goodid'")->setDec('stock');
                    $data['status'] = 2;
                    $shop = M("shoplist");
                    M("shoplist")->where("id='$byid'")->save($data);
                }

                if ($result) {
                    //记录行为
                    action_log('update_order', 'order', $data['id'], UID);
                    $orderapi = new OrderApi();
                    $orderapi->addOrderLog($id, array('msg'=>  get_username($uid).'更改了订单的状态为已发货','uid'=>$uid,'status'=>$status,'roleid'=>$uid));
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败' . $id);
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('order')->find($id);
            $detail = M('order')->where("id='$id'")->select();
            $list = M('shoplist')->where("orderid='$id'")->select();

            //获取所有配送方式及用户选中的配送方式
            $distribution = get_site_distribution();
            $this->assign('distribution', $distribution); //配送方式
            $this->assign('orderdistributionid', $info['distribution']);


            if (false === $info) {
                $this->error('获取订单信息错误');
            }
            $this->assign('list', $list);
            $this->assign('detail', $detail);
            $this->assign('info', $info);

            $this->meta_title = '订单发货';
            $this->display();
        }
    }

    /**
     * 删除订单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del() {
        if (IS_POST) {
            $ids = I('post.id');
            $order = M("order");

            if (is_array($ids)) {
                foreach ($ids as $id) {

                    $order->where("id='$id'")->delete();
                    $shop = M("shoplist");
                    $shop->where("orderid='$id'")->delete();
                }
            }
            $this->success("删除成功！");
        } else {
            $id = I('get.id');
            $db = M("order");
            $status = $db->where("id='$id'")->delete();
            if ($status) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

}
