<?php
namespace Admin\Model;
use Think\Model;

/**
 * 专题文章模型
 * @author wangcheng <253490851@qq.com>
 */

class SpecialtopicArticleModel extends Model {
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,80', '标题长度不能超过80个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH), 
        array('status', '1', self::MODEL_INSERT),
    ); 
}
