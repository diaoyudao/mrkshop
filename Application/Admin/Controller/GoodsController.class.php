<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page;

/**
 * 后台内容控制器
 * @author huajie <banhuajie@163.com>
 */
class GoodsController extends AdminController {

    /* 保存允许访问的公共方法 */
    static protected $allow = array( 'draftbox','mydocument');

    private $cate_id        =   null; //文档分类id

    /**
     * 检测需要动态判断的文档类目有关的权限
     *
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     *
     */
    protected function checkDynamic(){
        $cates = AuthGroupModel::getAuthCategories(UID);
        switch(strtolower(ACTION_NAME)){
            case 'index':   //文档列表
            case 'add':   // 新增
                $cate_id =  I('cate_id');
                break;
            case 'edit':    //编辑
            case 'update':  //更新
                $doc_id  =  I('id');
                $cate_id =  M('Document')->where(array('id'=>$doc_id))->getField('category_id');
                break;
            case 'setstatus': //更改状态
            case 'permit':    //回收站
                $doc_id  =  (array)I('ids');
                $cate_id =  M('Document')->where(array('id'=>array('in',$doc_id)))->getField('category_id',true);
                $cate_id =  array_unique($cate_id);
                break;
        }
        if(!$cate_id){
            return null;//不明
        }elseif( !is_array($cate_id) && in_array($cate_id,$cates) ) {
            return true;//有权限
        }elseif( is_array($cate_id) && $cate_id==array_intersect($cate_id,$cates) ){
            return true;//有权限
        }else{
            return false;//无权限
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
	$channel=   M('Subdomain')->where(array('status'=>1,'id'=>array('gt',1)))->field('id,name')->select();
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
            $value['url']   =   'Goods/index?domainid='.$value['domainid'].'&cate_id='.$value['id'];
            if($cate_id == $value['id'] && $hide_cate){
                $value['current'] = true;
            }else{
                $value['current'] = false;
            }
            if(!empty($value['_child'])){
                $is_child = false;
                foreach ($value['_child'] as $ka=>&$va){
                    $va['url']      =   'Goods/index?domainid='.$va['domainid'].'&cate_id='.$va['id'];
                    if(!empty($va['_child'])){
                        foreach ($va['_child'] as $k=>&$v){
                            $v['url']   =   'Goods/index?domainid='.$v['domainid'].'&cate_id='.$v['id'];
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
                //添加频道地址
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
//        $this->assign('productsgroupmenus',$pgroupchannel);

        //获取面包屑信息
        $nav = get_parent_category($cate_id);
        $this->assign('rightNav',   $nav);

        //获取回收站权限
        $this->assign('show_recycle', IS_ROOT || $this->checkRule('Admin/article/recycle'));
        //获取商品评论权限
        $this->assign('show_comment', IS_ROOT || $this->checkRule('Admin/Comment/index'));
        //获取商品搭配权限
//        $this->assign('show_productsgroup', IS_ROOT || $this->checkRule('Admin/ProductsGroup/index'));
        //获取草稿箱权限
        $this->assign('show_draftbox', C('OPEN_DRAFTBOX'));
        //获取审核列表权限
        $this->assign('show_examine', IS_ROOT || $this->checkRule('Admin/article/examine'));
    }

    /**
     * 分类文档列表页
     * @param integer $cate_id 分类id
     * @param integer $model_id 模型id
     * @param integer $position 推荐标志
     * @param integer $group_id 分组id
     */
    
    /*创建目录*/
    private function mkdirp( $dst ){
	if( file_exists( $dst ) ){
	    if( is_dir( $dst ) ){
		    return true;
	    }else{
		    return false;
	    }
	}
	mkdir( $dst, 0775, true );
	if( file_exists( $dst ) and is_dir( $dst ) ){
	    return true;
	}
	return false;
    }
    
    public function index($cate_id = null, $model_id = null, $position = null,$group_id=null){
        //获取左边菜单
       $this->getMenu(2);
        if($cate_id===null){
            $cate_id = $this->cate_id;
        }
        if(!empty($cate_id)){
            $pid = I('pid',0);
            // 获取列表绑定的模型
            if ($pid == 0) {
                $models     =   get_category($cate_id, 'model');
				// 获取分组定义
				$groups		=	get_category($cate_id, 'groups');
				if($groups){
					$groups	=	parse_field_attr($groups);
				}
            }else{ // 子文档列表
                $models     =   get_category($cate_id, 'model_sub');
            }
            if(is_null($model_id) && !is_numeric($models)){
                // 绑定多个模型 取基础模型的列表定义
                $model = M('Model')->getByName('product');
            }else{
                $model_id   =   $model_id ? : $models;
                //获取模型信息
                $model = M('Model')->getById($model_id);
                if (empty($model['list_grid'])) {
                    $model['list_grid'] = M('Model')->getFieldByName('product','list_grid');
                }
            }
            $this->assign('model', explode(',', $models));
        }else{
            // 获取基础模型信息
            $model = M('Model')->getByName('product');
            $model_id   =   null;
            $cate_id    =   0;
            $this->assign('model', null);
        }

        //解析列表规则
        $fields =	array();
        $grids  =	preg_split('/[;\r\n]+/s', trim($model['list_grid']));
        foreach ($grids as &$value) {
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']  =   $val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array  =   explode('|',$val);
                $fields[] = $array[0];
            }
        }

        // 文档模型列表始终要获取的数据字段 用于其他用途
        $fields[] = 'category_id';
        $fields[] = 'model_id';
        $fields[] = 'pid';
        // 过滤重复字段信息
        $fields =   array_unique($fields);
        // 列表查询
       // $list   =   $this->getDocumentList($cate_id,$model_id,$position,$fields,$group_id);
       $list   =   $this->getDocumentList($cate_id,5,$position,$fields);// $model_id用5替代
        // 列表显示处理
        $list   =   $this->parseDocumentList($list,$model_id);
        
        $this->assign('model_id',$model_id);
		$this->assign('group_id',$group_id);
        $this->assign('position',$position);
        $this->assign('groups', $groups);
	foreach($list as $k=>$v){
	    //循环查找domainid 对应的 mark
	    $domainid = M("document")->where("id=".$v["id"])->getField("domainid");
	    $list[$k]["domainid"]=$domainid;
	    $list[$k]["product_type"]=M("document_product")->where("id=".$v["id"])->getField("product_type");
//	    $list[$k]["iszhiyou"]=M("document_product")->where("id=".$v["id"])->getField("iszhiyou");
	}
        $this->assign('list',   $list);
        $this->assign('list_grids', $grids);
        $this->assign('model_list', $model);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->display();
    }

    /**
     * 默认文档列表方法
     * @param integer $cate_id 分类id
     * @param integer $model_id 模型id
     * @param integer $position 推荐标志
     * @param mixed $field 字段列表
     * @param integer $group_id 分组id
     */
    protected function getDocumentList($cate_id=0,$model_id=null,$position=null,$field=true,$group_id=null){
        /* 查询条件初始化 */
        $map = array();
        if(isset($_GET['title'])){
            $map['title']  = array('like', '%'.(string)I('title').'%');
        }
        if(isset($_GET['status'])){
            $map['status'] = I('status');
            $status = $map['status'];
        }else{
            $status = null;
            $map['status'] = array('in', '0,1,2');
        }
        if ( isset($_GET['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        if ( isset($_GET['nickname']) ) {
            $map['uid'] = M('Member')->where(array('nickname'=>I('nickname')))->getField('uid');
        }

        //2015/6/25 11:24 sheshanhu 添加频道条件
        if ( isset($_GET['domainid']) ) {
            $map['domainid'] = I('domainid');
        }

        // 构建列表数据
        $Document = M('Document');

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
        }
        /* $map['pid']         =   I('pid',0);
        if($map['pid']){ // 子文档列表忽略分类
            unset($map['category_id']);
        } */
        $Document->alias('DOCUMENT');
        if(!is_null($model_id)){
            $map['model_id']    =   $model_id;
            if(is_array($field) && array_diff($Document->getDbFields(),$field)){
                $modelName  =   M('Model')->getFieldById($model_id,'name');
                $Document->join('__DOCUMENT_'.strtoupper($modelName).'__ '.$modelName.' ON DOCUMENT.id='.$modelName.'.id');
                $key = array_search('id',$field);
                if(false  !== $key){
                    unset($field[$key]);
                    $field[] = 'DOCUMENT.id';
                }
            }
        }
        if(!is_null($position)){
            $map[] = "position & {$position} = {$position}";
        }
		if(!is_null($group_id)){
			$map['group_id']	=	$group_id;
		}
        $list = $this->lists($Document,$map,'DOCUMENT.id DESC',$field);

        if($map['pid']){
            // 获取上级文档
            $article    =   $Document->field('id,title,type')->find($map['pid']);
            $this->assign('article',$article);
        }
        //检查该分类是否允许发布内容
        $allow_publish  =   get_category($cate_id, 'allow_publish');

        $this->assign('status', $status);
        $this->assign('allow',  $allow_publish);
        $this->assign('pid',    $map['pid']);

        $this->meta_title = '文档列表';
        return $list;
    }
   /**
     * ajax获取次级
     * @author wangcheng <253490851@qq.com>
     */
    public function getcategory()
    {
        $pid = I("pid", "");
        if ($pid == "") {
            $this->error('error');
        } else {
            $map["domainid"] = $pid;
            $map['pid'] = 0;
            $map["status"] = 1;
            $map["ismenu"] = 2;
            $map['model'] = 5;
            $cat = M("category")->where($map)->field("id,title")->select();
            $html = "";
            if ($cat) {
                $html = $cat;
            }
            $attributehtml = $this->getattr($pid,0,0);
            $_data['status'] =1;
            $_data['info'] = $html;
            $_data['attributehtml'] = $attributehtml;
            $this->ajaxReturn($_data);
        }
    }
    
    public function getchildren()
    {
        $pid = I("pid", "");
        if ($pid == "") {
            $this->error('error');
        } else {
            $map["pid"] = $pid;
            $map["status"] = 1;
            $map["ismenu"] = 2;
            $map['model'] = 5;
            $cat = M("category")->where($map)->field("id,title")->select();
            $html = "";
            if ($cat) {
                $html = $cat;
            }
            $this->success($html);
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author huajie <banhuajie@163.com>
     */
    public function setStatus($model='Document'){
        return parent::setStatus('Document');
    }

    /**
     * 文档新增页面初始化
     * @author huajie <banhuajie@163.com>
     */
    public function add(){
        //获取左边菜单
       $this->getMenu(2);

        $cate_id    =   I('get.cate_id',0);
        $model_id   =   I('get.model_id',5);
				$group_id	=	I('get.group_id','');

        //empty($cate_id) && $this->error('参数不能为空！');
        empty($model_id) && $this->error('该分类未绑定模型！');

        //检查该分类是否允许发布
     //   $allow_publish = check_category($cate_id);
     //   !$allow_publish && $this->error('该分类不允许发布内容！');

        // 获取当前的模型信息
        $model    =   get_document_model($model_id);
	
				$cate_info =get_category($cate_id);
				
        //处理结果
        $info['pid']            =   $_GET['pid']?$_GET['pid']:0;
        $info['model_id']       =   $model_id;
        $info['category_id']    =   $cate_id;
				$info['group_id']		=	$group_id;
				$info['domainid']       =   $cate_info["domainid"];
        if($info['pid']){
            // 获取上级文档
            $article            =   M('Document')->field('id,title,type')->find($info['pid']);
            $this->assign('article',$article);
        }
        
        // domainlist
        $domainlist = M("Subdomain")->where(array('id'=> array('gt', 1)))->select();
        $this->assign("domainlist", $domainlist);

        //获取表单字段排序
        $fields = get_model_attribute($model['id']);
        $this->assign('info',       $info);
        $this->assign('fields',     $fields);
				$html = $this->getattr(2,0,0);//获取属性相关
				$this->assign('attributehtml', $html);
				//$this->getvolumeprice();//获取优惠相关
        $this->assign('type_list',  get_type_bycate($cate_id));
        $this->assign('model',      $model);

        $goodtypelist = M("GoodType")->where(array('status'=>1))->getField("id,name");
				$this->assign('goodtypelist',$goodtypelist);
        
         // 配送方式
        $distribution = M("distribution")->where(array('type'=>0,'status'=>1))->select();
        $this->assign('distribution',$distribution);
        
        //查询频道下的所有品牌
        $channelbrandlist=M('SubdomainBrand')->where("status >=1")->getField("id,name");
//        $channelbrandlist=M('SubdomainBrand')->where("status >=1 and domainid=".$cate_info["domainid"])->getField("id,name");
        $this->assign("channelbrandlist",$channelbrandlist);


        $this->meta_title = '新增'.$model['title'];
        $this->display();
    }

    /**
     * 文档编辑页面初始化
     * @author huajie <banhuajie@163.com>
     */
    public function edit(){
        //获取左边菜单
       $this->getMenu(2);

        $id     =   I('get.id','');
        if(empty($id)){
            $this->error('参数不能为空！');
        }

        // 获取详细数据
        $Document = D('Document');
        $data = $Document->detail($id);
        if(!$data){
            $this->error($Document->getError());
        }
        if($data['pid']){
            // 获取上级文档
            $article        =   $Document->field('id,title,type')->find($data['pid']);
            $this->assign('article',$article);
        }
        // 获取当前的模型信息
        $model    =   get_document_model($data['model_id']);
				$data["attri"]=$data["attri"] ? json_decode($data["attri"],true):array();
				
				if($data['unionarticle']){//查询管理商品
				    $map["id"]=array("in",explode(",",$data['unionarticle']));
				    $unionarticle=M("document")->where($map)->field("id,title")->select();
				    $this->assign("unionarticle",$unionarticle);
				}
				
        $this->assign('data', $data);
        $this->assign('model_id', $data['model_id']);
        $this->assign('model',      $model);

        //获取表单字段排序
        $fields = get_model_attribute($model['id']);
        $this->assign('fields',     $fields);


        //获取当前分类的文档类型
        $this->assign('type_list', get_type_bycate($data['category_id']));

				//wangcheng add attribute
				//获取已保存属性
				$alreadyattr = M("GoodAttr")->where("gid=".$id)->select();
				
				$cate_info =get_category($data["category_id"]);
			 
				$html = $this->getattr($cate_info["domainid"],$cate_info["brandid"],$id);//获取属性相关
				$this->assign('attributehtml', $html);

        // domainlist
        $domainlist = M("Subdomain")->where(array('id'=> array('gt', 1)))->select();
        $this->assign("domainlist", $domainlist);
        
        $category_list = M('category')->where(array('pid' => 0, 'status' => 1, 'domainid'=>$data['domainid'], 'model'=>5, 'ismenu' => 2))->field('id,title,pid,allow_publish')->order('pid,sort')->select();
        $this->assign('category_list', $category_list);
        //$subcategory_list = M('category')->where(array('pid' => $data['category_id'], 'status' => 1, 'domainid'=>$data['domainid'], 'model'=>5, 'ismenu' => 2))->field('id,title,pid,allow_publish')->order('pid,sort')->select();
        $subcategory_list = M('category')->where(array('pid' => $data['pid'], 'status' => 1, 'domainid'=>$data['domainid'], 'model'=>5, 'ismenu' => 2))->field('id,title,pid,allow_publish')->order('pid,sort')->select();
        $this->assign('subcategory_list', $subcategory_list);
        
        // 会员价格
        //$level_list = M("member_level")->where(array('status'=>array('egt',0)))->order("member_type asc,level desc")->select();
        $level_list = M("member_level")->where(array('status'=>1))->order("member_type asc,level desc")->select();
        $this->assign('level_list', $level_list);
        
        $member_level_price = M("document_product")->where(array('id'=>$id))->getField('member_level_price');
        $member_level_price = unserialize($member_level_price);

        $this->assign('member_level_price', $member_level_price);
        
        // 配送方式
        $distribution = M("distribution")->where(array('type'=>0,'status'=>1))->select();
        $this->assign('distribution',$distribution);
        
        
        $goodtypelist = M("GoodType")->where(array('status'=>1))->getField("id,name");
				$this->assign('goodtypelist',$goodtypelist);
			
				//查询频道下的所有品牌
				$channelbrandlist=M('SubdomainBrand')->where("status >=1")->getField("id,name");
				$this->assign("channelbrandlist",$channelbrandlist);

        $this->meta_title   =   '编辑文档';
        $this->display();
    }

    /**
     * 更新一条数据
     * @author huajie <banhuajie@163.com>
     */
    public function update(){
        $document   =   D('Document');
				$category_id = I("post.category_id");
				
				$info = get_category($category_id);
				$_POST["domainid"]= $info["domainid"];
	
        if (!$category_id) {
            $this->error('请选择分类');
        }

				//关联文章
				$uniongood = I("unionarticle");
				$_POST["unionarticle"]="";
			  $_POST['member_level_price'] = serialize($_POST['member_price']);
				if($uniongood[1]){
				    $_POST["unionarticle"]=implode(",",$uniongood[1]);
				}
        
        if(I('product_type') == 2 && !I('haiguan_rate')){
            $this->error('跨进商品海关税率不能为空');
        }
        if(I('price')<=0){
            $this->error("价格不能小于0");
        }
        
        $res = $document->update();
        if(!$res){
            $this->error($document->getError());
        }else{
	    			$goodid = I('post.id');
            clearcache('p_'.$goodid);
            $this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
        }
    }

    /**
     * 待审核列表
     */
    public function examine(){
        //获取左边菜单
       $this->getMenu(2);

        $map['status']  =   2;
        if ( !IS_ROOT ) {
            $cate_auth  =   AuthGroupModel::getAuthCategories(UID);
            if($cate_auth){
                $map['category_id']    =   array('IN',$cate_auth);
            }else{
                $map['category_id']    =   -1;
            }
        }
        $list = $this->lists(M('Document'),$map,'update_time desc');
        //处理列表数据
        if(is_array($list)){
            foreach ($list as $k=>&$v){
                $v['username']      =   get_nickname($v['uid']);
            }
        }

        $this->assign('list', $list);
        $this->meta_title       =   '待审核';
        $this->display();
    }

    /**
     * 回收站列表
     * @author huajie <banhuajie@163.com>
     */
    public function recycle(){
        //获取左边菜单
       $this->getMenu(2);

        $map['status']  =   -1;
        if ( !IS_ROOT ) {
            $cate_auth  =   AuthGroupModel::getAuthCategories(UID);
            if($cate_auth){
                $map['category_id']    =   array('IN',$cate_auth);
            }else{
                $map['category_id']    =   -1;
            }
        }
        $list = $this->lists(M('Document'),$map,'update_time desc');

        //处理列表数据
        if(is_array($list)){
            foreach ($list as $k=>&$v){
                $v['username']      =   get_nickname($v['uid']);
            }
        }

        $this->assign('list', $list);
        $this->meta_title       =   '回收站';
        $this->display();
    }

    /**
     * 写文章时自动保存至草稿箱
     * @author huajie <banhuajie@163.com>
     */
    public function autoSave(){
        $res = D('Document')->autoSave();
        if($res !== false){
            $return['data']     =   $res;
            $return['info']     =   '保存草稿成功';
            $return['status']   =   1;
            $this->ajaxReturn($return);
        }else{
            $this->error('保存草稿失败：'.D('Document')->getError());
        }
    }

    /**
     * 草稿箱
     * @author huajie <banhuajie@163.com>
     */
    public function draftBox(){
        //获取左边菜单
       $this->getMenu(2);

        $Document   =   D('Document');
        $map        =   array('status'=>3,'uid'=>UID);
        $list       =   $this->lists($Document,$map);
        //获取状态文字
        //int_to_string($list);

        $this->assign('list', $list);
        $this->meta_title = '草稿箱';
        $this->display();
    }

    /**
     * 我的文档
     * @author huajie <banhuajie@163.com>
     */
    public function mydocument($status = null, $title = null){
        //获取左边菜单
       $this->getMenu(2);

        $Document   =   D('Document');
        /* 查询条件初始化 */
        $map['uid'] = UID;
        if(isset($title)){
            $map['title']   =   array('like', '%'.$title.'%');
        }
        if(isset($status)){
            $map['status']  =   $status;
        }else{
            $map['status']  =   array('in', '0,1,2');
        }
        if ( isset($_GET['time-start']) ) {
            $map['update_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['update_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        //只查询pid为0的文章
        $map['pid'] = 0;
        $list = $this->lists($Document,$map,'update_time desc');
        $list = $this->parseDocumentList($list,1);

        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('status', $status);
        $this->assign('list', $list);
        $this->meta_title = '我的文档';
        $this->display();
    }

    /**
     * 还原被删除的数据
     * @author huajie <banhuajie@163.com>
     */
    public function permit(){
        /*参数过滤*/
        $ids = I('param.ids');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }

        /*拼接参数并修改状态*/
        $Model  =   'Document';
        $map    =   array();
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        $this->restore($Model,$map);
    }

    /**
     * 清空回收站
     * @author huajie <banhuajie@163.com>
     */
    public function clear(){
        $res = D('Document')->remove();
        if($res !== false){
            $this->success('清空回收站成功！');
        }else{
            $this->error('清空回收站失败！');
        }
    }

    /**
     * 移动文档
     * @author huajie <banhuajie@163.com>
     */
    public function move() {
        if(empty($_POST['ids'])) {
            $this->error('请选择要移动的文档！');
        }
        session('moveArticle', $_POST['ids']);
        session('copyArticle', null);
        $this->success('请选择要移动到的分类！');
    }

    /**
     * 拷贝文档
     * @author huajie <banhuajie@163.com>
     */
    public function copy() {
        if(empty($_POST['ids'])) {
            $this->error('请选择要复制的文档！');
        }
        session('copyArticle', $_POST['ids']);
        session('moveArticle', null);
        $this->success('请选择要复制到的分类！');
    }

    /**
     * 粘贴文档
     * @author huajie <banhuajie@163.com>
     */
    public function paste() {
        $moveList = session('moveArticle');
        $copyList = session('copyArticle');
        if(empty($moveList) && empty($copyList)) {
            $this->error('没有选择文档！');
        }
        if(!isset($_POST['cate_id'])) {
            $this->error('请选择要粘贴到的分类！');
        }
        $cate_id = I('post.cate_id');   //当前分类
        $pid = I('post.pid', 0);        //当前父类数据id

        //检查所选择的数据是否符合粘贴要求
        $check = $this->checkPaste(empty($moveList) ? $copyList : $moveList, $cate_id, $pid);
        if(!$check['status']){
            $this->error($check['info']);
        }

        if(!empty($moveList)) {// 移动    TODO:检查name重复
            foreach ($moveList as $key=>$value){
                $Model              =   M('Document');
                $map['id']          =   $value;
                $data['category_id']=   $cate_id;
                $data['pid']        =   $pid;
                //获取root
                if($pid == 0){
                    $data['root'] = 0;
                }else{
                    $p_root = $Model->getFieldById($pid, 'root');
                    $data['root'] = $p_root == 0 ? $pid : $p_root;
                }
                $res = $Model->where($map)->save($data);
            }
            session('moveArticle', null);
            if(false !== $res){
                $this->success('文档移动成功！');
            }else{
                $this->error('文档移动失败！');
            }
        }elseif(!empty($copyList)){ // 复制
            foreach ($copyList as $key=>$value){
                $Model  =   M('Document');
                $data   =   $Model->find($value);
                unset($data['id']);
                unset($data['name']);
                $data['category_id']    =   $cate_id;
                $data['pid']            =   $pid;
                $data['create_time']    =   NOW_TIME;
                $data['update_time']    =   NOW_TIME;
                //获取root
                if($pid == 0){
                    $data['root'] = 0;
                }else{
                    $p_root = $Model->getFieldById($pid, 'root');
                    $data['root'] = $p_root == 0 ? $pid : $p_root;
                }

                $result   =  $Model->add($data);
                if($result){
                    $logic      =   logic($data['model_id']);
                    $data       =   $logic->detail($value); //获取指定ID的扩展数据
                    $data['id'] =   $result;
                    $res        =   $logic->add($data);
                }
            }
            session('copyArticle', null);
            if($res){
                $this->success('文档复制成功！');
            }else{
                $this->error('文档复制失败！');
            }
        }
    }

    /**
     * 检查数据是否符合粘贴的要求
     * @author huajie <banhuajie@163.com>
     */
    protected function checkPaste($list, $cate_id, $pid){
        $return     =   array('status'=>1);
        $Document   =   D('Document');

        // 检查支持的文档模型
        if($pid){
            $modelList =   M('Category')->getFieldById($cate_id,'model_sub');   // 当前分类支持的文档模型
        }else{
            $modelList =   M('Category')->getFieldById($cate_id,'model');   // 当前分类支持的文档模型
        }
        
        foreach ($list as $key => $value){
            //不能将自己粘贴为自己的子内容
            if($value == $pid){
                $return['status'] = 0;
                $return['info'] = '不能将编号为 '.$value.' 的数据粘贴为他的子内容！';
                return $return;
            }
            // 移动文档的所属文档模型
            $modelType  =   $Document->getFieldById($value,'model_id');
            if(!in_array($modelType,explode(',',$modelList))) {
                $return['status'] = 0;
                $return['info'] = '当前分类的文档模型不支持编号为 '.$value.' 的数据！';
                return $return;
            }
        }

        // 检查支持的文档类型和层级规则
        $typeList =   M('Category')->getFieldById($cate_id,'type'); // 当前分类支持的文档模型
        foreach ($list as $key=>$value){
            // 移动文档的所属文档模型
            $modelType  =   $Document->getFieldById($value,'type');
            if(!in_array($modelType,explode(',',$typeList))) {
                $return['status'] = 0;
                $return['info'] = '当前分类的文档类型不支持编号为 '.$value.' 的数据！';
                return $return;
            }
            $res = $Document->checkDocumentType($modelType, $pid);
            if(!$res['status']){
                $return['status'] = 0;
                $return['info'] = $res['info'].'。错误数据编号：'.$value;
                return $return;
            }
        }

        return $return;
    }

    /**
     * 文档排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort(){
        if(IS_GET){
            //获取左边菜单
           $this->getMenu(2);

            $ids        =   I('get.ids');
            $cate_id    =   I('get.cate_id');
            $pid        =   I('get.pid');

            //获取排序的数据
            $map['status'] = array('gt',-1);
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }else{
                if($cate_id !== ''){
                    $map['category_id'] = $cate_id;
                }
                if($pid !== ''){
                    $map['pid'] = $pid;
                }
            }
            $list = M('Document')->where($map)->field('id,title')->order('level DESC,id DESC')->select();

            $this->assign('list', $list);
            $this->meta_title = '文档排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = array_reverse(explode(',', $ids));
            foreach ($ids as $key=>$value){
                $res = M('Document')->where(array('id'=>$value))->setField('level', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！');
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
    public function del(){
	if(IS_GET){
	    $id=I('get.ids');
	    $document   =   D('Document');
	    if( $document->where("id='$id'")->setField("status",-1)){
		clearcache('p_'.$id);
		$this->success('删除成功');
	    }
	    else{
		$this->error('删除失败');
	    }
	}
	
	if(IS_POST){
            $ids = I('post.id');
	    $document = M("document");
            if(is_array($ids)){
                             foreach($ids as $id){
		
                             $document->where("id='$id'")->setField("status",-1);
						
                }
            }
           $this->success("删除成功！");
        }
    }
    
    //获取相关商品属性相关
    private function getattr($domainid=0,$brandid=0,$id=0){
        //查找商品ID自身属性
        $html = '';
        $selfmap["gid"]=$id;
        $selfmap["attributeid"]=0;
        $selfattr=M("good_attr")->where($selfmap)->select();
        //$selfattr =array();
        if($selfattr){
            foreach ($selfattr AS $key => $val){
            $html .= "<div class='form-item cf'>";
            $html .= ($key==0) ? "<a href='javascript:;' class='attr_add_spec'>[+]</a>" : "<a href='javascript:;' class='attr_remove_spec'>[-] </a>";
            $html .= "<label class=\"item-label item-label_f\">自定义属性</label><input type='hidden' name='attr_id_list[]' value='0' />";
            $html .= '<input name="attr_keys_list[]" type="text" value="'.$val["keys"].'"/>';
            $html .=' 属性值<input type="text" name="attr_value_list[]" value="'.$val["value"].'"  />';
            $html .= '<input type="hidden" name="attr_goodattr_id_list[]" value="'.$val["id"].'" /><input type="hidden" name="attr_types_list[]" value="0" /><input type="hidden" name="attr_price_list[]" value="0" /><input type="hidden" name="inputtypes[]" value="0" /></div>';
            }
        }else{
            $html .= "<div class='form-item cf'><a href='javascript:;' class='attr_add_spec'>[+]</a>";
            $html .= "<label class=\"item-label item-label_f\">自定义属性</label><input type='hidden' name='attr_id_list[]' value='0' />";
            $html .= '<input name="attr_keys_list[]" type="text" value=""/>';
            $html .=' 属性值<input type="text" name="attr_value_list[]" value=""  />';
            $html .= '<input type="hidden" name="attr_goodattr_id_list[]" value="" /><input type="hidden" name="attr_types_list[]" value="0" /><input type="hidden" name="attr_price_list[]" value="0" /><input type="hidden" name="inputtypes[]" value="0" /></div>';
        }

        $where = array(
            'ga.status' => 1,
            'ga.domainid' => array('in',"1,'{$domainid}'")
        );

        //查找系统设置属性
//        $s="";
        if($brandid){
//            $s=" and (brandid=0 or brandid=".$brandid.")";
            $where['ga.brandid'] = array('in',"0,'{$brandid}'");
        }


    //    $sql ="SELECT GoodAttribute.id AS id,GoodAttribute.domainid AS domainid,GoodAttribute.brandid AS brandid,GoodAttribute.name AS name,GoodAttribute.inputtypes AS inputtypes,GoodAttribute.types AS types,GoodAttr.gid AS gid,GoodAttr.id AS gattrid,GoodAttr.attributeid AS attributeid,GoodAttr.attributesubid AS attributesubid,GoodAttr.value AS value,GoodAttr.price AS price FROM B2Cgood_attribute GoodAttribute LEFT JOIN B2Cgood_attr GoodAttr ON GoodAttribute.id=GoodAttr.attributeid  and gid=".$id."
    //  WHERE  GoodAttribute.status = 1 and ( domainid in ( 1,".$domainid.')'.$s." ) ORDER BY GoodAttribute.inputtypes asc ";
    //	$attr =M()->query($sql);
        $attr = M('good_attribute')->field('ga.id,ga.domainid,ga.brandid,ga.name,ga.inputtypes,ga.types,a.gid,a.id AS gattrid,a.attributeid,a.attributesubid,a.value,a.price')
            ->join("ga LEFT JOIN __GOOD_ATTR__ a ON ga.id=a.attributeid AND a.gid='{$id}'")
            ->where($where)
            ->order("ga.inputtypes ASC")
            ->select();
        $spec = 0;

        foreach ($attr AS $key => $val)
        {
            $html .= "<div class='form-item cf'>";
            if ($val['types'] == 1 || $val['types'] == 2)
            {
            $html .= ($spec != $val['id']) ?
                "<a href='javascript:;' class='attr_add_spec'>[+]</a>" :
                "<a href='javascript:;' class='attr_remove_spec'>[-] </a>";
            $spec = $val['id'];
            }

            $html .= "<label class=\"item-label item-label_f\">".$val['name']."</label><input type='hidden' name='attr_id_list[]' value='$val[id]' />";
            if ($val['inputtypes'] == 0)
            {
            $html .= '<input name="attr_keys_list[]" type="hidden" value="' .htmlspecialchars($val['name']). '" class="text input-large" /> ';
            $html .= '<input name="attr_value_list[]" type="text" value="' .htmlspecialchars($val['value']). '" class="text input-large" /> ';
            }
            elseif ($val['inputtypes'] == 2)
            {
            $html .= '<textarea name="attr_value_list[]" class="input-large">' .htmlspecialchars($val['value']). '</textarea>';
            }
            else
            {
            $html .= '<select name="attr_value_list[]">';
            $html .= '<option value="">---请选择---</option>';

            $attr_values = M("GoodAttributeSub")->where("status=1 and attributeid=".$val["id"])->getField("id,name");
            foreach ($attr_values as $k=> $opt)
            {
                $opt = trim(htmlspecialchars($opt));
                if( $val["types"]==0 ){
                $html .= ($val['value'] != $opt) ? '<option value="' . $k . '">' . $opt . '</option>' : '<option selected value="' . $k . '">' . $opt . '</option>';
                }else{
                $html   .= ($val['attributesubid'] != $k) ? '<option value="' . $k . '">' . $opt . '</option>' : '<option selected value="' . $k . '">' . $opt . '</option>';
                }
            }
            $html .= '</select> ';

            }
            $html .= '<input type="hidden" name="inputtypes[]" value="'.$val['inputtypes'].'" />';
            $html .= ($val['types'] == 1 || $val['types'] == 2) ? ' 价格<input type="text" name="attr_price_list[]" value="' . $val['price'] . '" size="5" maxlength="10" />' : ' <input type="hidden" name="attr_price_list[]" value="0" />';
            $html .= '<input type="hidden" name="attr_goodattr_id_list[]" value="'.$val['gattrid'].'" /><input type="hidden" name="attr_types_list[]" value="'.$val['types'].'" /></div>';
        }

        return $html;
    }
    
    //获取相关商品的优惠价格
    private function getvolumeprice($gid=0){
	if($gid){
	    $goodvolumepricelist = M("GoodVolumePrice")->where("gid=".$gid)->select();
	    $this->assign('goodvolumepricelist',$goodvolumepricelist);
	}
    }
    public function getpricebymarkprice(){
	$price=I("get.mprice");
	$s = sprintf("%.2f", $price*90/100);
	$this->success($s);
    }
    /**
     * 设置状态
     * @author wangcheng <253490851@qq.com>
    */
    function _before_setStatus(){
	$ids = I('request.ids');
	if( $ids ){
	    clearcache($ids,'p_');
	}
    }
    function comment(){
	if(IS_POST){
	    $start=I("start");
	    $content=I("content");
	    $commenttime=I("commenttime");
	    $user=I("user");
	    $gid=I("gid");
	    $goodinfo=M("document")->find($gid);
	    $data=array();
	    $common =array(
		"goodid"=>$gid,
		"domainid"=>$goodinfo['domainid'],
		"brandid"=>$goodinfo['brandid'],
		"category_id"=>$goodinfo['category_id'],
		"score"=>3,
		"servicescore"=>5,
		"deliveryscore"=>5,
		"ifcheck"=>1
	    );
	    foreach( $content as $k=>$v ){
		if($v){
		    $data[$k]=$common;
		    $data[$k]["content"]=$v;
		    $data[$k]["create_time"]=strtotime($commenttime[$k]);
		    $data[$k]["goodscore"]=$start[$k];
		    $data[$k]["uid"]= default_create_user($user[$k]);
		}
	    }
	    if($data){
		$res = M("comment")->addAll($data);
		if($res !== false){
		    M("document")->where('id='.$gid)->setInc('comment',count($data) );
		    clearcache('p_'.$gid);
		    $this->success('评论成功',U('Goods/index',array("cate_id"=>$goodinfo['category_id'] )) );
		}
	    }
	    $this->error('评论失败');
	}else{
	    $id=I("get.id");
	    $this->assign('gid',$id);
	    $this->display();
	}
    }
    
    public function searcharticle(){
        $keywords=I("keywords");
	$keywords = htmlspecialchars($keywords);
        if($keywords ){
            $map["status"]=1;
	    $map["model_id"]=2;
            $map["title"]=array("like",'%'.$keywords.'%');
            $uniongood=M("document")->where($map)->field("id,title")->limit(0,20)->order("id desc")->select();
            $this->success($uniongood);
        }
        $this->error(array());
    }
    
    // 会员等级价 = 根据会员等级折扣*销售价格
    
    public function getMemberPricelist($price){
        if(empty($price)){
            $this->error("请输入价格!");
        }
        // 'member_type'=>3
        //$level_list = M("member_level")->where(array('status'=>array('egt',0)))->order("member_type asc,level desc")->select();
        $level_list = M("member_level")->where(array('status'=>1))->order("member_type asc,level desc")->select();
        $this->assign('level_list', $level_list);
        $this->assign('price', $price);
        $re = $this->fetch('Goods/levelpirce');
        if ($re) {
            $this->success($re);
        }
    }
    
    
    
}