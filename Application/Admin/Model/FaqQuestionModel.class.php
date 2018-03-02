<?php
namespace Admin\Model;
use Think\Model;
use Think\Segment;
/**
 * 问答系统-问题模型
 * @author wangcheng <253490851@qq.com>
 */

class FaqQuestionModel extends Model {
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('keywords', 'require', '标签不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('content', 'require', '内容不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('domainid', 'require', '频道不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('pykeywords', 'getPinyin', self::MODEL_BOTH, 'callback'),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH), 
        array('status', '1', self::MODEL_INSERT),
    );
    
    protected function getPinyin(){ 
        $str=I("keywords");
        $str =str_replace(","," ",$str);
        $str2=I("title"); 
        $segment = new Segment(); 
        $s = $segment->convert_to_py($str." ".$str2);
        return $s;
    }
}
