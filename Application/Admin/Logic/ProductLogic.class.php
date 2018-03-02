<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Logic;

/**
 * 文档模型子模型 - 文章模型
 */
class ProductLogic extends BaseLogic{
    /* 自动验证规则 */
    protected $_validate = array( 
        array('pcode', 'require', '产品编号不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('content', 'getContent', '详细介绍不能为空！', self::MUST_VALIDATE , 'callback', self::MODEL_BOTH),
    );
    
    /* 自动完成规则 */
    protected $_auto = array( 
        array('promote_start_time', 'getPromoteStartTime', self::MODEL_BOTH,'callback'),
        array('promote_end_time', 'getPromoteEndTime', self::MODEL_BOTH,'callback'),  
    );
    protected function getPromoteStartTime(){
        $promote_start_time  =   I('post.promote_start_time');
        return $promote_start_time?strtotime($promote_start_time):time();
    }
    protected function getPromoteEndTime(){
        $promote_end_time    =   I('post.promote_end_time');
        return $promote_end_time?strtotime($promote_end_time):time();
    }

    /**
     * 获取文章的详细内容
     * @return boolean
     * @author huajie <banhuajie@163.com>
     */
    protected function getContent(){
        $type = I('post.type');
        $content = I('post.content');
        if($type > 1){//主题和段落必须有内容
            if(empty($content)){
                return false;
            }
        }else{  //目录没内容则生成空字符串
            if(empty($content)){
                $_POST['content'] = ' ';
            }
        }
        return true;
    }

}
