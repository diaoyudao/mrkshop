<?php
namespace Common\Util;
class Response{
    private $is_debug;
    public function __construct(){
            $this->debug();
    }

    public function debug(){
        $this->is_debug = true;
        return $this;
    }

    public function put($_fields){
        if(!array($_fields)){
            return array('result'=>'fail','code'=>'0xffff','参数错误');
        }
        session_start();
        $cookieStr= 'PHPSESSID='.session_id();
        session_write_close();
//        $uri = 'http://www.mrk.com/api.php';
        $uri = (is_ssl()?'https://':'http://').C('BASE_SITE_URL').'/api.php';
        if($this->is_debug){
            echo $uri.'?'.http_build_query($_fields);
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $uri);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_COOKIE,$cookieStr);
        curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($_fields));
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            return array('result'=>'fail','code'=>'0xffff',curl_error($ch));
        }
        curl_close($ch);
        $response = json_decode($response,true);
        return $response;
    }
}