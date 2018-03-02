<?php
namespace Admin\Model;
use Think\Model;
/**
 * 分类模型
 */
class AdvsModel extends Model{	
	/* 自动完成规则 */
	protected $_auto = array(
	    array('create_time', 'getCreateTime', self::MODEL_BOTH,'callback'),
	    array('end_time', 'getEndTime', self::MODEL_BOTH,'callback'),
	);
	 
	protected function _after_find(&$result,$options) {
		$sing = M('advertising')->find($result['position']);
		$result['positiontext'] = $sing['title'];
		$result['statustext'] =  $result['status'] == 0 ? '禁用' : '正常';
		$result['create_time'] = date('Y-m-d H:i', $result['create_time']);
		if($result['end_time']){
			$result['end_time'] = date('Y-m-d H:i', $result['end_time']);
		}else{
			$result['end_time'] = '';
		}
	} 
	protected function _after_select(&$result,$options){
		foreach($result as &$record){
			$this->_after_find($record,$options);
		}
	} 
	/* 时间处理规则 */
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