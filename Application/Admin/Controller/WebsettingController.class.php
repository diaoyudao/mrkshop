<?php

namespace Admin\Controller;

/**
 * 网站首页管理
 */
class WebsettingController extends AdminController {

    public function index() {
        $map['id'] = array('gt', 1);
        $subdomain = M("Subdomain")->where($map)->select();
        int_to_string($subdomain);
        $this->assign("subdomain", $subdomain);
        $this->meta_title = "PC首页管理";
        $this->display();
    }

    public function edit() {
        $domainid = I('id');
        $map['id'] =$domainid;
        $subdomain = M("Subdomain")->where($map)->find();
        if(!$subdomain){
            $this->error('参数错误');
        }
        // 模块信息
        $webconfig = D("webConfig");
        $web_code = M("WebCode")->where(array("web_id"=>$domainid))->select();
        // 分类列表
	$cate       =   M('Category')->where(array('status'=>1,'ismenu'=>2,'domainid'=>$domainid))->field('id,domainid,title,pid,allow_publish')->order('pid,sort')->select();
        $category_list          =   list_to_tree($cate);    //生成分类树
        // 商品列表
        $goods_lists = $this->searchgoods($domainid);
        $temp_code = array();
        foreach ($web_code as $key => $info) {
            $temp_code[$info['var_name']]['code_id'] = $info['code_id'];
            $temp_code[$info['var_name']]['var_name'] = $info['var_name'];
            $temp_code[$info['var_name']]['show_name'] = $info['show_name'];
            $temp_code[$info['var_name']]['code_info'] = $webconfig->get_array($info['code_info'],$info['code_type']);
        }
        $this->assign('info', $subdomain);
        $this->assign('category_list', $category_list);
        $this->assign('goods_lists', $goods_lists);
        $this->assign('code_info', $temp_code);
        $this->meta_title = "PC首页管理";
        $this->display('web_code');
    }

    /**
     * 保存设置
     */
    public function code_update() {
        $code_id = intval(I('code_id'));
        $var_name = (I('var_name'));
        $web_id = intval(I('id'));
        $model_web_config = D('WebConfig');
        $code = $model_web_config->getCodeRow($code_id, $web_id);
        
        if (!empty($code)) {
            $code_type = 'array';
            $code_info = $_POST[$var_name];
            $code_info =serialize($code_info);
            $_data = array(
                'web_id'=>$web_id,
                'code_type'=>$code_type,
                'var_name'=>$var_name,
                'code_info'=>$code_info,
                'show_name'=>$_POST[$var_name]['name']
            );
            $state = $model_web_config->updateCode(array('code_id' => $code_id), $_data);
        }else{
            $code_type = 'array';
            $code_info = $_POST[$var_name];
            $code_info = serialize($code_info); //  $model_web_config->get_str($code_info, $code_type);
            $_data = array(
                'web_id'=>$web_id,
                'code_type'=>$code_type,
                'var_name'=>$var_name,
                'code_info'=>$code_info,
                'show_name'=>$_POST[$var_name]['name']
            );
            $state = $model_web_config->data($_data)->add();
        }
        if ($state) {
            $this->success('操作成功');
            exit;
        } else {
            $this->error('操作失败');
            exit;
        }
    }
    
    /**
     * 热卖推荐管理模块
     */
    public function hot(){
        $this->display();
    }

        public function getCategory_list(){
        $domainid = I('domainid'); // 频道ＩＤ
        $pid = I("pid");
        if($pid){
            $category_list = M('Category')->where(array('status'=>1,'ismenu'=>2,'domainid'=>$domainid,'pid'=>$pid))->field('id,domainid,title,pid,allow_publish')->order('pid,sort')->select();
        }else{
            $category_list = M('Category')->where(array('status'=>1,'ismenu'=>2,'domainid'=>$domainid))->field('id,domainid,title,pid,allow_publish')->order('pid,sort')->select();
        }
//        $category_list          =   list_to_tree($cate);    //生成分类树
        $status = $category_list ? true : false;
        $this->ajaxReturn(array('info'=>$category_list,'status'=>$status),'json');
    }
    
    /**
     * 首页轮播图
     */
    public function banner(){
        $this->display();
    }
    /**
     * 首页轮播图
     */
    public function bottom_ad(){
        $this->display();
    }

        /**
     * 获取商品列表
     */
    public function getgoods_list(){
        $domainid = I('domainid'); // 频道ＩＤ
        $keyword = I("keywords");
        //分类商品列表
        $lists = $this->searchgoods($domainid,$keyword);
        $status = $lists ? true : false;
        $this->ajaxReturn(array('info'=>$lists,'status'=>$status),'json');
    }
    
    /**
     * 获取商品列表
     * @param type $keywords
     * @param type $domainid
     */
    private function searchgoods($domainid, $keywords = '') {
        //$domainid=I("pgdomainid");
//        $keywords=I("keywords");
//        $domainid = I('domainid');
        $Model = new \Think\Model();
        if ($domainid) {
            $sql = "select d.id,d.title,d.cover_id,d.price,dp.marketprice from __PREFIX__document d,__PREFIX__document_product dp where d.id=dp.id and d.status=1 and d.model_id=5  and d.domainid = {$domainid} and d.title like '%" . $keywords . "%';";
        } else {
            $sql = "select d.id,d.title,d.cover_id,d.price,dp.marketprice from __PREFIX__document d,__PREFIX__document_product dp where d.id=dp.id and d.status=1 and d.model_id=5 and d.title like '%" . $keywords . "%';";
        }
        $goods_list = $Model->query($sql);
        return $goods_list;
    }
    
    /**
     * 更改频道状态
     */
    public function changeStatus(){
        $method = I('method');
        $id = I('id');
        if(empty($id)){
            $this->error('参数错误');
        }
        if($method == 'show'){
          $bool =  M("Subdomain")->where(array('id'=>$id))->save(array('show'=>1));
        }else{
          $bool =  M("Subdomain")->where(array('id'=>$id))->save(array('show'=>0));
        }
        if($bool){
            $this->success('操作成功','',IS_AJAX);
        }else{
            $this->error('操作失败','',IS_AJAX);
        }
    }

}
