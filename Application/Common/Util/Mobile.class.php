<?php
namespace Common\Util;
class Mobile{
    private $config = array(
        'mobile'    =>  '',
        'message'   =>  '',
    );
    public function __construct(){
        
    }
    
    public function __set($key,$val){
        if(isset($this->config[$key])){
            $this->config[$key] = $val;
        }
    }
    
    public function __get($key){
        if(isset($this->config[$key])){
            return $this->config[$key];
        }
        return null;
    }
    
    public function register(){
        return 'register111';
        return $this->send();
    }
    
    public function login(){
       return 'login'; 
    }
    
    public function forgotpassword(){
        return 'forgotpassword';
    }
    
    public function editInfo(){
        return 'editInfo';
    }
    
    public function payment(){
        return 'payment';
    }
    
    
    protected function send(){
        if(!$this->config['mobile'] || !$this->config['message']){
            return false;
        }
        return 'Send Success';
    }
}