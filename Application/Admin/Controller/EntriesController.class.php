<?php
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page;

/**
 * 词条控制器
 * @author wangcheng
 */
class EntriesController extends AdminController {
    /**
     * 属性列表
     * @author wangcheng
     */
    /**
     * 专题列表
     * @author wangcheng <253490851@qq.com>
     */ 
    public function _filter( &$map){
        $map['status']=array('gt', -1);
        $cate_id = I("cate_id");
        $domainid = I("domainid");
        if($cate_id){
	    $map['category_id'] = $cate_id;
	    //by wangcheng 循环找出子分类的id
	    $cate_ids_array   = get_stemma( $cate_id,M("category"), 'id');
	    if($cate_ids_array){
		$cate_ids=array();
		foreach($cate_ids_array as $k=>$v){
		    $cate_ids[]=$v["id"];
		}
		$cate_ids[]=$cate_id;
		$map['category_id'] = array("in",$cate_ids); 
	    }
            $this->assign("cate_id",$cate_id);
        }
        if($domainid){
            $map['domainid'] =   $domainid;
        }
        $map['pid']         =   I('pid',0);
        if($map['pid']){ // 子文档列表忽略分类
            unset($map['category_id']);
        }
	$this->assign("pid",$map['pid'] );


        //搜索条件
        $title =   I('title');
        if(!empty($title)){
           $map["title"] = array("like","%" . $title . "%");
        }

        //$this->getMenu(4);
	$this->getEntriesnode(4);
        $this->meta_title = '词条列表';
    }
//    public function index(){
//        //获取所有频道
//        $channellist=M('Subdomain')->where("status >=1")->field("id,name")->select();  
//        $channelbrandlist=M('SubdomainBrand')->where("status >=1")->field("id,name,domainid")->select(); 
//        foreach($channellist as $k=> &$v){
//            $v["isdomain"]=true;
//            $att=M('entries')->where("status >=0 and brandid=0 and domainid=".$v["id"])->field("id,title,name,status")->select();
//            $v["_"]=$att;
//            foreach($channelbrandlist as $k1=> $v1){
//                $v1["isbrand"]=true;
//                $att=M('entries')->where("status >=0 and brandid=".$v1["id"]." and domainid=".$v["id"])->field("id,title,name,status")->select();
//                $v1["_"]=$att;
//                if($v["id"]==$v1["domainid"]){
//                    $v["_"][]=$v1;
//                }
//            }
//        }
//         
//        //// 记录当前列表页的cookie
//        Cookie('__forward__',       $_SERVER['REQUEST_URI']);
//        $this->assign('tree',      $channellist); 
//        $this->meta_title = '词条列表';
//        $this->getMenu(4);
//	$this->getEntriesnode(4);
//        $this->display();
//    }
    
    /* 新增 */
    public function _before_add(){
        if(IS_POST){
        }else{
            $cate_id = I("cate_id");
            $info["category_id"]=$cate_id;
	    $info["pid"]=I('pid');
            $cateinfo = get_category($cate_id); 
            $domainid = $cateinfo["domainid"];
            $brandid = $cateinfo["brandid"];
            $this->assign('domainid', $domainid);
            $this->assign('brandid', $brandid);
            $this->assign('info', $info);
            $this->commondata(); 
            $this->meta_title = '新增词条';
        }
    }
    /* 编辑 */
    public function _after_edit( &$info ){
        if(IS_POST){
	    clearcache('e_'.$info['id']); 
        }else{
            $this->commondata(); 
            $this->meta_title = '编辑词条';
        }
    }
    
    /* 新增分类 */
    private function commondata(){  
        //获取所有频道
        $channellist=M('Subdomain')->where("status >=1")->getField("id,name"); 
        $this->assign("channellist",$channellist);
        //获取所有品牌
        $channelbrandlist=M('SubdomainBrand')->where("status >=1")->getField("id,name"); 
        $this->assign("channelbrandlist",$channelbrandlist);
	 
        //$this->getMenu(4);
        $this->getEntriesnode(4);
    }
    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function tree($tree = null){ 
        $this->assign('tree', $tree);
        $this->display('tree');
    }
    
    
    protected function getMenu($ismenu=2){
        //获取动态分类
        $cate_auth  =   AuthGroupModel::getAuthCategories(UID); //获取当前用户所有的内容权限节点
        $cate_auth  =   $cate_auth == null ? array() : $cate_auth;
	$channel=   M('Subdomain')->where(array('status'=>1 ))->field('id,name')->select();
	$cate = M('Category')->where(array('status'=>1,'ismenu'=>$ismenu))->field('id,domainid,title,pid,allow_publish')->order('pid,sort')->select();

        //没有权限的分类则不显示
        if(!IS_ROOT){
            foreach ($cate as $key=>$value){
                if(!in_array($value['id'], $cate_auth)){
                    unset($cate[$key]);
                }
            }
        }

        $cate           =   list_to_tree($cate);    //生成分类树
        //获取分类id
        $cate_id        =   I('param.cate_id');
        $this->cate_id  =   $cate_id;

        //是否展开分类
        $hide_cate = false; 
        if(CONTROLLER_NAME == 'Baike'){
            $hide_cate  =   true;
        }
        //生成每个分类的url 
        foreach ($cate as $key=>&$value){
            $value['url']   =   'Baike/index?cate_id='.$value['id'];
            if($cate_id == $value['id'] && $hide_cate){
                $value['current'] = true;
            }else{
                $value['current'] = false;
            }
            if(!empty($value['_child'])){
                $is_child = false;
                foreach ($value['_child'] as $ka=>&$va){
                    $va['url']      =   'Baike/index?cate_id='.$va['id'];
                    if(!empty($va['_child'])){
                        foreach ($va['_child'] as $k=>&$v){
                            $v['url']   =   'Baike/index?cate_id='.$v['id'];
                            $v['pid']   =   $va['id'];
                            $is_child = $v['id'] == $cate_id ? true : false;
                        }
                    }
                    //展开子分类的父分类
                    if($va['id'] == $cate_id || $is_child){
                        $is_child = false;
                        if($hide_cate){
                            $value['current']   =   true;
                            $va['current']      =   true;
                        }else{
                            $value['current']   =   false;
                            $va['current']      =   false;
                        }
                    }else{
                        $va['current']      =   false;
                    }
                }
            }
            foreach($channel as $ck=> &$cv){
                if($cv["id"]==$value["domainid"]){
                            $cv['url']   =   'Baike/index?domainid='.$cv['id'];
                    if($value['current']){ $cv["current"]=1; } 
                    $cv["goodcat"][]=$value; 
                }
                if(!isset($cv['url'])){
                    $cv['url']   =   'Baike/index?domainid='.$cv['id'];
                }
            }
        } 
        $this->assign('nodes', $channel);
    }
    
    protected function getEntriesnode($ismenu=4){
        $channel=   M('Subdomain')->where(array('status'=>1 ))->field('id,name')->select();
	$cate = M('Category')->where(array('status'=>1,'ismenu'=>$ismenu))->field('id,domainid,title,pid,allow_publish')->order('pid,sort')->select();
 
        $cate = list_to_tree($cate);    //生成分类树
        //获取分类id
        $cate_id        =   I('param.cate_id');
        $this->cate_id  =   $cate_id;

        //是否展开分类
        $hide_cate = false;
        if(CONTROLLER_NAME == 'Entries'){
            $hide_cate  =   true;
        }
	$domainid = I("domainid");
        //生成每个分类的url 
        foreach ($cate as $key=>&$value){
            $value['url']   =   'Entries/index?cate_id='.$value['id'];
            if($cate_id == $value['id'] && $hide_cate){
                $value['current'] = true;
            }else{
                $value['current'] = false;
            }
            if(!empty($value['_child'])){
                $is_child = false;
                foreach ($value['_child'] as $ka=>&$va){
                    $va['url']      =   'Entries/index?cate_id='.$va['id'];
                    if(!empty($va['_child'])){
                        foreach ($va['_child'] as $k=>&$v){
                            $v['url']   =   'Entries/index?cate_id='.$v['id'];
                            $v['pid']   =   $va['id'];
                            $is_child = $v['id'] == $cate_id ? true : false;
                        }
                    }
                    //展开子分类的父分类
                    if($va['id'] == $cate_id || $is_child){
                        $is_child = false;
                        if($hide_cate){
                            $value['current']   =   true;
                            $va['current']      =   true;
                        }else{
                            $value['current']   =   false;
                            $va['current']      =   false;
                        }
                    }else{
                        $va['current']      =   false;
                    }
                }
            }
	    foreach($channel as $ck=> &$cv){
		if($cv["id"]==$value["domainid"]){
                    $cv['url']   =   'Entries/index?domainid='.$cv['id'];
		    if($value['current'] || $cv["id"]==$domainid ){
			$cv["current"]=1;
		    } 
		    $cv["goodcat"][]=$value; 
		}
	    }
        } 
        $this->assign('entriesnode',      $channel);
    }
    
    public function searchgoods(){
        $cate_id=I("cate_id");
        $info = get_category($cate_id);
        $keywords=I("keywords"); 
        if($keywords && $info){ 
            $map["status"]=$info["status"]; 
            $map["domainid"]=$info["domainid"];
            $map["title"]=array("like",'%'.$keywords.'%');
            $uniongood=M("document")->where($map)->field("id,title")->limit(0,20)->order("id desc")->select(); 
            $this->success($uniongood);
        }
        $this->error(array());
    }
    
    public function getchildren(){
        $pid=I("pid");
	$map["pid"]=$pid;
	$map["status"]=1;
	$entries=M("entries")->where($map)->field("id,title,category_id,create_time,status")->select();
	$html="";
	if($entries){
	    $cc=count($entries)-1;
	    foreach( $entries as $k=>$v ){
		$html .='<tr class="tr'.$pid.'"><td><input type="checkbox" class="ids row-selected" name=""  value="'.$v['id'].'"> </td><td>'.$v['id'].'</td><td>';
		if($cc==$k){
		    $class='iconend' ;
		}else{
		    $class='';
		}
		$html .='<span class="iconchild '.$class.'"></span>' ;
		$html .='<a href="'.U('Entries/edit',array('id'=>$v['id'],'cate_id'=>$v['category_id'])).'">'.$v['title'].'</a></td><td>'.date('Y-m-d H:i',$v['create_time']).'</td><td><a href="'.U('Entries/edit',array('id'=>$v['id'],'cate_id'=>$v['category_id'])).'">编辑</a><a href="'.U('setStatus?ids='.$v['id'].'&status='.abs(1-$v['status'])).'" class="ajax-get">'.show_status_op($v['status']).'</a><a class="confirm ajax-get" title="删除" href="'.U('del?id='.$v['id']).'">删除</a></td></tr>'; 
	    }
	}
	$this->success($html); 
    }
    
    /**
     * 设置状态
     * @author wangcheng <253490851@qq.com>
    */
    function _before_setStatus(){
	$ids = I('request.ids');
	if( $ids ){
	    clearcache($ids,'a_');
	}
    }
}
