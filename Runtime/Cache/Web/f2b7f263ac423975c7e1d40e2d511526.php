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

    <style>
        .m-nav-fix{position: fixed;top:0;width: 100%;background-color:#fff;}
        .swiper-pagination-bullet{
            opacity: 1.0;
            background-color: #fff;
            width: 20px;
            height: 20px;
            margin: 0 10px;
        }
        .swiper-pagination-bullet-active{
            border: 1px solid red;
        }        
        .promise2 li p{
            font-size: 14px;
        }
        .classfy2 .air-line3{
            top: -220px;
        }
        .wrap{
            padding-bottom: 30px;padding-top:35px;
        }
    </style>
 
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

    <!-- 内容  s-->
    <div class="nav-top " id="j_nav" >
        <div class="towp" >
            <div class="logo">
                <a href="#"><!-- <img src="/Public/Web/img/logo2.png" /> -->
                妙品生活 </a>
            </div>
            <ul>
                <?php if(is_array($_menus)): $i = 0; $__LIST__ = $_menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i;?><li <?php if(($nav["title"]) == "首页"): ?>class="active1"<?php endif; ?> >
                        <a href="<?php echo (get_nav_url($nav["url"])); ?>" target="<?php if(($nav["target"]) == "1"): ?>_blank<?php else: ?>_self<?php endif; ?>"><?php echo ($nav["title"]); ?></a>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <!-- 轮播 -->
   <div class="flexslider pr">
            <?php echo hook('Advs',array('mark'=>'index_A1','domainid'=>'','domaintype'=>'site','show_page'=>'pc_banner'));?>
     </div>

    <!-- 保证 -->
    <div class="promise">
        <div class="towp"></div>
    </div>
    <!-- 内容 -->
    <div class="wrap">
        <!--限时抢购 S-->
        <?php if(!empty($xianshi_goods)): ?><div class="towp Limit-buy  ">
                <h3 class="buy-tit">
                    <span class="b-tit">限时抢购</span>
                    <div class="countdown">距活动结束还剩
                        <!--<span class="title">距本次结束：</span>-->
                        <span id="RemainD"><?php echo ($ptime["day"]); ?></span> 天
                        <span id="RemainH"><?php echo ($ptime["hour"]); ?></span> 时
                        <span id="RemainM"><?php echo ($ptime["point"]); ?></span> 分
                        <span id="RemainS"><?php echo ($ptime["times"]); ?></span> 秒
                    </div>
                    <script>
                        function FreshTime()
                        {
                            var endtime = new Date("<?php echo (date('Y/m/d, H:i:s',$xianshi_active["end_time"])); ?>");//结束时间
                            var nowtime = new Date();//当前时间
                            var lefttime = parseInt((endtime.getTime() - nowtime.getTime()) / 1000);
                            d = parseInt(lefttime / 3600 / 24);
                            h = parseInt((lefttime / 3600) % 24);
                            m = parseInt((lefttime / 60) % 60);
                            s = parseInt(lefttime % 60);

                            document.getElementById("RemainD").innerHTML = d;
                            document.getElementById("RemainH").innerHTML = h;
                            document.getElementById("RemainM").innerHTML = m;
                            document.getElementById("RemainS").innerHTML = s;
                            if (lefttime <= 0) {
                                document.getElementById("LeftTime").innerHTML = "团购已结束";
                                clearInterval(sh);
                            }
                            ;
                        }
                        ;
                        FreshTime();
                        var sh;
                        sh = setInterval(FreshTime, 1000);

                    </script>
                    <a href="<?php echo U('Goods/xianshi');?>" class="bg fr"></a>
                </h3>
                <!--<h3 class="buy-tit"><span class="b-tit">限时抢购</span><a href="<?php echo U('Goods/xianshi');?>" class="bg fr"></a></h3>-->
                <ul class=" limiProList">
                    <?php if(is_array($xianshi_goods)): $i = 0; $__LIST__ = array_slice($xianshi_goods,0,6,true);if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><li class="">
                            <a class="proImg" href="<?php echo U('Goods/detail',array('active'=>'pomotion','id'=>$goods['goods_id']));?>">
                                <img src="/Uploads/Picture//<?php echo (get_cover($goods["goods_image"],'path')); ?>" />
                                <strong><?php echo ($goods["goods_name"]); ?></strong>
                            </a>
                            <div class="pric"><span class="price">￥<span class="bigFont"><?php echo ($goods["xianshi_price"]); ?></span></span><strike>￥<?php echo ($goods["goods_org_price"]); ?></strike></div>
                            <div class="proHover">
                                <p class="remin"><span>今日</span>剩余数量<?php echo ($goods["xianshi_stock"]); ?></p>
                                <div class="img"><a href="<?php echo U('Goods/detail',array('active'=>'pomotion','id'=>$goods['goods_id']));?>"><img src="/Uploads/Picture//<?php echo (get_cover($goods["goods_image"],'path')); ?>" /></a></div>
                                <?php if(($goods["xianshi_stock"]) == "0"): ?><p class="grab"><a class="" style="background:gray;" href="javascript:;;">抢光啦</a></p>
                                <?php else: ?>
                                <p class="grab"><a class="" href="<?php echo U('Goods/detail',array('active'=>'pomotion','id'=>$goods['goods_id']));?>">我抢</a></p><?php endif; ?>
                            </div>
                        </li><?php endforeach; endif; else: echo "$empty" ;endif; ?>
                </ul>
                <div class="air-line1"></div>
            </div><?php endif; ?>
        <!--限时抢购 E-->
        <!--热卖推荐 S-->
        <div class="towp hot-sale clear" style="display:none;">
            <div class="hot-sale1  fl">
                <div class=" hot1  ">
                    <h3>热卖推荐/<span>HOT SALE</span></h3>
                    <div class="swiper-container ">
                        <ul class="swiper-wrapper">
                            <?php echo hook('Advs',array('mark'=>'hot_A1','domainid'=>'','domaintype'=>'site'));?>
                        </ul>
                    </div>
                </div>
                <div class="hot2">
                    <div> <?php echo hook('Advs',array('mark'=>'hot_A2','domainid'=>'','domaintype'=>'site'));?></div>
                </div>
                <i class="bg right-top"></i>
            </div>
            <div class="hot-sale2  fl">
                <div class="sale-wrap ">
                    <ul class="hot-sale2-1 ">
                        <?php echo hook('Advs',array('mark'=>'hot_A3','domainid'=>'','domaintype'=>'site'));?>
                    </ul>
                </div>
                <ul class="hot-sale2-2 ">
                    <?php echo hook('Advs',array('mark'=>'hot_A4','domainid'=>'','domaintype'=>'site'));?>
                </ul>
            </div>
            <div class="air-line2"></div>
        </div>
        <!--热卖推荐 E-->
        <!--新品推荐 s-->
        <?php if(!empty($new_goods)): ?><div class="new_product_box towp">
                <div class="n-tit">
                    <h3>新品推荐/<span>NEW&nbsp&nbspPRODUCTS</span></h3>
                    <a href="<?php echo U('Goods/lists');?>">更多推荐</a>
                </div>
                <ul class=" limiProList">
                    <?php if(is_array($new_goods)): $i = 0; $__LIST__ = $new_goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$newgood): $mod = ($i % 2 );++$i;?><li class="">
                            <a class="proImg" href="<?php echo U('Goods/detail',array('channelname'=>$newgood['channelname'],'id'=>$newgood['id']));?>" title="<?php echo ($newgood["title"]); ?>">
                                <img src="<?php echo ($newgood['pics_img'][$newgood['cover_id']]); ?>" alt="<?php echo ($newgood["title"]); ?>"/>
                                <strong><?php echo getsubstrutf8($newgood['title'],0,30);?></strong>
                            </a>
                            <div class="pric"><span class="price">￥<span class="bigFont"><?php echo ($newgood["show_price"]); ?></span><strike>￥<?php echo ($newgood["marketprice"]); ?></strike></div>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
                <div class="air-line1"></div>
            </div><?php endif; ?>
        <!--新品推荐 E-->


        <!-- 楼层模块 S-->
        <!-- 楼层模块 S-->
       <?php if(is_array($web_code)): $k = 0; $__LIST__ = $web_code;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$moduleinfo): $mod = ($k % 2 );++$k;?><div class="classfy1  clear towp">
           <div class="sideNaiv">
               <div class="sN-tit">
                   <p class="floor"><?php echo ($moduleinfo["tit"]["code_info"]["floor"]); ?></p>
                   <div  class="img "><img alt="<?php echo ($moduleinfo["tit"]["code_info"]["name"]); ?>" src="/Uploads/Picture//1/<?php echo (get_cover($moduleinfo["tit"]["code_info"]["icon"],'path')); ?>" /> </div>
                   <p><?php echo ($moduleinfo["tit"]["code_info"]["en_name"]); ?> </p>
                   <h4><span><?php echo ($moduleinfo["tit"]["code_info"]["name"]); ?></span></h4>
               </div>
               <div>
                   <a href="<?php echo ($moduleinfo["left_ad"]["code_info"]["ad_url"]); ?>"><img style="width:214px;height: 228px;" alt="<?php echo ($moduleinfo["left_ad"]["code_info"]["ad_name"]); ?>" src="/Uploads/Picture//1/<?php echo (get_cover($moduleinfo["left_ad"]["code_info"]["icon"],'path')); ?>" > </a>
               </div>
               <div class="w100">
                   <div class="li-naiv">
                       <ul class="li-naiv-list">
                           <?php if(is_array($moduleinfo["cate_list"]["code_info"]["goods_class"])): $i = 0; $__LIST__ = array_slice($moduleinfo["cate_list"]["code_info"]["goods_class"],0,6,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$class): $mod = ($i % 2 );++$i;?><li><span><a href="<?php echo U('Goods/lists',array('domainid'=>$class['domainid'],'id'=>$class['id']));?>"><?php echo ($class["gc_name"]); ?></a></span></li><?php endforeach; endif; else: echo "" ;endif; ?>
                       </ul>
                   </div>
               </div>
           </div>
           <div class="swiper-container cla-swi">
               <ul class="swiper-wrapper">
                   <?php if(empty($moduleinfo["mid_ad"]["code_info"]["icon"])): ?>没有广告
                   <?php else: ?>
                       <li class="swiper-slide"><a title="<?php echo ($moduleinfo["mid_ad"]["code_info"]["ad_name"]); ?>" href="<?php echo ($moduleinfo["mid_ad"]["code_info"]["ad_url"]); ?>">
                               <img style="width:270px;height: 432px;" alt="<?php echo ($moduleinfo["mid_ad"]["code_info"]["ad_name"]); ?>" src="/Uploads/Picture//1/<?php echo (get_cover($moduleinfo["mid_ad"]["code_info"]["icon"],'path')); ?>" /> </a></li><?php endif; ?>
               </ul>
           </div>
           <div class="cla-pro fl">
               <ul class="pro-list ">
                   <?php if(is_array($moduleinfo["mid_goods"]["code_info"]["goods_list"])): $i = 0; $__LIST__ = array_slice($moduleinfo["mid_goods"]["code_info"]["goods_list"],0,6,true);if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><li>
                       <a href="<?php echo U('Goods/detail',array('id'=>$goods['id']));?>">
                           <img style="width:136px; height: 123px;" src="/Uploads/Picture//<?php echo (get_cover($goods["cover_id"],'path')); ?>" />
                           <strong><?php echo ($goods["title"]); ?></strong>
                       </a>
                       <div class="pric"><span class="price">￥<span class="bigFont"><?php echo ($goods["show_price"]); ?></span></span><a href="javascript: addcart('<?php echo ($goods["id"]); ?>',1,'<?php echo ($goods["show_price"]); ?>','');"><i class="bg "></i></a></div>
                   </li><?php endforeach; endif; else: echo "暂时没有数据" ;endif; ?>
               </ul>
           </div>
           <div class="xlph fl">
               <h3><i class="bg"></i>一周热卖TOP5</h3>
               <ul class="xlph-list xlph-list<?php echo ($k); ?>">
                    <?php if(is_array($moduleinfo["hot_goods"]["code_info"]["goods_list"])): $k = 0; $__LIST__ = array_slice($moduleinfo["hot_goods"]["code_info"]["goods_list"],0,5,true);if( count($__LIST__)==0 ) : echo "暂时没有数据" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($k % 2 );++$k; if(($k) == "1"): ?><li class="active">
                       <span><i class="bg num<?php echo ($k); ?>"><?php echo ($k); ?></i></span>
                       <a class="img" href="<?php echo U('Goods/detail',array('id'=>$goods['id']));?>">
                           <img width="100" src="/Uploads/Picture//<?php echo (get_cover($goods["cover_id"],'path')); ?>"/>
                           <strong><?php echo ($goods["title"]); ?></strong>
                       </a>
                       <div class="pric"><span class="price">￥<span class="bigFont"><?php echo ($goods["show_price"]); ?></span></span></div>
                   </li>
                    <?php else: ?>
                    <li class="">
                       <span><i class="bg num<?php echo ($k); ?>"><?php echo ($k); ?></i></span>
                       <a class="img" href="<?php echo U('Goods/detail',array('id'=>$goods['id']));?>">
                           <img width="100" src="/Uploads/Picture//<?php echo (get_cover($goods["cover_id"],'path')); ?>"/>
                           <strong><?php echo ($goods["title"]); ?></strong>
                       </a>
                       <div class="pric"><span class="price">￥<span class="bigFont"><?php echo ($goods["show_price"]); ?></span></span></div>
                   </li><?php endif; endforeach; endif; else: echo "暂时没有数据" ;endif; ?>
               </ul>
           </div>
       </div><?php endforeach; endif; else: echo "" ;endif; ?>
       <!-- 楼层模块 E-->
      <div class="classfy1 classfy2 clear towp" style="display:none;">
            <div class="sideNaiv">
                <div class="sN-tit">
                    <p class="floor">F2</p>
                    <div  class="img "><img src="/Public/Web/img/cal-2.png" /> </div>
                    <p>INFANT & MOM </p>
                    <h4><span>进口日化</span></h4>
                </div>
                <div>
                    <a href="#"><img src="/Public/Web/img/cal1.jpg" > </a>
                </div>
                <div class="w100">
                    <div class="li-naiv">
                        <ul class="li-naiv-list">
                            <li><span><a href="#">孕期营养</a></span></li>
                            <li><span><a href="#">安全湿巾</a></span></li>
                            <li><span><a href="#">驱蚊防蚊</a></span></li>
                            <li><span><a href="#">孕期营养</a></span></li>
                            <li><span><a href="#">安全湿巾</a></span></li>
                            <li><span><a href="#">驱蚊防蚊</a></span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="swiper-container cla-swi">
                <ul class="swiper-wrapper">
                    <li class="swiper-slide"><a href="#"><img src="/Public/Web/img/cla/cla1.jpg" /> </a></li>
                    <li class="swiper-slide"><a href="#"><img src="/Public/Web/img/cla/cla1.jpg" /> </a></li>
                    <li class="swiper-slide"><a href="#"><img src="/Public/Web/img/cla/cla1.jpg" /> </a></li>
                </ul>
            </div>
            <div class="cla-pro fl">
                <ul class="pro-list ">
                    <li>
                        <a href="#">
                            <img src="/Public/Web/img/cla/pro1.jpg" />
                            <strong>韩国进口纸尿裤100个装</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span><a href="#"><i class="bg "></i></a></div>
                    </li>
                    <li>
                        <a href="#">
                            <img src="/Public/Web/img/cla/pro1.jpg" />
                            <strong>韩国进口纸尿裤100个装</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span><a href="#"><i class="bg "></i></a></div>
                    </li>
                    <li>
                        <a href="#">
                            <img src="/Public/Web/img/cla/pro1.jpg" />
                            <strong>韩国进口纸尿裤100个装</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span><a href="#"><i class="bg "></i></a></div>
                    </li>
                    <li>
                        <a href="#">
                            <img src="/Public/Web/img/cla/pro1.jpg" />
                            <strong>韩国进口纸尿裤100个装</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span><a href="#"><i class="bg "></i></a></div>
                    </li>
                    <li>
                        <a href="#">
                            <img src="/Public/Web/img/cla/pro1.jpg" />
                            <strong>韩国进口纸尿裤100个装</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span><a href="#"><i class="bg "></i></a></div>
                    </li>
                    <li>
                        <a href="#">
                            <img src="/Public/Web/img/cla/pro1.jpg" />
                            <strong>韩国进口纸尿裤100个装</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span><a href="#"><i class="bg "></i></a></div>
                    </li>
                </ul>
            </div>
            <div class="xlph fl">
                <h3><i class="bg"></i>一周热卖TOP6</h3>
                <ul class="xlph-list">
                    <li class="active">
                        <span><i class="bg num1">1</i></span>
                        <a class="img" href="#">
                            <img src="/Public/Web/img/week-top/pro1.jpg"/>
                            <strong>现货贝因美奶粉顺丰包邮</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span></div>
                    </li>
                    <li class="">
                        <span><i class="bg num2">2</i></span>
                        <a class="img" href="#">
                            <img src="/Public/Web/img/week-top/pro1.jpg"/>
                            <strong>现货贝因美奶粉顺丰包邮</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span></div>
                    </li>
                    <li class="">
                        <span><i class="bg num3">3</i></span>
                        <a class="img" href="#">
                            <img src="/Public/Web/img/week-top/pro1.jpg"/>
                            <strong>现货贝因美奶粉顺丰包邮</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span></div>
                    </li>
                    <li class="">
                        <span><i class="bg num4">4</i></span>
                        <a class="img" href="#">
                            <img src="/Public/Web/img/week-top/pro1.jpg"/>
                            <strong>现货贝因美奶粉顺丰包邮</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span></div>
                    </li>
                    <li class="">
                        <span class=""><i class="bg num4">4</i></span>
                        <a class="img" href="#">
                            <img src="/Public/Web/img/week-top/pro1.jpg"/>
                            <strong>现货贝因美奶粉顺丰包邮现货贝因美奶粉顺丰包邮</strong>
                        </a>
                        <div class="pric"><span class="price">￥<span class="bigFont">78</span>.00</span></div>
                    </li>
                </ul>
            </div>
            <div class="air-line3"></div>
        </div>
      
        <!-- 楼层模块 E-->
        <div class="bot-banner towp">
            <ul class="clear">
                <?php echo hook('Advs',array('mark'=>'index_A2','domainid'=>'','domaintype'=>'site'));?>
            </ul>
        </div>
    </div>
    <!-- 内容 e -->


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

    <script type="text/javascript" src="/Public/Web/js/jquery.flexslider-min.js"></script>
    <script>
          $(function(){
                   $('.flexslider').flexslider({
                            directionNav: true,
                            pauseOnAction: false
                    }); 
                });
//        var mySwiper = new Swiper('.banner_box .swiper-container', {
//            autoplay: 5000, //可选选项，自动滑动
//            pagination: '.banner_box .pagination',
//            paginationClickable: true,
//            loop: true,
//        });
                        $(function() {
                            $('.xlph-list li').mouseenter(function() {
                                cname = $(this).attr("class");
                                if (cname != "active") {
                                    $(this).toggleClass("active").siblings().removeClass('active')
                                }
                            });
                            $(".all-naiv").removeClass('all-naiv2')
                        });
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
                        })
    </script>

<script>

    var _CART_URL = "<?php echo U('Cart/addItem');?>";
    var _FAVORTABLE_URL = "<?php echo U('Favortable/addfavortable');?>";
    var LOGIN_URL = "<?php echo U('member/login');?>";

    $('.allClassfy').click(function() {
        $('.all-naiv').fadeToggle();
    });


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
</body>
</html>