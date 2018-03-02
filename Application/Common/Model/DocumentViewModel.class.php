<?php
namespace Common\Model;
use Think\Model\ViewModel;
use Think\Page;
/**
 * 产品基础模型
        //'id','uid','name','title','category_id','description','root' ,'pid','model_id' ,'type' ,'position' ,'link_id','cover_id','display' ,'deadline' ,'attach','view','comment','extend' ,'level' ,'create_time','update_time','status','price','tuan_price','qg_price','ms_price','brand','sales' ,'percent' ,'domainid','brandid' ,'keywords','ifcheck','collectionnum' ,'ishot' ,'meta_title','meta_keyword','meta_description'

'id','category_id','title','description','cover_id','comment','sales','price','domainid','brandid','keywords','ishot','ifcheck','view','collectionnum','comment','create_time','update_time','status','display'

 */
class DocumentViewModel extends ViewModel{
//	public $viewFields = array(
//		'document'  =>array('id','uid','name','title','category_id','description','root' ,'pid','model_id' ,'type' ,'position' ,'link_id','cover_id','display' ,'deadline' ,'attach','view','comment','extend' ,'level' ,'create_time','update_time','status','price','tuan_price','qg_price','ms_price','brand','sales' ,'percent' ,'domainid','brandid' ,'keywords','ifcheck','collectionnum' ,'ishot' ,'meta_title','meta_keyword','meta_description','_type'=>'LEFT'),
//		'document_product'=>array('issales','totalsales','marketprice','yprice','stock','content','instructions','unionarticle','_on'=>'document_product.id=document.id','zizhi','volumedes','pcode','pics','_type'=>'LEFT'),
//		'subdomain'=>array('mark'=>'channelname', '_on'=>'document.domainid=subdomain.id','_type'=>'LEFT'),
//	); 
	public $viewFields = array(
		'document'  =>array('id','uid','name','title','category_id','description','root' ,'pid','model_id' ,'type' ,'position' ,'link_id','cover_id','display' ,'deadline' ,'attach','view','comment','extend' ,'level' ,'create_time','update_time','status','price','tuan_price','qg_price','ms_price','brand','sales' ,'percent' ,'domainid','brandid' ,'keywords','ifcheck','collectionnum' ,'ishot' ,'meta_title','meta_keyword','meta_description','_type'=>'LEFT'),
		'document_product'=>array('issales','totalsales','marketprice','member_level_price','warehouse','product_type','iszhiyou','affilite_a','affilite_b','product_unit','haiguan_rate','weight','storage_alarm','yprice','stock','content','unionarticle','_on'=>'document_product.id=document.id','volumedes','pcode','pics','_type'=>'LEFT'),
		'subdomain'=>array('mark'=>'channelname', '_on'=>'document.domainid=subdomain.id','_type'=>'LEFT'),
	);  
}
