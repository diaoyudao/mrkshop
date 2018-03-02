<?php
namespace Common\Model;
use Think\Model\ViewModel;
use Think\Page;
/**
 * 问答系统-问题模型
 * @author wangcheng <253490851@qq.com>
 */

class MemberViewModel extends ViewModel { 
    	public $viewFields = array(
		'ucenter_member'=>array('id','username' ,'email' ,'mobile' ,'reg_time' ,'reg_ip' ,'last_login_time' ,'last_login_ip' ,'update_time' ,'status','is_admin' ,'face' ,'_type'=>'LEFT'),
                'member'  =>array('uid' ,'nickname','realname','account' ,'paykey' ,'sex','code','memo','address' ,'birthday','qq' ,'member_type' ,'member_level_id','member_agent_id','score','login' ,'reg_ip'  ,'last_login_ip' ,'last_login_time' ,'status','_on'=>'member.uid=ucenter_member.id','_type'=>'LEFT'),
	); 
        
        
        
        
        /**
         *获取会员信息 
         */
        public function info($map){
            return $this->where($map)->find();
        }
}

