<?php

namespace Wap\Controller;

use Wap\Controller\HomeController;

class CategoryController extends HomeController {

    protected function _initialize() {
        parent::_initialize();
        $this->assign('menu_local', '首页');
    }

    public function index() {
        $map = array();
        $map["id"] = array("gt", 1);
        $domainlist = $this->getDomain($map);
        $this->assign("domainlist", $domainlist);

        $category = $this->categorylist();
//        dump($category);exit;
        $this->assign('category_list', $category);
        $this->display();
    }

}
