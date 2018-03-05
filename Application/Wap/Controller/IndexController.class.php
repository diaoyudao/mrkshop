<?php

namespace Wap\Controller;

use Api\Api\IndexApi;

class IndexController extends HomeController {

    protected function _initialize() {
        parent::_initialize();
//        $this->assign('menu_local', '首页');
        $menus = $this->_initializeMenus(2);
        $this->assign('menulist', $menus);
    }

    public function index() {
        
        $m = new IndexApi();
        $data = $m->home_wap();
        $this->assign('newgoodslist', $data['newgoodslist']);
        $this->assign('hotgoodslist', $data['hotgoodslist']);
        $this->display();
    }

}
