<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller { 
    /* 空操作，用于输出404页面 */
    public function _empty(){
	$this->redirect('Index/index');
    } 
    protected function _initialize(){
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置 
        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }
         
        //设置图片路径
        define ("__PICURL__", __ROOT__."/Uploads/Picture/");
        //头像地址
        define ("__PICURLFACE__", __ROOT__."/Uploads/Face/");
        

	
        //获取导航
        $map=array();
        $map["types"]=0;
	$map["domainid"]=1; 
        $nav = $this->getNav($map); 
        $this->assign("navlist",$nav); 

        //查询所有帮助中心信息
        $cateid = "'shouhoufuwu','xinshourumen','guanyuwomen','zhifupeisongfangshi','bangzhuzhongxin'";
        $map = array();
        $map['ismenu'] = 3;
        $map['_string']="name in (".$cateid.")";
        $helpcatelist = $this->common_menulist($map,$cateid);
        $this->assign("helpcatelist",$helpcatelist);

        /*热门搜索*/
	$hotlinklist = $this->getFriendLinks(5); 
	$this->assign ( 'hotlinklist', $hotlinklist);
	$tuijianlinklist = $this->getFriendLinks(9); 
	$this->assign ( 'bottom_tuijian_linklist', $tuijianlinklist); 
    }

    /* 用户登录检测 */
    protected function login(){
	/* 用户登录检测 */
	is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
    }
      
    /**
     * 短信验证码发送接口 
     * @param int        $type    基本的查询条件 
     * @author wangcheng 
     * @return array|false
     * 返回true or false
     */
    public function sendPhoneMsg( $type=1,$tel="" ){
	if(!$tel) return false;
	//短信接口用户名 $uid
	$uid = C("SMSACCOUNT");
	//短信接口密码 $passwd
	$passwd = C("SMSPASSWORD");
	$sign =C("SMSSIGNATURE");
	//随机4个数字  
	$code = "";  
	$arr = array();  
	for($i=0;$i<6;$i++){
	    $arr[$i] = rand(0,9);  
	    $code .= (string)$arr[$i];  
	} 
	//发送到的目标手机号码 $telphone
	$telphone = $tel;//'15123132519';
	//短信内容 $message
	$msg = "尊敬的用户,您的验证码为:".$code.",请及时填写!";
	$message = iconv("UTF-8","GB2312",$msg); 
	
	$gateway = "http://mb345.com:999/ws/batchSend.aspx?CorpID={$uid}&Pwd={$passwd}&Mobile={$telphone}&Content={$message}&Cell=&SendTime=";
	$result = file_get_contents($gateway); 

    //$result = 1;
	if(  $result == 0 || $result == 1)
	{
	    $map["type"]=$type;
	    $map["phone"]=$telphone;
	    $re=M("PhoneVerify")->where($map)->delete();
	    $data=array();
	    $data["code"]=$code;
	    $data["phone"]=$telphone;
	    $data["type"]=$type;
	    $data["create_time"]=time();
	    $data["update_time"]=time();
	    $data["status"]=1;
	    $re=M("PhoneVerify")->add($data);
	    return true;
	}
	return false;
    }
    
    /**
     * 短信验证码发送接口 
     * @param int        $type    基本的查询条件 
     * @author wangcheng 
     * @return array|false
     * 返回true or false
     */
    public function newpwdPhoneMsg( $tel="" ){
	if(!$tel) return false;
	//短信接口用户名 $uid
	$uid = C("SMSACCOUNT");
	//短信接口密码 $passwd
	$passwd = C("SMSPASSWORD");
	$sign ="";//C("SMSSIGNATURE");
	//随机4个数字  
	$code = "";  
	$arr = array();  
	for($i=0;$i<6;$i++){
	    $arr[$i] = rand(0,9);  
	    $code .= (string)$arr[$i];  
	} 
	//发送到的目标手机号码 $telphone
	$telphone = $tel;//'15123132519';
	//短信内容 $message
	$msg = "尊敬的易美康用户您好,您的新密码已重置为:".$code.",请及时修改!";
	$message = iconv("UTF-8","GB2312",$msg); 
	$gateway = "http://mb345.com:999/ws/batchSend.aspx?CorpID={$uid}&Pwd={$passwd}&Mobile={$telphone}&Content={$message}&Cell=&SendTime=";
	$result = file_get_contents($gateway); 

    //$result = 1;

	if(  $result == 0 || $result == 1)
	{
	    $map["type"]=2;
	    $map["phone"]=$telphone;
	    $re=M("PhoneVerify")->where($map)->delete();
	    $data=array();
	    $data["code"]=$code;
	    $data["phone"]=$telphone;
	    $data["type"]=1;
	    $data["create_time"]=time();
	    $data["update_time"]=time();
	    $data["status"]=1;
	    $re=M("PhoneVerify")->add($data);
	    return $code;
	}
	return "";
    }
    /*
    * 产品公共模块查询调用
    * return array 
    */
    public function getDocument($map = array(), $order = "id desc",$limit=null) {
	$map['status'] = 1;
	$map['display'] = 1;
	$map["ifcheck"] = 1; 
	if(!isset($map["issales"])){
		$map["issales"] = 1;
	    }else{
	    unset($map["issales"]);
	} 
	if(!isset($map["model_id"])){
	    $map["model_id"] = 5;
	}
	if($limit){
	    $lists =D("DocumentView")->where($map)->field("id")->order($order)->limit($limit)->select(); 
	}else{
	    $lists =D("DocumentView")->where($map)->field("id")->order($order)->select(); 
	} 

	$documentViewModel = D("DocumentView"); 
	foreach($lists as $k => $v){
	    $product_detail = array();
	    $product_detail = S('p_'.$v['id']); 
	    if(!$product_detail){
		$product_detail = $documentViewModel->find($v['id']); 
		if( $product_detail["cover_id"] || $product_detail['pics'] ){
		    $arr=array();
		    if( $product_detail['pics'] ){
			$arr=explode(",",$product_detail['pics']);
		    }
		    $arr[]=$product_detail["cover_id"];
		    $arrmap["id"]=array("in",$arr);
		    
		    $attinfo = M("picture")->where($arrmap)->getField("id,path");
		    
		     
		    if(isset($attinfo[$product_detail['cover_id']])){
			$url =  __PICURL__.$product_detail['domainid'].'/'.$attinfo[$product_detail['cover_id']]; 
			$newurl = $this->ymk_image_thumb($url,200,200); 
			$attinfo[$product_detail['cover_id']] = $newurl; 
		    }
 		    
		    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
		    foreach($attinfo as $ckey=>$cvalue){
			if($ckey==$product_detail['cover_id']) continue;
			$attinfo[$ckey] = __PICURL__.$product_detail['domainid'].'/'.$cvalue;
		    }
		    $product_detail["pics_img"]  = $attinfo;
		}
		S('p_'.$v['id'], $product_detail);
	    } 
	    $lists[$k] = $product_detail; 
	}
	if($limit==1){
	    return $lists[0];
	} 
	return $lists;
    }
    
    /*
    * 文章公共模块查询调用
    * return array 
    */
    public function getArticle($map = array(), $order = "id desc", $limit=null) {
	$map['status'] = 1;
	$map['display'] = 1;//可见
	$map["ifcheck"] = 1;//已审核
	$map["model_id"] = 2;//文章模型
	if($limit){
	    $lists =M("document")->where($map)->field("id")->order($order)->limit($limit)->select(); 
	}else{
	    $lists =M("document")->where($map)->field("id")->order($order)->select(); 
	}
	$documentViewModel = D("ArticleView"); 
	foreach($lists as $k => $v){
		    $article_detail = array();
		    $article_detail = S('a_'.$v['id']);
		    if(!$article_detail){
			$article_detail = $documentViewModel->find($v['id']); 
			if($article_detail["cover_id"]){
			    $arrmap["id"]=$article_detail["cover_id"];
			    $attinfo=array();
			    $attinfo = M("picture")->where($arrmap)->getField("id,path"); 
			    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
			    foreach($attinfo as $ckey=>$cvalue){
				$attinfo[$ckey] = __PICURL__.$article_detail['domainid'].'/'.$cvalue;
			    } 
			    $article_detail["pics_img"] =$attinfo; 
			}
			//标签分析
			if(isset($article_detail['keywords']) && !empty($article_detail['keywords'])){
			    $article_detail["tags"] = $this->analyist_tags($article_detail['keywords'],array(',',' '));
			}else{
			    $article_detail["tags"] = array();
			} 
			S('a_'.$v['id'], $article_detail);
		    } 
		    $lists[$k] = $article_detail; 
	}
	if($limit==1){
	    return $lists[0];
	} 
	return $lists;
    }
    
    /*
    * 评论公共模块查询调用
    * return array 
    */
    public function getComment($map = array(), $order = "id desc", $limit=null) {
	$map['status'] = 1; 
	$map["ifcheck"] = 1;//已审核
	$comment =D("CommentView");
	if($limit){
	    $lists =$comment->where($map)->field("id")->order($order)->limit($limit)->select(); 
	}else{
	    $lists = $comment->where($map)->field("id")->order($order)->select(); 
	} 
	foreach($lists as $k => $v){
	    $comment_detail = array();
	    //$comment_detail = S('pl_'.$v['id']); 
	    if(!$comment_detail){
		$comment_detail = $comment->find($v['id']);
		if($comment_detail["face"]){
		    $attinfo = M("picture")->find($comment_detail["face"]); 
		    $comment_detail["face"] =$attinfo["path"]; 
		} 
		S('pl_'.$v['id'], $comment_detail);
	    } 
	    $lists[$k] = $comment_detail; 
	} 
	return $lists;
    }
     /*
    * 分类公共模块查询调用
    * return array 
    */
    public function getCatalog($map = array(), $order = "id desc", $limit=null) {
	$map['status'] = 1;
	$map['display'] = 1;//可见
	$map["ifcheck"] = 1;//已审核 
	if($limit){
	    $lists =M("category")->where($map)->field("id")->order($order)->limit($limit)->select(); 
	}else{
	    $lists =M("category")->where($map)->field("id")->order($order)->select(); 
	}
	$category = D("category"); 
	foreach($lists as $k => $v){
	    $category_detail = array();
	    $category_detail = S('c_'.$v['id']); 
	    if(!$category_detail){
		$category_detail = $category->find($v['id']);
		S('c_'.$v['id'], $category_detail);
	    }
	    $lists[$k] = $category_detail; 
	}
	if( $limit==1 ){
	    return $lists?$lists[0]:$lists;
	}
	return $lists;
    }
    
    /*
    * 频道公共模块查询调用
    * return array 
    */
    public function getDomain($map = array(), $order = "id desc", $limit=null) {
	$map['status'] = 1; 
	if($limit){
	    $lists =M("subdomain")->where($map)->field("id")->order($order)->limit($limit)->select(); 
	}else{
	    $lists =M("subdomain")->where($map)->field("id")->order($order)->select(); 
	}
	$subdomain = M("subdomain"); 
	foreach($lists as $k => $v){
	    $domain_detail = array();
	    //$domain_detail = S('d_'.$v['id']); 
	    if(!$domain_detail){
		$domain_detail = $subdomain->find($v['id']);
		S('d_'.$v['id'], $domain_detail);
	    }
	    $lists[$k] = $domain_detail; 
	}
	if( $limit==1 ){
	    return $lists?$lists[0]:$lists;
	}
	return $lists;
    }
    /*
     * 问答公共模块查询调用
     * return array 
     */
    public function getfaqQuestion($map = array(), $order = "id desc", $limit=null) {
	//$field = 'id,title,content,category_id,keywords,bestanswerid,create_time,answercount';
	$field = '*';
	//$map["domainid"]=cookie("current_domainid");
	$map["ifcheck"]=1;
	$map["status"]=1; 
	if($limit){
	    $lists =M("faq_question")->where($map)->field("id")->order($order)->limit($limit)->select(); 
	}else{
	    $lists =M("faq_question")->where($map)->field("id")->order($order)->select(); 
	} 
	
	foreach($lists as $k => $v){
	    $question_detail = array();
	    $question_detail = S('faq_q_'.$v['id']); 
	    if(!$question_detail){
		$question_detail = M("faq_question")->field($field)->find($v['id']); 
		//标签分析
		if ( trim($question_detail['keywords']) ) {
		    $question_detail["tags"] = explode(",",$question_detail['keywords']);
		} else {
		    $question_detail["tags"] = array();
		} 
		S('faq_q_'.$v['id'], $product_detail); 
	    }
	    
	    $answer_detail=array(); 
	    if( $question_detail["bestanswerid"] ){
		$answer_detail = S('faq_a_'.$question_detail["bestanswerid"]);
		if(!$answer_detail){
		    $answer_detail = M("faq_answer")->find( $question_detail["bestanswerid"] );
		    $answer_detail["doctorinfo"]=M("faq_doctor")->find($answer_detail["doctorid"]);
		    S('faq_a_'.$question_detail["bestanswerid"],$answer_detail);
		} 
	    }
	    $question_detail["answer"]=$answer_detail;
	    $lists[$k] = $question_detail; 
	}
	if($limit==1){
	    return $lists[0];
	}
	return $lists; 
    }
    /*
     * 百科公共模块查询调用
     * return array 
     */
    public function getBaike($map = array(), $order = "id desc", $limit=null) {
       //查询条件
	//$map["domainid"]=cookie("current_domainid");
	$map["status"]=1;
	$map["ismenu"]=4;
	$map["ifcheck"]=1;
	//查询结果
	if($limit){
	    $lists =M("baike")->where($map)->field("id")->order($order)->limit($limit)->select(); 
	}else{
	    $lists =M("baike")->where($map)->field("id")->order($order)->select(); 
	} 
	foreach($lists as $k => $v){
	    $baike_detail = array();
	    $baike_detail = S('b_'.$v['id']); 
	    if(!$baike_detail){
		$baike_detail = M("baike")->find($v['id']);
		//对图片进行处理
		if( $baike_detail["cover_id"] ){ 
		    $attinfo = M("picture")->find($baike_detail["cover_id"]); 
		    $baike_detail["att_name"] =$attinfo["path"]; 
		}
	      S('b_'.$v['id'], $baike_detail); 
	    } 
	    $lists[$k] = $baike_detail; 
	}
	return $lists; 
    }
    
    /*
     * 专题公共模块查询调用
     * return array 
     */
    public function getSpecial($map = array(), $order = "id desc", $limit=null) {
       //查询条件 
	$map["status"]=1; 
	//查询结果
	if($limit){
	    $lists =M("specialtopic")->where($map)->field("id")->order($order)->limit($limit)->select(); 
	}else{
	    $lists =M("specialtopic")->where($map)->field("id")->order($order)->select(); 
	} 
	foreach($lists as $k => $v){
	    $zt_detail = array();
	    $zt_detail = S('zt_'.$v['id']); 
	    if(!$zt_detail){
		$zt_detail = M("specialtopic")->find($v['id']);
		//对图片进行处理
		if ($zt_detail["cover_id"] ) { 
		    $arrmap["id"] =$zt_detail["cover_id"];
		    $attinfo=array();
		    $attinfo = M("picture")->where($arrmap)->getField("id,path"); 
		    foreach($attinfo as $ckey=>$cvalue){
			$attinfo[$ckey] = __PICURL__.$zt_detail['domainid'].'/'.$cvalue;
		    } 
		    $zt_detail["pics_img"] = $attinfo;
		}
		S('zt_'.$v['id'], $zt_detail); 
	    }
	    $lists[$k] = $zt_detail; 
	}
	if($limit==1){
	    return $lists[0];
	} 
	return $lists; 
    }
    
    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base    基本的查询条件
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *
     * @return array|false
     * 返回数据集
     */
    protected function _lists ($model,$where=array(),$order='',$base = array('status'=>array('egt',0)),$field=true){
        $options    =   array();
        $REQUEST    =   array_merge(I('post.'),I('get.'));//(array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']); 
        $options['where'] = array_filter(array_merge( (array)$base, /*$REQUEST,*/ (array)$where ),function($val){
            if($val===''||$val===null){
                return false;
            }else{
                return true;
            }
        });
        if( empty($options['where'])){
            unset($options['where']);
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{ 
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $page->rollPage = 5;
        $p =$page->frontshow(); 
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
	$aPage = I("page",1,"intval"); 
	$this->assign('p', $aPage);
        $options['limit'] = $page->firstRow.','.$page->listRows;
        $model->setProperty('options',$options);
	$list = $model->field($field)->select();
        return $list;
    }
    
     protected function _ajaxlists ( $model,$where=array(),$order='',$base = array('status'=>array('egt',0)),$field=true,$callback){ 
        $REQUEST    =   array_merge(I('post.'),I('get.')); 
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']); 
        $options['where'] = array_filter(array_merge( (array)$base, /*$REQUEST,*/ (array)$where ),function($val){
            if($val===''||$val===null){
                return false;
            }else{
                return true;
            }
        });
        if( empty($options['where'])){
            unset($options['where']);
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();
	
        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{ 
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\AjaxPage($total, $listRows,$callback);
        if($total>$listRows){
            $page->setConfig('theme','%first% %prePage% %upPage% %linkPage% %downPage% %nextPage% %end% %totalRow% %header% %nowPage%/%totalPage% 页 ');
        }
        $page->rollPage = 5;
        $p =$page->frontshow(); 
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;
        $model->setProperty('options',$options);
	$list = $model->field($field)->select(); 
        return $list;
    }
    
    /**热门搜索热词**/
    public function getHotsearch(){
	$arr = array();
	$str=M('config')->where('id="40"')->getField("value");
	$hotsearch=explode(",",$str);
	return $hotsearch; 
    }
    /**获取分类，并生成树形**/
    public function getCategory($map=array()){
        $field = 'id,domainid,name,pid,title';
        //$map["domainid"]=cookie("current_domainid");
        $map["display"]=1; 
        $categoryq = D('Category')->field($field)->order('sort asc')->where($map)->select(); 
        $catelist = $this->unlimitedForLevel($categoryq); 
        return $catelist;
    }
    
    /**获取树形结构**/
    public function unlimitedForLevel($cate,$name = 'child',$pid = 0){
	$arr = array();
	foreach ($cate as $key => $v) {
	    //判断，如果$v['pid'] == $pid的则压入数组Child
	    if ($v['pid'] == $pid) {
		//递归执行
		$v[$name] = self::unlimitedForLevel($cate,$name,$v['id']);
		$arr[] = $v;
	    }
	}
	return $arr;
    }


    /**获取分类，并生成树形 根据频道区分类别**/
    public function getDomainidCategory($map=array()){
        $field = 'id,name,pid,title,domainid';
        //$map["domainid"]=cookie("current_domainid");
        $map["display"]=1; 
        $categoryq = D('Category')->field($field)->order('sort asc')->where($map)->select(); 
        $catelist = $this->unlimitedForLevel($categoryq);

        if(!empty($catelist)){
            $hadchild = array();
            foreach($catelist as $ckey=>$catechild){
                if(!empty($catechild['child'])){
                    $hadchild[] = $catechild;
                    unset($catelist[$ckey]);
                }
            }
           $catelist = array_merge($hadchild, $catelist);
        }

        $domainmap = array();//查询所有频道信息，优先级排序
        $domainmap["status"]=1; 
        $alldomaininfo =M("subdomain")->where($domainmap)->order("weight DESC")->getField("id,name,url,mark,meta_title,icon,keywords");
        $andomainid = array();
        foreach($catelist as $kcate){
            $andomainid[$kcate['domainid']][] = $kcate;
        }
        $recatelist = array();
        foreach($alldomaininfo as $kdomainid=>$kdomainidvalue){
           if(isset($andomainid[$kdomainid])) {
               $kdomainidvalue['catelist'] = $andomainid[$kdomainid];
               $recatelist[] = $kdomainidvalue;
           }
        }

        return $recatelist;
    }


    /*
     * 友情链接
     * return array 
     */
    public function getFriendLinks( $position=1 ,$domainid=0,$checkdomain=0){
	//$map["domainid"]=cookie("current_domainid");
	if($checkdomain==1){
	    $map["domainid"]=$domainid;
	}
	$map["position"]= array("in",$position.",0"); 
	$map["status"]=1; 
	$lists =M("links")->where($map)->field("id")->select();
	foreach($lists as $k => $v){
	    $linkdetail = array();
	    $linkdetail = S('f_'.$v['id']); 
	    if(!$linkdetail){
		$linkdetail = M("links")->find($v['id']);
		S('f_'.$v['id'], $linkdetail); 
	    } 
	    $lists[$k] = $linkdetail; 
	}
	return $lists;
    }
    
    /*
     * 获取标签
     * return array 
     */
    public function getTags( $map=array() ){
	//$map["domainid"]=cookie("current_domainid"); 
	$map["status"]=1; 
	$lists =M("tags")->where($map)->field("id")->select();
	foreach($lists as $k => $v){
	    $tagdetail = array();
	    $tagdetail = S('t_'.$v['id']); 
	    if(!$tagdetail){
		$tagdetail = M("tags")->find($v['id']);
		S('t_'.$v['id'], $tagdetail); 
	    } 
	    $lists[$k] = $tagdetail; 
	}
	return $lists;
    } 

    
    
    /***************************************************************
      *created date:2015/4/20 14:43 
      *created author:sheshanhu
      *content:
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function getEntriesList($type='',$num){
       //查询条件
	//$map["domainid"]=cookie("current_domainid");
	if($type == 'ishot'){
	     $map["ishot"]=1;
	}elseif($type == 'isrecommend'){
	     $map["isrecommend"]=1;
	}
	$map["status"]=1;
	//create_time 创建时间，最新百科
	//ishot 是否热门
	//isrecommend 是否推荐
	//查询结果
	$lists =M("entries")->where($map)->field("id")->order('create_time desc')->limit($num)->select();
	//对结果处理
	foreach($lists as $k => $v){
	    $baike_detail = array();
	    $baike_detail = S('e_'.$v['id']); 
	    if(!$baike_detail){
	      $baike_detail = M("entries")->find($v['id']);
	      //如果是推荐百科要有图片
	      if($type == 'isrecommend'){
		 if($baike_detail["cover_id"]){ 
			//对图片进行处理
			if( $baike_detail["cover_id"] || $baike_detail['pics'] ){
			    $arr = array();
			    if(isset($baike_detail['pics'])){
			    $arr=explode(",",$baike_detail['pics']);
			    }
			    $arr[]=$baike_detail["cover_id"];
			    $arrmap["id"]=array("in",$arr);
			    $attinfo=array();
			    $attinfo = M("picture")->where($arrmap)->getField("id,path");
                
                //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                foreach($attinfo as $ckey=>$cvalue){
                    $attinfo[$ckey] = __PICURL__.$baike_detail['domainid'].'/'.$cvalue;
                }

			    $baike_detail["pics_img"] = $attinfo;
			} 
		  }
	      }
	      S('e_'.$v['id'], $baike_detail); 
	    }
	    $lists[$k] = $baike_detail; 
	}
	    return $lists; 
    }
    
    
    /***************************************************************
      *created date:2015/4/20 12:01 
      *created author:sheshanhu
      *content:查询百科表中最新的前10天记录
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function getNewBaikeArticle($type='',$num,$baikeid=0,$order='create_time desc'){
       //查询条件
	//$map["domainid"]=cookie("current_domainid");
	$map["display"]=1;
	$map["ismenu"]=4;//百科分类类型
	$map["status"]=1;

	if($type == 'ishot'){
	    $map["ishot"]=1;
	}elseif($type == 'isrecommend'){
	    $map["isrecommend"]=1;
	}elseif($type == 'id'){
	    $map["id"]=$baikeid;
	}elseif($type=='cover_id'){
	    $map["cover_id"]= array('gt',0);
	}
    
	//create_time 创建时间，最新百科
	//ishot 是否热门
	//isrecommend 是否推荐
	//查询结果
	$lists =M("baike")->where($map)->field("id")->order( $order )->limit($num)->select();
	//对结果处理
	foreach($lists as $k => $v){
	    $baike_detail = array();
	    //$baike_detail = S('b_'.$v['id']); 
	    if(!$baike_detail){
	      $baike_detail = M("baike")->find($v['id']);
		//对图片进行处理
		if( $baike_detail["cover_id"]  ){//|| $baike_detail['pics']
		    $arr = array();
		    /*if(isset($baike_detail['pics'])){
		    $arr=explode(",",$baike_detail['pics']);
		    }*/
		    $arr[]=$baike_detail["cover_id"];
		    $arrmap["id"]=array("in",$arr);
		    $attinfo=array();
		    $attinfo = M("picture")->where($arrmap)->getField("id,path");
            
            //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
            foreach($attinfo as $ckey=>$cvalue){
                $attinfo[$ckey] = __PICURL__.$baike_detail['domainid'].'/'.$cvalue;
            }

		    $baike_detail["pics_img"] = $attinfo;
		}
	      S('b_'.$v['id'], $baike_detail); 
	    } 
	    $lists[$k] = $baike_detail; 
	}
	return $lists; 
    }
        
    /**
    * 分类查询 分类列表页面调用
    * @param int | string $id  查询分类名称或id
    * @author wangcheng 
    * @return array|false
    */
    public function categoryinfo( $id = 0){
	/* 标识正确性检测 */
	$id = $id ? $id : I('get.id', 0);
	if(empty($id)){
	    $this->error('没有指定分类！');
	}
	/* 获取分类信息 */
	$category = D('Category')->info($id);
	if($category && 1 == $category['status']){
	    switch ($category['display']) {
		case 0:
		$this->error('该分类禁止显示！');
		break;
		//TODO: 更多分类显示状态判断
		default:
		return $category;
	    }
	} else {
	    $this->error('分类不存在或被禁用！');
	}
    }
    /**
    * 获取导航
    * @param array | array map  查询条件
    * @author wangcheng 
    * @return array|false
    */
    public function getNav( $map = array() ){
	//$map["domainid"]=cookie("current_domainid"); 
	$map["status"]=1; 
	$lists =M("channel")->where($map)->field("id")->order("sort asc")->select();
	foreach($lists as $k => $v){
	    $navdetail = array();
	    //$navdetail = S('nav_'.$v['id']); 
	    if(!$navdetail){
		$navdetail = M("channel")->find($v['id']);
		S('nav_'.$v['id'], $navdetail); 
	    } 
	    $lists[$k] = $navdetail; 
	}
	return $lists;
    }
    
    /**
    * 获取所有品牌
    * @param array | array map  查询条件
    * @author wangcheng 
    * @return array|false
    */
    public function getBrand( $map = array(),$order = "id desc", $limit=null) { 
	$map["status"]=1;
	$brandModel = D("subdomain_brand");
	if(!empty($limit)){
		$lists =$brandModel->where($map)->field("id")->order($order)->limit($limit)->select();
	}else{
		$lists =$brandModel->where($map)->field("id")->order($order)->select();
	}
	foreach($lists as $k => $v){
	    $brand_detail = array();
	    $brand_detail = S('pp_'.$v['id']); 
	    if(!$brand_detail){
		$brand_detail = $brandModel->find($v['id']); 
		if( $brand_detail["bgimg"] || $brand_detail['icon'] ){
		    $arr=array();
		    if( $brand_detail['bgimg'] ){
			$arr[]=$brand_detail['bgimg'];
		    }
		    if( $brand_detail['icon'] ){
			$arr[]=$brand_detail['icon'];
		    }
		    $arrmap["id"]=array("in",$arr);
		    $attinfo=array();
		    $attinfo = M("picture")->where($arrmap)->getField("id,path"); 

		    //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
		    foreach($attinfo as $ckey=>$cvalue){
			$attinfo[$ckey] = __PICURL__.$brand_detail['domainid'].'/'.$cvalue;
		    } 
		    $brand_detail["pics_img"] = $attinfo;
		} 
		S('pp_'.$v['id'], $brand_detail);
	    } 
	    $lists[$k] = $brand_detail; 
	}
	if($limit==1){
	    return $lists[0];
	}
	return $lists;
    }

    /***************************************************************
     *created date:2015/4/29 13:55
     *created author:sheshanhu
     *content:标签内容分解
     *modefiy person:
     *modefiy date:
     *note:
     ****************************************************************/
    public function analyist_tags($keywords, $strt = array(" ")) {
        $return = array();
        if (!empty($keywords)) {
            $keywords2 = preg_replace("/[\s]+/is"," ",$keywords);
            foreach ($strt as $vb) {
                $keywords2 = str_replace($vb," ",$keywords2);
            }
            $return = explode(" ",$keywords2);
        }
        /*if (count($return) > 1) {
            unset($return[array_search($keywords, $return) ]);
        }*/
        return $return;
    }

    /***************************************************************
     *created date:2015/4/29 13:55
     *created author:sheshanhu
     *content:查询分类信息
     *modefiy person:
     *modefiy date:
     *note:
     ****************************************************************/
    public function common_menulist($map=array(),$categoryid) {
        $field = 'id,name,pid,title';
        //$map["domainid"] = cookie("current_domainid");
        $map["display"] = 1;
        $map["status"] = 1;
        if(!isset($map["ismenu"])){
            $map["ismenu"] = 3;
        }
        $categoryq = D('Category')->field($field)->order('sort asc')->where($map)->select();
        
        if(!empty($categoryq)){
            $levelid = array();
            foreach($categoryq as $cate){
              $levelid[] =  $cate['id'];
            }
            $stringid = implode(',',$levelid);
            $map['_string']='id in ('.$stringid.') OR pid in ('.$stringid.')';
            $categoryq = D('Category')->field($field)->order('sort asc')->where($map)->select();
        }

        $catelist = $this->unlimitedForLevel($categoryq);
        return $catelist;
    }


     /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    public function showRegError($code = 0) {
        switch ($code) {
            case -1:
                $error = "用户名长度必须在16个字符以内！";
                break;

            case -2:
                $error = "用户名被禁止注册！";
                break;

            case -3:
                $error = "用户名被占用！";
                break;

            case -4:
                $error = "密码长度必须在6-30个字符之间！";
                break;

            case -5:
                $error = "邮箱格式不正确！";
                break;

            case -6:
                $error = "邮箱长度必须在1-32个字符之间！";
                break;

            case -7:
                $error = "邮箱被禁止注册！";
                break;

            case -8:
                $error = "邮箱被占用！";
                break;

            case -9:
                $error = "手机格式不正确！";
                break;

            case -10:
                $error = "手机被禁止注册！";
                break;

            case -11:
                $error = "手机号被占用！";
                break;

            default:
                $error = "未知错误";
        }
        return $error;
    }

    /***************************************************************
      *created date:2015/5/21 11:41 
      *created author:sheshanhu
      *content:对输入框中的特殊字符进行判断
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function int_num_validate($num) {
        $returnnum = $num;
        if (!preg_match ("/[0-9]+/i", $num)) {
          $returnnum =1;
        }elseif($num<=0){
          $returnnum =1;
        }
        return $returnnum;
    }
    
    /***************************************************************
      *created date:2015/6/16 
      *created author:sheshanhu
      *content:搜索时提示标题，查询出前十条记录
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function checktitle(){
        $map=array();
        //$map["domainid"]=cookie("current_domainid");  
        $map['status'] = 1;
        $map['ifcheck'] = 1;
        $modelname = '';
        $order = '' ;
        if(CONTROLLER_NAME == 'Article'){
            $modelname = 'document';//文章
            $map["model_id"] = 2;//文章模型
            $map["display"]=1; 
            $order = 'view DESC';
        }elseif(CONTROLLER_NAME == 'Good'){
            $modelname = 'DocumentView';//商品
            $map["display"]=1; 
            $map["model_id"] = 5;
            $order = 'view DESC';
        }elseif(CONTROLLER_NAME == 'Ask'){
            $modelname = 'faq_question';//问答
            
        }elseif(CONTROLLER_NAME == 'Special'){
            $modelname = 'specialtopic'; //专题
            $order = 'views DESC';
        }elseif(CONTROLLER_NAME == 'Baike'){
            $modelname = 'baike'; //百科
            $order = 'views DESC';
        }else{
            $modelname = CONTROLLER_NAME;
        }
        $model = D ($modelname);

        $title = I('keyword');
        if(!empty($title)){
           $map["title"] = array("like","%" . $title . "%");  
        }

        $list=array();
        if (! empty ( $model )) {
            $list = $this->_lists($model, $map,$order);
        }
        //返回标题
        $return = array();
        if(!empty($list)){
            foreach($list as $v){
                $return[] = $v['title'];
            }
        }
       $this->ajaxReturn($return);
    }


    /***************************************************************
      *created date: 2015/6/19 15:12
      *created author:sheshanhu
      *content:根据商品组合编号查询组合信息
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function getproductsgroup($map = array(), $order = "id desc",$limit=null) {
	$map['status'] = 1;
        //$map["domainid"]= cookie("current_domainid");
        if($limit){
            $lists =M("ProductsGroup")->where($map)->field("id")->order($order)->limit($limit)->select(); 
        }else{
            $lists =M("ProductsGroup")->where($map)->field("id")->order($order)->select(); 
        }

        $productsgroupModel = D("ProductsGroup"); 
        foreach($lists as $k => $v){
            $product_detail = array();
            $product_detail = S('pg_'.$v['id']); 
            if(!$product_detail){
                $product_detail = $productsgroupModel->find($v['id']);
                //组合商品信息查询
                $gmap = array();
                $gmap['id'] = array('in',$product_detail['uniongood']);
                $product_detail['goodsinfo'] = $this->getDocument($gmap);
                S('pg_'.$v['id'], $product_detail);
            } 
            $lists[$k] = $product_detail; 
        }
        if($limit==1){
            return $lists[0];
        }
        return $lists;
    }

    /***************************************************************
      *created date:2015/6/29 15:17 
      *created author:sheshanhu
      *content:全局过滤敏感词
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function filter_sensitive_word(){
        foreach($_POST as $k=>$v){
            $_POST[$k] = $this->replace_sensitive_word($v);
        }
    }


    /***************************************************************
      *created date:2015/6/29 15:17 
      *created author:sheshanhu
      *content:替换指定的敏感词
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    public function  replace_sensitive_word($word){
        $arr = file("./sensitive_word.txt");//敏感词典
        $arr1 = array();
        foreach($arr as $k=>$v){
            $arr1["num".$k] = trim($v);
        }
        return  $content = str_replace($arr1,"*",$word);
    }


    /***************************************************************
      *created date:2015/6/26 10:38 
      *created author:sheshanhu
      *content:图片缩略图生成及地址返回
      *modefiy person:
      *modefiy date:
      *note:
    ****************************************************************/
    function ymk_image_thumb($url,$width,$height,$iscreted=false,$type='Picture',$rootpath=''){
        // URL /yzjj/Uploads/Picture/2/2015-06-17/5581404d75cc4.jpg
        //对图片分析获取图片名称
        /*Array
        (
            [dirname] => /yzjj/Uploads/Picture/2/2015-06-17
            [basename] => 5581404d75cc4.jpg
            [extension] => jpg
            [filename] => 5581404d75cc4
        )*/

        $path_parts = pathinfo($url);
        $dirname = $path_parts['dirname'];
        $dirarray = explode('Uploads/'.$type.'/',$dirname); 
        if(empty($rootpath)){
            $uploadimage = C('PICTURE_UPLOAD');//获取上传图片的文件夹地址
            $newdirname = $uploadimage['rootPath'].$dirarray[1].'/';
        }else{
            $newdirname = $rootpath.$dirarray[1].'/';
        } 
        $reurl = $newdirname.$path_parts['basename'];

        $newimagename = $path_parts['filename'].'_'.$width.'x'.$height.'.'.$path_parts['extension'];

        $newurl = $newdirname.$newimagename;
   
        if(file_exists($reurl)){
            $size = getimagesize($reurl);
            if($size[0]>0 && $size[1]>0){
                //判断文件是否存在，如果存在则不生成，
                if(!file_exists($newurl) || $iscreted){
                    $image = new \Think\Image(); 
                    $image->open($reurl);
                     //按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
                    $b = $image->thumb($width, $height)->save($newurl);
                }
            }
        }

        //如果创建成功，图片存在则返回图片，如果不存在，则返回原地址。
        if(file_exists($newurl)){
            $newurl = $path_parts['dirname'].'/'.$newimagename;
            return $newurl;
        }else{
           return $url;
        }
    }
    
    //后台获取伪静态url地址
    function admingeturl($url,$vars,$suffix,$domain){
	$file1   = APP_PATH.'Home/Conf/config.php';
        C( load_config($file1) ) ;
	$file2   = APP_PATH.'Home/Conf/router.php';
        C( load_config($file2) ) ; 
	$url1 = U($url,$vars,$suffix,$domain );
	$file3   = APP_PATH.'Admin/Conf/config.php';
	C( load_config($file3) ) ; 
	return $url1;
    }
    function clearpic($dir) {
	//先删除目录下的文件：
	$dh=opendir( SITE_PATH.'Uploads/Picture' );
	while ($file=readdir($dh)) {
	  if($file!="." && $file!="..") {
	    $fullpath=$dir."/".$file;
	    if(!is_dir($fullpath)) {
		unlink($fullpath);
	    } else {
		deldir($fullpath);
	    }
	  }
	}
       
	closedir($dh);
	//删除当前文件夹：
	if(rmdir($dir)) {
	  return true;
	} else {
	  return false;
	}
    }
}


