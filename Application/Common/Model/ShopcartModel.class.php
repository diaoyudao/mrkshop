<?php

namespace Common\Model;

/**
 * 购物车模型
 */
class ShopcartModel extends CommonModel{


    /**
     * 加入购物车
     * @param array $data 商品数据
     */
    public function addCart($data = array()){

    }

    /**
     * 查询购物车列表
     * @param $map array 筛选条件
     * @return mixed
     */
    public function getCart($map = array()) {
        if (!$map['c.uid']) {
            $user = D("member");
            $uid = $user->uid();
            $map["c.uid"] = $uid;
        }
        if (!$map['c.shop_id']) {
            $shop = D("merchant_shop");
            $sid = $shop->sid();
            $map["c.shop_id"] = $sid;
        }
        $list = $this->alias('c')
            ->field('c.id cart_id,c.ischeck,c.num,c.price,c.parameters,g.id goods_id,g.goods_name,g.goods_thums,p.url,p.path')
            ->join('LEFT JOIN __GOODS__ g ON g.goods_id=c.goodid')
            ->join('LEFT JOIN __PICTURE__ p ON g.goods_thums=p.id')
            ->where($map)
            ->select();
        if($list){
            //处理下图片
            foreach($list as $key=>$val){
                $list[$key]['goods_thums_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
            }
        }
        return $list;
    }

    /**
     * 购物车中商品的种类数量
     * @param $map array 筛选条件
     * @return int
     */
    public function getCnt($map=array()){
        if (!$map['uid']) {
            $user = D("member");
            $uid = $user->uid();
            $map["uid"] = $uid;
        }
        if (!$map['sid']) {
            $shop = D("merchant_shop");
            $sid = $shop->sid();
            $map["shop_id"] = $sid;
        }
        $map['goodid'] =array('gt',1);
        return $this->where($map)->count();
    }

    /**
     * 购物车中商品的总金额
     * @param $map array 筛选条件
     * @return string
     */
    public function getTotal($map=array()) {
        $total = 0.00;
        if (!$map['uid']) {
            $user = D("member");
            $uid = $user->uid();
            $map["uid"] = $uid;
        }
        if (!$map['sid']) {
            $shop = D("merchant_shop");
            $sid = $shop->sid();
            $map["shop_id"] = $sid;
        }
        //数量为0，价钱为0
        if ($this->getCnt($map) == 0) {
            return $total;
        }else{
            $data = $this->where($map)->select();
            foreach ($data as $k=>$val) {
                $total += floatval($val['num'] * $val['price']);
            }
        }
        return sprintf("%01.2f", $total);
    }

    /**
     * 购物车中商品的总个数
     * @param $map array 筛选条件
     * @return string
     */
    public function getNum($map = array()){
        $sum = 0;
        if (!$map['uid']) {
            $user = D("member");
            $uid = $user->uid();
            $map["uid"] = $uid;
        }
        if (!$map['sid']) {
            $shop = D("merchant_shop");
            $sid = $shop->sid();
            $map["shop_id"] = $sid;
        }

        $map['goodid'] =array('gt',1);
        if ($this->getCnt($map) == 0) {
            return $sum;
        }
        else{
            $data=$this->where($map)->select();
            foreach ($data as $k=>$item)
            {
                $sum += $item['num'];
            }
        }
        return $sum;
    }

    /**
     * 增加购物车中商品的个数
     * @param int $id 购物车ID
     * @param int $inputnum 增加数量
     * @return int 返回最新的数量
     */
    public function inc($id,$inputnum = 1){
        $this->where(array('id'=>$id))->setInc("num",$inputnum);
        //返回最新的数量
        return $this->where(array('id'=>$id))->getField("num");
    }

    /**
     * 减少购物车中商品的个数
     * @param int $id 购物车ID
     * @param int $inputnum
     * @return int 返回最新的数量
     */
    public function dec($id,$inputnum= 1){
        $num = $this->where(array('id'=>$id))->getField("num");
        //判断数量
        $inputnum = ($inputnum > $num) ? $num :$inputnum;
        //减少数量
        $this->where(array('id'=>$id))->setDec("num",$inputnum);
        //返回最新的数量
        return $this->where(array('id'=>$id))->getField("num");
    }

    /**
     * 删除购物车中的商品
     * @param $id 购物车ID
     * @return mixed
     */
    public function deleteid($id){
        return $this->where(array('id'=>$id))->delete();
    } 
}
