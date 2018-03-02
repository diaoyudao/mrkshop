<?php

namespace Web\Controller;

use Api\Api\IndexApi;

class TestController extends HomeController{
    
    
    /**
     * 首页测试
     */
    public function index(){
        $page = new IndexApi();
        
        $re = $page->index();
        
        dump($re);
    }
}

