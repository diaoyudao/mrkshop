<?php

namespace Common\Model;

/**
 * 售后单模型
 */
class OrderRefundModel extends CommonModel{

	/* 自动验证 */
	protected $_validate = array(
		/* 验证手机号码 */
        array('uid', 'require','关联用户ID不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('order_id', 'require','关联订单ID不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('shop_id', 'require','关联店铺ID不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	/* 自动完成 */
	protected $_auto = array(
		array("create_time", NOW_TIME, self::MODEL_INSERT),
        array("update_time", NOW_TIME, self::MODEL_UPDATE),
		array("status", 1, self::MODEL_INSERT),
	);

    /**
     * 创建售后单
     * @param array $data 提交的数据
     * @return bool
     */
    public function createRefund($data = array()){
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
     * 获取订单详情
     * @param $id_sn订单号或订单ID
     * @return bool|mixed
     */
    public function getInfo($id_sn){
        $map = array();
        if(is_numeric($id_sn)){ //通过ID查询
            $map['r.id'] = $id_sn;
        } else { //通过订单号查询
            $map['r.refund_sn'] = $id_sn;
        }
        $refund_info = $this->field('r.*,p.url,p.path')
            ->join("r LEFT JOIN __PICTURE__ p ON p.id=r.goods_image")
            ->where($map)
            ->find();
        if(empty($refund_info) || $refund_info['status'] == 0){
            $this->error = "订单不存在或已被删除";
            return false;
        }
        $refund_info['goods_image_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
        return $refund_info;
    }

    /**
     * 获取列表
     * @param array $map 筛选条件
     * @param bool $isLimit 是否分页
     * @param string $order 排序
     * @param int $page 页码
     * @param int $size 每页数量
     * @return array
     */
    public function getList($map = array(),$isLimit = true,$order = "id desc",$page=1,$size=10){
        $map['r.status'] = 1;
        //总数
        $count = $this->alias('r')->where($map)->count();
        //是否分页
        if($isLimit){
            $list = $this->alias('r')->field('r.*,p.url,p.path')
                ->join("r LEFT JOIN __PICTURE__ p ON p.id=r.goods_image")
                ->where($map)->page("{$page},{$size}")->order($order)->select();
        }else{
            $list = $this->alias('r')->field('r.*,p.url,p.path')
                ->join("r LEFT JOIN __PICTURE__ p ON p.id=r.goods_image")
                ->where($map)->select();
        }
        if($list){
            foreach($list as $key=>$val){
                //处理下图片
                $list[$key]['goods_image_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
            }
        }

        return array('list'=>$list,'count'=>$count);
    }

}
