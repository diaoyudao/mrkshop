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
        body{
            background-color: #fff;
        }
        .m-nav-fix{position: fixed;top:0;width: 100%;background-color:#F5F5F5;}

        .peis{
            margin: 40px 0;
        }
        .peis div{
            border: none;
        }
        .buyer .buyer_box{
            height: auto;
            padding: 0;
            border: none;
        }
        .buyer_box input{
            width: 218px; 
            height: 28px;
            padding: 0 5px;
            line-height: 28px;
            border: 1px solid #dcdcdc;
            font-size: 12px;       
            font-family: '宋体';
            color: #333;
        }
        .buyer_box div{
            margin: 10px 0;
            border: none;
            padding-left: 20px;
        }
        .buyer_box label{
            font-family: '宋体';
            font-size: 12px;
            color: #1d1d1d;
        }
        .peis h2{
            background-color: transparent;
        }
        .step{
            background-position: center -104px; 
        }
        .btns input{
            height: 30px;
            line-height: 30px;
            width: 90px;
            border-radius: 5px;
            color: #fff;
            font-family: '宋体';
            font-size: 12px;
            background-color: #f16f71;
            border: none;
            margin-left: 60px;
        }
        .peis .sect_box{
            display: inline-block;
            *display:inline;
            *zoom:1;
            position: relative;
            padding: 0;
            margin-left: 40px;
        }
        .sect_box div{
            height: 28px;
            width: 130px;
            border: 1px solid #ccc;
            line-height: 28px;
            padding: 0 10px;
            font-size: 12px;
            color: #666;
            background: url(/Public/Web/img/bottoms.png) no-repeat 120px center;
        }
        .sect_box ul{
            position: absolute;
            top: 30px;
            left: 0;
            display: none;
            background-color: #f0f0f0;
        }
        .sect_box ul li{
            border: 1px solid #ccc;
            height: 28px;
            line-height: 28px;
            width: 130px;
            padding: 0 10px;
            border-top: none;
            font-size: 12px;
            color: #666;
        }
        .sect_box ul .active{
            background-color: #fff;
        }
        .peis p{
            margin-bottom: 10px;
        }
        .mes .count .xx_ts{
            color: #863177;
            padding-left: 20px;
            background: url(/Public/Web/img/xx_ts.png) no-repeat 0 center;
            float: right;
        }
        .all-naiv{display:none;}
        .m-nav-fix{position: fixed;top:0;width: 100%;background-color:#F5F5F5;}
        .input-disabled{ cursor: not-allowed;background: #ccc;}
        .position {display: none;}
    </style>
 
</head>
<body>
    <!-- 头部 -->
<!-- 头部 -->
<header style="background-color: #fff;">
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
    <div class="head clear towp">
        <?php $logo = C('SITE_LOGO') ?>
        <style>

            .head .logos {
                background: rgba(0, 0, 0, 0) url("/Uploads/Picture/logo/<?php echo (get_cover($logo,'path')); ?>") no-repeat scroll center center;
            }
        </style>
        <a class="logos" href="<?php echo U('Index/index');?>"></a>
        <a class="logins" href="javascript:;;">购物车</a>
    </div>
</header>

<!-- /头部 -->
<!-- 主体 -->

    <!-- 位置 -->
    <div class="towp  position">
        您的位置：<span><a href="#">商品分类</a></span><span class="fonS"> > </span><span class="colo1">商品详情</span>
    </div>
    <!-- 内容 -->
    <div class="towp step2"></div>
    <div class="towp fillOrder" >
        <form action='<?php echo U("Checkout/createorder");?>' method="post" name="createorderform" id="createorderform" onsubmit=" return checkaddress();"> 
            <div class="add" id="myaddresslist">
                我的地址列表
            </div>

            <div class="peis" id="shippinglist">
                <!--配送方式列表-->
            </div>
            <div class=" quRen">
                <h2>订单信息 <span class="fr back-shopcar"><a href="<?php echo U('cart/index');?>"><i class="bg"></i>返回购物车修改</a></span> </h2>
                <br>
                <style>
                   .back-shopcar span{margin-right: 20px;} 
                </style>
                <?php if(is_array($ordergoodslist)): $i = 0; $__LIST__ = $ordergoodslist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$shoplist): $mod = ($i % 2 );++$i;?><h2><?php echo ($distribution[$key]); ?>包裹 
                        <span class="fr back-shopcar" >
                            <span>重量:<?php echo ($new_shipping[$key]['weight']); ?>千克</span>
                            <span>合计:￥<?php echo ($new_shipping[$key]['total']); ?></span>
                            <span>运费:￥<span id='shipping_fee_ware_<?php echo ($key); ?>'><?php echo ($new_shipping[$key]['shipping_fee']); ?></span></span>
                        </span></h2> 
                    <table width="100%" border="0" cellpadding="0" style="border-collapse:collapse;">
                    <thead>
                        <th colspan="2" style="text-align: center;padding-left: 20px;">商品名称</th>
                        <th width="140">单价</th><th width="140">数量 </th><th width="140">小计 </th>
                    </thead>
                    <tbody>
                        <?php if(is_array($shoplist)): $i = 0; $__LIST__ = $shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['type'] == 0): ?><tr>
                                        <td width="80px"><img style="width:80px;height: 80px;" src="<?php echo (get_cover_picture_url($vo["goodid"])); ?>" /> </td>
                                        <td width="440" class="pro-intr4">
                                            <a href="<?php echo U('Goods/detail',array('channelname'=>$vo['channelname'],'id'=>$vo['goodid']));?>" target="_blank" ><?php echo (get_good_name($vo["goodid"])); ?> </a>
                                            <div class="attr"><span><?php echo ($vo["parameters"]); ?></span>
                                                <span style="color:red;"><?php echo ($vo["promsg"]); ?></span>
                                            </div>
                                        </td>
                                        <td>¥<?php echo ($vo["price"]); ?></td> 
                                        <td> <?php echo ($vo["num"]); ?> </td>
                                        <td class="red">¥<span id="total<?php echo ($vo["sort"]); ?>"><?php echo ($vo["total"]); ?></span></td> 
                                    </tr>
                                    <?php elseif($vo['type'] == 1): ?>
                                    <tr>
                                        <td width="80px"><img src="<?php echo (get_cover_picture_url($vo["goodid"])); ?>" /> </td>
                                        <td width="440" class="pro-intr4">
                                            <a href="<?php echo U('Goods/detail',array('channelname'=>$vo['channelname'],'id'=>$vo['goodid']));?>" target="_blank" ><?php echo (get_good_name($vo["goodid"])); ?></a>
                                            <div class="attr"><span><?php echo ($vo["parameters"]); ?></span>
                                                <span style="color:red"><?php echo ($vo["promsg"]); ?></span>
                                            </div>
                                        </td>
                                        <td>¥<?php echo ($vo["price"]); ?></td> 
                                        <td> <?php echo ($vo["num"]); ?> </td>
                                        <td class="red">¥<span id="total<?php echo ($vo["sort"]); ?>"><?php echo ($vo["total"]); ?></span></td> 
                                    </tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                    </table>
                    <hr><?php endforeach; endif; else: echo "" ;endif; ?>


                
            </div>
    </div>
    <div class="towp mes">
        <p>给商家留言：<input type="text" name='order_message' class="txt1 txt2" /></p>
        <div class="count">
            <p>商品总重量：<span><i id="goods_weight"><?php echo ($goodsTotalWeight); ?></i>千克</span>   </p>
            <p> <?php echo ($sum); ?>件商品，商品总金额：<span>￥<i id="allproduct"><?php echo ($total); ?></i> </span></p>
            <?php if($orderType == 2 or $orderType == 3): ?><p>税费：<span>￥<i id="haiguan_rate_total"><?php echo ($haiguan_rate_total); ?></i></span>     </p><?php endif; ?>

            <p>运费：<span>￥<i id="pricedis"><?php echo ($shipping_fee); ?></i> </span>    </p>        

            <div class='con'><p> 应付总额：<span><i>￥<b id="allprice"><?php echo ($all); ?></b></i></span>   </p>    </div>    
            <!--<p class=""> 应付总额（不含运费）：￥<span id="allprice"><?php echo ($all); ?></span></p>--> 
        </div>
        <div class="fff" >
            <!--<span>应付总额：<strong id="allprice">￥<?php echo ($all); ?></strong></span>-->
            <input type="hidden" name="senderid" id="senderid" >
            <input type="hidden" name="tag" value="<?php echo ($tag); ?>"> 
            <input type="hidden" name="orderType" value="<?php echo ($orderType); ?>"> 
            <input type="hidden" name="iscart" value="<?php echo ($iscart); ?>"> 
            <input type="hidden" name="shipping_fee" value="<?php echo ($shipping_fee); ?>"> 
            <input type="submit" value="提交订单" />
        </div>
    </div>
</form>

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
        var address_layer = '';
        //地址编辑
        function open_address(address_id) {
            $.ajax({
                url: "<?php echo U('Checkout/ajaxaddress');?>",
                type: "post",
                dataType: "json",
                data: {id: address_id},
                success: function(json) {
                    address_layer = layer.open({
                        title: '编辑地址',
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['660px', '440px'], //宽高
                        content: json.info
                    });
                    return false;
                }
            });
        }

        /**
         * 异步获取配送方式
         * @returns {undefined}
         */
        function shippinglist(address_id) {
            var orderType = "<?php echo ($orderType); ?>";
            $.ajax({
                url: "<?php echo U('Checkout/ajaxshipping');?>",
                type: "post",
                dataType: "json",
                data: {address_id: address_id, orderType: orderType},
                success: function(json) {
                    if (json.status) {
//                        $("#shippinglist").html(json.info);
                        $('#pricedis').text(json.money);   // 运费
                        $("input[name=shipping_fee]").val(json.money);

                        if (json.disable == false) {
                            $("input[type=submit]").attr('disabled', 'disabled').css({'background': '#ccc', 'cursor': 'not-allowed'});
                        } else {
                            $("input[type=submit]").removeAttr("disabled").css({'background': '', 'cursor': 'pointer'});
                        }
                        clacOrderFee(); // 计算订单金额
                        return false;
                    }

                    layer.msg('加载地址错误');
                    return false;
                }
            });
        }
        
        
        function getshippingfee(address_id){
             $.ajax({
                url: "<?php echo U('Checkout/ajaxshippingfee');?>",
                type: "post",
                dataType: "json",
                data: {address_id: address_id},
                success: function(json) {
                    if (json.status) {
//                        $("#shippinglist").html(json.info);
                        $.each(json.fee,function(index, item)
                        { 
                            // alert( "the man's no. is: " + index + ",and " + content.name + " is learning " + content.lang ); 
//                            console.log(item.shipping_fee);
                            $("#shipping_fee_ware_"+index).html(item.shipping_fee);
                        });

                        $("input[name=shipping_fee]").val(json.money);
                        $('#pricedis').text(json.money);   // 运费
                        if (json.disable == false) {
                            $("input[type=submit]").attr('disabled', 'disabled').css({'background': '#ccc', 'cursor': 'not-allowed'});
                        } else {
                            $("input[type=submit]").removeAttr("disabled").css({'background': '', 'cursor': 'pointer'});
                        }
                        clacOrderFee(); // 计算订单金额
                        return false;
                    }

                    layer.msg('加载地址错误');
                    return false;
                }
            });
        }

        /**
         * 获取我的地址列表
         * @returns {undefined}
         */
        function myaddresslist() {
            $.ajax({
                url: "<?php echo U('Checkout/ajaxaddresslist');?>",
                type: "post",
                dataType: "json",
//                data: {id: address_id},
                success: function(json) {
                    if (json.status) {
                        $("#myaddresslist").html(json.info);
                        return false;
                    }
                    layer.msg('加载地址错误');
                    return false;
                }
            });
        }
        function delAddress(aid) {
            //判断新地址是否选中,获取地址id
            var con_index = layer.confirm('确定要删除该地址吗？', {icon: 3, title: '提示'}, function(index) {
                if (index) {
                    if (aid == null) {
                          alert("选择地址无效!");
                        return false;
                    } else {
                        $.ajax({
                            type: 'post', //传送的方式,get/post
                            url: '<?php echo U("Member/deleteAddress");?>', //发送数据的地址
                            data: {id: aid},
                            dataType: "json",
                            success: function(data)
                            {
                                layer.msg(data.msg);
                                myaddresslist();
                                // shippinglist(0);
                                getshippingfee(0);
                            },
                            error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                                alert(XMLHttpRequest + thrownError);
                            }
                        });      
                     }
                    layer.close(con_index);
                }
            });

        }


        $(function() {
            myaddresslist();    // 加载我的地址列表
//            shippinglist(0);     // 加载配送方式
            getshippingfee(0);
            /*重新计算运费及总金额*/
            $("#shippinglist").delegate(".radiodistribution", "click", function() {
                var yunfei = parseFloat($(this).attr('money'));
//                yunfei =  isNaN(yunfei) ? yunfei : 0;
                var yfsump = yunfei.toFixed(2);
                $('#pricedis').text(yfsump);   // 运费
                clacOrderFee();
            });

        });

        /**
         * 计算订单价格
         * @returns {undefined}
         */
        function clacOrderFee() {

            var yunfei = parseFloat($('#pricedis').text());  // 运费
            yunfei = yunfei.toFixed(2);
            var allproduct = parseFloat($('#allproduct').text());  // 商品价格
            var haiguan_rate_total = parseFloat($('#haiguan_rate_total').text());  // 海关费
            if (isNaN(haiguan_rate_total)) {
                haiguan_rate_total = 0;
            }
            var allprice = parseFloat(eval(allproduct) + eval(yunfei) + eval(haiguan_rate_total));       // 订单总价
            var sump = allprice.toFixed(2);

            $('#allprice').text(sump);
        }
//        选择地址
        function  chooseaddress(obj) {
            var address_id = $(obj).attr("addressid");
//            shippinglist(address_id);
            getshippingfee(address_id);
            $("#senderid").val(address_id);
            $(obj).parents('li').siblings().addClass('active6').removeClass('active6')
            $(obj).parents('li').addClass('active6');
//            $(obj).parents('li').toggleClass('active6');
        }

        /*
         *检查地址是否选择 
         */
        function checkaddress() {
            //判断默认地址是否选中,获取地址id
            var addressid = $(".active6").find("a.setDef").attr('addressid');
            $("#senderid").val(addressid);
//            alert(addressid);
            if (typeof (addressid) == "undefined") {
                document.getElementById('myaddresslist').scrollIntoView();
                layer.msg("请选择一个收货地址");
                return false;
            } else {
                return true;
            }
            return false;
        }

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