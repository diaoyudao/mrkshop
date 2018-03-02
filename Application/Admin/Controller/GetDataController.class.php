<?php
namespace Admin\Controller;
/**
 * 后台采集控制器
 * @author qchlian <3580164@qq.com>
 */
class GetDataController extends AdminController {
    /**
     * 淘宝采集 
     * @author qchlian <3580164@qq.com>
     */ 
    function getdataalibaba(){
	set_time_limit(0);
	$url = I("url");
	$url = parse_url($url); 
	$content="";
	$data = array();  
	$url="http://detail.1688.com".$url["path"]; 
	ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	$content=file_get_contents($url); 
	$content=iconv('gbk','utf-8//IGNORE',$content); 
	$rules = $this->_getRules("alibaba");
	// 数据数组 
	$params = array('price','content','name','attr','product_img');//'description', 'content', 'price','product_img');
	$from="alibaba";
	foreach ($params as $v) {
	    $data[$v] = $this->collectOne($from,$content, $rules[$v],$v);
	}
	$data["status"]=1;
	 
	if( $data ){
	    $this->ajaxReturn($data);
	}else{
	    $data["status"]=0;
	    $this->ajaxReturn($data);
	} 
    }
    
    /**
     * 淘宝采集 
     * @author qchlian <3580164@qq.com>
     */ 
    function getdatataobao(){
	set_time_limit(0);
	$url = I("url");
	$url = parse_url($url);
	$arr=array();
	parse_str($url["query"],$arr);
	$content="";
	$data = array();
	if( isset($arr["id"]) ){
	    $url="http://item.taobao.com/item.htm?id=".$arr["id"]; 
	    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	    $content=file_get_contents($url);  
	    $content=iconv('gbk','utf-8//IGNORE',$content);
	    $rules = $this->_getRules("taobao");
	    // 数据数组 
	    $params = array('name', 'attr','description', 'content', 'price','product_img');
	    $from="taobao";
	    foreach ($params as $v) {
		$data[$v] = $this->collectOne($from,$content, $rules[$v],$v);
	    }
	    $data["status"]=1;
	}
	if( $data ){
	    $this->ajaxReturn($data);
	}else{
	    $data["status"]=0;
	    $this->ajaxReturn($data);
	} 
    }
    
    /**
     * 1688评论采集 
     * @author qchlian <3580164@qq.com>
     */ 
    function getcommentalibaba(){
	set_time_limit(0);
	$url = I("url");
	$url = parse_url($url); 
	$content="";
	$data = array();  
	$url="http://detail.1688.com".$url["path"]; 
	ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	$content=file_get_contents($url); 
	$content=iconv('gbk','utf-8//IGNORE',$content); 
	preg_match('/member_id:\"(.+?)\"/is',$content,$matches );
	preg_match('/offer_id:\"(.+?)\"/is',$content,$matches2 );
	$member_id=$matches[1];
	$offerid=$matches2[1];
	if( $member_id && $offerid ){
	    $page = I("caiji_pages",1,intval);
	 
	    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	    $url="http://rate.1688.com/remark/offerDetail/rates.json?_input_charset=GBK&offerId=".$offerid."&member_id=".$member_id."&page=".$page; 
	    $content=file_get_contents($url); 
	    $taobao_api=iconv('gbk','utf-8//IGNORE',$content); 
	    $taobao_api=str_replace("}]})","}]}",$taobao_api);
	    $taobao_api=str_replace("卖家","客服",$taobao_api);
	    $taobao_api=str_replace("老板","客服",$taobao_api);
	    $taobao_api=str_replace("你家","",$taobao_api);
	    $taobao_api=str_replace("5分","5星",$taobao_api);
	    $taobao_api=str_replace("五分","五星",$taobao_api);
	    $taobao_api=str_replace("家","",$taobao_api);
	    $taobao_api=str_replace("旗舰","",$taobao_api);
	    $taobao_api=str_replace("掌柜","客服",$taobao_api);
	    $taobao_api=str_replace("老婆","姐姐",$taobao_api);
	    $taobao_api=str_replace("天猫","",$taobao_api);
	    $taobao_api=str_replace("淘宝","",$taobao_api);
	    $taobao_api=str_replace("此用户没有填写评论!","收货后特意查了下，是正品哦。",$taobao_api);
	    $taobao_api=str_replace("评价方未及时做出评价,系统默认好评!","",$taobao_api); 
	    $taobao_api=str_replace("&nbsp;","",$taobao_api);
	    $taobao_api=str_replace("&rdquo;","",$taobao_api);
	    $taobao_api=str_replace("&ldquo;","",$taobao_api);
	    $taobao_api=str_replace("&middot;","",$taobao_api);
	    $taobao_api=str_replace("&apos;","",$taobao_api);
	    $taobao_api=str_replace("&quot;","",$taobao_api);
	    $taobao_api=str_replace("&hellip;","",$taobao_api);
	    $taobao_api=str_replace("\\\\r","",$taobao_api);
	    $taobao_api=str_replace("\\\\n","",$taobao_api);
	    $taobao_api=str_replace("\r","",$taobao_api);
	    $taobao_api=str_replace("\n","",$taobao_api);
	    $taobao_api=strip_tags($taobao_api); 
	    $data["commentlist"]=json_decode($taobao_api,true); 
	    $data["status"]=1; 
	} 
	 
	if( $data ){
	    $this->ajaxReturn($data);
	}else{
	    $data["status"]=0;
	    $this->ajaxReturn($data);
	} 
    }
    
    /**
     * 淘宝评论采集 
     * @author qchlian <3580164@qq.com>
     */ 
    function getcommenttaobao(){
	set_time_limit(0);
	$url = I("url");
	$url = parse_url($url);
	$arr=array();
	parse_str($url["query"],$arr);
	$content="";
	$data = array();
	if( isset($arr["id"]) ){
	    $url="http://item.taobao.com/item.htm?id=".$arr["id"]; 
	    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	    $content=file_get_contents($url);  
	    $content=iconv('gbk','utf-8//IGNORE',$content); 
	    if( preg_match('/data-listApi="(.+?)"/is',$content,$matches ) ){
		$page = I("caiji_pages",1,intval);
		$commenturl = "http:".$matches[1]."&currentPageNum=".$page;
		$taobao_api=file_get_contents($commenturl);
		$taobao_api=iconv('gbk','utf-8//IGNORE',$taobao_api);
		$taobao_api=str_replace("}]})","}]}",$taobao_api);
		$taobao_api=str_replace("卖家","客服",$taobao_api);
		$taobao_api=str_replace("老板","客服",$taobao_api);
		$taobao_api=str_replace("你家","",$taobao_api);
		$taobao_api=str_replace("5分","5星",$taobao_api);
		$taobao_api=str_replace("五分","五星",$taobao_api);
		$taobao_api=str_replace("家","",$taobao_api);
		$taobao_api=str_replace("旗舰","",$taobao_api);
		$taobao_api=str_replace("掌柜","客服",$taobao_api);
		$taobao_api=str_replace("老婆","姐姐",$taobao_api);
		$taobao_api=str_replace("天猫","",$taobao_api);
		$taobao_api=str_replace("淘宝","",$taobao_api);
		$taobao_api=str_replace("此用户没有填写评论!","收货后特意查了下，是正品哦。",$taobao_api);
		$taobao_api=str_replace("评价方未及时做出评价,系统默认好评!","",$taobao_api); 
		$taobao_api=str_replace("&nbsp;","",$taobao_api);
		$taobao_api=str_replace("&rdquo;","",$taobao_api);
		$taobao_api=str_replace("&ldquo;","",$taobao_api);
		$taobao_api=str_replace("&middot;","",$taobao_api);
		$taobao_api=str_replace("&apos;","",$taobao_api);
		$taobao_api=str_replace("&quot;","",$taobao_api);
		$taobao_api=str_replace("&hellip;","",$taobao_api);
		$taobao_api=str_replace("\\\\r","",$taobao_api);
		$taobao_api=str_replace("\\\\n","",$taobao_api);
		$taobao_api=str_replace("\r","",$taobao_api);
		$taobao_api=str_replace("\n","",$taobao_api);
		$taobao_api=strip_tags($taobao_api); 
		$data["commentlist"]=$taobao_api;
		$data["status"]=1; 
	    } 
	}
	if( $data ){
	    $this->ajaxReturn($data);
	}else{
	    $data["status"]=0;
	    $this->ajaxReturn($data);
	} 
    }
    
    /**
     * 天猫评论采集 
     * @author qchlian <3580164@qq.com>
     */ 
    function getcommenttmall(){
	set_time_limit(0);
	$url = I("url");
	$url = parse_url($url);
	$arr=array();
	parse_str($url["query"],$arr);
	$content="";
	$data = array();
	if( isset($arr["id"]) ){
	    $url="http://item.taobao.com/item.htm?id=".$arr["id"]; 
	    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	    $content=file_get_contents($url);
	    $content=iconv('gbk','utf-8//IGNORE',$content); 
	    if( preg_match('/<input type="hidden" id="dsr-userid" value="([^<>]*)"\/>/',$content,$matches ) ){
		$seller_id = $matches[1];
		$page = I("caiji_pages",1,intval);
		$commenturl="http://rate.tmall.com/list_detail_rate.htm?itemId=".$arr["id"]."&sellerId=".trim($seller_id)."&currentPage=".$page; 
		$tianmao_api=file_get_contents($commenturl);
		$tianmao_api=iconv('gbk','utf-8//IGNORE',$tianmao_api); 
		$tianmao_api=str_replace("卖家","客服",$tianmao_api);
		$tianmao_api=str_replace("老板","客服",$tianmao_api);
		$tianmao_api=str_replace("你家","",$tianmao_api);
		$tianmao_api=str_replace("5分","5星",$tianmao_api);
		$tianmao_api=str_replace("五分","五星",$tianmao_api);
		$tianmao_api=str_replace("家","",$tianmao_api);
		$tianmao_api=str_replace("旗舰","",$tianmao_api);
		$tianmao_api=str_replace("掌柜","客服",$tianmao_api);
		$tianmao_api=str_replace("老婆","姐姐",$tianmao_api);
		$tianmao_api=str_replace("天猫","",$tianmao_api);
		$tianmao_api=str_replace("淘宝","",$tianmao_api);
		$tianmao_api=str_replace("此用户没有填写评论!","收货后特意查了下，是正品哦。",$tianmao_api);
		$tianmao_api=str_replace("&nbsp;","",$tianmao_api);
		$tianmao_api=str_replace("&rdquo;","",$tianmao_api);
		$tianmao_api=str_replace("&ldquo;","",$tianmao_api);
		$tianmao_api=str_replace("&middot;","",$tianmao_api);
		$tianmao_api=str_replace("&apos;","",$tianmao_api);
		$tianmao_api=str_replace("&quot;","",$tianmao_api);
		$tianmao_api=str_replace("&hellip;","",$tianmao_api);
		$tianmao_api=str_replace("\\\\r","",$tianmao_api);
		$tianmao_api=str_replace("\\\\n","",$tianmao_api);
		$tianmao_api=str_replace("\r","",$tianmao_api);
		$tianmao_api=str_replace("\n","",$tianmao_api); 
		$tianmao_api=strip_tags($tianmao_api); 
		$data["commentlist"]="({".$tianmao_api."})";
		$data["status"]=1; 
	    } 
	}
	if( $data ){
	    $this->ajaxReturn($data);
	}else{
	    $data["status"]=0;
	    $this->ajaxReturn($data);
	} 
    }
    
    /**
     * 天猫采集 
     * @author qchlian <3580164@qq.com>
     */ 
    function getdatatmall(){
	set_time_limit(0);
	$url = I("url");
	$url = parse_url($url);
	$arr=array();
	parse_str($url["query"],$arr);
	$content="";
	$data = array();
	if( isset($arr["id"]) ){
	    $url="http://detail.tmall.com/item.htm?id=".$arr["id"]; 
	    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	    $content=file_get_contents($url);  
	    $content=iconv('gbk','utf-8//IGNORE',$content);
	    $rules = $this->_getRules("tmall");
	    // 数据数组 
	    $params = array('name', 'attr','description', 'content', 'price','product_img');
	    $from="tmall";
	    foreach ($params as $v) {
		$data[$v] = $this->collectOne($from,$content, $rules[$v],$v);
	    }
	    $data["status"]=1;
	} 
	if( $data ){
	    $this->ajaxReturn($data);
	}else{
	    $data["status"]=0;
	    $this->ajaxReturn($data);
	} 
    }
    
    /**
     * yao95095采集 
     * @author qchlian <3580164@qq.com>
     */
    function getdatayao(){
	set_time_limit(0);
	$url = I("url");
	$urla = parse_url($url);
	$arr=array();
	parse_str($urla["query"],$arr);
	$content="";
	$data = array();
	if( isset($arr["id"]) ){
	    //$url="http://detail.yao.95095.com/item.htm?".$arr["id"]; 
	    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	    $content=file_get_contents($url);  
	    $content=iconv('gbk','utf-8//IGNORE',$content); 
	    $rules = $this->_getRules("yao95095");
	    // 数据数组 
	    $params = array('name', 'attr','description', 'content', 'price','product_img');
	    $from="yao95095";
	    foreach ($params as $v) {
		$data[$v] = $this->collectOne($from,$content, $rules[$v],$v);
	    }
	    $data["status"]=1;
	}
	if( $data ){
	    $this->ajaxReturn($data);
	}else{
	    $data["status"]=0;
	    $this->ajaxReturn($data);
	} 
    }
    
    /**
     * 匹配一条数据
     *
     * @param string $source 网页源码
     * @param string $preg 采集规则
     * @return string|bool 成功返回结果，失败返回false
     */
    public function collectOne($from,&$source, $preg,$field="") 
    { 
	if($from=="taobao"){
	    if ( preg_match($preg, $source, $matches) ) {
		if($field=="attr"){ 
		    $matches[0]=str_replace("：",":",$matches[0]);
		    preg_match_all('/<li (.*?)>(.*?):/',$matches[0],$c_attrNames);
		    preg_match_all('/title="(.*?)"/',$matches[0],$c_attrValues);
		    $arr["keys"]=$c_attrNames[2];
		    $arr["value"]=$c_attrValues[1];
		    return $arr;
		}elseif($field=="content"){
		    $contenturl = trim( $matches[1] );
		    $contenturl ="http://dsc.taobaocdn.com/".$contenturl;
		    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
		    $content = file_get_contents( $contenturl ); 
		    $content=iconv('gbk','utf-8//IGNORE',$content); 
		    $content = substr($content,10,-3); 
		    $arr["content"]=$content;
		    //去除换行留下的\符号
		    $content_arr = explode("\n",$content);
		    if( $content_arr){
			$content_arr_new=array();
			foreach( $content_arr as $k=>$v ){
			    $vv=trim($v);
			    $s=substr($vv,-1,1);
			    if( $s=="\\"){
				$vv=substr($vv,0,-1);
			    }
			    $content_arr_new[]=$vv;
			}
			$content=implode("\n",$content_arr_new);
		    }
		    preg_match_all('/src="(.*?)"/',$content,$picarr);
		    $imgarray = $picarr[1];
		    if($imgarray){
			foreach($imgarray as $k=> $v){
			    if(strpos($v,'http:') === false){
				$v="http:".$v;
			    } 
			    $imgarray[$k]=$this->_getRemoteimg($v,"editor");
			    $content=str_replace($v,$imgarray[$k],$content);
			}
		    }
		    preg_match_all('/href="(.*?)"/',$content,$hrefarr);
		    if($hrefarr[1]){
			$content=str_replace($hrefarr[1],array("javascript:;"),$content);
		    }
		    $arr["content"]=$content;
		    $arr["imgs"]=$imgarray;
		    return $arr;
		}else if($field=="product_img"){
		    preg_match_all('/src="(.*?)"/',$matches[0],$picarr);
		    $imgarray = $picarr[1];
		    if($imgarray){ 
			foreach($imgarray as $k=> $v){
			    if(strpos($v,'http:') === false){
				$v="http:".$v;
			    } 
			    $imgarray[$k]=str_replace("_50x50.jpg","",$v);
			    $iscover = $k>0?false:true;
			    $imgarray[$k]=$this->_getRemoteimg($imgarray[$k],"pic",$iscover);
			}
		    }
		    return $imgarray; 
		}else{
		    $matches[1]=str_replace(array("-淘宝网","旗舰店"),array("",""),$matches[1]); 
		    return trim($matches[1]);
		} 
	    }
	}
	else if($from=="tmall"){
	    if (preg_match($preg, $source, $matches)) {
		if($field=="attr"){ 
		    $matches[0]=str_replace("：",":",$matches[0]);
		    preg_match_all('/<li (.*?)>(.*?):/',$matches[0],$c_attrNames);
		    preg_match_all('/title="(.*?)"/',$matches[0],$c_attrValues);
		    $arr["keys"]=$c_attrNames[2];
		    $arr["value"]=$c_attrValues[1];
		    return $arr;
		}elseif($field=="content"){
		    $contenturl = trim( $matches[1] ); 
		    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
		    $content = file_get_contents("http:".$contenturl); 
		    $content=iconv('gbk','utf-8//IGNORE',$content);
		    $content=str_replace("_.webp","",$content);
		    $content = substr($content,10,-3);
		    //去除换行留下的\符号
		    $content_arr = explode("\n",$content);
		    if( $content_arr){
			$content_arr_new=array();
			foreach( $content_arr as $k=>$v ){
			    $vv=trim($v);
			    $s=substr($vv,-1,1);
			    if( $s=="\\"){
				$vv=substr($vv,0,-1);
			    }
			    $content_arr_new[]=$vv;
			}
			$content=implode("\n",$content_arr_new);
		    }
		    $arr["content"]=$content;
		    preg_match_all('/src="(.*?)"/',$content,$picarr);
		    $imgarray = $picarr[1]; 
		    if($imgarray){
			foreach($imgarray as $k=> $v){ 
			    if(strpos($v,'http:') === false){
				$v="http:".$v;
			    } 
			    $imgarray[$k]=$this->_getRemoteimg($v,"editor");
			    $content=str_replace($v,$imgarray[$k],$content);
			}
		    }
		    preg_match_all('/href="(.*?)"/',$content,$hrefarr);
		    if($hrefarr[1]){
			$content=str_replace($hrefarr[1],array("javascript:;"),$content);
		    }
		    $arr["content"]=$content;
		    $arr["imgs"]=$imgarray; 
		    return $arr;
		}else if($field=="product_img"){
		    preg_match_all('/src="(.*?)"/',$matches[0],$picarr);
		    $imgarray = $picarr[1];
		    if($imgarray){
			foreach($imgarray as $k=> $v){
			    if(strpos($v,'http:') === false){
				$v="http:".$v;
			    } 
			    $imgarray[$k]=str_replace("_60x60q90.jpg","",$v); 
			    $iscover = $k>0?false:true;
			    $imgarray[$k]=$this->_getRemoteimg($imgarray[$k],"pic",$iscover); 
			}
		    }
		    return $imgarray; 
		}else if($field=="description"){ 
		    preg_match('/\<p>(.+?)\<\/p\>/is',$matches[1] ,$des);
		    return trim($des[1]);
		}else{
		    $matches[1]=str_replace(array("-淘宝网","旗舰店"),array("",""),$matches[1]); 
		    return trim($matches[1]);
		} 
	    }
	}
	else if($from=="yao95095"){
	    if (preg_match($preg, $source, $matches)) {
		if($field=="attr"){
		    $matches[0]=str_replace("：",":",$matches[0]);
		    preg_match_all('/<li (.*?)>(.*?):/',$matches[0],$c_attrNames);
		    preg_match_all('/title="(.*?)"/',$matches[0],$c_attrValues);
		    $arr["keys"]=$c_attrNames[2];
		    $arr["value"]=$c_attrValues[1];
		    return $arr;
		}elseif($field=="content"){
		    $contenturl = trim( $matches[1] ); 
		    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
		    $content = file_get_contents("http:".$contenturl); 
		    $content=iconv('gbk','utf-8//IGNORE',$content);
		    $content=str_replace("_.webp","",$content);
		    $content = substr($content,10,-3);
		    //去除换行留下的\符号
		    $content_arr = explode("\n",$content);
		    if( $content_arr){
			$content_arr_new=array();
			foreach( $content_arr as $k=>$v ){
			    $vv=trim($v);
			    $s=substr($vv,-1,1);
			    if( $s=="\\"){
				$vv=substr($vv,0,-1);
			    }
			    $content_arr_new[]=$vv;
			}
			$content=implode("\n",$content_arr_new);
		    }
		    $arr["content"]=$content;
		    preg_match_all('/src="(.*?)"/',$content,$picarr);
		    $imgarray = $picarr[1]; 
		    if($imgarray){
			foreach($imgarray as $k=> $v){ 
			    if(strpos($v,'http:') === false){
				$v="http:".$v;
			    } 
			    $imgarray[$k]=$this->_getRemoteimg($v,"editor");
			    $content=str_replace($v,$imgarray[$k],$content);
			} 
		    }
		    preg_match_all('/href="(.*?)"/',$content,$hrefarr);
		    if($hrefarr[1]){
			$content=str_replace($hrefarr[1],array("javascript:;"),$content);
		    }
		    $arr["content"]=$content;
		    $arr["imgs"]=$imgarray; 
		    return $arr;
		}else if($field=="product_img"){
		    preg_match_all('/src="(.*?)"/',$matches[0],$picarr);
		    $imgarray = $picarr[1];
		    if($imgarray){
			foreach($imgarray as $k=> $v){
			    if(strpos($v,'http:') === false){
				$v="http:".$v;
			    } 
			    $imgarray[$k]=str_replace("_60x60q90.jpg","",$v); 
			    $iscover = $k>0?false:true;
			    $imgarray[$k]=$this->_getRemoteimg($imgarray[$k],"pic",$iscover);
			}
		    }
		    return $imgarray; 
		}else if($field=="description"){ 
		    preg_match('/\<p>(.+?)\<\/p\>/is',$matches[1] ,$des);
		    return trim($des[1]);
		}else{
		    $matches[1]=str_replace(array("-淘宝网","旗舰店"),array("",""),$matches[1]); 
		    return trim($matches[1]);
		} 
	    }
	}
	else if($from=="alibaba"){ 
	    if ( preg_match($preg, $source, $matches) ) { 
		if($field=="attr"){
		    $arr=array();
		    if( $matches[2]){
			preg_match_all('/<td([^>]*)\s*\>(.*?)\<\/td>/',$matches[2],$c_attr); 
			if($c_attr[2]){
			    $arr["keys"]=$arr["value"]=array();
			    foreach( $c_attr[2] as $k=>$v ){ 
				if( ($k%2)==0 ){
				    $arr["keys"][]=$v;
				}else{
				    $arr["value"][]=$v;
				}
			    }
			}
		    }
		    return $arr;
		}
		elseif($field=="content"){
		    preg_match('/data-tfs-url="(.*?)"/',$matches[0],$matches2); 
		    $contenturl = trim( $matches2[1] ); 
		    ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
		    $content = file_get_contents( $contenturl ); 
		    $content=iconv('gbk','utf-8//IGNORE',$content); 
		    $content = substr($content,10,-3); 
		    $arr["content"]=$content;
		    //去除换行留下的\符号
		    $content_arr = explode("\n",$content);
		    if( $content_arr){
			$content_arr_new=array();
			foreach( $content_arr as $k=>$v ){
			    $vv=trim($v);
			    $s=substr($vv,-1,1);
			    if( $s=="\\"){
				$vv=substr($vv,0,-1);
			    }
			    $content_arr_new[]=$vv;
			}
			$content=implode("\n",$content_arr_new);
		    }
		    preg_match_all('/src="(.*?)"/',$content,$picarr);
		    $imgarray = $picarr[1];
		    if($imgarray){
			foreach($imgarray as $k=> $v){
			    if(strpos($v,'http:') === false){
				$v="http:".$v;
			    } 
			    $imgarray[$k]=$this->_getRemoteimg($v,"editor");
			    $content=str_replace($v,$imgarray[$k],$content);
			}
		    }
		    preg_match_all('/href="(.*?)"/',$content,$hrefarr);
		    if($hrefarr[1]){
			$content=str_replace($hrefarr[1],array("javascript:;"),$content);
		    }
		    $arr["content"]=$content;
		    $arr["imgs"]=$imgarray;
		    return $arr;
		}
		else if($field=="product_img"){
		    preg_match_all('/src="(.*?)"/',$matches[0],$picarr);
		    $imgarray = $picarr[1];
		    if($imgarray){
			foreach($imgarray as $k=> $v){
			    if(strpos($v,'http:') === false){
				$v="http:".$v;
			    } 
			    $imgarray[$k]=str_replace("32x32.","",$v); 
			    $iscover = $k>0?false:true;
			    $imgarray[$k]=$this->_getRemoteimg($imgarray[$k],"pic",$iscover);
			}
		    }
		    return $imgarray; 
		}else{
		    $matches[1]=str_replace(array("-淘宝网","旗舰店"),array("",""),$matches[1]); 
		    return trim($matches[1]);
		} 
	    }
	}
        return false;
    }
    
    /**
     * 获取远程图片 
     * @param string $site_name 站点名称
     * @param string $name 某个站点规则名称
     * @return string|array 指定规则名称返回规则字符串，否返回某个站点规则
     */
    private function _getRemoteimg($url,$pictype="pic",$iscover=false)
    {
	$domainid = I("domainid");
	$basepath = './Uploads/Caiji/';
	if($pictype=="pic"){
	    $tt=2;
	    if( $iscover){ $tt=1; }
	    $f=$basepath.'Picture/'.$domainid.'/'.$tt.date("/Y/m/d")."/";//保存根路径 这里需要跟config里面的配置一致
	}else if($pictype=="editor"){
	    $f=$basepath.'Editor/'.date("Y/m/d")."/";//保存根路径 这里需要跟config里面的配置一致 
	}
	$this->mkdirp($f);
	import('@.Caiji.Segment'); 
	$snoopy = new \Lib\Caiji\Snoopy;
	ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;)');
	$snoopy->fetch($url);
	$filename = uniqid().".jpg";
	file_put_contents($f.$filename,$snoopy->results);
	$file=$f.$filename;
	if($pictype=='editor'){ 
	    $file=str_replace("./Uploads/","/Uploads/",$file);
	}
	return $file; 
    }
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
    /**
     * 获取站点采集规则
     *
     * @param string $site_name 站点名称
     * @param string $name 某个站点规则名称
     * @return string|array 指定规则名称返回规则字符串，否返回某个站点规则
     */
    private function _getRules($site_name='taobao', $name='')
    {
        static $rules = array();
        if (empty($rules)) {
            // 淘宝
            $rules['taobao'] = array(
                //'page_total' => '/class="page-info"\>\d+\/(\d+)\<\/span\>/', // 单页商品数目
                //'search-result' => '/class="search-result"\>\s+共搜索到\<span\>\s+(\d+)\s+\<\/span\>/', // 店铺总商品数
                'shop_category' => '/(http:\/\/.+?\/category-\d+\.htm\?)search=y&catName/',
                'goods_id' => '/class="item-name"\shref="http:\/\/item\.taobao\.com\/item\.htm\?id=(\d+)"/', // 商品ID
                'name' => '/\<title\>(.+?)<\/title\>/is', // 商品名称
		'description' => '/\<p\sclass="tb-subtitle">(.+?)<\/p\>/is', // 商品名称    
                'price' => '/\<em\sclass="tb-rmb-num"\>(.+?)\<\/em\>/', // 商品价格
                'attr' => '/\<ul\sclass="attributes-list"\>(.+?)\<\/ul\>/is', // 商品简介
                'product_img' => '/<(ul)[^c]*id=\"J_UlThumb\"[^>]*>(.+?)<\/ul\>/is',
                'content' => '/"\/\/dsc\.taobaocdn.com\/(.+?)"/', // 商品描述url
                'shop_intro' => '/\<p\sclass="base-info"\>(.+?)\<\/p\>/is', // 店铺简介
            );
            // 天猫
            $rules['tmall'] = array(
                //'page_total' => '/class="ui-page-s-len"\>\d+\/(\d+)\<\/b\>/', // 总页数
                //'goods_id' => '/class="item.+?data-id="(\d+)"/is', // 商品ID
                'name' => '/\<title\>(.+?)-tmall.+?\<\/title\>/is', // 商品名称
		'description' => '/\<div\sclass="tb-detail-hd">(.+?)<\/div\>/is', // 商品名称    tb-detail-hd
                'price' => ' /class="J_originalPrice"\>(.+?)\<\/strong\>/', // 商品价格
                'attr' => '/<(ul)[^c]*id=\"J_AttrUL\"[^>]*>.*<\/\\1>/is',
		//'intro' => '/\<div\sclass="attributes-list"\sid="J_AttrList"\>(.+?)\<\/div\>\s+\<\/div\>/is', // 简介 
                'product_img' => '/<(ul)[^c]*id=\"J_UlThumb\"[^>]*>(.+?)<\/ul\>/is',
                'content' => '/"descUrl":"(.+?)"/', // 商品描述
                'shop_intro' => '/class="extend"\>(.+?)\<\/div\>/is', // 店铺简介
            );
	    // yao95095
            $rules['yao95095'] = array(
                //'page_total' => '/class="ui-page-s-len"\>\d+\/(\d+)\<\/b\>/', // 总页数
                //'goods_id' => '/class="item.+?data-id="(\d+)"/is', // 商品ID
                'name' => '/\<title\>(.+?)-tmall.+?\<\/title\>/is', // 商品名称
		'description' => '/\<div\sclass="tb-detail-hd">(.+?)<\/div\>/is', // 商品名称    tb-detail-hd
                'price' => ' /class="J_originalPrice"\>(.+?)\<\/strong\>/', // 商品价格
                'attr' => '/<(ul)[^c]*id=\"J_AttrUL\"[^>]*>.*\<\/ul\>/is',
		//'intro' => '/\<div\sclass="attributes-list"\sid="J_AttrList"\>(.+?)\<\/div\>\s+\<\/div\>/is', // 简介 
                'product_img' => '/<(ul)[^c]*id=\"J_UlThumb\"[^>]*>(.+?)<\/ul\>/is',
                'content' => '/"descUrl":"(.+?)"/', // 商品描述
                'shop_intro' => '/class="extend"\>(.+?)\<\/div\>/is', // 店铺简介
            );
	    //阿里巴巴 1688
	    $rules['alibaba'] = array( 
                'name' => '/\<h1 class=\"d-title"\>(.+?)\<\/h1\>/', // 商品名称
		'description' => '', // 商品名称    tb-detail-hd
                'price' => '/<meta property=\"og:product:price\" content=\"(.*?)\"\/>/', // 商品价格
                'attr' => '/<div([^>]*)\s*id=\"mod-detail-attributes\"[^>]*>(.+?)\<\/div\>/is', 
                'product_img' => '/<div([^>]*)\s*id=\"dt-tab\"[^>]*>(.+?)\<\/ul\>/is', // 商品pic
                'content' => '/\<div\sid="desc-lazyload-container"(.+?)\>/is', //商品描述url 
            ); 
        }
        $site_rules = $rules[$site_name];
        return ($name) ? $site_rules[$name] : $site_rules;
    }
}