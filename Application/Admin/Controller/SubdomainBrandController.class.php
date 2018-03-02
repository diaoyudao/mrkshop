<?php
namespace Admin\Controller;

/**
 * 后台品牌控制器
 * @author wangcheng <253490851@qq.com>
 */

class SubdomainBrandController extends AdminController {
    
    
    public function _initialize() {
        $this->getMenu(2);
        parent::_initialize();
    }
    
    /**
     * 品牌列表
     * @author wangcheng <253490851@qq.com>
     */ 
    public function _filter(){
        $map  = array('status' => array('gt', -1));
    }
    
    public function _after_list( &$list ){
        $channellist=M('Subdomain')->where("status >=1")->getField("id,name");
        foreach($list as $k=>$v ){
            $list[$k]["channelname"]=$channellist[$v['domainid']];
        }
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        //$ids=M("subdomain_brand_key")->getField("firstkey,id",true);
        //foreach( $list as $k=>$v ){
        //    if( is_numeric($v["firstkey"]) ){
        //        $kkk=27;
        //    }else{
        //        $kkk=$ids[$v["firstkey"]];
        //    }
        //    M("subdomain_brand")->where("id=".$v["id"])->setField("firstkeyid",$kkk); 
        //}
        
    }
    /**
     * 添加之前的操作
     * @author wangcheng <253490851@qq.com>
     */
    public function _before_add(){
        if(IS_POST){
            //记录行为 
        } else {
            //获取频道
            $channellist=M('Subdomain')->where("status >=1")->field("id,name")->select(); 
            $this->assign("channellist",$channellist);
            $info["ishot"]=0;
            $this->assign("info",$info);
            $this->meta_title = '新增品牌';
        }
    }
    
    /**
     * 插入数据之后的操作
     * @author wangcheng <253490851@qq.com>
     */
    public function _after_insert(){
        if(IS_POST){
            //记录行为--后期扩展
            action_log('update_subdomainbrand', 'subdomainbrand', $id, UID);
        } else { 
             
        }
    }

    /**
     * 更新之前的操作
     * @author wangcheng <253490851@qq.com>
     */
    public function _before_edit(){
        if(IS_POST){
            $folders = I("folders");
            $arr=array();
            foreach($folders as $k=>$v){
                if($v["title"]){
                    $v["des"]=I("foldersdes".$k);
                    $arr[]=$v;
                } 
            }
            if($arr){
                $_POST["folderdes"]=$arr;
            } 
        }
    }
    /**
     * 编辑品牌
     * @author wangcheng <253490851@qq.com>
     */
    public function _after_edit( &$info ){
        if(IS_POST){  
            //记录行为--后期扩展
            action_log('update_channel', 'channel', $data['id'], UID);
            clearcache('pp_'.$info['id'] );
        } else {
            //条目数据处理
//            if($info["folderdes"]){
//                $info["folderdes"]=json_decode($info["folderdes"],true);
//            }
//            if($info["re_pics_info"]){
//                $info["re_pics_info"]=json_decode($info["re_pics_info"],true);
//            }
//            if($info["hot_pics_info"]){
//                $info["hot_pics_info"]=json_decode($info["hot_pics_info"],true);
//            }
            //获取频道
            $channellist=M('Subdomain')->where("status >=1")->field("id,name")->select(); 
            $this->assign("channellist",$channellist);
            //获取品牌分类
            if( $info['domainid'] ){
                $catagorylist=M('category')->where("status >=1 and ismenu=1 and domainid=".$info['domainid'])->field("id,title")->select(); 
                $this->assign("brandcategory",$catagorylist); 
            } 
            //清空缓存
            $this->meta_title = '编辑品牌'; 
        }
    }
    
    public function editortpl(){ 
        //获取频道
        $this->assign("k",I("k"));
        $tpl = $this->fetch('editortpl');
        echo $tpl;exit;
    }
    
    /**
     * 设置状态
     * @author wangcheng <253490851@qq.com>
    */
    function _before_setStatus(){
	$ids = I('request.ids');
	if( $ids ){
	    clearcache($ids,'pp_');
	}
    }
}