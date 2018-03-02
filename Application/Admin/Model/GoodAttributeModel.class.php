<?php
namespace Admin\Model;
use Think\Model;
/**
 * 自定义商品属性模型
 * @author wangcheng <253490851@qq.com>
 */
class GoodAttributeModel extends Model {
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        //array('name', 'require', '标识不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        //array('name', '/^[_a-zA-Z]{1,30}$/', '标识只能为英文字母,下划线组成(1-30位)', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        //
        array(array('domainid','brandid','name'), '', '同频道或同品牌下属性名称已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH), 
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );

}
