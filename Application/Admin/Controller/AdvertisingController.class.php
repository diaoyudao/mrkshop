<?php
namespace Admin\Controller; 
/**
 * 后台广告管理控制器
 * @author qchlian
 */
class AdvertisingController extends AdminController {
    public function _initialize(){ 
        parent::_initialize(); 
    }
    /**
     * 问题列表
     * @author wangcheng <253490851@qq.com>
     */ 
    public function _filter( &$map){ 
        $status =   I('status');
        if(!empty($status)){
            $map['status'] = $status;
        }else{
            $map['status'] = array('gt', -1);
        } 
	$domainid = I("domainid",0); 
	if($domainid){
            $map['domainid'] = $domainid;
        }
        $this->assign("domainid",$domainid);
        //搜索条件
        $title =   I('title');
        if(!empty($title)){
           $map["title"] = array("like","%" . $title . "%");
        } 
	// 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->getMenu(); 
    }
    
    public function imgindex(){
	$map=array();
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        } 
        $model = D ("advertising_page"); 
        $arr=array();
        if (! empty ( $model )) {
            $list = $this->lists($model, $map);
            if (method_exists ( $this, '_after_list' )) {
                $this->_after_list ( $list );
            }
            $this->assign('list', $list);
        }
        $this->display();
    }
    
      
    public function _before_add(){
	if(IS_POST){
	    $_POST['status']=1; 
	}else{
	    //获取所有频道
	    $channellist=M('Subdomain')->where("status >=1")->getField("id,name"); 
	    $this->assign("channellist",$channellist);
	    $domainid = I("domainid",0); 
	    $this->assign("domainid",$domainid);
	    $this->getMenu();
	}
    }
    
    /* 编辑 */
    public function _before_edit(){
	//获取所有频道
	$channellist=M('Subdomain')->where("status >=1")->getField("id,name"); 
	$this->assign("channellist",$channellist);
	//获取所有品牌
	$channelbrandlist=M('SubdomainBrand')->where("status >=1")->getField("id,name"); 
	$this->assign("channelbrandlist",$channelbrandlist);
	$domainid = I("domainid",0); 
	$this->assign("domainid",$domainid);
	$this->getMenu();
    }
    
    /**
     * 左侧导航
     * @author wangcheng <253490851@qq.com>
     */
    protected function getMenu(){
	$channel=   M('Subdomain')->where(array('status'=>1 ))->field('id,name')->select(); 
        //是否展开分类
        $hide_cate = true;
        $domainid = I("domainid"); 
	foreach($channel as $ck=> &$cv){
	    $cv['url']   =   'Advertising/index?domainid='.$cv['id'];
	    if($cv["id"]==$domainid){ 
		$cv["current"]=1; 
	    }
	}
	$this->assign("domainid",$domainid);
        $this->assign('nodes', $channel);
    } 
}