<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 用户模型
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

class MemberLevelModel extends Model {

     protected $_validate = array(
        array('level_name', 'require', '会员等级名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('discount', 'require', '等级折扣不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('level', 'require', '会员等级不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('member_type', 'require', '会员类型不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    	
    );
      protected $_auto = array(
 
        array('createtime', NOW_TIME, self::MODEL_INSERT),
       
    );
      
      
    /**
     * 更新会员等级
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
        action_log('Member_Level', 'add', $data['id'] ? $data['id'] : $res, UID);

        return $res;
    }

    public function lists($map, $order = 'sort DESC', $field = true){
//        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

    
  
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
            $map['level_name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }

}
