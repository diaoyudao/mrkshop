<?php
namespace Web\Controller;
use Web\Controller\HomeController;
use Api\Api\IndexApi;
class IndexController extends HomeController {
    protected function _initialize(){
        parent::_initialize();
        /*if (!$this->merchantId) {
            $this->redirect(url('member/login'));
            exit;
        }*/
        $this->assign('menu_local','首页');
    }

    
    public function index(){
//        $m = new UserApi;
//        $res = $m->index();
//        dump($res);
//        echo CONTROLLER_NAME;
        $m = new IndexApi();
        $data = $m->home_pc();
        $this->assign('web_code',$data['web_code']);
        $this->assign('xianshi_goods',$data['xianshi_goods']);
        $this->assign('xianshi_active',$data['xianshi_active']);
        $this->assign('new_goods',$data['new_goods']);
        $this->assign('empty','<span style="color:red;" class="empty">没有数据</span>');
        $this->display();
    }

    

    /**
     * @desc 首页
     * @author winter melon
     */
    public function index2(){
		
        $parameters['m']    = 'page';
        $parameters['a']    = 'get_index_data';
        $parameters['type'] = 'pc';
        
        $response = $this->query($parameters);
        //dump($response);exit;
        if($response['result']!='success' || $response['code']!='0x0000'){
                $this->error($response['info']);
        }
        $_items = $response['info'];
        $hot_sales = $index_banner = array();
        $specialModule = array(
            'hot_sales',
            'index_banner',
            'news_center',
            'tea_banner'
        );
        $specialItems = array();
        $modules = array();
        foreach ($_items as $item){
            if(in_array($item['module_code'],$specialModule)){
                $specialItems[$item['module_code']] = $item;
            }else{
                $modules[] = $item;

            }
        }
        extract($specialItems);
        foreach ($specialModule as $module){
            $this->assign($module,$$module);
        }
        $footerNavigation = $this->footer_navigation();
        $this->assign('footer_data',$footerNavigation);
        $this->assign('modules',$modules);
        $this->assign('show_subnav',true);
        $this->display();
    }
    public function footer_navigation(){
        $parameters['m']            = 'information';
        $parameters['a']            = 'get_information_category_items';
        $parameters['is_recommend'] = 1;
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