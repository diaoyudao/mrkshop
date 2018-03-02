<?php
namespace Admin\Controller;
/**
 * 后台热门标签控制器
  * @author qchlian
 */
class TagsController extends AdminController { 
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
	$type = I("types","");
	if( $type ){
	    $map["types"] = $type;
	}
	$this->assign("types",$type);
        //搜索条件
        $title =   I('keywords');
        if(!empty($title)){
           $map["keywords"] = array("like","%" . $title . "%");
        }
        Cookie('__forward__',$_SERVER['REQUEST_URI']); 
    }
    
    public function _before_add(){
	$type = I("types","");
	if( $type=="" ){
	    $this->error("请切换到相关类型再添加");
	}
	$info=array();
	$info["types"]=$type;
	$info["ishot"]=0;
	$this->assign("info",$info); 
    }
    
    public function _after_edit( $info ){
	 if(IS_POST){ 
	    clearcache('t_'.$info['id']); 
        }
    }
}