<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | author 烟消云散 <1010422715@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Api\Api\OrderApi;

/**
 * 后台订单控制器
 * @author 烟消云散 <1010422715@qq.com>
 */
class OrderController extends AdminController {

    /**
     * 订单管理
     * author 烟消云散 <1010422715@qq.com>
     */
    public function index() {
        /* 查询条件初始化 */
        $map = array();
        $map = array('status' => array('egt', -2));
        
        
        $_POST['r'] = 20;
        $orderid = I("orderid");
        if ($orderid) {
            $map['orderid'] = array('like', '%' . $orderid . '%');
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
     * 已取消的订单
     */
    public function cancellist() {

        /* 查询条件初始化 */
        $map = array();
        $map['status'] = -2;

        $_POST['r'] = 20;
        $orderid = I("orderid");
        if ($orderid) {
            $map['orderid'] = array('like', '%' . $orderid . '%');
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

        $this->meta_title = '已取消的订单';
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
        $orderapi = new OrderApi();
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
                		$old_pricetotal = $_POST['old_pricetotal'];//订单总金额原始值
                		$old_total = $_POST['old_total'];//订单总商品金额原始值
                		
                		$old_shipprice = $_POST['old_shipprice'];//订单运费原始值
                		$new_shipprice = $_POST['shipprice'];//新订单运费
                		
                		$goods_num = $_POST['goods_num'];//订单商品数量
                		$ogoods_price = $_POST['ogoods_price'];//原始订单商品价格
                		$ngoods_price = $_POST['ngoods_price'];//新订单商品价格
                		$oprice = $_POST['oprice'];//旧订单商品价格
                		  		
                		$shoplist = M('shoplist');
                		$new_total = $g_total = $og_total= 0;
                		
                		//循环修改商品结算价格和商品总金额及原始值
                		foreach($ngoods_price as $key=>$val){
                			//计算新商品金额
                			$g_total = intval($goods_num[$key]) * $val;
                			
                			$updata = array();
                			$updata['price'] = $val;
                			$updata['total'] = $g_total;
                			$updata['o_price'] = $ogoods_price[$key];
                			if($oprice[$key] != $val){//如果有改变，修改更新时间
                				$updata['change_time'] = NOW_TIME;
                			}
                			$shoplist->where("id='{$key}' AND orderid='{$id}'")->save($updata);
                			$new_total += $g_total;//商品总金额
                		}
                		
                		//修改订单表中订单总额和订单商品总额及原始值
                		//计算新的订单总金额：旧订单总金额-旧商品总金额-旧运费+新的商品总金额
                		$new_pricetotal = (($old_pricetotal-$old_total-$old_shipprice) > 0) ? ($old_pricetotal-$old_total-$old_shipprice+$new_total) : $new_total;
                		$new_pricetotal = $new_pricetotal+$new_shipprice;//加上运费
                		M('order')->where("id='{$id}'")->setField(array('pricetotal'=>$new_pricetotal,'total'=>$new_total,'o_pricetotal'=>$old_pricetotal,'o_total'=>$old_total,'o_shipprice'=>$old_shipprice));

                    //记录行为
                    action_log('update_order', 'order', $id, UID);

                    $data = array('msg' => '更改了订单信息', 'roleid' => UID, 'uid' => $uid);
                    $orderapi->addOrderLog($_POST['id'], $data);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败55' . $id);
                }
            } else {
                $this->error($Form->getError());
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
                //$product_detail = S('p_'.$v['goodid']);
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
                    //S('p_' . $v['goodid'], $product_detail);
                }

                $product_detail['price'] = 0;
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
            $address = M('order_address')->where("orderid='$id'")->select();
            $this->assign('alist', $address);
            if (false === $info) {
                $this->error('获取订单信息错误');
            }

            $orderlog = $orderapi->getOrderLog($id);
            $this->assign('listlog', $orderlog);
            $this->assign('list', $list);
            $this->assign('detail', $detail);
            $this->assign('info', $info);
            //$this->assign('a', $orderid);
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

                $Form->create();
                $user = session('user_auth');
                $uid = $user['uid'];
                $Form->assistant = $uid;
                $Form->update_time = NOW_TIME;
                $Form->status = "2";
                $Form->send_time = time(); //2015/5/8 14:13 sheshanhu 订单发货状态操作时间
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

    /**
     * 打印订单
     */
    public function order_print() {
        $order_status = C('ORDER_STATUS');
        $id = I('id');
        if (empty($id)) {
            $this->error('参数错误');
        }
        /* 获取数据 */
        $detail = M('order')->find($id);
        $list = M('shoplist')->where("orderid='$id'")->select();
        $documentViewModel = D("DocumentView");
        foreach ($list as $k => $v) {
            $product_detail = array();
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
            }
            $product_detail['price'] = 0;
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
        //如果是银行汇款，查看汇款信息
        if ($detail['ispay'] == 5) {
            $map['orderid'] = $detail['id'];
            $map['uid'] = $detail['uid'];
            $order_bank = M('order_bank')->where($map)->find();
            $order_bank['bankid'] = M('bank')->find($order_bank['bankid']);
        }
        if (false === $detail) {
            $this->error('获取订单信息错误');
        } else {
            if ($detail['invoice'])
                $detail['invoice'] = unserialize($detail['invoice']);
        }

        // 订单状态
        $detail['status_value'] = $order_status[$detail['status']];
        // 创建时间
        $detail['create_time_value'] = date('Y-m-d H:i:s', $detail['create_time']);
        // 派送时间
        if (!empty($detail['send_time'])) {
            $detail['send_time_value'] = date('Y-m-d H:i:s', $detail['send_time']);
        }
        if (!empty($detail['payment_time'])) {
            $detail['payment_time_value'] = date('Y-m-d H:i:s', $detail['payment_time']);
        }
        // 支付信息
        $pay_info = M('pay')->where("out_trade_no='" . $detail['orderid'] . "'")->find();
        $detail['pay_info'] = $pay_info;
        $this->assign('info', $detail);

        $address = M("order_address")->where(array('orderid' => $id))->find();
        $this->assign('address', $address);

//        dump($list);exit;

        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 导出
     */
    public function excel_export() {
        $condition = array();
        $list = M('order')->where($condition)->select();
        if ($list) {
            $this->createExcel($list);
        }
        exit;
    }

    /*
     * 创建excel
     */

    public function createExcel($data) {
        vendor('Excel.Excel');
        $excel_obj = new \Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        //header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '订单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '订单总金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '商品金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '运费');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '优惠金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '支付方式');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '用户名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '下单时间');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '物流公司');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '订单来源');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '状态');
        //data
        foreach ($data as $k => $v) {
            $excel_data[$k + 1][] = array('data' => $v['tag']);
            $excel_data[$k + 1][] = array('format' => 'Number', 'data' => $v['pricetotal']);
            $excel_data[$k + 1][] = array('format' => 'Number', 'data' => ncPriceFormat($v['total']));
            $excel_data[$k + 1][] = array('format' => 'Number', 'data' => ncPriceFormat($v['shipprice']));
            $excel_data[$k + 1][] = array('format' => 'Number', 'data' => ncPriceFormat($v['discount_amount']));
            $excel_data[$k + 1][] = array('data' => $v['pay_type']);
            $excel_data[$k + 1][] = array('data' => get_username($v['uid']));
            $excel_data[$k + 1][] = array('data' => date("Y-m-d H:i:s", $v['create_time']));
            $excel_data[$k + 1][] = array('data' => $v['tool']);
            $excel_data[$k + 1][] = array('data' => $v['order_from']);
            $excel_data[$k + 1][] = array('data' => $this->status_txt($v['status']));
        }
        define(CHARSET, "utf-8");
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('订单列表', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('订单列表', CHARSET) . date('Y-m-d-H', time()));
    }

    private function status_txt($status) {
        $statetxt = "";
        switch ($status) {
            case -3:
                $statetxt = "退货退款";
                break;
            case -2:
                $statetxt = "取消订单";
                break;
            case -1:
                $statetxt = "待付款";
                break;
            case 1:
                $statetxt = "已付款";
                break;
            case 2:
                $statetxt = "待收货";
                break;
            case 3:
                $statetxt = "已完成";
                break;
            default:
                $statetxt = '';
                break;
        }
        return $statetxt;
    }

    /**
     * 更改订单状态 已付款
     */
    public function ChangeStatus($id = 0) {
        if (IS_POST) {
            $Form = D('order');
            $user = session('user_auth');
            $uid = $user['uid'];
            if ($_POST["id"]) {
                $Form->create();
                $id = $_POST["id"];
                $status = $_POST['status'];
                $Form->update_time = NOW_TIME;
                $Form->pay_type = $_POST['pay_type'];  //   付款方式
                $Form->status = $status;  //   付款状态
                if ($status == 1) {
                    $Form->payment_time = NOW_TIME;
                    $Form->ispay = 2;  //   订单已付款
                    // 更改发货单状态 为已付款
                    M("order_delivery")->where(array('order_id' => $id))->data(array('status' => 1, 'update_time' => NOW_TIME))->save();
                    //更改商品提现状态为已付款
                    M("affiliate_log")->where(array('order_id' => $id))->setField(array('status' => 1));
                }
                $Form->assistant = $uid;

                $result = $Form->where("id='$id'")->save();
                if ($result) {
                    //记录行为
                    action_log('update_order', 'order', $id, UID);
                    $orderapi = new OrderApi();
                    $orderapi->addOrderLog($id, array('msg' => get_username($uid) . '更改了订单的状态', 'uid' => $uid, 'status' => $status, 'roleid' => UID));
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败55' . $id);
                }
            } else {
                $this->error($Form->getError());
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

            $payemnt_list = get_site_payment();
//           dump($payemnt_list);
            $this->assign("payemnt_list", $payemnt_list);
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
            $status = $info['status'];
            if ($info['status'] == -1) {
                $status = 1;
            } else {
                $status +=1;
            }
            $this->assign('status', $status);
            $this->meta_title = "更改订单状态";
            $this->display('pay');
        }
    }

    /**
     * 发货单列表
     */
    public function delivery_list() {
        $warehouse = I("warehouse");
        $orderid = I('orderid');
        // 获取发货单
        if ($warehouse) {
            $condition['warehouse'] = $warehouse;
        }
        if ($orderid) {
            $condition['order_sn'] = array('like', '%' . $orderid . '%');
        }
        $condition['status'] = array('egt',1);
//        $delivery = M("order_delivery")->where($condition)->select();
         $delivery = $this->lists('order_delivery', $condition, 'status asc');
         foreach ($delivery as $key => &$value) {
             $value['pay_amount'] = M('order')->where(array('id'=>$value['order_id']))->getField('pricetotal');
             
         }
        $this->assign('delivery', $delivery);

        // 获取发货仓列表
        $distribution = M("distribution")->where(array('type' => 0))->select();
        $this->assign('warehouse', $warehouse);
        $this->assign('distribution', $distribution);
        $this->display();
    }

    /**
     * 编辑发货单
     */
    public function delivery() {
        if (IS_POST) {
            $post = $_POST;
            if(empty($post['invoice_no']) || empty($post['shipping_name'])){
                $this->error("物流公司或物流单号不能为空！");
            }
            $data['shipping_name'] = $post['shipping_name'];
            $data['invoice_no'] = $post['invoice_no'];
            $data['consignee'] = $post['consignee'];
            $data['tel'] = $post['tel'];
            $data['send_address'] = $post['send_address'];
            $data['update_time'] = NOW_TIME;
            $data['send_time'] = NOW_TIME;
            $data['status'] = 2;
            $data['action_user'] = UID;
            $delivery = M("order_delivery")->where(array('order_id' => $post['order_id'], 'delivery_id' => $post['delivery_id']))->find();
            $res = M("order_delivery")->where(array('order_id' => $post['order_id'], 'delivery_id' => $post['delivery_id']))->data($data)->save();
            if ($res) {
                
                // 如果都发货了 则订单状态变成已发货
                $sum = M('order_delivery')->where(array('order_id'=>$post['order_id']))->count();  // 获取订单的所有分运单
                $issend = M('order_delivery')->where(array('order_id' => $post['order_id'], 'status' => 2))->count();  // 获取已经评论的商品数量
                if ($sum == $issend) {
                    $res = M('order')->where(array('id' => $post['order_id']))->setField('status', 2);
                }
                
                $orderapi = new OrderApi();
                $orderapi->addOrderLog($delivery['order_id'], array('msg' => get_username() . '发货' . get_shipping($delivery['warehouse'] . '包裹'), 'uid' => $delivery['uid'], 'status' => 2, 'roleid' => UID));
                $this->success('发货成功');
            } else {
                $this->error('发货失败');
            }
        } else {
            $id = I('id');
            $condition['delivery_id'] = $id;
            $delivery = M("order_delivery")->where($condition)->find();
            $delivery['consignee']? : $delivery['consignee'] = C('SHOPNAME');
            $delivery['tel']? : $delivery['tel'] = C('CONTACT');
            $delivery['send_address']? : $delivery['send_address'] = C('ADDRESS');
            $delivery['buy_message'] = M("order")->where(array('id'=>$delivery['order_id']))->getField('message');
            $this->assign('info', $delivery);
            // 获取地址信息
            $address = M("order_address")->where(array('id' => $delivery['address_id']))->find();
            $this->assign('address', $address);
//        dump($delivery);
            // 商品信息
            $list = M("shoplist")->where(array('orderid' => $delivery['order_id'], 'warehouse' => $delivery['warehouse']))->select();
            $this->assign('list', $list);
            $this->display();
        }
    }

}
