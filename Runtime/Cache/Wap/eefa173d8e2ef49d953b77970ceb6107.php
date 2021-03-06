<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
<title><?php if(!empty($meta_title)): echo ($meta_title); ?>-<?php echo C('SITENAME'); else: echo C('WEB_SITE_TITLE'); endif; ?></title>
<meta name="keywords" content="<?php if(empty($meta_keyword)): echo C('WEB_SITE_KEYWORD'); else: echo ($meta_keyword); endif; ?>"/>
<meta name="description" content="<?php if(empty($meta_keyword)): echo C('WEB_SITE_DESCRIPTION'); else: echo ($meta_description); endif; ?>">
<link href="favicon.ico" type="image/x-icon" rel="shortcut icon"/>

<link rel="stylesheet" href="/Public/Wap/style/main.css">
<link rel="stylesheet" href="/Public/Wap/style/swiper.min.css">
<script type="text/javascript" src="/Public/Wap/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="/Public/Wap/js/swiper.jquery.min.js"></script>

    <style>
        .buy_agin a{ float: right; }
        .buy_agin a.qx{
            background-color: #ccc;
            border-radius: 0.08rem;
            color: #fff !important;
            cursor: pointer;
            display: block;
            height: 0.8rem;
            line-height: 0.8rem;
            margin: 0 auto;
            margin-left: 0.2rem;
            text-align: center;
            width: 1.2rem;
            font-size: 0.24rem;
            height: 0.5rem;
            line-height: 0.5rem;
            padding: 0 0.2rem;
        }
        .buy_agin .pj{
            background-color: #e9a938;
            border-radius: 0.08rem;
            color: #fff !important;
            cursor: pointer;
            display: block;
            height: 0.8rem;
            line-height: 0.8rem;
            margin: 0 auto;
            margin-left: 0.2rem;
            text-align: center;
            width: 1.2rem;
            font-size: 0.24rem;
            height: 0.5rem;
            line-height: 0.5rem;
            padding: 0 0.2rem;
        }
    </style>
 
<script>
    $('html').css('fontSize', ($(window).width() > 640 ? 640 : $(window).width()) / 640 * 100);
    $(function() {
        $('html').css('fontSize', $('body').width() / 640 * 100);
    });
</script>
</head>
<body>
<!-- 头部 -->

<!-- /头部 -->
<!-- 主体 -->

    <!--头部 S-->
    <div class="herder">
        <a href="javascript:history.go(-1);" class="back"><i></i></a>
        <h3>订单管理</h3>
        <!-- <div class="herd_r"><a href="search.html" class="search"><i></i></a></div>-->
    </div>
    <div class="t_line"></div>
    <!--头部 E--> 
    <!--订单列表 S--> 
    <ul class="tab pinjia order_mana">
        <li <?php if(($state_type) == "all"): ?>class="active"<?php endif; ?>><a href="<?php echo U('order/index');?>">所有</a></li>
        <li <?php if(($state_type) == "state_new"): ?>class="active"<?php endif; ?>><a  href="<?php echo U('order/index',array('state_type'=>'state_new'));?>">待付款</a></li>
        <li <?php if(($state_type) == "state_send"): ?>class="active"<?php endif; ?>><a href="<?php echo U('order/index',array('state_type'=>'state_send'));?>">待收货</a></li>
        <li <?php if(($state_type) == "state_noeval"): ?>class="active"<?php endif; ?>><a href="<?php echo U('order/index',array('state_type'=>'state_noeval'));?>">待评价</a></li>
        <li <?php if(($state_type) == "state_success"): ?>class="active"<?php endif; ?>><a  href="<?php echo U('order/index',array('state_type'=>'state_success'));?>">已完成</a></li>
    </ul>
    <div id="conten_id">
        <?php if(!empty($orderlist)): if(is_array($orderlist)): $i = 0; $__LIST__ = $orderlist;if( count($__LIST__)==0 ) : echo "没有订单" ;else: foreach($__LIST__ as $key=>$order): $mod = ($i % 2 );++$i;?><div class="order_list">
                    <div class="two_clum">
                        <span class="fl"><i>订单号:</i><a href="<?php echo ($order["detail_url"]); ?>"><?php echo ($order["orderid"]); ?></a></span>
                        <span class="fr"><?php echo ($order["orderStatus"]["status_txt"]); ?></span> 
                    </div>
                    <ul>
                        <?php if(is_array($order["goodslist"])): $k = 0; $__LIST__ = $order["goodslist"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($k % 2 );++$k;?><li>
                                <a href="<?php echo ($order["detail_url"]); ?>">
                                    <img src="<?php echo ($goods['pics_img'][$goods['cover_id']]); ?>"/>
                                    <div class="pro_info">
                                        <h3 style="height: auto;white-space: normal;"><a href="<?php echo U('Goods/detail',array('channelname'=>$goods['channelname'],'id'=>$goods['goodid'] ));?>"><?php echo (get_good_name($goods["goodid"])); ?></a></h3>
                                        <p class="pric">
                                            <span>￥<span class="font36"><?php echo ($goods["price"]); ?></span>
                                            </span>
                                            <i> X <?php echo ($goods["num"]); ?> </i>
                                        <?php if($order["status"] == 3 ): ?><!--and $goods.iscomment eq 0-->
                                            <?php $completetime =$order['complete_time'] ; $refundtime =(C("ORDER_AUTO_REFUND") ? : 1 ) * 24*60*60; if($completetime+$refundtime > time()){ ?>
                                            <?php if($goods["refund_id"] > 0): ?><a class='sc' href="<?php echo U('Refund/detail',array('id'=>$goods['refund_id']));?>">查看售后</a>
                                            <?php else: ?>
                                            <a class='sc' href="<?php echo U('Refund/apply_refund',array('orderid'=>$order['id'],'goods_id'=>$goods['goodid'],'id'=>$goods['id']));?>">申请售后</a><?php endif; ?>
                                            <?php } endif; ?>
                                        </p>
                                    </div>
                                </a>
                            </li><?php endforeach; endif; else: echo "没有订单" ;endif; ?>
                    </ul>
                    <div class="two_clum">
                        <span class="fl"><i>时间：</i><?php echo (date("Y-m-d H:i:s",$order["create_time"])); ?></span>
                        <span class="fr">总额：<span class="cost">￥<?php echo ($order["pricetotal"]); ?></i></span> 
                    </div>
                    <?php if(!empty($order["handle"])): ?><div class="buy_agin">
                            <?php echo ($order["handle"]); ?>
                        </div><?php endif; ?>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php else: ?>
            <div class="no_data">
                <h3>
                    <i class=""> <img src="/Public/Wap/images/ts.png"/> </i> 没有订单记录哦
                </h3>						        	
            </div><?php endif; ?>
    </div>
    <div id="loding_ajax" style=" text-align:center;height:.88rem;line-height:.88rem;font-size: .44rem;font-weight: bold;color: #000; display: none;">加载中····</div>
</div>

<!--订单列表 E--> 

<!-- /主体 -->
<!-- 底部 -->
<!--底部 S-->
<div class="footer">
  <ul>
    <li <?php if((CONTROLLER_NAME) == "Index"): ?>class="nav_hover"<?php endif; ?>><a href="<?php echo U('Index/index');?>"><i class="footer_nav_a"></i>
      <p>首页</p>
      </a></li>
    <li <?php if((CONTROLLER_NAME) == "Category"): ?>class="nav_hover"<?php endif; ?> ><a href="<?php echo U('Category/index');?>"><i class="footer_nav_b"></i>
      <p>全部分类</p>
      </a></li>
    <li <?php if((CONTROLLER_NAME) == "Cart"): ?>class="nav_hover"<?php endif; ?> ><a href="<?php echo U('Cart/index');?>"><i class="footer_nav_c"></i>
      <p>购物车</p>
      <em class="cart_sum"><?php echo ($cart_list["goods_count"]); ?></em> </a></li>
    <li <?php if((CONTROLLER_NAME) == "Member"): ?>class="nav_hover"<?php endif; ?> ><a href="<?php echo U('Member/index');?>"><i class="footer_nav_d"></i>
      <p>个人中心</p>
      </a></li>
  </ul>
</div>
<div class="b_line"></div>
<!--底部 E-->
<script type="text/javascript">
    var ThinkPHP = window.Think = {
        "MID": "<?php echo ($guid); ?>",
        "UID": "<?php echo ($uid); ?>",
        "IMG": "/Public/Wap/images", //项目公共目录地址
        "ROOT": "", //当前网站地址
        "JS": "/Public/Wap/js", //当前项目地址
        "APP": "/wap.php", //当前项目地址
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
    var _CART_URL = "<?php echo U('Cart/addItem');?>";
    var _FAVORTABLE_URL = "<?php echo U('Favortable/addfavortable');?>";
    var LOGIN_URL = "<?php echo U('member/login');?>";
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
            website = website + url[1] + '/Goods/lists/keywords/' + keywords + '.html';
            window.location.href = website;
        }
        return false;
    }
</script>
<?php echo (wap_wx_share($share_arr)); ?>
<!-- /底部 -->

    <script src="/Public/Wap/js/jquery-1.11.2.min.js"></script>
    <script src="/Public/Wap/js/ajaxpage.js"></script>
    <script src="/Public/static/layer/layer.js"></script>
    <script src="/Public/Wap/js/mrk.js" type="text/javascript" charset="utf-8"></script>

    <script src="/Public/Wap/js/ajaxpage.js" ></script>
    <script>
        $(function() {
            AjaxPage.pageCount = "<?php echo ((isset($totalPages) && ($totalPages !== ""))?($totalPages):0); ?>";
            AjaxPage.append = 1;
            AjaxPage.method = 'GET';
            AjaxPage.dataType = "html";
            AjaxPage.Conten = $('#conten_id');
            AjaxPage.alert = $('#loding_ajax');
            AjaxPage.filter.ajax = 1;
            AjaxPage.filter.state_type = "<?php echo ($state_type); ?>";
            $(window).scroll(function() {
                if ($(window).height() + 200 + $(window).scrollTop() > $('body').outerHeight() && AjaxPage.canpage) {
                    AjaxPage.canpage = false;
                    AjaxPage.gotoPageNext();
                }
            });
        });
    </script>
    <script>

        $(function() {
            $("a.confirm").click(function() {
                var msg = $(this).attr("data-msg");
                var href = $(this).attr('href');
                layer.confirm(msg, {icon: 3, title: '提示信息'}, function(index) {
                    if (index) {
                        layer.close(index);
                        window.location.href = href;
                        return true;
                    } else {
                        return false;
                    }
                });
                return false;
            });
        });

    </script>


</body>
</html>