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

    <link href="/Public/Web/css/css.css" rel="stylesheet" type="text/css" />
    <link href="/Public/Web/css/swiper-3.3.0.min.css" rel="stylesheet" type="text/css" />
    <style>
        .all-naiv{display:none;}
				.detail-wrap h2{
					white-space: normal;
			    min-height: 32px;
			    height: auto;
			    line-height: normal;}
					.collected i{ background: url(/Public/Web/img/add_15.png) no-repeat -33px 2px !important;}
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

    <!-- 位置 -->
    <div class="towp  position">
        您的位置：
        <span><a href="<?php echo U('Index/index');?>" title="">首页 </a> </span>
        <span class="fonS"> > </span>
        <?php if($domaindinfo): ?><a href="<?php echo U('goods/lists',array('domainid'=>$domaindinfo['id']));?>" title="<?php echo ($domaindinfo["name"]); ?>"><?php echo ($domaindinfo["name"]); ?></a>
            <span>&gt;</span><?php endif; ?>
        <?php if($info['cateinfo']['p']): ?><a href="<?php echo U('Goods/lists',array('domainid'=>$info['cateinfo']['p']['domainid'],'id'=>$info['cateinfo']['p']['id']));?>" title=""><?php echo ($info["cateinfo"]["p"]["title"]); ?></a>
            <span>&gt;</span><?php endif; ?>
        <?php if($info['cateinfo']): ?><a href="<?php echo U('Goods/lists',array('domainid'=>$info['cateinfo']['domainid'],'id'=>$info['cateinfo']['id']));?>" title="<?php echo ($info["cateinfo"]["title"]); ?>"><?php echo ($info["cateinfo"]["title"]); ?></a>
            <span>&gt;</span><?php endif; ?>
        <span class="colo1"> <?php echo ($info["title"]); ?></span>  
    </div>
    <!-- 详情 -->
    <div class="detail towp  clear">
        <div class="preview ">
            <div class="smallImg">
                <div class="scrollbutton smallImgUp disabled"></div>
                <div id="imageMenu">
                    <ul class="det-thumb ">
                        <?php if(is_array($info["pics_img"])): $k = 0; $__LIST__ = $info["pics_img"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$picimg): $mod = ($k % 2 );++$k; if(($k) == "1"): ?><li id="onlickImg">
                                <img src="<?php echo ($picimg); ?>" midsrc="<?php echo ($picimg); ?>" bigsrc="<?php echo ($picimg); ?>" />
                            </li>
                            <?php else: ?>
                            <li>
                                <img src="<?php echo ($picimg); ?>" midsrc="<?php echo ($picimg); ?>" bigsrc="<?php echo ($picimg); ?>" />
                            </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
                <div class="scrollbutton smallImgDown"></div>
            </div>
            <div id="vertical" class="booth bigImg">
                <!--<img src="<?php echo ($info['pics_img'][$info['cover_id']]); ?>" id="midimg" alt="<?php echo ($info["title"]); ?>">-->
                <?php if(is_array($info["pics_img"])): $k = 0; $__LIST__ = $info["pics_img"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$picimg): $mod = ($k % 2 );++$k; if(($k) == "1"): ?><img src="<?php echo ($picimg); ?>" id="midimg" alt="<?php echo ($info["title"]); ?>" /><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                
                <div style="display:none;" id="winSelector"></div>
                <p class="AddFavorite">
                    <a href="javascript:;" class="addfavortable <?php if(($info["favor"]) == "1"): ?>collected<?php endif; ?>" data-id="<?php echo ($info["id"]); ?>" title="<?php if(($info["favor"]) == "1"): ?>取消收藏<?php else: ?>收藏商品<?php endif; ?>">
		                	<i class="bg"></i><span><?php if(($info["favor"]) == "1"): ?>已收藏<?php else: ?>收藏商品<?php endif; ?>(<?php echo ($goodscollect); ?>)</span></a>
                商品编号：<?php echo ($info["pcode"]); ?>
                </p>
            </div>
            <div id="bigView" style="display:none;"><img width="800" height="800" alt="" src="" /></div>
        </div>

        <!--详情参数 start-->

        <div class="detail-wrap ">
            <div class="pro-info">
                <h2 title="<?php echo ($info["title"]); ?>"><?php echo ($info["title"]); ?></h2>
                <p class="de-intr"><?php echo strip_tags($info['description']);?></p>
            </div>
            <div class="de-price">
                <p>市场价：<s><span class="fm_1">￥</span><span id="zzmarketprice"><?php echo ($info["marketprice"]); ?></span></s></p>
                <p>原价：<s class="fm_1">￥</s><span class="big-font c_line" id="zzprice"><?php echo ($info["price"]); ?></span></p>
                <!---有促销价时显示--->
                <p class="fc3">会员价：<span class="fm_1">￥</span><span class="big-font" id="zzmemberprice"><?php echo ($info["member_price"]); ?></span></p>
                <!--结束--->
                <!---没有促销价时显示--->
                <!--<p class="fc3">会员价：<span class="fm_1">￥</span><span class="big-font">1500.00</span></p>-->
                <!---结束-->
                <!--促销价--->
                <div class="line_2"></div>
                <p>当前库存：<span class="sales_num">
                <?php if(($info['stock'] > $info['storage_alarm'])): ?>&nbsp;有货   
                        <?php else: ?>
                        &nbsp; <?php echo ($info['stock']); endif; ?>
                    </span></p>
                
                
            </div>

            <div class="four">
                <?php if(is_array($info["goodattr"])): $i = 0; $__LIST__ = $info["goodattr"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr): $mod = ($i % 2 );++$i;?><dl class="taste" style="min-height:26px;height:auto !important;overflow:hidden;padding-left:65px"><dt><?php echo ($attr["name"]); ?>：</dt>
                        <?php if($attr['attr_type'] == 1): if(is_array($attr["sub"])): $i = 0; $__LIST__ = $attr["sub"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subs): $mod = ($i % 2 );++$i;?><dd style="margin-bottom:5px" class="pro_taocan <?php if(($key) == "0"): ?>active2<?php endif; ?>" onclick="selecradiotattr(this);" attrid="<?php echo ($subs["id"]); ?>" price="<?php echo ((isset($subs["price"]) && ($subs["price"] !== ""))?($subs["price"]):0); ?>"><?php echo ($subs["value"]); ?>(+&yen;<?php echo ((isset($subs["price"]) && ($subs["price"] !== ""))?($subs["price"]):0); ?>)</dd><?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?>
                            <?php if(is_array($attr["sub"])): $i = 0; $__LIST__ = $attr["sub"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subs): $mod = ($i % 2 );++$i;?><dd style="margin-bottom:5px" class="pro_checkbox_taocan <?php if(($key) == "0"): ?>active2<?php endif; ?>" onclick="selectcheckboxattr(this);" attrid="<?php echo ($subs["id"]); ?>" price="<?php echo ($subs["price"]); ?>"><?php echo ($subs["value"]); ?>(+&yen;<?php echo ($subs["price"]); ?>)</dd><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                    </dl><?php endforeach; endif; else: echo "" ;endif; ?> 
            </div>
            <?php if(($info['stock'] > 0) and ($info['issales'] == 1)): ?><dl class="count "><dt>数&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：</dt>
                    <dd class="coun-min">-</dd><dd class="count-num"> 
                        <input id="coun-num" class="goods_number"  maxlength="3" type="text" value="1" ></dd>
                    <dd class="coun-plu">+</dd>
<!--                    <?php if(($info['stock'] > $info['storage_alarm'])): ?>&nbsp;（有货）    
                        <?php else: ?>
                        &nbsp; <?php echo ($info['stock']); endif; ?>-->

                </dl>
                <div class="pay-add">
                    <a class="topay nowbuybutton" href="javascript:;;" >立即购买</a>
                    <a href="javascript:;;" class="add-car addcartbutton"><i class="bg"></i>加入购物车</a>
                </div>
                <?php else: ?>
                <!-- 已抢光 -->
                <div class="tips">
                    <img src="/Public/Web/img/sale_out.png" />
                </div><?php endif; ?>
            <form id='buynow' action="<?php echo U('Checkout/buynow');?>" method="post">
                <input type="hidden" name="id" id="goodid" value="<?php echo ($info["id"]); ?>"/> 
                <input type="hidden" name="price[]" id="inputprice" value="<?php echo ($info["price"]); ?>"/> 
                <input type="hidden" name="memberprice[]" id="inputmemberprice" value="<?php echo ($info["member_price"]); ?>"/> 
                <input type="hidden" name="marketprice[]" id="inputmarketprice" value="<?php echo ($info["marketprice"]); ?>"/> 
                <input type="hidden" name="sort[]"  value="<?php echo ($info["id"]); ?>"/>
                <input type="hidden" name="parameters[]" id="parametersid" /> 
                <input type="hidden" name="store_id" id="store_id" value="0" /> 
                <input type="hidden" name="proid" id="proid" value="0" /> 
                <input type="hidden" name="number" id="number" value="1" /> 
            </form>

        </div>

        <!--详情参数 end-->

        <?php if(!empty($history_list)): $count = count($history_list); ?>
            <?php if($count > 3): ?><div class="warp-fr">
                    <p><span>看了又看</span></p>
                    <div class="btn_box">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                <?php if(is_array($history_list)): $key = 0; $__LIST__ = $history_list;if( count($__LIST__)==0 ) : echo "没有数据" ;else: foreach($__LIST__ as $key=>$tj): $mod = ($key % 2 );++$key;?><div  id="btn<?php echo ($key); ?>" class="swiper-slide">
                                        <a href="<?php echo U('Goods/detail',array('channelname'=>$tj['channelname'],'id'=>$tj['id']));?>" title="">
                                            <img style="width:160px; height: 160px;" src="<?php echo ($tj['pics_img'][$tj['cover_id']]); ?>" alt="<?php echo (msubstr($tj["title"],0,27)); ?>" title="<?php echo (msubstr($tj["title"],0,27)); ?>"></a>
                                        <span>￥<?php echo ($tj["price"]); ?></span>
                                    </div><?php endforeach; endif; else: echo "没有数据" ;endif; ?>
                            </div>
                            <!-- Add Arrows -->
                        </div>
                        <div class="swiper_btn">
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="warp-fr klike" style="height:445px">
                    <p><span>看了又看</span></p>
                    <?php if(is_array($history_list)): $key = 0; $__LIST__ = $history_list;if( count($__LIST__)==0 ) : echo "没有数据" ;else: foreach($__LIST__ as $key=>$tj): $mod = ($key % 2 );++$key;?><div id="btn1" class="swiper-slide" style="height:103.3333px">
                            <a href="<?php echo U('Goods/detail',array('channelname'=>$tj['channelname'],'id'=>$tj['id']));?>">
                                <img src="<?php echo ($tj['pics_img'][$tj['cover_id']]); ?>"/></a><span>￥<?php echo ($tj["price"]); ?></span>
                        </div><?php endforeach; endif; else: echo "没有数据" ;endif; ?>
                    <!--                    <div id="btn1" class="swiper-slide" style="height:103.3333px">
                                            <a href="javascript:;"><img src="include/img/cp.png"/></a><span>￥109.00</span></div>-->
                </div><?php endif; endif; ?>
    </div>
</div>
<!--<div class="mask-black"></div>
<div class="add-goods">
    <span class="close fr"><input class="yd-colse" name="" value="X" type="button" ></span>
    <p class="f14 fb f3">您选购的商品已经添加到购物车</p>
    <p>
        <span class="fl"><a class="" href="#"><span class="go-shop">&lt;&lt; 继续购物</span></a></span>
        <span class="btncheck"><a href="#">立即结算</a></span>
    </p>
</div>-->
<!-- 详情列表 S -->
<!-- 详情内容 S -->
<div class="container towp  clear">
    <div class="left-bar fl">
        <?php if(!empty($hotgoodlist)): ?><div class="hot">
                <h2><i class="bg"></i>热销推荐</h2>
                <div class="hot-tj" >
                    <ul>
                        <?php if(is_array($hotgoodlist)): $i = 0; $__LIST__ = $hotgoodlist;if( count($__LIST__)==0 ) : echo "没有热销推荐" ;else: foreach($__LIST__ as $key=>$hotgood): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Goods/detail',array('channelname'=>$hotgood['channelname'],'id'=>$hotgood['id']));?>" title="<?php echo ($hotgood["title"]); ?>">
                                    <img src="<?php echo ($hotgood['pics_img'][$hotgood['cover_id']]); ?>" alt="<?php echo ($hotgood["title"]); ?>">
                                    <strong>
                                        <?php echo getsubstrutf8($hotgood['title'],0,15);?>
                                    </strong></a>
                                <span class="price">&yen;<?php echo ($hotgood["price"]); ?></span>
                            </li><?php endforeach; endif; else: echo "没有热销推荐" ;endif; ?>
                    </ul>
                </div>
            </div><?php endif; ?>
        <div class="rank">
            <h2><i class="bg"></i>销量排行</h2>
            <div class="rank-tj" >
                <ul>
                    <?php if(is_array($salesgoodlist)): $i = 0; $__LIST__ = $salesgoodlist;if( count($__LIST__)==0 ) : echo "没有销量排行" ;else: foreach($__LIST__ as $key=>$salesgood): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Goods/detail',array('channelname'=>$salesgood['channelname'],'id'=>$salesgood['id']));?>" title="<?php echo ($salesgood["title"]); ?>">
                                <img src="<?php echo ($salesgood['pics_img'][$salesgood['cover_id']]); ?>" alt="<?php echo ($salesgood["title"]); ?>">
                                <strong>
                                    <?php echo getsubstrutf8($salesgood['title'],0,15);?>
                                </strong></a>
                            <span class="price">&yen;<?php echo ($salesgood["price"]); ?></span>
                        </li><?php endforeach; endif; else: echo "没有销量排行" ;endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="de-content ">
        <div class="cont-tit" id="j_nav2">
            <a  class="active4" href="#spxq">商品详情</a>
            <a href="#pjxq">评价详情(<?php echo ($info["comment"]); ?>)</a>
            <a href="#jyjl">交易记录(<?php echo ($recordcount); ?>)</a>
            <a class="add-car addcartbutton">加入购物车</a></div>
        <div class="pro-intr"  id="spxq">
            <ul class="cp_clist">
                <?php if(is_array($contentgoodattr)): $i = 0; $__LIST__ = $contentgoodattr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attrs): $mod = ($i % 2 );++$i;?><li><span><?php echo ($attrs["name"]); ?>：</span><?php echo ($attrs["value"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
        <!--商品详情 S-->
        <div class="rcon_edit" >
            <div class="detail-img">
                <?php echo ($info["content"]); ?>
            </div>
        </div>
        <!--  评价详情S -->
        <div id="pjxq">
            <!--<div class="pro-intr clear ">-->
            <!--<div class="title clear fl" ><span class="tit">好评率</span><br /><span class="fon60 fl c5">100%</span></div>-->
            <!--<div class="fl   com">-->
            <!--<p>好评 <span class="c5">(80)</span><i class="good"><span></span></i></p>-->
            <!--<p>中评 <span class="c5">(20)</span><i class="soso"><span></span></i></p>-->
            <!--<p>差评 <span class="c5">(10)</span><i class="bad"><span></span></i></p>-->
            <!--</div>-->
            <!--</div>-->

            <!--  评价列表 S -->
            <div class="dh" id="aboutcommentlist">
                <?php echo ($aboutcommentlist); ?>
            </div>
            <!--  评价列表 E -->
            <!--交易记录 S-->
            <div id="jyjl" id="recordlist">
                <?php echo ($recordlist); ?>
            </div> 
            <!--交易记录 E-->
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

<script type="text/javascript" src="/Public/Web/js/zoom.1.0.1.js" ></script>
<script type="text/javascript" src="/Public/Web/js/swiper.jquery.min.js"></script>
<script>
var delcollect_url = '<?php echo U("Favortable/delcollect");?>';
$(function() {
    // 加入购物车
    $(".addcartbutton").click(function() {
        // 检查库存
        var stock = "<?php echo ($info["stock"]); ?>";
        var goods_number = parseInt($("#coun-num").val());
        if (goods_number > stock) {
            layer.msg("商品库存不足 ！");
            return false;
        }
        var parameters = $("#parametersid").val();//参数
        addcart('<?php echo ($info["id"]); ?>', goods_number, '<?php echo ($info["member_price"]); ?>', parameters, 0, 0, 0);
        return false;
    });

    // 立即购买
    $(".nowbuybutton").click(function() {
        var is_login = "<?php echo is_login();?>";
        if (is_login <= 0) {
            layer.msg('请先登录');
            location.href = "<?php echo U('Member/login');?>";
            return false;
        }
        $("input[name=number]").val(parseInt($("#coun-num").val()));
        $("#buynow").submit();
        return false;
    });
    
    // 增加商品数量
    $('.coun-plu').on('click', function() {
        var _temp_num = 0;
        if (isNaN(parseInt($('#coun-num').val()))) {
            _temp_num = 0;
        } else {
            _temp_num = parseInt($('#coun-num').val());
        }
        if (_temp_num + 1 > "<?php echo ($info["stock"]); ?>") {
            layer.msg('商品库存不足');
            return false;
        }
        $('#coun-num').val(_temp_num + 1);
    });
    // 减少商品数量
    $('.coun-min').on('click', function() {
        if ($('#coun-num').val() > 1) {
            $('#coun-num').val(parseInt($('#coun-num').val()) - 1);
        } else {
            $('#coun-num').val(1);
        }
    });
    resetproductprice();
});
</script>
<script>
var ajaxcomment = 0, ajaxaboutask = 0;
function selecradiotattr(obj) {
    $(obj).parent().find(".pro_taocan").removeClass("active2");
    $(obj).addClass('active2');
    picArr = [];
    var objo = $("div.four");
    for (var i = 0; i < objo.find("dd.active2").length; i++) {
        picArr.push(objo.find('.active2:eq(' + i + ')').attr('attrid'));
    }
    picStr = picArr.join(',');
    $("#parametersid").val(picStr);
    resetproductprice();
}

function resetproductprice() {
    var attrprice = 0;
    var objo = $("div.four");
    var pi = 0;
    picArr = [];
    for (var i = 0; i < objo.find("dd.active2").length; i++) {
        pi = objo.find('.active2:eq(' + i + ')').attr('price');
        picArr.push(objo.find('.active2:eq(' + i + ')').attr('attrid'));
        attrprice += parseFloat(pi);
    }
    picStr = picArr.join(',');
    $("#parametersid").val(picStr);
    var gprice = $("#inputprice").val();//价格
    var sprice = parseFloat(attrprice) + parseFloat(gprice);
    var sump = sprice.toFixed(2);
    $('#zzprice').html(sump);   // 销售价

    var memberpirce = parseFloat($("#inputmemberprice").val()) + parseFloat(attrprice);
    var marketprice = parseFloat($("#inputmarketprice").val()) + parseFloat(attrprice);
    $('#zzmemberprice').html(memberpirce.toFixed(2)); //会员价
    $('#zzmarketprice').html(marketprice.toFixed(2));// 市场价
}

function selectcheckboxattr(obj) {
    console.log(obj);
    if ($(obj).hasClass('active2')) {
        $(obj).removeClass('active2');
    } else {
        $(obj).addClass('active2');
    }
    picArr = [];
    var objt = $("div.four");
    for (var i = 0; i < objt.find("dd.active2").length; i++) {
        picArr.push(objt.find('.active2:eq(' + i + ')').attr('attrid'));
    }
    ;
    picStr = picArr.join(',');
    $("#parametersid").val(picStr);
    resetproductprice();
}
</script>

<script>
//        <!-- 看了又看 -->         
var mySwiper = new Swiper('.btn_box .swiper-container', {
    pagination: '.btn_box .swiper-pagination',
    nextButton: '.btn_box .swiper-button-next',
    prevButton: '.btn_box .swiper-button-prev',
    slidesPerView: 2,
    paginationClickable: true,
    spaceBetween: 10, direction: 'vertical', autoplay: 3000, loop: true,
});

//        <!-- 全部分类 -->
$(function() {
	$('.allClass').mouseenter(function() {
		$('.leftNaiv2').show();
	});
	$('.allClass').mouseleave(function() {
		$('.leftNaiv2').hide();
	});
});

//          停留在顶部
$(function() {
var nav = $('#j_nav2');
var navTop = nav.offset().top;
 $(window).scroll(function(e) {
	 if ($(this).scrollTop() >= navTop) {
	 		nav.addClass('m-nav-fix');
	   	nav.find('.add-car').show();
	 } else {
	 		nav.removeClass('m-nav-fix');
	    nav.find('.add-car').hide();
	 }
	 
	 $("#j_nav2 a").click(function(){
	     $(this).addClass("active4").siblings().removeClass("active4");
	 });
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