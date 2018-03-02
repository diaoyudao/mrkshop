<?php
namespace Admin\Controller; 
/**
 * 后台广告管理控制器
 * @author qchlian
 */
class AdvertisingPageController extends AdminController {
    public function _initialize(){ 
        parent::_initialize();
	define ( "__UPLOADS__", __ROOT__."/Uploads/");
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
        $this->assign("domainid",$domainid);
        //搜索条件
        $title =   I('title');
        if(!empty($title)){
           $map["title"] = array("like","%" . $title . "%");
        } 
	// 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']); 
    }
    
    function imgdetail($id = 0){
	$name=CONTROLLER_NAME;
	$model = D ( $name );
	$info = array();
	/* 获取数据 */
	$info = $model->find($id); 
	if(false === $info){
	    $this->error('获取配置信息错误');
	}
	$this->assign('info', $info);
	$this->display();
    }
      
    public function _before_add(){
	if(IS_POST){
	    $_POST['status']=1;
	    $advpositionicon = I("advpositionicon");
	    $advposition = I("advposition");
	    $data=array();
	    foreach( $advpositionicon as $k=>$v){
		if($v && $advposition[$k]){
		    $d=array();
		    $d["icon"]=$v;
		    $d["map"]=$advposition[$k];
		    array_push($data,$d);
		}
	    }
	    $_POST["content"]=json_encode($data); 
	}else{
	    //获取所有频道
	    $channellist=M('Subdomain')->where("status >=1")->getField("id,name"); 
	    $this->assign("channellist",$channellist);
	    $domainid = I("domainid",0); 
	    $this->assign("domainid",$domainid);
	}
    }
    
    public function _before_edit(){
	if(IS_POST){
	    $advpositionicon = I("advpositionicon");
	    $advposition = I("advposition");
	    $data=array();
	    foreach( $advpositionicon as $k=>$v){
		if($v && $advposition[$k]){
		    $d=array();
		    $d["icon"]=$v;
		    $d["map"]=$advposition[$k];
		    array_push($data,$d);
		}
	    }
	    $_POST["content"]=json_encode($data); 
	}
    }
    
    /* 编辑 */
    public function _after_edit( &$info ){
	if(IS_POST){ 
	   
	}else{
	    //获取所有频道
	    $channellist=M('Subdomain')->where("status >=1")->getField("id,name"); 
	    $this->assign("channellist",$channellist);
	    //获取所有品牌
	    $channelbrandlist=M('SubdomainBrand')->where("status >=1")->getField("id,name"); 
	    $this->assign("channelbrandlist",$channelbrandlist);
	    $domainid = I("domainid",0); 
	    $this->assign("domainid",$domainid); 
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
	    $cv['url']   =   'AdvertisingPage/index?domainid='.$cv['id'];
	    if($cv["id"]==$domainid){ 
		$cv["current"]=1; 
	    }
	}
	$this->assign('domainid', $domainid);
        $this->assign('nodes', $channel);
    } 
    
    /**
     * 上传图片
     * @author qchlian
     */
    public function uploadImg(){
        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => ''); 
        /* 调用文件上传组件上传文件 */
        $Picture = D('AdvertisingPage');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
	$setting = array('mimes'    => '','maxSize'=> 0,'exts'=> 'jpg,gif,png,jpeg', 'autoSub'=> true, 'subName'=>'', 'rootPath' => './Uploads/','savePath' =>'','saveName' => array('uniqid', ''),'saveExt'=> '', 'replace'=> false, 'hash'=> true,'callback' => false);
	 
	$domainid = I("domainid","");
	$setting["domainid"]=$domainid;
	if( $domainid ){
	    $setting["subName"]="Adimgpage/".$domainid;
	}
	
        $info = $Picture->upload(
            $_FILES,
            $setting,
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        ); 

        /* 记录图片信息 */
        if($info){
            $return['status'] = 1;
            $return = array_merge($info['download'], $return);
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }

        /* 返回JSON数据 */
	ob_end_clean();
        $this->ajaxReturn($return);
    }
}