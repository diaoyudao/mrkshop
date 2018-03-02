<?php
namespace Admin\Model;
use Think\Model\ViewModel;

/**
 * 问答系统-回复视图模型
 * @author wangcheng <253490851@qq.com>
 */
class FaqAnswerViewModel extends ViewModel {
    public $viewFields = array(
        'FaqAnswer'=>array('id','questionid','doctorid','ifcheck','content','create_time','update_time','status'),
        'FaqDoctor'=>array('name','nickname','_on'=>'FaqAnswer.doctorid=FaqDoctor.id'), 
    );
}
