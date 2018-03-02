<?php
namespace Admin\Model;
use Think\Model\ViewModel;
use Think\Page;
/**
 * 问答系统-问题模型
 * @author wangcheng <253490851@qq.com>
 */

class MemberAdminViewModel extends ViewModel { 
    	public $viewFields = array(
		'ucenter_member'=>array('id','username' ,'email' ,'mobile' ,'reg_time' ,'reg_ip' ,'last_login_time' ,'last_login_ip' ,'update_time' ,'status' ,'face' ,'_type'=>'LEFT'),
		'ucenter_admin'=>array('id','member_id','status','_on'=>'ucenter_admin.member_id=ucenter_member.id','_type'=>'LEFT'),
                'member'  =>array('uid' ,'nickname','realname','account' ,'paykey' ,'sex' ,'birthday','qq','score','login' ,'reg_ip' ,'reg_time' ,'last_login_ip' ,'last_login_time' ,'status','_on'=>'member.uid=ucenter_member.id','_type'=>'LEFT'),
	); 
}

