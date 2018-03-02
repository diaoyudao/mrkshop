<?php

namespace Admin\Model;
use Think\Model;

/**
 * 品牌模型
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class BrandModel extends Model{

    protected $_validate = array(
    	
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
       
    );

    /**
     * 获取品牌详细信息
     * @param  milit   $id ID或标识
     * @param  boolean $field 查询字段
     * @return array  
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function info($id, $field = true){
        /* 获取信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }

    /**
     * 获取品牌树，指定品牌则返回指定品牌极其子品牌，不指定则返回所有品牌树
     * @param  integer $id    品牌ID
     * @param  boolean $field 查询字段
     * @return array          品牌树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */


    /**
     * 更新品牌信息
     * @return boolean 更新状态
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function update(){
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
        S('sys_brand_list', null);

        //记录行为
        action_log('update_Brand', 'brand', $data['id'] ? $data['id'] : $res, UID);

        return $res;
    }

    
}
