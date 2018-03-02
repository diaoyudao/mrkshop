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

class MemberBankModel extends Model {

     protected $_validate = array(
        array('bankname', 'require', '银行名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('bankcode', 'require', '银行账号不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('bankcode', '16,20', '银行账号一般在[16~~20]位数字', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('bankuser', 'require', '银行账户不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    	
    );
      protected $_auto = array(
 
        array('create_time', 'getCreateTime', self::MODEL_BOTH,'callback'),
        array('update_time', NOW_TIME, self::MODEL_INSERT),
       
    );
      
      
   protected function getCreateTime(){
        $create_time    =   I('post.create_time');
        return $create_time?strtotime($create_time):NOW_TIME;
    }
      
    /**
     * 更新
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
        action_log('Member_Bank', 'add', $data['id'] ? $data['id'] : $res, UID);

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
