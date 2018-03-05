<?php

namespace Common\Model;

/**
 * 会员收藏模型
 */
class FavortableModel extends CommonModel{

    /* 自动验证规则 */
    protected $_validate = array(
        array('uid', 'require', '收藏会员ID不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('type', 'require', '收藏类型不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('data_id', 'require', '关联数据ID不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', '1', self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 添加收藏
     * @param array $data
     * @return bool
     */
    public function addFavortable($data){
        if($this->create($data)){
            if($this->add($data)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 收藏列表
     * @param int $type 收藏类型 1为商品 2为店铺
     * @param int $page 当前页码
     * @param int $size 每页条数
     * @return array|mixed
     */
    public function getfavor($type,$page=1,$size=10) {
        $map['f.status'] = 1;
        $map['f.uid'] = session('uid');
        if($type == 1){
            $favorlist = $this->field('f.id,f.create_time,g.goods_name,g.id goods_id,p.url,p.path')
                ->join('LEFT JOIN __GOODS__ g ON f.data_id=g.id ')
                ->join('LEFT JOIN __PICTURE__ p ON p.id=g.goods_thums')
                ->where($map)
                ->page("{$page},{$size}")
                ->select();
        }else{
            $favorlist = $this->field('f.id,f.create_time,s.sname,s.id shop_id,p.url,p.path')
                ->join('LEFT JOIN _MERCHANT_SHOP__ s ON f.data_id=g.id ')
                ->join('LEFT JOIN _MERCHANT_SHOPINFO__ si ON si.sid=s.id ')
                ->join('LEFT JOIN __PICTURE__ p ON p.id=si.logo')
                ->where($map)
                ->select();
        }
        if($favorlist){
            foreach($favorlist as $key=>$val){
                //处理下图片
                $favorlist[$key]['favor_img_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
            }
        }
        return $favorlist;
    }

    /**
     * 判断是否已收藏
     * @param int $type 收藏类型 1为商品 2为店铺
     * @param int $data_id 关联数据ID
     * @return bool
     */
    public function isCollected($type = 1,$data_id){
        $map = array(
            'status' => 1,
            'uid' => session('uid'),
            'type' => $type,
            'data_id' => $data_id
        );
        if($this->where($map)->find()){
            return true;
        }else{
            return false;
        }
    }

}
