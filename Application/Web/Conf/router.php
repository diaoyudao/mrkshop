<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-27
 * Time: 下午4:54
 * @author 想天小郑<zzl@ourstu.com>
 */
 array(
    /**
     * 路由的key必须写全称,且必须全小写. 比如: 使用'wap/index/index', 而非'wap'.
     */
    'router' => array( 
        /*首页*/ 
        'admin/index/index'                     => 'manage',
        'web/index/index'                      => 'index',
        /*登录模块*/
        'web/member/login'                       => 'login',
        'web/member/getpwd'                      => 'forgotpwd',
        'web/member/register'                    => 'register',
        'web/member/logout'                      => 'logout',
        /*限时抢购*/
        'web/goods/xianshi'                       => 'xianshi',
        /*商品模块*/
        'web/good/index'                       => 'product/lists_0_0_0_1_0_[p|0]',
        'web/good/detail'                      => '[channelname]/[id]',
        'web/good/lists'                       => 'product/lists_[domainid|0]_[id|0]_[brand|0]_[order|0]_[sort|0]_[p|0]', 
        'web/good/search'                       => 'product/search/lists_[domainid|0]_[id|0]_[brand|0]_[order|0]_[sort|0]_[p|0]',
        'web/good/comment'                      => 'product/comment_[id]_[p|0]', 
        
        'web/comment/ajaxlists'                => 'comment-ajaxlists',
        'web/ask/ajaxlists'                    => 'ask-ajaxlists', 
        
        /*文章模块*/
        'web/article/index'                    => 'article/lists_0_[p|0]',
        'web/article/detail'                   => 'article/[id]',
        'web/article/lists'                    => 'article/lists_[id]_[p|0]',
        'web/article/search'                   => 'article/search/lists_[p|0]',
        /*问答模块*/
        'web/ask/index'                        => 'ask/',
        'web/ask/newest'                       => 'ask/newest',
        
        'web/ask/detail'                       => 'ask/[id]',
        'web/ask/lists'                        => 'ask/lists_[id|0]_[type|0]_[p|0]',
        'web/ask/search'                       => 'ask/search/lists_[type|0]_[p|0]', 
        /*百科模块*/
        'web/baike/index'                      => 'baike/',
        'web/baike/category'                   => 'baike/[id]',
        'web/baike/lists'                      => 'baike/lists_[id]_[p|0]',
        'web/baike/entries'                    => 'baike/entries/[id]',
        'web/baike/detail'                     => 'baike_info/[id]',
        'web/baike/search'                     => 'baike/search/lists_[p|0]',
        /*个人中心*/
        'web/center/allorder'                  => 'center/allorder/[p|0]',
        'web/center/needpay'                   => 'center/needpay/[p|0]',
        'web/center/tobeshipped'               => 'center/tobeshipped/[p|0]',
        'web/center/tobeconfirmed'             => 'center/tobeconfirmed/[p|0]', 
        
        'web/center/comment'                   => 'center/comment/[p|0]',
        'web/center/collect'                   => 'center/collect/[p|0]',
        'web/center/information'               => 'center/information',
        'web/center/address'                   => 'center/address',
        'web/center/index'                     => 'center/index',
        'web/order/detail'                     => 'order/[id]',

        'web/order/complete'                   => 'order/complete/[id]',
        'web/comment/goods'                    => 'comment/goods/[id]',
        'web/pay/index'                        => 'pay/index',
        'web/center/updateimage'               => 'center/updateimage',
        'web/user/cut'                         => 'user/cut/[id]',
        'web/user/changeprofile'               => 'user/changeprofile',
        'web/user/profile'                     => 'user/profile',
        'web/user/mobile'                      => 'user/mobile',
        'web/user/update'                      => 'user/update',
        'web/center/save'                      => 'center/save',

        /*评论页面*/
        'web/comment/index'                     => 'comment/lists_0_0_[p|0]',
        'web/comment/lists'                     => 'comment/lists_[id]_[brand|0]_[p|0]',
        

        /*专题*/ 
        'web/special/index'                     => 'zt/[p|0]',
        'web/special/lists'                     => 'zt/[domainmark]_[p|0]',
        'web/special/search'                    => 'zt/search/lists_[p|0]', 

        /*404*/
        'web/public/nonepage'                     => '404',
        /*排行榜*/
        'web/rank/index'                     => 'rank/',
        'web/rank/lists'                     => 'rank/lists_[types]_[category]',
         
        /*购物车*/
        'web/shopcart/index'                      => 'shopcart/',
        'web/shopcart/orderconfirm'               => 'orderconfirm/',
        'web/shopcart/createorder'               => 'createorder/',
        
        'web/help/index'                      => 'help/',
        'web/help/detail'                      => 'help/[id]',
        'web/help/feedback'                     => 'feedback/',
        
         /*品牌中心*/
        'web/brand/index'                      => 'brand/', 
        'web/brand/lists'                      => 'brand/[firstkey]_[p|0]', 
        'web/brand/detail'                      => '[id]/', 
        'web/special/detail'                     => '[id]/',
        'web/special/article'                   => '[mark]/article_[id]',
        /*频道*/
        'web/channel/index'                   => '[id]/',
    ), 
);