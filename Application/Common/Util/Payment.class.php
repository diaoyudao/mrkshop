<?php
namespace Common\Util;
use Common\Util\Payment\Driver;
class Payment{
    public $adapter_instance;
    public function __construct($adapter = '',$config = array()){
        $this->set_adapter($adapter, $config);
    }
    
    /**
     * @desc web service 支付组件构造
     * @param String $adapter 支付组件名称
     * @param Array $config  组件相应配置
     * @return boolean|object
     */
    public function set_adapter($adapter,$config){
        if(!is_string($adapter)) return false;
        $class = '\\Common\\Util\\Payment\\Driver\\'.ucwords($adapter);
        if(class_exists($class)){
            $this->adapter_instance = new $class($config);
        }else{
            E('支付脚手架加载失败');
        }
        return $this->adapter_instance;
    }
    
    
    public function __call($method,$args){
        if(method_exists($this,$method)){
            return call_user_func_array(array(& $this, $method), $args);
        }elseif(!empty($this->adapter_instance) && ($this->adapter_instance instanceof Driver) && method_exists($this->adapter_instance, $method)){
            return call_user_func_array(array(& $this->adapter_instance, $method), $args);
        }
    }
}