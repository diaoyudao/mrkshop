<?php

namespace Common\Model;

/**
 * 品牌模型
 */
class GoodsBrandModel extends CommonModel{

    protected $_validate = array(
        array('brand_name', 'require', '品牌名不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('brand_name', 'checkBrandName', '品牌名已经存在', self::VALUE_VALIDATE , 'callback', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
    );

    /**
     * 检查名称是否重复
     * @param string $brand_name 商品名称
     * @return true无重复，false已存在
     */
    protected function checkBrandName($brand_name = '')
    {
        $name = $brand_name ? $brand_name :trim(I('post.brand_name'));
        $id = intval(I('post.id', 0));
        $map = array(
            'brand_name' => $name,
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
     * 获取品牌详细信息
     * @param  milit   $id ID或名称
     * @param  boolean $field 查询字段
     * @return array
     */
    public function info($id, $field = true){
        /* 获取信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过名称查询
            $map['brand_name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }

    /**
     * 获取品牌列表
     * @param $field string 查询字段
     * @param $order string 排序
     * @param $map array 筛选条件
     * @return array
     */
    public function getList($field = true,$order="sort desc",$map = array()){
        /* 获取所有商品品牌 */
        $map['status']  = 1;
        $list = $this->field($field)
            ->join('b LEFT JOIN __PICTURE__ p ON p.id=b.brand_logo')
            ->where($map)->order($order)->select();

        //处理logo
        if($list){
            foreach($list as $key=>$val){
                if($val['brand_logo']){
                    $list[$key]['brand_logo_url'] = get_pic_url($val['brand_logo']);
                }
            }
        }
        return $list;
    }


    /**
     * 保存品牌信息
     * @return boolean 更新状态
     */
    public function saveData(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }

        /* 添加或更新数据 */
        if(empty($data['id'])){
            $res = $this->add();
        }else{
            $res = $this->save();
        }

        //更新品牌缓存
//        S('sys_brand_list', null);

        //记录行为
        action_log('save_Brand', 'goods_brand', $data['id'] ? $data['id'] : $res, MID);
        if($res === false){
            $this->error = "品牌保存失败";
            return false;
        }else{
            return true;
        }
    }

    
}
