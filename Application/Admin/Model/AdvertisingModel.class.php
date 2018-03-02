<?php
namespace Admin\Model;
use Think\Model;
/**
 * 广告位模型
 * @author wangcheng
 */ 
class AdvertisingModel extends Model{
	protected $_validate = array( 
		array('title', 'require', '位置名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
		array('mark', 'require', '标识不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array(array('domainid','brandid','mark'), '', '同一频道或同一品牌下标识已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH), 
	);
    
	protected $_auto = array( 
	    array('status', '1', self::MODEL_BOTH),
	);
	protected function _after_find(&$result,$options) {
		$typetext = array(1=>'单图',2=>'多图',3=>'文字',4=>'代码');
		$result['typetext'] = $typetext[$result['type']];
		$result['statustext'] =  $result['status'] == 0 ? '禁用' : '正常';
	}	
	
	protected function _after_select(&$result,$options){
		foreach($result as &$record){
			$this->_after_find($record,$options);
		}
	}
		
	/**
	 * 新增或更新一个文档
	 * @return boolean fasle 失败 ， int  成功 返回完整的数据
	 */
	public function update(){
		//判断同一个频道 或者同一品牌下 标示是否重复
		/* 获取数据对象 */
		$data = $this->create();
		if(empty($data)){
			return false;
		}
		/* 添加或新增基础内容 */
		if(empty($data['id'])){ //新增数据
			$id = $this->add(); //添加基础内容
			if(!$id){
				$this->error = '新增广告内容出错！';
				return false;
			}
		} else { //更新数据
			$status = $this->save(); //更新基础内容
			if(false === $status){
				$this->error = '更新广告内容出错！';
				return false;
			}
		}
		//内容添加或更新完成
		return $data;
	}
	
	/* 禁用 */
	public function forbidden($id){
		return $this->save(array('id'=>$id,'status'=>'0'));
	}
	
	/* 启用 */
	public function off($id){
		return $this->save(array('id'=>$id,'status'=>'1'));
	}
	
	/* 删除 */
	public function del($id){
		return $this->delete($id);
	}	
	
	/* 获取编辑数据 */
	public function detail($id){
		$data = $this->find($id);
		return $data;
	}	
}