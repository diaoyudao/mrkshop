<?php

namespace Common\Model;

/**
 * 订单模型
 */
class OrderModel extends CommonModel{

	/* 自动验证 */
	protected $_validate = array(
		/* 验证手机号码 */
		array('uid', 'require','关联用户ID不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('shop_id', 'require','关联店铺ID不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	/* 自动完成 */
	protected $_auto = array(
		array("create_time", NOW_TIME, self::MODEL_INSERT),
        array("update_time", NOW_TIME, self::MODEL_UPDATE),
		array("status", 1, self::MODEL_INSERT),
	);

    /**
     * 创建订单
     * @param array $data 提交的数据
     * @return bool
     */
    public function createOrder($data = array()){
        /* 添加数据 */
        if ($this->create($data)) {
            $id = $res = $this->add();
            if($res){
                return $id;//返回创建成功的订单ID
            }else{
                $this->error = '创建订单失败';
                return false;
            }
        } else {
            $this->error = $this->getError(); //错误详情见自动验证注释
            return false;
        }
    }

    /**
     * 添加订单商品数据
     * @param $order_id 订单ID
     * @param $goods_list 订单商品列表
     */
    public function insertOrderGoods($order_id,$goods_list){
        $orderGoodsModel = M('order_goods');
        foreach($goods_list as $key=>$val){
            $goods_list[$key]['order_id'] = $order_id;
        }

        $res = $orderGoodsModel->addAll($goods_list);
        if(false === $res){
            return false;
        }
        return true;
    }

    /**
     * 获取订单详情
     * @param $id_sn订单号或订单ID
     * @param mixed $field 查询的字段
     * @return bool|mixed
     */
    public function getInfo($id_sn, $field = true){
        $map = array();
        if(is_numeric($id_sn)){ //通过ID查询
            $map['id'] = $id_sn;
        } else { //通过订单号查询
            $map['order_sn'] = $id_sn;
        }
        $order_info = $this->field($field)->where($map)->find();
        if(empty($order_info) || $order_info['status'] == 0){
            $this->error = "订单不存在或已被删除";
            return false;
        }

        //获取订单商品数据
        $order_info['goods_list'] = $this->getOrderGoodsList($order_info['id']);
        return $order_info;
    }

    /**
     * 获取订单及商品列表
     * @param array $map 筛选条件
     * @param bool $isLimit 是否分页
     * @param mixed $fields 要查询的字段
     * @param string $order 排序
     * @param int $page 页码
     * @param int $size 每页数量
     * @return array
     */
    public function getList($map = array(),$isLimit = true,$fields=true,$order = "id desc",$page=1,$size=10){
        $map['o.status'] = 1;
        //总数
        $count = $this->alias('o')->where($map)->count();

        if($isLimit){
            $list = $this->alias('o')->field($fields)
                ->where($map)->page("{$page},{$size}")->order($order)->select();
        }else{
            $list = $this->alias('o')->field($fields)->where($map)->select();
        }
        if($list){
            foreach($list as $key=>$val){
                //获取订单商品数据
                $list[$key]['goods_list'] = $this->getOrderGoodsList($val['goods_id']);
            }
        }

        return array('list'=>$list,'count'=>$count);
    }

    /**
     * 获取指定订单的商品详情
     * @param $order_id int 订单ID
     * @return mixed
     */
    protected function getOrderGoodsList($order_id){
        $orderGoodsModel = M('order_goods');
        $goods_list = $orderGoodsModel->field('g.*,p.url,p.path')
            ->join('g LEFT JOIN __PICTURE__ p ON g.goods_thums=p.id')
            ->where(array('g.order_id'=>$order_id))
            ->select();

        foreach($goods_list as $key=>$val){
            //处理下图片
            if($val['goods_thums'] > 0){
                $goods_list[$key]['goods_thums_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
            }
        }
        return $goods_list;
    }

    /**
     * 更新订单数据
     * @param int $id 订单ID
     * @param array $data 更新的数据
     * @return bool
     */
    public function updateOrder($id,$data=array()){
        return $this->where(array('id'=>$id))->save($data);
    }


    /**
     * 取消订单
     * @param array $order_info 订单数据
     */
    public function cancelOrder($order_info)
    {
        $data = array();
        M()->startTrans();
        // 未付款，直接取消订单
        if ($order_info['ispay'] == 1 && $order_info['order_status'] == -1) {
            $data['status'] = -2;
            $data['cancel_time'] = NOW_TIME;
            if(!$this->updateOrder($order_info['id'],$data)){
                M()->rollback();
                return false;
            }
            //添加订单日志记录
            $msg = $order_info['msg'] ? : get_username() . '取消了订单';
            if($this->addOrderLog($order_info['id'], array('msg' => $msg, 'status' => -2))){
                M()->rollback();
                return false;
            }
            // 库存还原，销量还原
            if ($this->orginGoodsStock($order_info)) {
                M()->commit();
                return true;
            } else {
                M()->rollback();
                return false;
            }
        } elseif ($order_info['order_status'] == 1) {  // 已付款，生成退款单，未发货
            $data['status'] = -3;
            $data['cancel_time'] = NOW_TIME;
            if(!$this->updateOrder($order_info['id'],$data)){
                M()->rollback();
                return false;
            }
            //生成退款单
            $refunddata = array(
                'order_id' => $order_info['id'],
                'shop_id' => $order_info['shop_id'],
                'order_sn' => $order_info['order_sn'],
                'uid' => $order_info['uid'],
                'user_name' => get_username($order_info['uid']),
                'refund_type' => 1,
                'refund_state' => 0,
                'refund_sn' => $this->makePaySn(NOW_TIME . $order_info['id']), // 生成退款单号
                'refund_amount' => $order_info['pricetotal'],//订单实际总额
                'order_goods_type' => $order_info['order_type'],
                'add_time' => NOW_TIME,
            );
            if(!D('OrderRefund')->create($refunddata)){
                M()->rollback();
                return false;
            }
            if(!D('OrderRefund')->add($refunddata)){
                M()->rollback();
                return false;
            }

            // 库存还原，销量还原
            if(!$this->orginGoodsStock($order_info)){
                M()->rollback();
                return false;
            }

            //添加日志
            $this->addOrderLog($order_info['id'], array('msg' => get_username() . '取消了订单', 'status' => -2));
            $this->addOrderLog($order_info['id'], array('msg' => '系统生成了退款单号：' . $refunddata['refund_sn'] . '，请注意查看', 'status' => -2));

            M()->commit();
            return true;
        } else {
            $this->error = '订单状态错误';
            return false;
        }
    }

    /**
     * 生成订单单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    public function makePaySn($member_id) {
        return mt_rand(10, 99)
        . sprintf('%010d', time() - 946656000)
        . sprintf('%03d', (float) microtime() * 1000)
        . sprintf('%03d', (int) $member_id % 1000);
    }

    /**
     * 还原订单商品库存
     * @param array $order_info
     * @return bool
     */
    private function orginGoodsStock($order_info) {
        $res = true;
        //获取订单商品数据
        $ordergooods = M('order_goods')->where(array('orderid' => $order_info['id']))->select();
        foreach ($ordergooods as $key => $value) {
            //判断是活动商品还是普通商品
            if($ordergooods['group_type'] > 0 && $ordergooods['group_type'] == 1){ //团购或者抢购活动商品
                $save_data = array(
                    'group_num' => array('exp',"`group_num`+{$value['num']}"),
                    'sale_num' => array('exp',"`sale_num`-{$value['num']}")
                );
                $res = M('groups_goods')->where(array("id" => $value["group_gid"]))->save(array($save_data));
                if(false === $res){
                    break;
                }
            }else{
                //修改商品总库存数量
                $save_data = array(
                    'goods_number' => array('exp',"`goods_number`+{$value['num']}"),
                    'salenum' => array('exp',"`salenum`+{$value['num']}"),
                );
                $res = M('good')->where(array("id" => $value["goods_id"]))->save(array($save_data));

                //判断该商品是否有属性
                if($value['product_id'] > 0){
                    //修改商品属性库存数量
                    $save_data = array(
                        'product_number' => array('exp',"`product_number`+{$value['num']}"),
                        'sale_number' => array('exp',"`sale_number`-{$value['num']}")
                    );
                    $res = M('goods_products')->where(array("id" => $value['product_id']))->save(array($save_data));
                }
                if(false === $res){
                    break;
                }
            }
        }
        return $res;
    }

    /**
     * 添加订单日志
     * @param int $orderid 订单ID
     * @param array $data 提交的数据
     * @return mixed
     */
    public function addOrderLog($orderid, $data) {
        $order_log_data = array();
        $order_log = M("order_log");

        $order_log_data['order_id'] = $orderid;
        $order_log_data['log_msg'] = $data['msg'];
        $order_log_data['log_time'] = NOW_TIME;
        $order_log_data['log_role'] = $data['roleid'] ? :UID;
        $order_log_data['log_user'] = $data['uid'] ? : UID;
        $order_log_data['log_orderstate'] = $this->status ? : $data['status'];
        $order_log->add($order_log_data);
    }

    /**
     * 获取订单日志列表
     * @param int $orderid 订单ID
     * @return array
     */
    public function getOrderLog($orderid) {
        return M("order_log")->where(array('order_id' => $orderid))->order("log_id desc")->select();
    }
}
