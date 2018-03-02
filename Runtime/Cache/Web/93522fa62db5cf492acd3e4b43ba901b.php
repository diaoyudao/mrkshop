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
        .all-naiv{display:none;}
        .m-nav-fix{position: fixed;top:0;width: 100%;background-color:#F5F5F5;}
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

    <div class="towp">
        <div class="cash">
            <i class="cash_ico"></i>
            <h2>收银台 </h2>
            <span><?php echo C('CONTACT');?></span>
        </div>
    </div>
    <div class="paySuc">
        <p class="p1">订单提交成功，请您及时付款，以便尽快为您发货!</p>
        <p class="p2"> 
            <span>订单号：<?php echo ($orderid); ?>
                (<a href="<?php echo U('order/details',array('orderid'=>$orderid));?>">订单详情</a>)
            </span>
            <span>收款方：<?php echo C('SITENAME');?></span>
        </p>
        <div class="de-price2">应付金额 : ￥    <span class="big-font"><?php echo ($order_total); ?></span>        元</div>
    </div>
    <form action='<?php echo U("Payment/pay");?>' id="payform" method="post"  name="myform"  class="payform" target="_blank">
        <input type="hidden" name="orderid" value="<?php echo ($orderid); ?>" />
        <input type="hidden" name="order_total" value="<?php echo ($order_total); ?>">

        <div class="chose-way towp bor"> 
            <h2>支付方式</h2>
            <div class="chose">
                <div class="way paymentlist">
                    <?php if(is_array($paymentlist)): $key = 0; $__LIST__ = $paymentlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$payment): $mod = ($key % 2 );++$key; if(($key) == "1"): ?><input type="hidden" name="paycode" value="<?php echo ($payment["paycode"]); ?>" />
                        <input type="hidden" name="payid" value="<?php echo ($payment["id"]); ?>">
                        <span title='<?php echo ($payment["payname"]); ?>' payname="<?php echo ($payment["payname"]); ?>" paycode='<?php echo ($payment["paycode"]); ?>' payid='<?php echo ($payment["id"]); ?>'  style="background: url('/Public/Web/img/payment/<?php echo ($payment["paycode"]); ?>.jpg') no-repeat scroll center center;" class="pay1 pay_way active" ><i class="bg"></i></span>
                        <?php else: ?>
                        <span title='<?php echo ($payment["payname"]); ?>' payname="<?php echo ($payment["payname"]); ?>" paycode='<?php echo ($payment["paycode"]); ?>' payid='<?php echo ($payment["id"]); ?>'  style="background: url('/Public/Web/img/payment/<?php echo ($payment["paycode"]); ?>.jpg') no-repeat scroll center center;" class="pay1 pay_way" ><i class="bg"></i></span><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <div class="way1" style="display:none;">
                    <h3>支付平台</h3>
                    <span class="pay1 ">支付宝接口</span>
                </div>
                <!-- 微信支付 -->
                <div class="payWecCon bor" style="display:none;">
                    <div class="pay-intr ">
                        <p>二维码将在<span class="dead-time">3分4秒</span>后过期，请尽快完成支付，
                            支付完成钱不要关闭该窗口</p>
                        <div class="code bor">
                            <img src="/Public/Web/img/code.png" />
                            <div> <span>请使用微信扫一扫扫描二维码支付</span></div>
                        </div>
                    </div>
                    <div>
                        <img src="/Public/Web/img/to-pay1.png">
                    </div>
                </div>
                <!-- 银联支付 -->
                <div class="payBank" style="display: none;">
                    <div >
                        <span class=" "><img src="/Public/Web/img/bank1.jpg" /> </span>
                        <span class=" active15"><img src="/Public/Web/img/bank2.jpg" /> </span>
                        <span class=" "><img src="/Public/Web/img/bank2.jpg" /> </span>
                        <span class=" "><img src="/Public/Web/img/bank1.jpg" /> </span>
                        <span class=" "><img src="/Public/Web/img/bank2.jpg" /> </span>
                        <span class=" "><img src="/Public/Web/img/bank1.jpg" /> </span>
                    </div>
                    <!-- 展开更多银行 -->
                    <div class="zanK "><em class="morBank active">展开更多银行<i class="bg"></i></em></div>
                </div>
            </div>
        </div>
    </form>
    <div class="towp to-pay">
        <input type="button" onclick="submitpay();" class="a-btn" value="立即去支付" />
        <a href="<?php echo U('order/index');?>">返回我的订单</a>
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
        $(function() {
            $(".pay_way").click(function() {
                $(".paymentlist").find(".pay_way").removeClass("active");
                $(this).addClass("active");
                $("input[name=payid]").val($(this).attr('payid'));
                $("input[name=paycode]").val($(this).attr('paycode'));
            });

        });

        function submitpay() {
            var codeid = $("input[name=payid]").val();
            var orderid = $("input[name=orderid]").val();
            if (codeid.length <= 0 || orderid.length <= 0) {
                layer.msg('请选择支付方式！');
                return false;
            }
            $("#payform").submit();
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