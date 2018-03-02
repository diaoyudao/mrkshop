<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
    <head>
    <meta charset="UTF-8">
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
<title><?php if(!empty($meta_title)): echo ($meta_title); ?>-<?php echo C('SITENAME'); else: echo C('WEB_SITE_TITLE'); endif; ?></title>
<meta name="keywords" content="<?php if(empty($meta_keyword)): echo C('WEB_SITE_KEYWORD'); else: echo ($meta_keyword); endif; ?>"/>
<meta name="description" content="<?php if(empty($meta_keyword)): echo C('WEB_SITE_DESCRIPTION'); else: echo ($meta_description); endif; ?>">
<link href="favicon.ico" type="image/x-icon" rel="shortcut icon"/>
<link href="/Public/Web/css/base.css" rel="stylesheet" />
<link href="/Public/Web/css/index.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="/Public/Web/css/snapUp.css"/>
<link rel="stylesheet" type="text/css" href="/Public/Web/css/idangerous.swiper.css"/>
<script src="/Public/Web/js/idangerous.swiper.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/Public/Web/js/jquery-1.11.2.min.js"></script>
 
</head>
<body>
    <!-- 头部 -->
<!-- 头部 -->
<header>
    <!-- 顶部 -->
    <div class="top">
        <div class="towp">
    <div class="wellcom fl">
        <span>欢迎光临！</span>
        <?php if(is_login()): ?><span> <a title="<?php echo session('user_auth.username');?>" href="<?php echo U('member/index');?>"><?php echo session('user_auth.username');?></a>&nbsp;&nbsp;&nbsp;&nbsp;欢迎您!</span>
            <span> <a title="退出" href="<?php echo U('member/logout');?>">退出</a></span>
            <?php else: ?>
            <span> <a href="<?php echo U('member/login');?>">请登录</a></span>
            <span> <a href="<?php echo U('member/register');?>">免费注册</a></span><?php endif; ?>
    </div>
    <div class="topMenu fr">
        <span class="top-myOrder"><a href="<?php echo U('order/index');?>"><i class="bg"></i>我的订单</a></span>
        <span class="top-user"><a href="<?php echo U('member/index');?>"><i class="bg"></i>我的账号</a></span>
        <span class="topOnlineSevic"><a href="<?php echo U('help/index');?>"><i class="bg"></i>客服中心</a></span>
        <span class="topPhone">
            <a href="javascript:;;"><i class="bg"></i>手机版</a>
            <span class="app">
                <img src="/Public/Web/img/code.png" />
                妙品生活WAP
            </span>
        </span>
        <span class="top-qq"><a title="QQ:<?php echo C('QQ');?>" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo C('QQ');?>&site=qq&menu=yes" target="_blank"><i class="bg"></i>在线客服</a></span>
    </div>
</div>
    </div>
    <!-- 搜索 -->
    <div class="search-box">
        <div class="towp ">
            <!-- LOGO -->  
            <?php $logo = C('SITE_LOGO'); ?>
            <div class="logo fl"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>"><img src="/Uploads/Picture/logo/<?php echo (get_cover($logo,'path')); ?>"/> </a></div>
            <!-- 搜索框 -->
            <div class="search-wrap fr">
                <div class="search-con ">
                    <div class="search search-form">
                        <input type="text" class="sear-txt" id="keywords" name="keywords" value="<?php echo I('keywords');?>" placeholder="输入关键词" />
                        <input type="button" id='search' url="<?php echo U('Goods/lists');?>" value="" class="sear-btn bor" >
                        <ul class="sear-naiv">
                            <?php if(is_array($hotSearch)): $i = 0; $__LIST__ = $hotSearch;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$hotwords): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('goods/lists',array('domainid'=>$domainid,'keywords'=>$hotwords));?>" class="active"><?php echo ($hotwords); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                    <div class="add-car  ">
                        <div class="car"><a href="<?php echo U('Cart/index');?>" class="bg"><i class="cart_goods_num"><span class="cart_sum"><?php echo ($cart_list["goods_count"]); ?></span></i></a> </div>
                        <!--购物车 S-->
                        <div class="shopcar">
                            <h4>最新加入的商品</h4>
                            <ul id='head_cart'>
                                <?php if(is_array($cart_list["cart_list"])): $i = 0; $__LIST__ = $cart_list["cart_list"];if( count($__LIST__)==0 ) : echo "购物车空空如也" ;else: foreach($__LIST__ as $key=>$cart): $mod = ($i % 2 );++$i;?><li >
                                        <div style="width: 20%"><img src="<?php echo (get_cover_picture_url($cart["goodid"])); ?>" alt="<?php echo (get_good_name($cart["goodid"])); ?>" /></div>
                                        <div style="width: 54%" class="intr "><?php echo (get_good_name($cart["goodid"])); ?></div>
                                        <div style=" width:18%" class="price"><p>￥<?php echo ($cart[price]); ?>x<?php echo ($cart["num"]); ?></p> 
                                            <a href="javascript:;;" onclick="delcart2(this);" class="delcart2" rel="<?php echo ($cart["sort"]); ?>">&nbsp;&nbsp;删除</a></div>
                                    </li><?php endforeach; endif; else: echo "购物车空空如也" ;endif; ?>
                            </ul>
                            <div class="count">共<span class="cart_sum"><?php echo ($cart_list["goods_count"]); ?></span>件商品</span> <b>共计￥<span id='total' ><?php echo ($cart_list["goods_total"]); ?></span></b> <a class="topay " href="<?php echo U('cart/index');?>">去购物车结算<i class="bg"></i></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<script>

    $(function() {
        //搜索功能
        $("#search").click(function() {
            var url = $(this).attr('url');
            var status = $("#sch-sort-txt").attr("data");
            var query = $('.search-form').find('input').serialize();
            query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            query = query.replace(/^&/g, '');
//                if (status != '') {
//                    query = 'status=' + status + "&" + query;
//                }
            if (url.indexOf('?') > 0) {
                url += '&' + query;
            } else {
                url += '?' + query;
            }
            window.location.href = url;
        });

        //回车自动提交
        $('.search-form').find('input').keyup(function(event) {
            if (event.keyCode === 13) {
                $("#search").click();
            }
        });
    });


    $(".add-car").mouseenter(function() {
        $.ajax({
            type: 'post', //传送的方式,get/post
            url: '<?php echo U("Cart/ajax_cart");?>', //发送数据的地址
            dataType: "json",
            success: function(data) {
//                $(".shopcar").empty();
                $(".shopcar").html(data.info);
            }
        });
    })

    function delcart2(obj) {
//        var obj = $(this);
        var string = $(obj).attr("rel");
        $.ajax({
            type: 'post', //传送的方式,get/post
            url: '<?php echo U("Cart/delItemheader");?>', //发送数据的地址
            data: {sort: string},
            dataType: "json",
            success: function(data) {
                $(obj).parents("li").slideUp().remove();
                //$("span.cart_count").text(data.count);
                //$(".cart_price").text(data.price);
//                regoodsprice();
                $("span.cart_sum").text(data.goods_count);
                $("#total" + string).text(data.goods_total);
                var a = data.sum;
                if (a == "0") {
                    $(".head_cart").html('没有数据');
                }
            },
            error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                alert(XMLHttpRequest + thrownError);
            }
        });
        return false;
    }
    //点击删除购物车中商品
    $(".delcart222").click(function() {
        var obj = $(this);
        var string = $(obj).attr("rel");
        $.ajax({
            type: 'post', //传送的方式,get/post
            url: '<?php echo U("Cart/delItem");?>', //发送数据的地址
            data: {sort: string},
            dataType: "json",
            success: function(data) {
                $(obj).parents("li").slideUp().remove();
                //$("span.cart_count").text(data.count);
                //$(".cart_price").text(data.price);
//                regoodsprice();
                $("span.cart_sum").text(data.sum);
                $("#total" + string).text(data.nowgoodtotal);
                var a = data.sum;
                if (a == "0") {
                    $(".head_cart").html('没有数据');
                }
            },
            error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                alert(XMLHttpRequest + thrownError);
            }
        });
        return false;
    });
</script>
<div class="nav">
    <div class="towp">
        <ul>
            <li class="allClassfy"><a href="javascript:;"><i class="bg"></i>全部分类</a>
                <div class="all-naiv all-naiv2">
                    <ul>
                        <?php if(is_array($category_list)): $i = 0; $__LIST__ = $category_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$categoryinfo): $mod = ($i % 2 );++$i;?><li class="ls">
                                <a href="<?php echo U('goods/lists',array('domainid'=>$categoryinfo['id']));?>">
                                    <i style="background-image:url(/Uploads/Picture//<?php echo ($categoryinfo["id"]); ?>/<?php echo (get_good_img($categoryinfo["icon"])); ?>)"></i>
                                    <?php echo ($categoryinfo["name"]); ?></a>
                                <div class="showbox">
                                    <div class="third_cal">
                                        <?php if(is_array($categoryinfo["catelist"])): $i = 0; $__LIST__ = $categoryinfo["catelist"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$catelist): $mod = ($i % 2 );++$i;?><dl><dt> <a href="<?php echo U('Goods/lists',array('domainid'=>$catelist['domainid'],'pid'=>$catelist['id']));?>"><?php echo ($catelist["title"]); ?></a> </dt>
                                                <dd>
                                                <?php if(is_array($catelist["child"])): $i = 0; $__LIST__ = $catelist["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Goods/lists',array('domainid'=>$child['domainid'],'id'=>$child['id'],'pid'=>$child['pid']));?>"><span><?php echo ($child["title"]); ?></span></a><?php endforeach; endif; else: echo "" ;endif; ?>
                                                </dd>
                                            </dl><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </div>
                                    <?php echo hook('Advs',array('mark'=>'cate_index_'.$categoryinfo['id'],'domainid'=>$categoryinfo['id'],'domaintype'=>'site','show_page'=>'cate'));?>
                                </div>
                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
            </li>
            <li class="otherClassfy">
                <ul>
                    <?php if(is_array($_menus)): $i = 0; $__LIST__ = $_menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i; if(($nav["types"] == 0) and ($menunum < 9)): $menunum = $menunum+1; ?>
                            <li <?php if(($nav["name"] == ACTION_NAME) or I('type') == $nav["name"] ): ?>class="active1"<?php endif; ?> ><a href="<?php echo (get_nav_url($nav["url"])); ?>" target="<?php if(($nav["target"]) == "1"): ?>_blank<?php else: ?>_self<?php endif; ?>"><?php echo ($nav["title"]); ?></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    <?php if(session('memberinfo.member_type') == 2 || session('memberinfo.member_type') == 1): ?><li <?php if(I('type') == 1): ?>class="active1"<?php endif; ?> ><a href="<?php echo U('Goods/lists',array('type'=>1));?>" target="">日常商品</a></li><?php endif; ?>
                    <!-- <?php if(session('memberinfo.member_type') == 3 && session('memberinfo.member_agent_id') != 0): ?><li <?php if(ACTION_NAME == 'storegoods'): ?>class="active1"<?php endif; ?> ><a href="<?php echo U('Goods/storegoods',array('store_id'=>session('memberinfo.member_agent_id')));?>" target="">店铺精选</a></li><?php endif; ?> -->
                </ul>
            </li>
        </ul>
    </div>
</div>
<script>
    $(function() {
        var nav = $('#j_nav');
        var bnav = $('.nav');
        var navTop = bnav.offset().top;
        $(window).scroll(function(e) {
            if ($(this).scrollTop() >= navTop) {
                nav.css('display', 'block');
                nav.addClass('m-nav-fix');
            } else {
                nav.css('display', 'none');
                nav.removeClass('m-nav-fix');
            }
        });
        //分类左右高度控掉  
        var lh = $(".all-naiv").height();
        var rh = $(".showbox").height() + 25;
        if (rh < lh) {
            $(".showbox").css({"min-height": lh - 25});
        }
        //end
    });
</script>

<!-- /头部 -->
<!-- 主体 -->

    <div class="wrap" style="background-color: #f5f5f5;">
        <div class="guidance towp"><a href="javascript:;">首页</a><a href="javascript:;"> > 会员首页</a></div>
        <div class="content_mc towp clear">
            <div class="content_left">
                <?php if(session('memberinfo.member_type') == 2): ?><span class="centre_box"><a class="centre" href="javascript:;">管理中心</a></span>
    <div class="menu">
        <!--<div class="tit"><a href="javascript:;">管理中心</a></div>-->
        <dl>
            <dd><a <?php if(ACTION_NAME == 'goodsmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/goodsmanage');?>">商品管理</a></dd>
            <dd><a <?php if(in_array(ACTION_NAME,array('orderlist','orderdetail','ordership','orderpay')) and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/orderlist');?>">订单管理</a></dd>
            <dd><a <?php if(ACTION_NAME == 'membermanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/membermanage');?>">会员管理</a></dd>
            <dd><a <?php if(ACTION_NAME == 'goodsmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/goodsmanage');?>">评价管理</a></dd>
            <dd><a <?php if(ACTION_NAME == 'refundmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/refundmanage');?>">退货管理</a></dd>
            <dd><a <?php if(in_array(ACTION_NAME,array('statics','statclasses','statorder','statmember')) and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?> href="<?php echo U('Agent/statics');?>">数据统计</a></dd>
            <dd><a <?php if(ACTION_NAME == 'storeinfo' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/storeinfo');?>">门店资料</a></dd>
            <dd><a <?php if(ACTION_NAME == 'billmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/billmanage',array('type'=>'log'));?>">提成管理</a></dd>
            <!--<dd <?php if((CONTROLLER_NAME) == "Express"): ?>class="current"<?php endif; ?>><a href="<?php echo U('express/index');?>">快件管理</a></dd>-->
        </dl>
    </div><?php endif; ?>
<?php if(session('memberinfo.member_type') == 1): ?><span class="centre_box"><a class="centre" href="javascript:;">管理中心</a></span>
    <div class="menu">
        <!--<div class="tit"><a href="javascript:;">管理中心</a></div>-->
        <dl>
            <dd><a <?php if(ACTION_NAME == 'membermanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/membermanage');?>">会员管理</a></dd>
            <dd><a <?php if(ACTION_NAME == 'billmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?> href="<?php echo U('Agent/billmanage',array('type'=>'log'));?>">提成管理</a></dd>
            <dd><a <?php if(in_array(ACTION_NAME,array('statics','statclasses','statorder','statmember')) and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?> href="<?php echo U('Agent/statics');?>">数据统计</a></dd>
        </dl>
    </div><?php endif; ?>


<span class="centre_box"><a class="centre" href="<?php echo U('member/index');?>">会员中心</a></span>
<div class="menu">
    <!--<div class="tit"><a href="javascript:;">管理中心</a></div>-->
    <dl>
        <dd><a  <?php if(ACTION_NAME == 'index' and CONTROLLER_NAME == Member): ?>class="current"<?php endif; ?> href="<?php echo U('member/index');?>">会员中心</a></dd>
        <dd><a <?php if((ACTION_NAME) == "mycollection"): ?>class="current"<?php endif; ?> href="<?php echo U('member/mycollection');?>">我的收藏</a></dd>
        <!--        <?php if(session('memberinfo.member_type') == 1): ?><dd <?php if((CONTROLLER_NAME) == "Agent"): ?>class="current"<?php endif; ?>><a href="<?php echo U('agent/index');?>">代理管理</a></dd><?php endif; ?>-->
        <!--        <?php if(session('memberinfo.member_type') == 2): ?><dd <?php if((CONTROLLER_NAME) == "Agent"): ?>class="current"<?php endif; ?>><a href="<?php echo U('agent/index');?>">代理管理</a></dd>
                    <dd <?php if((CONTROLLER_NAME) == "Express"): ?>class="current"<?php endif; ?>><a href="<?php echo U('express/index');?>">快件管理</a></dd><?php endif; ?>-->
        <dd><a <?php if((CONTROLLER_NAME) == "Order"): ?>class="current"<?php endif; ?> href="<?php echo U('order/index');?>">订单管理</a></dd>
        <dd><a <?php if((CONTROLLER_NAME) == "Comment"): ?>class="current"<?php endif; ?> href="<?php echo U('Comment/index');?>">评价管理</a></dd>
        <dd><a <?php if((ACTION_NAME) == "address"): ?>class="current"<?php endif; ?> href="<?php echo U('member/address');?>">地址管理</a></dd>
        <dd><a <?php if((CONTROLLER_NAME) == "Safety"): ?>class="current"<?php endif; ?> href="<?php echo U('Safety/safety');?>">安全验证</a></dd>
        <dd><a <?php if((CONTROLLER_NAME) == "Refund"): ?>class="current"<?php endif; ?> href="<?php echo U('refund/index');?>">售后服务</a></dd>
        <dd><a <?php if((CONTROLLER_NAME) == "Message"): ?>class="current"<?php endif; ?> href="<?php echo U('Message/index');?>">建议投诉</a></dd>
    </dl>
</div>
            </div>
            <div class="content_right" style="border: 0;">
                <div class="members_box">
                    <div class="members clear">
                        <!--                        <div class="tx_box">
                                                    <a class="tx" href="<?php echo U('member/cut',array('id'=>$uid));?>">
                                                        <?php if(empty($memberInfo["face"])): ?><img src="/Public/Web/img/cal1.jpg" width="100" height="100"/>
                                                            <?php else: ?>
                                                            <?php $random = time(); ?>
                                                            <img src="/Uploads/Face/<?php echo ($uid); ?>/face.jpg?r=<?php echo ($random); ?>"  /><?php endif; ?>
                                                        <span>编辑</span>
                                                    </a>
                                                </div>-->
                        <div class="message_left">
                            <a class="tx" href="javascript:;">
                                <?php if(!empty($faceid)): $random = time(); ?>
                                    <div class="upload-pre-item"><img src="/Uploads/Face/<?php echo ($uid); ?>/face.jpg?r=<?php echo ($random); ?>"  width="300" height="300"/></div>
                                    <?php else: ?>
                                    <div class="upload-pre-item"><img src="/Public/Web/img/agencyManage/tou.jpg"  width="300" height="300"/></div><?php endif; ?>
                                <!--<img src="/Public/Web/img/agencyManage/tou.jpg"/>-->
                            </a>
                            <a class="bj" href="<?php echo U('member/cut',array('id'=>$uid));?>">编辑头像</a>
                        </div>
                        <div class="information">
                            <p><?php echo session('user_auth.username');?></p>
                            <p><span><em><?php echo ($memberInfo["levelInfo"]["level_n"]); ?></em></span>
                                <!--<span>资深</span>-->
                            </p>
                            <ul class="security clear">
                                <li>安全等级:</li>
                                <li></li>
                                <li>中</li>
                                <li><a href="<?php echo U('Safety/safety');?>">提升</a></li>
                            </ul>
                            <p><a href="<?php echo U('Member/information');?>">我的个人资料</a></p>
                        </div>
                    </div>
                    <div class="state">
                        <ul class="clear">
                            <li><a href="<?php echo U('order/index',array('state_type'=>'state_new'));?>"> <p>待付款<span>（<?php echo ((isset($ordernum["nopaynum"]) && ($ordernum["nopaynum"] !== ""))?($ordernum["nopaynum"]):"0"); ?>）</span></p> </a></li>
                            <li><a href="<?php echo U('order/index',array('state_type'=>'state_pay'));?>"> <p>待发货<span>（<?php echo ((isset($ordernum["paynum"]) && ($ordernum["paynum"] !== ""))?($ordernum["paynum"]):"0"); ?>）</span></p> </a></li>
                            <li><a href="<?php echo U('order/index',array('state_type'=>'state_send'));?>"> <p>待收货(<span>（<?php echo ((isset($ordernum["shipnum"]) && ($ordernum["shipnum"] !== ""))?($ordernum["shipnum"]):"0"); ?>）</span></p> </a></li>
                            <li><a href="<?php echo U('order/index',array('state_type'=>'state_noeval'));?>"> <p>待评价<span>(<?php echo ((isset($ordernum["noevalnum"]) && ($ordernum["noevalnum"] !== ""))?($ordernum["noevalnum"]):'0'); ?>)</span></p> </a></li>
                        </ul>
                    </div>
                </div>
                <div class="orderForm_box">
                    <div class="orderForm_tit clear">
                        <h3>最近购买订单</h3>
                        <a href="<?php echo U('order/index');?>">查看更多订单</a>
                    </div>
                </div>
                <!--列表 S-->
                <?php if(!empty($orderlist)): ?><div class="order-table">
                        <div class="order-tit order-tit2">
                            <div class="tables">
                                <table width="100%">
                                    <colgroup span="8">
                                        <col width="33%" />
                                        <col width="8%" />
                                        <col width="8%" />
                                        <col width="8%" />
                                        <col width="8%" />
                                        <col width="10%" />
                                        <col width="10%" />
                                        <col width="15%" />
                                    </colgroup>
                                    <tr><th>商品</th><th>价格（元）</th><th>数量</th><th>税费</th><th>运费</th><th>总价</th><th>订单状态</th><th>操作</th></tr>
                                </table>
                            </div>
                        </div>
                        <?php if(is_array($orderlist)): $i = 0; $__LIST__ = array_slice($orderlist,0,3,true);if( count($__LIST__)==0 ) : echo "没有订单" ;else: foreach($__LIST__ as $key=>$order): $mod = ($i % 2 );++$i;?><div class="tables_1">
                                <table width="100%">
                                    <tr>
                                        <td>
                                            <!--<input type="checkbox" name="" id="" value="" />-->

                                            <span>订单编号：<?php echo ($order["orderid"]); ?></span>
                                            <span>下单时间：<?php echo (date("Y-m-d H:i:s",$order["create_time"])); ?></span>
                                            <a href="<?php echo ($order["detail_url"]); ?>">查看订单详情</a>
                                        </td>
                                    </tr>
                                </table>

                                <table class="cp" width="100%">
                                    <colgroup span="8">
                                        <col width="33%" />
                                        <col width="8%" />
                                        <col width="8%" />
                                        <col width="8%" />
                                        <col width="8%" />
                                        <col width="10%" />
                                        <col width="10%" />
                                        <col width="15%" />
                                    </colgroup>
                                    <?php if(is_array($order["goodslist"])): $k = 0; $__LIST__ = $order["goodslist"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($k % 2 );++$k; if($k == 1): ?><tr>
                                                <td class="clear">
                                                    <!--<input type="checkbox" name="" id=""/>-->
                                                    <a class="im" href="<?php echo U('Goods/detail',array('channelname'=>$goods['channelname'],'id'=>$goods['goodid'] ));?>"><img src="<?php echo ($goods['pics_img'][$goods['cover_id']]); ?>"/></a>
                                                    <div class="xinx">
                                                        <p><a target="_blank" href="<?php echo U('Goods/detail',array('channelname'=>$goods['channelname'],'id'=>$goods['goodid'] ));?>"><?php echo (get_good_name($goods["goodid"])); ?></a></p>
                                                        <?php if(!empty($goods["parameters"])): ?><em>规格：<?php echo ((isset($goods["parameters"]) && ($goods["parameters"] !== ""))?($goods["parameters"]):"无"); ?></em><?php endif; ?>
                                                        <?php if($order["status"] == 3 and $order["refund_status"] == 0 and $goods["iscomment"] == 0): ?><a class='sc' href="<?php echo U('Refund/apply_refund',array('orderid'=>$order['id'],'goods_id'=>$goods['goodid'],'id'=>$goods['id']));?>">申请售后</a><?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>￥<?php echo ($goods["price"]); ?></td>
                                                <td>x<?php echo ($goods["num"]); ?></td>
                                                <td>&yen;<?php echo ($goods["haiguan_rate"]); ?></td>
                                                <td rowspan="<?php echo ($order["productnum"]); ?>">￥<?php echo ($order["shipprice"]); ?></td>
                                                <td  rowspan="<?php echo ($order["productnum"]); ?>">￥<?php echo ($order["pricetotal"]); ?></td>
                                                <td  rowspan="<?php echo ($order["productnum"]); ?>"><?php echo ($order["orderStatus"]["status_txt"]); ?></td>
                                                <td rowspan="<?php echo ($order["productnum"]); ?>">
                                            <?php if(!empty($order["balanceString"])): ?><span><?php echo ($order["balanceString"]); ?></span><?php endif; ?>
                                            <?php echo ($order["handle"]); ?>
                                            <?php if(!empty($order["tool"])): ?><a class="qx" href="javascript:;">（<?php echo ($order["tool"]); ?>）</a><?php endif; ?>
                                            </td>
                                            </tr>
                                            <?php else: ?>
                                            <tr>
                                                <td class="clear">
                                                    <!--<input type="checkbox" name="" id=""/>-->
                                                    <a class="im" href="<?php echo U('Goods/detail',array('channelname'=>$goods['channelname'],'id'=>$goods['goodid'] ));?>"><img src="<?php echo ($goods['pics_img'][$goods['cover_id']]); ?>"/></a>
                                                    <div class="xinx">
                                                        <p><a href="<?php echo U('Goods/detail',array('channelname'=>$goods['channelname'],'id'=>$goods['goodid'] ));?>"><?php echo (get_good_name($goods["goodid"])); ?></a></p>
                                                        <?php if(!empty($goods["parameters"])): ?><em>规格：<?php echo ((isset($goods["parameters"]) && ($goods["parameters"] !== ""))?($goods["parameters"]):"无"); ?></em><?php endif; ?>
                                                        <?php if($order["status"] == 3 and $order["refund_status"] == 0 and $goods["iscomment"] == 0): ?><a class='sc' href="<?php echo U('Refund/apply_refund',array('orderid'=>$order['id'],'goods_id'=>$goods['goodid'],'id'=>$goods['id']));?>">申请售后</a><?php endif; ?>
                                                    </div>
                                                </td>
                                                <td><?php echo ($goods["price"]); ?></td>
                                                <td>x<?php echo ($goods["num"]); ?></td>
                                                <td>0.00</td>
                                            </tr><?php endif; endforeach; endif; else: echo "没有订单" ;endif; ?>
                                </table>
                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div> <!--列表 E-->
                    <?php else: ?>
                    <div class="no_data">
                        <h3>
                            <i class=""> <img src="/Public/Web/img/confirmGoods/ts.png"/> </i> 最近没有购买记录！
                        </h3>						        	
                    </div><?php endif; ?>
                <div class="history hot">
                    <h3>浏览历史</h3>
                    <?php if(!empty($history_list)): ?><ul class="clear">
                            <?php if(is_array($history_list)): $key = 0; $__LIST__ = array_slice($history_list,0,4,true);if( count($__LIST__)==0 ) : echo "没有数据" ;else: foreach($__LIST__ as $key=>$tj): $mod = ($key % 2 );++$key;?><li>
                                    <a href="<?php echo U('Goods/detail',array('channelname'=>$tj['channelname'],'id'=>$tj['id']));?>"><img src="<?php echo ($tj['pics_img'][$tj['cover_id']]); ?>"/><span><?php echo (msubstr($tj["title"],0,27)); ?></span></a>
                                    <p><em>￥<span><?php echo ($tj["price"]); ?></span></em><i>￥<?php echo ($tj["marketprice"]); ?></i></p>
                                    <p class="dy">销量：<span><?php echo ($tj["totalsales"]); ?></span>件</p>
                                </li><?php endforeach; endif; else: echo "没有数据" ;endif; ?>
                            <?php else: ?>
                        </ul>
                        <div class="no_data">
                            <h3>
                                <i class=""> <img src="/Public/Web/img/confirmGoods/ts.png"/> </i> 没有历史浏览商品！
                            </h3>						        	
                        </div><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<!-- /主体 -->
<!-- 底部 -->
<a href="javascript:;" class="i_btntop"></a>
<footer>
    <div class="promise2">
        <div class="towp">
            <ul>
                <li>
                    <i class="bg  prom1"></i>
                    <p>快捷物流</p>
                    <p>最快捷得送达方式</p>
                </li>
                <li>
                    <i class="bg prom2"></i>
                    <p>优质产区</p>
                    <p>来自全球最优质的产区</p>
                </li>
                <li>
                    <i class="bg prom3"></i>
                    <p>服务保证</p>
                    <p>保证产品的购买、配送质量</p>
                </li>
                <li>
                    <i class="bg prom4"></i>
                    <p>国际正品保障</p>
                    <p>绝对正品保证</p>
                </li>
            </ul>
        </div>
    </div>
    <div class="promise3" style="display:none;">
        <div class="towp">
            <?php if(is_array($helpcatelist)): $i = 0; $__LIST__ = $helpcatelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><dl><dt><?php echo ($list["title"]); ?></dt>
                    <?php if(is_array($list["child"])): $i = 0; $__LIST__ = $list["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($i % 2 );++$i;?><dd><a href="<?php echo U('help/detail',array('id'=>$child['name']));?>"><?php echo ($child["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?>
                </dl><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <?php echo hook('ReturnTop');?>
    </div>
    <div class="copy"><?php echo C('WEB_SITE_ICP');?></div>
    <div class="footer-ico"><img src="/Public/Web/img/foter-ico.jpg" /> </div>
    <p style="display: block;"><div class="sss"></div></p>
<a href="javascript:;"><div class="eee">

    </div></a>
</footer>
<script type="text/javascript">
    var ThinkPHP = window.Think = {
        "MID": "<?php echo ($guid); ?>",
        "UID": "<?php echo ($uid); ?>",
        "IMG": "/Public/Web/img", //项目公共目录地址
        "ROOT": "", //当前网站地址
        "JS": "/Public/Web/js", //当前项目地址
        "APP": "", //当前项目地址
        "PUBLIC": "/Public", //项目公共目录地址
        'SITE_URL': "<?php echo SITE_URL;?>",
        "DEEP": "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
        "MODEL": ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
        "VAR": ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"],
        "searchUrl": '<?php echo U("Web/Goods/lists");?>',
        "commentUrl": '<?php echo U("Web/Comment/getCommentListById");?>',
        "diggUrl": '<?php echo U("Web/Document/digg");?>',
        "undiggUrl": '<?php echo U("Web/Document/digg");?>',
        "collectiontUrl": '<?php echo U("Web/MyCollection/addcollection");?>',
        "messageUrl": '<?php echo U("Web/Message/insert");?>',
        "replyUrl": '<?php echo U("Web/Message/reply");?>',
        "countUrl": '<?php echo U("Web/Message/saveMessageCount");?>',
        "deleUrl": '<?php echo U("Web/Message/deleMessage");?>',
        "unfollowUrl": '<?php echo U("Web/Follow/unFollow");?>',
        "dofollowUrl": '<?php echo U("Web/Follow/doFollow");?>',
        "docollectionURL": '<?php echo U("Web/Member/doCollection");?>',
        "delcommentUrl": '<?php echo U("Web/Comment/delcomment");?>',
        "denounceUrl": '<?php echo U("Web/Denounce/adddenounce");?>',
        "getMoreCommentUrl": '<?php echo U("Web/Comment/getMoreComment");?>',
        "searchUser": '<?php echo U("Web/Member/searchUser");?>',
        "ajaxComment": '<?php echo U("Web/Comment/ajaxComment");?>',
        "login": '<?php echo U("Web/Member/login");?>',
        "indexUrl": '<?php echo U("Web/Index/changestyle");?>'
    };
    function U(url, params, rewrite) {
        if (window.Think.MODEL[0] == 2) {
            var website = window.Think.ROOT + '/';
            url = url.split('/');
            if (url[0] == '' || url[0] == '@')
                url[0] = '';
            if (!url[1])
                url[1] = 'Index';
            if (!url[2])
                url[2] = 'index';
            website = website + url[1] + '/' + url[2];

            if (params) {
                params = params.join('/');
                website = website + '/' + params;
            }
            if (!rewrite) {
                website = website + '.html';
            }

        } else {
            var website = window.Think.ROOT + '/index.php';
            url = url.split('/');
            if (url[0] == '' || url[0] == '@')
                url[0] = APPNAME;
            if (!url[1])
                url[1] = 'Index';
            if (!url[2])
                url[2] = 'index';
            website = website + '?s=/' + url[0] + '/' + url[1] + '/' + url[2];
            if (params) {
                params = params.join('/');
                website = website + '/' + params;
            }
            if (!rewrite) {
                website = website + '.html';
            }
        }

        if (typeof (window.Think.MODEL[1]) != 'undefined') {
            website = website.toLowerCase();
        }
        return website;
    }

    function search() {
        var keywords = $('#keywords').val();
        var urlnew = $('#search_form').attr('action');
        if (keywords == '' || urlnew == '') {
            return false;
        } else {
            var arr = [];
            arr[0] = keywords;
            arr[1] = 1;
            var urlhref = U(urlnew, arr, false);
            window.location.href = urlhref;
        }
        return false;
    }
    function search2() {
        var keywords = $('#keywords').val();
        var urlnew = $('#search_form').attr('action');
        if (keywords == '' || urlnew == '') {
            return false;
        } else {
            url = urlnew.split('/');
            var website = window.Think.ROOT + '/';
            website = website + url[1] + '/lists/keywords/' + keywords + '.html';
//        alert(website); return false;

            window.location.href = website;

        }
        return false;
    }

    //返回顶部
    $('.i_btntop').click(function() {
        $('html, body').animate({scrollTop: 0}, '500');
    });
    var poxr = ($(window).width() - 1400) / 2;
    var vtop_b = $(document).scrollTop();
    if (vtop_b > 200) {
        $('.i_btntop').show();
    }
    $(window).scroll(function() {
        var vtop = $(document).scrollTop();
        if (vtop > 200) {
            $('.i_btntop').show();
        } else {
            $('.i_btntop').hide();
        }
    })

    //结束返回

</script>
<div id="cart_message_box" style="display: none;">
    <style>
        .add-goods2 {
            background: #fff none repeat scroll 0 0;
            border: none;
            display: block;
            height: 100px;
            /*margin: -75px 0 0 -220px;*/
            /*padding: 20px;*/
            position: relative;
            text-align: center;
            width: 400px;
            /*z-index: 1001;*/
        }
        .layui-layer-mrk{
            border:  4px solid #f16f71;
        }
        .layui-layer-mrk .layui-layer-title{display: none;}
    </style>
    <div class="add-goods add-goods2" >
        <a href="javascript:javascript:layer.closeAll();" class="t_close"></a>
        <div  class="f14 fb f3 con">您选购的商品已经添加到购物车</div>
        <p class="tc">
            <a class="" href="javascript:layer.closeAll();"><span class="go-shop">&lt;&lt; 继续购物</span></a>
            <a href="<?php echo U('Cart/index');?>" class="t_btn1">立即结算</a>
        </p>
    </div>

</div>
<!-- /底部 -->

<div class="tdivbg" id="tdivbg"></div>
<div class="browser" id="browser">
    <img src="/Public/Web/img/msg.png" class="pic">
    <p>
        <a href="https://www.google.com/intl/cn/chrome/browser/" class="chrome">Chrome</a>
        <a href="https://www.mozilla.org/zh-CN/firefox/new/" class="firefox">Firefox</a>
        <a href="http://windows.microsoft.com/zh-CN/internet-explorer/download-ie" class="ie">IE</a>
    </p>
</div>
<script src="/Public/Web/js/jquery-1.11.2.min.js"></script>
<script src="/Public/static/layer/layer.js"></script>
<script src="/Public/Web/js/mrk.js" type="text/javascript" charset="utf-8"></script>

<script>
    $('.allClassfy').click(function() {
        $('.all-naiv').fadeToggle();
    });

    var _CART_URL = "<?php echo U('Cart/addItem');?>";
    var _FAVORTABLE_URL = "<?php echo U('Favortable/addfavortable');?>";
    var LOGIN_URL = "<?php echo U('member/login');?>";
    /*判断浏览器版本是否过低*/
    var Sys = {};

    var ua = navigator.userAgent.toLowerCase();

    if (window.ActiveXObject) {

        Sys.ie = ua.match(/msie ([\d.]+)/)[1];
        //$(".tdivbg,.browser").css({"dislay":"block"});
        if (Sys.ie <= 7) {

            document.getElementById("tdivbg").style.display = "block";
            document.getElementById("browser").style.display = "block";
            document.body.style.overflow = "hidden";

        }
    }
</script>
<style>
    .all-naiv{ display: none;}
</style>
</body>
</html>