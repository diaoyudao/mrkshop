<?php
namespace Admin\Model;
use Think\Model;

/**
 * 问答系统-医生模型
 * @author wangcheng <253490851@qq.com>
 */

class FaqDoctorModel extends Model {
    protected $_validate = array( 
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH), 
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH), 
        array('status', '1', self::MODEL_INSERT),
    );

}
