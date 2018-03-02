<?php
namespace Web\Model;
use Think\Model\ViewModel;
/**
 * 产品基础模型
 */
class ArticleViewModel extends ViewModel{
	public $viewFields = array(
		'document'  =>array('id','category_id','title','description','cover_id','domainid','brandid','keywords','meta_title','meta_keyword','meta_description','ifcheck','view','create_time','_type'=>'LEFT'),
		'document_article'=>array('content','fromlink','fromtitle','_on'=>'document_article.id=document.id','_type'=>'LEFT'),
	); 
}
