<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'DEFAULT_MODULE' => 'Web',
    'MODULE_DENY_LIST' => array('Common', 'User', 'Api'),
    //'MODULE_ALLOW_LIST'  => array('Home','Admin'),
//    'DEFAULT_THEME' => 'Default', // 默认主题
    'APP_SUB_DOMAIN_DEPLOY' => 1, // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES' => array(
        'http://miaork.m1ju.com/' => 'Web', // admin.domain1.com域名指向Admin模块
        'm.miaork.m1ju.com' => 'Wap', // test.domain2.com域名指向Test模块
    ), // 子域名部署规则


    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'zi5`QkO1AtBPdr}q%Y?T;b&0@Jx/jUE"ea_6l.cM', //默认数据加密KEY

    /* 调试配置 */
    'SHOW_PAGE_TRACE' => true,
    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,SQL', // 只记录EMERG ALERT CRIT ERR 错误
    'DB_SQL_BUILD_CACHE' => true,
    'DB_SQL_BUILD_LENGTH' => 20, // SQL缓存的队列长度

    /* 用户相关设置 */
    'USER_MAX_CACHE' => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL' => 3, //URL模式
    'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR' => '/', //PATHINFO URL分割符
    'BASE_SITE_URL' => $_SERVER['HTTP_HOST'],
    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数
//
//    /* 数据库配置 */
//    'DB_TYPE' => 'mysql', // 数据库类型
//    'DB_HOST' => '172.16.30.128', // 服务器地址
//    'DB_NAME' => 'mrkshop', // 数据库名
//    'DB_USER' => 'mysqluser', // 用户名
//    'DB_PWD' => 'tt@test', // 密码
//    'DB_PORT' => '3306', // 端口
//    'DB_PREFIX' => 'b2c_', // 数据库表前缀
	
     /* 数据库配置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'mrkshop', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'root', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'mrk_', // 数据库表前缀
    
	
//    -以下为短信接口的一些参数
    'message' => array(
        //申请的短信接口平台
        'http' => 'https://api.netease.im/sms/sendcode.action',
        //在云信使申请的短信验证用户账号（需要跟系统免费申请短信条数）
        'AppKey' => '0c6ab52108e0596417f596f05e7ae013',
        //在云信使申请短信验证的用户密码
        'AppSecret' => '02184da78572',
        ),

    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),
    'LOAD_EXT_CONFIG' => 'router',
);
