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

}
