<?php
/**
 * 限时促销
 */
namespace Admin\Model;
use Think\Model;


class PromotionXianshiModel extends Model{

    protected $tableName = 'p_xianshi';
    protected $_validate = array(
        array('xianshi_name', 'require', '活动名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('start_time', 'require', '开始时间不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('end_time', 'require', '结束时间不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('discount', 'require', '默认折扣不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    
    	
    	
    );

  protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 获取促销活动详细信息
     * @param  milit   $id 促销活动ID或标识
     * @param  boolean $field 查询字段
     * @return array     促销活动信息
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function info($id,$map=array(), $field = true){
        /* 获取促销活动信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['xianshi_id'] = $id;
        } else { //通过标识查询
            $map['xianshi_name'] = $id;
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
    public function lists($map = array(), $order = 'xianshi_id DESC', $field = true){
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
        if(empty($data['xianshi_id'])){
            $res = $this->add();
        }else{
            $res = $this->save();
        }

        //记录行为
        action_log('PromotionXianshi', 'add', $data['id'] ? $data['id'] : $res, UID);

        return $res;
    }

    
}
