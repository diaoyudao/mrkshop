<?php
namespace Admin\Model;
use Think\Model;
/**
 * 网址导航模型
 * @author wangcheng
 */
class SitemapModel extends Model{
    protected $_validate = array( 
        array('name', 'require', '链接名称不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH), 
        array('url',  'require', '链接地址不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
	array('url', '/^http:\/\/[0-9a-z\.\/\-\_]+\.([0-9a-z\.\/\-\_]+)$/', '链接地址不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH), 
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_BOTH),
    ); 
    /**
     * 更新分类信息
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
        return $res;
    } 
}
