<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 1010422715@qq.com All rights reserved.
// +----------------------------------------------------------------------
// | author 烟消云散 <1010422715@qq.com>
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

/**
 * 货架格子类
 */
class ExpressCabintGridModel extends Model{

    protected $_validate = array(
       
      
        array('grid_name', 'require', '货架格子名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    
    	
    	
    );

  protected $_auto = array(
 
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
       
    );

    /**
     * 获取优惠券详细信息
     * @param  milit   $id 优惠券ID或标识
     * @param  boolean $field 查询字段
     * @return array     优惠券信息
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function info($id, $field = true){
        /* 获取优惠券信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['grid_name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }
    
    /**
     * 
     * @param type $map   arrray() 条件
     * @param type $order 排序
     * @param type $field 字段
     * @return type
     */
    public function lists($map = array(), $order = 'id DESC', $field = true){
        return $this->field($field)->where($map)->order($order)->select();
    }




    /**
     * 更新货架信息
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

        //记录行为
        action_log('expresscabintgrid', 'add/update', $data['id'] ? $data['id'] : $res, UID);

        return $res;
    }

    
}
