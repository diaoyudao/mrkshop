<?php
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page; 

/***************************************************************
  *created date: 
  *created author:sheshanhu
  *content:后台商品组合-问题控制器
  *modefiy person:
  *modefiy date:
  *note:
****************************************************************/
class ProductsGroupController extends AdminController {

    function index(){
        $map=array();
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        } 
        $model = D (CONTROLLER_NAME); 
        $arr=array();
        if (! empty ( $model )) {
            $list = $this->lists($model, $map,'id DESC');
            if (method_exists ( $this, '_after_list' )) {
                $this->_after_list ( $list );
            }

            //对每个组合的商品信息获取 productsinfo
            foreach($list as $key=>&$value){
                $value['goodsinfo']  = unserialize($value['goodsinfo']);
            }
            $this->assign('list', $list);
        }
        
        $this->display();
    }

    /***************************************************************
      *created date:2015/6/18 10:41 
      *created author:sheshanhu
      *content:组合列表
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function _filter( &$map){
        $map['status']=array('gt', -1);
        $cate_id = I("cate_id");
	    $domainid = I("pgdomainid");
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
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->getMenu(5); 
    }

    /***************************************************************
      *created date: 2015/6/18　10:42
      *created author:sheshanhu
      *content:添加组合
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function _before_add(){
        if(IS_POST){
            //记录行为 
        } else { 
	       $domainid = I("pgdomainid");
           $info['domainid'] = $domainid;
           $info['status'] = 0;
           $info['create_time'] = time();
           $this->assign('info', $info);
	       $this->getMenu(2); 
            $this->meta_title = '新增商品组合';
        }
    }

    public function add(){
        $this->getMenu(2);
        if(IS_POST){
	        $uniongood = I("uniongood");
            $_POST["uniongood"]="";

            if(isset($uniongood[1]) && count($uniongood[1])>1){ 
                $_POST["uniongood"]=$uniongood[1];
            }else{
                $this->error('请选择至少两个商品。');
            }

            $map = array();
            $map["id"] = array("in",$uniongood[1]);
            $goods = M("document")->where($map)->field("id,title,price,cover_id,domainid")->select();
            $_POST["goodsinfo"]= serialize($goods);

            //groupcode
            $_POST["groupcode"]= strtoupper(md5(uniqid(mt_rand(),true)));


            $name=CONTROLLER_NAME;
            $model = D ($name);
            if (false === $model->create ()) {
                    $this->error ( $model->getError () );
            }
            //保存当前数据对象
            $list=$model->add ();
            if ($list!==false) { //保存成功
                    //返回控制器新增后的操作
                    if (method_exists ( $this, '_after_insert' )) {
                        $this->_after_insert ( $list );
                    }
                    $this->success ('新增成功!', U('index'));
            } else {
                    //失败提示
                    $this->error ('新增失败!');
            }
        }else{
		   $this->display();
        }
    }


    public function edit($id = 0){
        if(IS_POST){

            $name=CONTROLLER_NAME;
            $model = D ($name);
            $data = $model->create();

            if($data){
                if($model->save()){
                    //返回控制器新增后的操作
                    if (method_exists ( $this, '_after_edit' )) {
                        $this->_after_edit ( $data );
                    }
                    clearcache('pg_'.$id);
                    if(Cookie('__forward__')){
                                $this->success ('编辑成功!', Cookie('__forward__'));
                            }else{
                                $this->success ('编辑成功!', U('index'));
                            } 
                        } else {
                            $this->error('编辑失败');
                        }
            } else {
                $this->error($model->getError());
            }
        } else {
            $name=CONTROLLER_NAME;
            $model = D ( $name );
            $info = array();
            /* 获取数据 */
            $info = $model->find($id); 
            if(false === $info){
                $this->error('获取配置信息错误');
            }
            if (method_exists ( $this, '_after_edit' )) {
                $this->_after_edit ( $info );
            }
            $this->assign('info', $info);

		   $this->display();

        }
    }



    /***************************************************************
      *created date:2015/6/18　10:42 
      *created author:sheshanhu
      *content:编辑组合
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function _before_edit(){
        if(IS_POST){
            $uniongood = I("uniongood");
            $_POST["uniongood"] = "";

            if(isset($uniongood[1]) && count($uniongood[1])>1){ 
                $_POST["uniongood"]=$uniongood[1];
            }else{
                $this->error('请选择至少两个商品。');
            }

            $map = array();
            $map["id"] = array("in",$uniongood[1]);
            $goods = M("document")->where($map)->field("id,title,price,cover_id,domainid")->select();
            $_POST["goodsinfo"]= serialize($goods);

            $_POST["groupcode"]= strtoupper(md5(uniqid(mt_rand(),true)));

        } else { 
             
        }
    }
    
    /**
     * 编辑问题
     * 
     */
    public function _after_edit( &$data){
        if(IS_POST){
            //记录行为--后期扩展
            action_log('update_productsgroup', 'channel', $data['id'], UID);
        } else { 
	        $this->getMenu(2);
            if($data['uniongood']){//查询管理商品
                $map["status"]=1;
                $map["id"]=array("in",explode(",",$data['uniongood']));
                //两张表联合查询获取商品销售价和市场价
                //$uniongood=M("document")->where($map)->field("id,title,price")->select();

                $Model = new \Think\Model();
                $sql = "select d.id,d.title,d.price,dp.marketprice from __PREFIX__document d,__PREFIX__document_product dp where d.id=dp.id and d.status=1 and d.id in(".$data['uniongood'].");";
                $uniongood = $Model->query($sql);

                $this->assign("uniongood",$uniongood);
            } 
            $this->meta_title = '编辑商品组合';
        }
    }
   
    /**
     * 显示左边菜单，进行权限控制
     * @author huajie <banhuajie@163.com>
     */
    protected function getMenu($ismenu = null){
        //获取动态分类
        $cate_auth  =   AuthGroupModel::getAuthCategories(UID); //获取当前用户所有的内容权限节点
        $cate_auth  =   $cate_auth == null ? array() : $cate_auth;
	$channel=   M('Subdomain')->where(array('status'=>1 ))->field('id,name')->select();
    $pgroupchannel = $channel;
	$cate       =   M('Category')->where(array('status'=>1,'ismenu'=>$ismenu))->field('id,domainid,title,pid,allow_publish')->order('pid,sort')->select();

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
        if(ACTION_NAME != 'recycle' && ACTION_NAME != 'draftbox' && ACTION_NAME != 'mydocument'){
            $hide_cate  =   true;
        }
	$domainid = I("domainid");
        //生成每个分类的url
        foreach ($cate as $key=>&$value){
            $value['url']   =   'Goods/index?cate_id='.$value['id'];
            if($cate_id == $value['id'] && $hide_cate){
                $value['current'] = true;
            }else{
                $value['current'] = false;
            }
            if(!empty($value['_child'])){
                $is_child = false;
                foreach ($value['_child'] as $ka=>&$va){
                    $va['url']      =   'Goods/index?cate_id='.$va['id'];
                    if(!empty($va['_child'])){
                        foreach ($va['_child'] as $k=>&$v){
                            $v['url']   =   'Goods/index?cate_id='.$v['id'];
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
                    //$cv['url']   =   'Goods/index?domainid='.$cv['id'];
                    if($value['current'] || $cv["id"]==$domainid ){
                    $cv["current"]=1;
                    } 
                    $cv["goodcat"][]=$value; 
                }
                if(!isset($cv['url'])){
                    $cv['url']   =   'Goods/index?domainid='.$cv['id'];
                }
            }
        } 
        $this->assign('nodes',      $channel);
        $this->assign('cate_id',    $this->cate_id);

        //商品搭配中的频道
        $pgdomainid = I("pgdomainid");
	    foreach($pgroupchannel as $ck=> &$cv){ 
		    $cv['url']   =   'ProductsGroup/index?pgdomainid='.$cv['id'];
		    if( $cv["id"]==$pgdomainid ){
			$cv["current"]=1;
		    }
	    }
        $this->assign('pgdomainid',    $pgdomainid);
        $this->assign('productsgroupmenus',$pgroupchannel);

        //获取面包屑信息
        $nav = get_parent_category($cate_id);
        $this->assign('rightNav',   $nav);

        //获取回收站权限
        $this->assign('show_recycle', IS_ROOT || $this->checkRule('Admin/article/recycle'));
        //获取商品组合权限
        $this->assign('show_comment', IS_ROOT || $this->checkRule('Admin/ProductsGroup/index'));
        //获取商品搭配权限
        $this->assign('show_productsgroup', IS_ROOT || $this->checkRule('Admin/ProductsGroup/index'));
        //获取草稿箱权限
        $this->assign('show_draftbox', C('OPEN_DRAFTBOX'));
        //获取审核列表权限
        $this->assign('show_examine', IS_ROOT || $this->checkRule('Admin/article/examine'));
    }
    
      
    public function searchgoods(){
        //$domainid=I("pgdomainid");
        $keywords=I("keywords");
        $map = array();// && $domainid
        if($keywords){ 
            $map["status"] = 1;
            $map["model_id"] = 5;//商品模型
            //$map["domainid"]=$domainid;
            $map["title"]=array("like",'%'.$keywords.'%');
            //$uniongood=M("document")->where($map)->field("id,title,price")->limit(0,20)->order("id desc")->select(); 

            $Model = new \Think\Model();
            $sql = "select d.id,d.title,d.price,dp.marketprice from __PREFIX__document d,__PREFIX__document_product dp where d.id=dp.id and d.status=1 and d.model_id=5 and d.title like '%".$keywords."%';";
            $uniongood = $Model->query($sql);


            $this->success($uniongood);
        }
        $this->error(array());
    }
    
    /**
     * 审核问题
     * 
     */
    function audit(){
	$id = I('id',0);
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
	    $ifcheck = I('ifcheck',0);
        $map = array('id' => array('in', $id) ); 
        $model = M ("ProductsGroup"); 
        $list=$model->where ( $map )->setField ( 'ifcheck', $ifcheck );
	    //clearcache('pl_'.$info['id']); 
        if ($list!==false) {
            $this->success('审核成功');
        } else {
            $this->error('审核失败！');
        }
    }

    /***************************************************************
      *created date:2015/5/8 15:17 
      *created author:sheshanhu
      *content:删除用户提交的组合
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function del(){
        if(IS_GET){
            $id=I('get.id');  
            $document   =   D('ProductsGroup');
            if( $document->where("id='$id'")->setField("status",-1)){ 
		    //clearcache("pl_".$ids); 
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
        if(IS_POST){
            $ids = I('post.id');
            $document = M("ProductsGroup");
            if(is_array($ids)){
               foreach($ids as $id){
                 $document->where("id='$id'")->setField("status",-1);       
                }
            }
           $this->success("删除成功！");
        }
    }

    /***************************************************************
      *created date:2015/5/8 15:17 
      *created author:sheshanhu
      *content:组合状态修改
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function setStatus($model='ProductsGroup'){
        return parent::setStatus('ProductsGroup');
    }
    
    /**
     * 设置状态
     * 
    */
    function _before_setStatus(){
	$ids = I('request.ids');
	if( $ids ){
	    clearcache($ids,'pg_');
	}
    }

}