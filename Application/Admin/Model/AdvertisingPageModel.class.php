<?php
namespace Admin\Model;
use Think\Model;
use Think\Upload;
/**
 * 广告位模型
 * @author wangcheng
 */ 
class AdvertisingPageModel extends Model{
	protected $_validate = array( 
		array('title', 'require', '位置名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
		array('imgs', 'require', '图片必须上传', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH), 
	);
    
	protected $_auto = array( 
	    array('status', '1', self::MODEL_BOTH),
	);
	protected function _after_find(&$result,$options) {
	    if($result["content"]){
		$result["content"]=json_decode($result["content"],true);
	    }
	} 
	//
	//protected function _after_select(&$result,$options){
	//	foreach($result as &$record){
	//		$this->_after_find($record,$options);
	//	}
	//}
	
    /**
     * 文件上传
     * @param  array  $files   要上传的文件列表（通常是$_FILES数组）
     * @param  array  $setting 文件上传配置
     * @param  string $driver  上传驱动名称
     * @param  array  $config  上传驱动配置
     * @return array           文件上传成功后的信息
     */
    public function upload($files, $setting, $driver = 'Local', $config = null){ 
	if( !$setting["domainid"] ){
	    $this->error = "请选择相关频道";
            return false;
	}
        /* 上传文件 */ 
	$setting['removeTrash'] = array($this, 'removeTrash');
        $Upload = new Upload($setting, $driver, $config);
        $info   = $Upload->upload($files);
        if($info){ //文件上传成功，记录文件信息
            foreach ($info as $key => &$value) {
                /* 已经存在文件记录 */
                if(isset($value['id']) && is_numeric($value['id'])){
                    continue;
                } 
                $value['path'] = $value['savepath'].$value['savename'];
            }
            return $info; //文件上传成功
        } else {
            $this->error = $Upload->getError();
            return false;
        }
    }
}