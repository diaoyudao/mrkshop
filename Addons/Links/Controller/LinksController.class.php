<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Addons\Links\Controller;
use Admin\Controller\AddonsController; 

class LinksController extends AddonsController{
	/* 添加友情连接 */
	public function add(){
		//获取所有频道
		$channellist=M('Subdomain')->where("status >=1")->getField("id,name"); 
		$this->assign("channellist",$channellist);
		$current = U('/addons/adminlist/name/Links');
		$this->assign('current',$current);
		$this->display(T('Addons://Links@Links/edit'));
	}
	
	/* 编辑友情连接 */
	public function edit(){
		$id     =   I('get.id','');
		$current = U('/addons/adminlist/name/Links');
		$detail = D('Addons://Links/Links')->detail($id);
		$this->assign('info',$detail);
		$this->assign('current',$current);
		//获取所有频道
		$channellist=M('Subdomain')->where("status >=1")->getField("id,name"); 
		$this->assign("channellist",$channellist);
		$this->display(T('Addons://Links@Links/edit'));
	}
	
	/* 禁用友情连接 */
	public function forbidden(){
		$id     =   I('get.id','');
		if(D('Addons://Links/Links')->forbidden($id)){
			$this->success('成功禁用该友情连接',Cookie('__forward__'));
		}else{
			$this->error(D('Addons://Links/Links')->getError());
		}
	}
	
	/* 启用友情连接 */
	public function off(){
		$id     =   I('get.id','');
		if(D('Addons://Links/Links')->off($id)){
			$this->success('成功启用该友情连接',Cookie('__forward__'));
		}else{
			$this->error(D('Addons://Links/Links')->getError());
		}
	}
	
	/* 删除友情连接 */
	public function del(){
		$id     =   I('get.id','');
		if(D('Addons://Links/Links')->del($id)){
			$this->success('删除成功',Cookie('__forward__'));
		}else{
			$this->error(D('Addons://Links/Links')->getError());
		}
	}
	
	/* 更新友情连接 */
	public function update(){
		$_POST["isbold"]=I("isbold",0);
		$res = D('Addons://Links/Links')->update();
		if(!$res){
			$this->error(D('Addons://Links/Links')->getError());
		}else{
			if($res['id']){
				$this->success('更新成功',Cookie('__forward__'));
			}else{
				$this->success('新增成功',Cookie('__forward__'));
			}
		}
	}
}
