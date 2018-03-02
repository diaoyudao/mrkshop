<?php
namespace Admin\Controller; 
/**
 * 后台广告管理控制器
 * @author qchlian
 */
class AdvsController extends AdminController {
    public function _initialize(){ 
        parent::_initialize();
	$this->getMenu(); 
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
         
        //搜索条件
        $title =   I('title');
        if(!empty($title)){
           $map["title"] = array("like","%" . $title . "%");
        } 
	$mark=I("mark");
	$position = I("position",0);
	if($mark){
	    $advertising = M ("advertising");
	    $advmap["mark"]=$mark;
	    $advmap["status"]=array("gt",-1);
	    $advmap['domainid'] = $domainid;
	    $advertisinginfo=$advertising->where($advmap)->find();
	    if($advertisinginfo){
		$map["position"]= $advertisinginfo["id"];
		$this->assign('advertisinginfo', $advertisinginfo);
	    }else{
		$map["position"]="";
	    }
	}
	if($position){
	    $map["position"]=$position;
	    $advertising = M('advertising')->find($position); 
	    $this->assign('advertisinginfo',$advertising);
	}
	
	$imgpageid = I("imgpageid");
	$this->assign('imgpageid', $imgpageid);
	$imginfo = D("AdvertisingPage")->find($imgpageid); 
	$this->assign('imginfo', $imginfo);
	
	// 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']); 
    }
    
    public function _before_add(){
	if(IS_POST){
	    $_POST['status']=1; 
	}else{
	    $position=I("position");
	    $singcurrent = M('advertising')->find( $position );
	    $map=array();
	    $domainid = I("domainid",0);  
            $map['domainid'] = $domainid;
	    $map['status'] =1;
	    $sing = M('advertising')->where( $map )->select();
	    $this->assign('singcurrent',$singcurrent);
	    $this->assign('sing',$sing);
	    $info["position"]=$position;
	    $this->assign('info',$info); 
	}
    }
    
    /* 编辑 */
    public function _after_edit( &$info){
	$map=array();
	$singcurrent = M('advertising')->find($info['position']);
	$info['type'] = $singcurrent['type'];
	$this->assign('singcurrent',$singcurrent); 
	$map['domainid'] = $singcurrent['domainid'];
	$map['status'] =1;
	$sing = M('advertising')->where( $map )->select(); 
	$this->assign('sing',$sing); 
	$cover = M('picture')->find($info['advspic']); 
	$info['path'] = $cover['path']; 
    }
    
    public function _before_edit(){
	if( IS_POST ){
	    $_POST["showtitle"]=I("showtitle",0);
	}
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