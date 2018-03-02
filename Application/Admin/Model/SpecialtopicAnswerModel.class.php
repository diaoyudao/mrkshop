<?php
namespace Admin\Model;
use Think\Model;

/**
 * 问答系统-回复模型
 * @author wangcheng <253490851@qq.com>
 */

class SpecialtopicAnswerModel extends Model {
    protected $_validate = array(
        array('doctorid', 'require', '回答人不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('content', 'require', '回复内容不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('domainid', 'require', '频道不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH), 
        array('status', '1', self::MODEL_INSERT),
    );

}
