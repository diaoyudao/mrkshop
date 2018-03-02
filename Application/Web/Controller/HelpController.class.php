<?php
namespace Web\Controller;
use Web\Controller\HomeController;
class HelpController extends HomeController{
    protected function _initialize(){
        parent::_initialize();
    }

    /**
     * @desc 客服中心
     * @author winter melon
     */
    public function index(){
        $map = array();
        $map['name'] = 'bangzhuzhongxin';
        //分频道$map["domainid"]=cookie("current_domainid");
        $map["display"]=1;
        $categoryid = D('Category')->where($map)->getField('id');
        $rmap = array();
        $rmap['category_id'] = $categoryid;
        $rmap["model_id"] = 2;
        $article= $this->getArticle($rmap,'update_time desc',1);
        if(!empty($article)){
            $this->assign("article",$article);
            //设置seo
            $this->meta_title = $article['meta_title']?:$article['title']; 
            $this->meta_keyword = $article['meta_keyword']; 
            $this->meta_description= $article['meta_description']; 

        }else{
            $this->meta_title = '客服中心';
        }
        
        $this->assign('curl_parent','bangzhuzhongxin');
        $this->display('detail');
    }

    /**
     * 文章详情页
     */
    public function detail(){
        $postlist = I('get.');
        $categoryname = $postlist['id'];
        $map = array();
        $map['name'] = $categoryname;
        //分频道$map["domainid"]=cookie("current_domainid");
        $map["display"]=1;
        $category = D('Category')->where($map)->find();//getField('id');
        $parent_name = D("Category")->where(array('id'=>$category['pid']))->getField('name');
        $this->assign('curl_parent',$parent_name);
        $this->assign('curl_name',$categoryname);
        $rmap = array();
        $rmap['category_id'] = $category['id'];
        $rmap["model_id"] = 2;
        $article= $this->getArticle($rmap,'update_time desc',1);
        if(!empty($article)){ 
            $this->assign("article",$article);
            $this->meta_title = $article['meta_title']?:$article['title']; 
            $this->meta_keyword = $article['meta_keyword']; 
            $this->meta_description= $article['meta_description'];
        }
        $this->assign('cur_cateid', $article['category_id']);
        $this->display();
    }

    public function navigation(){
        $parameters['m']    = 'information';
        $parameters['a']    = 'get_information_category_items';
        $response = $this->query($parameters);
        if($response['result']!='success' || $response['code']!='0x0000'){
            $this->error($response['info']);
        }
        $_items = $response['info'];
        $data = array();
        foreach ($_items as $key=>$val){
            if(empty($val['children'])){
                $data[] = $val;
            }
        }
        return $data;
    }
}