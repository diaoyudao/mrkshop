<?php
namespace Common\Model;

/**
 * 商品属性模型
 */
class GoodAttributeModel extends CommonModel {
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('brand_name', 'checkAttrName', '属性名称已经存在', self::VALUE_VALIDATE , 'callback', self::MODEL_BOTH)
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 检查名称是否重复
     * @param string $attr_name 商品名称
     * @return true无重复，false已存在
     */
    protected function checkAttrName($attr_name = '')
    {
        $name = $attr_name ? $attr_name :trim(I('post.attr_name'));
        $id = intval(I('post.id', 0));
        $map = array(
            'attr_name' => $name,
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
     * 获取商品属性信息
     * @param  milit   $id ID或标识
     * @param  boolean $field 查询字段
     * @return array
     */
    public function info($id, $field = true){
        /* 获取信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过名称查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }

    /**
     * 获取列表
     * @param $field string 查询字段
     * @param $order string 排序
     * @param $map array 筛选条件
     * @return array
     */
    public function getList($field = true,$order="sort desc",$map = array()){
        $map['status']  = 1;
        $list = $this->field($field)->where($map)->order($order)->select();
        return $list;
    }

    /**
     * 保存商品属性信息
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

        //记录行为
        action_log('save_goods_attribute', 'goods_attribute', $data['id'] ? $data['id'] : $res, MID);
        if($res === false){
            $this->error = "商品属性保存失败";
            return false;
        }else{
            return true;
        }
    }

}
