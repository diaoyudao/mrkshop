<?php
/**
 * 代理管理
 */
namespace Web\Controller;
use Web\Controller\HomeController;
class AgentController extends HomeController{
    
    protected function _initialize() {
        parent::_initialize();
    }
    
    
    public function index(){
        $this->display();
    }
    
    
}