<?php
namespace Common\Model;
use Think\Model\ViewModel; 
/**
 * 评论基础视图
 */
class CommentViewModel extends ViewModel{
    public $viewFields = array(
	'comment'  =>array('id','goodid','create_time','goodscore','pics','tag','score','servicescore','deliveryscore','content','uid','ifcheck','status','domainid','brandid','_type'=>'LEFT'),
	'document' =>array('status'=>'gstatus','_on'=>'document.id=comment.goodid'),
	'ucenter_member'=>array('face','username', '_on'=>'comment.uid=ucenter_member.id' ),
    ); 
}
