<?php

namespace Common\Model;

/**
 * 商品模型
 */
class GoodsModel extends CommonModel {
    /* 自动验证规则 */
    protected $_validate = array(
        array('cat_id', 'require', '分类不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('goods_sn', 'checkGoodsSn', '商品编号已经存在', self::VALUE_VALIDATE , 'callback', self::MODEL_BOTH),
        array('goods_name', 'require', '商品名不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('goods_name', '2,40', '商品名长度不能超过40个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('goods_name', 'checkGoodsName', '商品名已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
        array('market_price', 'require', '市场价不能为空', self::VALUE_VALIDATE , 'regex', self::MODEL_BOTH),
        array('shop_price', 'require', '店铺价不能为空', self::VALUE_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('goods_sn', 'trim', self::MODEL_BOTH, 'function'),
        array('goods_name', 'trim', self::MODEL_BOTH, 'function'),
        array('goods_brief', 'trim', self::MODEL_BOTH, 'function'),
        array('goods_desc', 'htmlspecialchars', self::MODEL_BOTH, 'function'),
        array('create_time', NOW_TIME, self::MODEL_BOTH),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
    );


    /**
     * 检查商品名称是否重复
     * @param string $goods_name 商品名称
     * @return true无重复，false已存在
     */
    protected function checkGoodsName($goods_name = '')
    {
        $name = $goods_name ? $goods_name :trim(I('post.goods_name'));
        $shop_id = intval(I('post.shop_id', 0));
        $id = intval(I('post.id', 0));
        $map = array(
            'goods_name' => $name,
            'shop_id' => $shop_id,
            'status' => 1
        );
        if($id > 0){
            $map['id'] = array('neq',$id);
        }

        if ($this->where($map)->find()){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 检查商品编号是否已存在
     * @param string $goods_sn
     * @return true无重复，false已存在
     */
    protected function checkGoodsSn($goods_sn = '')
    {
        $goods_sn = $goods_sn ? $goods_sn : trim(I('post.goods_sn'));
        $shop_id = intval(I('post.shop_id', 0));
        $id = intval(I('post.id', 0));
        $map = array(
            'goods_sn' => $goods_sn,
            'shop_id' => $shop_id,
            'status' => 1
        );
        if($id > 0){
            $map['id'] = array('neq',$id);
        }

        if ($this->where($map)->find()){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 获取商品列表
     * @param array $map 商品筛选条件
     * @param bool $isLimit 是否分页
     * @param string $order 排序
     * @param int $page 当前页面
     * @param int $size 每页条数
     * @return array
     */
    public function getList($map = array(),$isLimit = true,$order = "id desc",$page=1,$size=10){
        $map['g.status'] = 1;
        //总数
        $count = $this->alias('g')->join('LEFT JOIN __PICTURE__ p ON g.goods_thums=p.id')->where($map)->count();
        if($isLimit){
            $list = $this->alias('g')->field('g.*,p.path,p.url')
                ->join('LEFT JOIN __PICTURE__ p ON g.goods_thums=p.id')
                ->where($map)->page("{$page},{$size}")->order($order)->select();
        }else{
            $list = $this->alias('g')->field('g.*,p.path,p.url')
                ->join('LEFT JOIN __PICTURE__ p ON g.goods_thums=p.id')
                ->where($map)->select();
        }
        if($list){
            foreach($list as $key=>$val){
                //处理图片数据
                $list[$key]['goods_thums_url'] = $val['url'] ? $val['url'] : __PICURL__ . $val['path'];
                //获取商品属性
                $list[$key]['attr_arr'] = $this->getAttr($val['id']);
                if($list[$key]['attr_arr']['pro']){
                    $list[$key]['stock_num'] = $this->getProStock($val['id']);
                }
            }
        }

        return array('list'=>$list,'count'=>$count);
    }

    /**
     * 获取商品详情
     * @param $id int 商品ID
     * @return bool|mixed
     */
    public function getOne($id){
        $goods_info = $this->where(array('id'=>$id,'status'=>1))->find();
        if(empty($goods_info)){
            $this->error = '商品不存在';
            return false;
        }
        if($goods_info['is_sale'] == 0){
            $this->error = '该商品已下架';
            return false;
        }
        //获取该商品所有图片
        $pic_list = M('picture')->field('id,url,path')->where(array('data_id'=>$id,'types'=>1,'status'=>1))->select();
        //对图片地址进行组装
        if($pic_list){
            foreach($pic_list as $key=>$val){
                //将商品的缩略图去掉
                if($goods_info['goods_thums'] == $val['id']){
                    continue;
                }else{
                    $pic_list[] =!empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
                }
            }
        }
        $goods_info['pics_img'] = $pic_list;
        //获取属性规格
        $goods_info['attr_arr'] = $this->getAttr($id);
        if($goods_info['attr_arr']['pro']){
            $goods_info['stock_num'] = $this->getProStock($id);
        }

        return $goods_info;
    }

    /**
     * 保存商品（新增或更新）
     * @param array $data 提交的数据
     * @return boolean  ture-成功，false-失败
     */
    public function saveGoods($data = array()) {
        if(empty($data)){
            $this->error = '提交数据不能为空';
            return false;
        }
        if(!$this->create($data)){
            $this->error = $this->getError();
            return false;
        }

        /* 添加或新增 */
        if(empty($data['id'])){ //新增数据
            $id = $this->add(); //添加基础内容
            if(!$id){
                $this->error = '添加商品失败';
                return false;
            }
            //处理商品属性和相册
            $this->disposeGoods($id);
        }else{ //更新数据
            $id = $data['id'];
            $res = $this->save(); //更新基础内容
            if(false === $res){
                $this->error = '更新商品出错';
                return false;
            }
            //处理商品属性和相册
            $this->disposeGoods($id);
        }

        //行为记录
        if($id){
            action_log('add_goods', 'goods', $id, MID);
        }
        return true;
    }

    /**
     * 删除商品
     * @param $id int 商品ID
     * @return bool
     */
    public function delGoods($id){
        //只做数据软删除
        $res = $this->where(array('goods_id'=>$id))->setField('status',0);
        if(false === $res){
            $this->error = '商品删除失败或已删除';
            return false;
        }
        return true;
    }

    /**
     * 获取商品属性
     * @param $id int 商品ID
     * @return mixed
     */
    public function getAttr($id) {
        /* 初始化数组 */
        $arr = array(
            'pro'=>array(), // 属性
            'spe'=>array()  // 规格
        );
        /* 获得商品的属性和规格 */
        $attr_list = M('goods_attr')
            ->field('a.id attr_id,a.name attr_name,a.inputtypes input_type,a.types attr_type,g.id goods_attr_id,g.attr_value,g.attr_price,g.attr_img,g.attr_thumb')
            ->join('g LEFT JOIN __GOODS_ATTRIBUTE__ a ON a.id=g.attr_id AND a.showdetial=1 AND a.status=1')
            ->where(array('g.goods_id'=>$id,'g.status'=>1))
            ->order('a.sort,g.id')
            ->select();

        if($attr_list) {
            foreach($attr_list as $key=>$val){
                $val['attr_value'] = str_replace("\n", '<br />', $val['attr_value']);
                if ($val['input_type'] == 0)//类型为手工录入的 为规格参数
                {
                    $arr['spe'][$val['attr_id']]['name']  = $val['attr_name'];
                    $arr['spe'][$val['attr_id']]['value'] = $val['attr_value'];
                }
                else
                {
                    $arr['pro'][$val['attr_id']]['attr_type'] = $val['attr_type'];
                    $arr['pro'][$val['attr_id']]['name']     = $val['attr_name'];
                    $arr['pro'][$val['attr_id']]['values'][] = array(
                        'id' => $val['goods_attr_id'],
                        'price' => number_format($val['attr_price'], 2, '.', ''),
                        'attr_img' => get_pic_url($val['attr_img']),
                        'attr_thumb' => get_pic_url($val['attr_thumb'])
                    );
                }
            }

        }
        return $arr;
    }

    /**
     * 添加商品货品属性数据
     * @param array $data
     */
    public function addGoodsProducts($data=array()){
        return M('goods_products')->add($data);
    }

    /**
     * 获取指定商品的属性数量总库存
     * @param $goods_id
     * @return int
     */
    public function getProStock($goods_id) {
        //仓库库存
        $stock = M('goods_products')->where(array('goods_id'=>$goods_id,'status'=>1))->sum('product_number');
        return intval($stock);
    }

    /**
     * 处理商品属性和图片
     * @param int $gid 商品的ID
     */
    private function disposeGoods($gid=0){
        //处理属性
        $this->doattr($gid);
        //处理图片
        $this->movepic($gid);
    }

    /**
     * 处理商品属性
     * @param $id 商品ID
     */
    private function doattr($id){
        /* 处理属性 */
        if ((isset($_POST['attr_id_list']) && isset($_POST['attr_value_list'])) || (empty($_POST['attr_id_list']) && empty($_POST['attr_value_list'])))
        {
            //初始化数组
            $data_insert=array();//添加数组
            $date_delete = array();//删除数组

            // 取得原有的属性值
            $arr = M("goods_attr")->where(array('goods_id'=>$id,'status'=>1))->field("id")->select();
            foreach( $arr as $k=>$v ){
                $date_delete[$v['id']]= $v['id'];
            }

            // 循环现有的，根据原有的做相应处理
            if(isset($_POST['attr_id_list']))
            {
                foreach ($_POST['attr_id_list'] AS $key => $attr_id)
                {
                    $gattrid = $_POST['attr_goodattr_id_list'][$key];
                    $attr_value = trim($_POST['attr_value_list'][$key]);
                    $attr_price = floatval($_POST['attr_price_list'][$key]);
                    $attr_img = intval($_POST['attr_img_list'][$key]);
                    $attr_thumb = intval($_POST['attr_thumb_list'][$key]);

                    if (!empty($attr_value) && $attr_value!="undefined" &&  $attr_value!="false")
                    {
                        if ( $gattrid )
                        {
                            // 如果原来有直接更新
                            if( isset($date_delete[$gattrid]) ){
                                $data=array();
                                $data["id"] = $gattrid;
                                $data["attr_id"]=$attr_id;
                                $data["attr_value"] = $attr_value;
                                $data["attr_price"] = $attr_price;
                                $data["attr_img"] = $attr_img;
                                $data["attr_thumb"] = $attr_thumb;
                                //更新属性
                                M("goods_attr")->save($data);
                                unset($date_delete[$gattrid]);
                            }
                        }
                        else
                        {
                            $data=array();
                            $data["goods_id"]=$id;
                            $data["attr_id"]=$attr_id;
                            $data["attr_value"] = $attr_value;
                            $data["attr_price"] = $attr_price;
                            $data["attr_img"] = $attr_img;
                            $data["attr_thumb"] = $attr_thumb;
                            $data_insert[]=$data;
                        }
                    }
                }
            }
            //删除属性
            if($date_delete){
                $map=array();
                $map["id"]=array("in",$date_delete);
                M("goods_attr")->where($map)->setField('status',0);//做软删除
            }
            //增加属性
            if($data_insert) M("goods_attr")->addAll($data_insert);
        }
    }

    /**处理商品图片
     * @param $gid 商品ID
     */
    private function movepic($gid){
        if(isset($_POST['pic_list']) && !empty($_POST['pic_list'])){
            //取得原有相册ID
            $pic_ids_arr = M('picture')->where(array('data_id'=>$gid,'types'=>1))->getField('id',true);
            if($pic_ids_arr){
                $date_delete = array();//预定义删除图片的数组
                $data_insert = array();//预定义添加图片的数组

                foreach( $pic_ids_arr as $k=>$pid ){
                    $date_delete[$pid]= $pid;
                }
                //循环处理数据
                foreach($_POST['pic_list'] as $key=>$pic_id){
                    if(isset($date_delete[$pic_id])){
                        unset($date_delete[$pic_id]);
                    }else{
                        $data_insert[] = $pic_id;
                    }
                }
                //将商品ID绑定到图片上
                if($data_insert){
                    M('picture')->where(array('id'=>array('in',$data_insert)))->setField('data_id',$gid);
                }
                //删除图片
                if($date_delete){
                    M('picture')->where(array('id'=>array('in',$date_delete)))->setField('status','0');
                }
            }else{
                //将商品ID绑定到图片上
                M('picture')->where(array('id'=>array('in',$_POST['pic_list'])))->setField('data_id',$gid);
            }
        }
    }
}
