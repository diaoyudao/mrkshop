<?php
namespace Admin\Model;
use Think\Model;
/**
 * 搭配组合模型
 * 
 */
class ProductsGroupModel extends Model {
    protected $_validate = array(
        array('price', 'require', '商品不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        /*array('subtitle', 'require', '副标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('preview', 'require', '导语不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('des', 'require', '描述不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),*/
    );

    protected $_auto = array(
        array('uniongood', 'arr2str', self::MODEL_BOTH, 'function'),
        array('uniongood', null, self::MODEL_BOTH, 'ignore'),
        array('create_time', 'getCreateTime', self::MODEL_BOTH,'callback'),
        array('end_time', 'getEndTime', self::MODEL_BOTH,'callback'),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );
    
    protected function getCreateTime(){
		$create_time    =   I('post.create_time');
		return $create_time?strtotime($create_time):NOW_TIME;
	}
    /* 时间处理规则 */
	protected function getEndTime(){
		$end_time    =   I('post.end_time');
		return $end_time?strtotime($end_time):0;
	}

}
