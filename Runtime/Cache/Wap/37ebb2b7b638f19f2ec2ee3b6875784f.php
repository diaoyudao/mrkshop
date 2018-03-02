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
        <!--<a href="javascript:history.go(-1);" class="back"><i></i></a>-->
        <h3>个人中心</h3>
    </div>
    <div class="t_line"></div>
    <!--头部 E--> 
    <!--基本信息 S--> 
    <div class="base_info clear">
        <a class="edit_info" href="<?php echo U('Member/information');?>">
            <div class="head_pro">
              <?php if(empty($faceid)): ?><img src="<?php echo ($head_url); ?>" />
                    <?php else: ?>
                    <?php $random = time(); ?>
                    <img src="/Uploads/Face/<?php echo ($uid); ?>/face.jpg?r=<?php echo ($random); ?>"  /><?php endif; ?>
            </div>
            <div class="name">
                <h2 class="myName"><?php echo ($memberInfo["username"]); ?></h2>
                <p class="gread"><?php echo ($memberInfo["levelInfo"]["level_n"]); ?><i class="lv<?php echo ($memberInfo["levelInfo"]["level"]); ?>"></i></p>
            </div>		
        </a>
    </div>
    <div class="nav_list nav_list2" style="display: none;">
        <ul style="margin-bottom:0">
            <li>
                <a href="<?php echo U('Order/index');?>">
                    <i class="nav_l_ico nav_l_ico10">
                        <img src="/Public/Wap/images/allOrder.png" />
                    </i>全部订单
                    <span style="float:right;margin-right:0.2rem;font-size: 0.18rem;">查看全部订单</span><i class="Get"></i>
                </a>
            </li> 
        </ul>
    </div>
    <!--基本信息 E--> 

    <!--会员导航 S--> 
    <div class="user_nav2">
        <ul class="clear">
            <li>
                <a href="<?php echo U('order/index',array('state_type'=>'state_new'));?>">
                    <span class="ico ico1"></span>
                    <strong>待付款</strong>
                    <i class="pub_tips"><?php echo ($ordernum["nopaynum"]); ?></i>
                </a>	
            </li>
            <li>
                <a href="<?php echo U('order/index',array('state_type'=>'state_pay'));?>">
                    <span class="ico ico2"></span>
                    <strong>待发货</strong>
                    <i class="pub_tips"><?php echo ($ordernum["paynum"]); ?></i>
                </a>	
            </li>
            <li>
                <a href="<?php echo U('order/index',array('state_type'=>'state_send'));?>">
                    <span class="ico ico3"></span>
                    <strong>待收货</strong>
                    <i class="pub_tips"><?php echo ($ordernum["shipnum"]); ?></i>
                </a>	
            </li>
            <li>
                <a href="<?php echo U('order/index',array('state_type'=>'state_noeval'));?>">
                    <span class="ico ico4"></span>
                    <strong>待评价</strong>
                    <i class="pub_tips"><?php echo ($ordernum["noevalnum"]); ?></i>
                </a>	
            </li>
            <li>
                <a href="<?php echo U('Refund/index',array('all'=>1));?>">
                    <span class="ico ico5"></span>
                    <strong>售后</strong>
                    <i class="pub_tips">.</i>
                </a>	
            </li>
        </ul>
    </div>
    <!--会员导航 E--> 

    <!--导航列表 S--> 
    <div class="nav_list nav_list2">
        <ul>
            <li>
                <a href="<?php echo U('Order/index');?>">
                    <i class="nav_l_ico nav_l_ico10">
                        <img src="/Public/Wap/images/allOrder.png" />
                    </i>全部订单
                    <i class="Get"></i>
                </a>
            </li> 
            <li>
                <a href="<?php echo U('Member/mycollection',array('all'=>1));?>">
                    <i class="nav_l_ico nav_l_ico1">
                        <img src="/Public/Wap/images/heart.png" />
                    </i>我的收藏
                    <i class="Get"></i>
                </a>
            </li> 	
            <li>
                <a href="<?php echo U('Comment/index');?>">
                    <i class="nav_l_ico nav_l_ico2">
                        <img src="/Public/Wap/images/pingjia.png"/>
                    </i>我的评价
                    <i class="Get"></i>
                </a>
            </li> 
            <li style='display: none;'>
                <a href="toMycomment.html">
                    <i class="nav_l_ico nav_l_ico9">
                        <img src="/Public/Wap/images/juan.png"/>
                    </i>我的优惠券
                    <i class="Get"></i>
                </a>
            </li> 	
            <li>
                <a href="<?php echo U('Refund/index',array('all'=>1));?>">
                    <i class="nav_l_ico nav_l_ico9">
                        <img src="/Public/Wap/images/shouhou.png"/>
                    </i>售后管理
                    <i class="Get"></i>
                </a>
            </li> 	
            <li>
                <a href="<?php echo U('Message/index',array('all'=>1));?>">
                    <i class="nav_l_ico nav_l_ico9">
                        <img src="/Public/Wap/images/tousu.png"/>
                    </i>建议投诉
                    <i class="Get"></i>
                </a>
            </li> 	
        </ul>
        <ul>
            <li>
                <a href="<?php echo U('Safety/safety');?>">
                    <i class="nav_l_ico nav_l_ico3">
                        <img src="/Public/Wap/images/safe.png" />
                    </i>安全中心
                    <i class="Get"></i>
                </a>
            </li> 	
            <li>
                <a href="<?php echo U('Member/address');?>">
                    <i class="nav_l_ico nav_l_ico4">
                        <img src="/Public/Wap/images/add.png"/>
                    </i>地址管理
                    <i class="Get"></i>
                </a>
            </li> 	
        </ul>
        <ul class="none">
            <li>
                <a href="daili.html">
                    <i class="nav_l_ico nav_l_ico5">
                        <img src="/Public/Wap/images/sing-user.png" />
                    </i>代理管理
                    <i class="Get"></i>
                </a>
            </li> 	
            <li>
                <a href="kuaiJian.html">
                    <i class="nav_l_ico nav_l_ico6">
                        <img src="/Public/Wap/images/kuaijian.png"/>
                    </i>快件管理
                    <i class="Get"></i>
                </a>
            </li>
            <li>
                <a href="code.html">
                    <i class="nav_l_ico nav_l_ico7">
                        <img src="/Public/Wap/images/classfy.png" />
                    </i>邀请码/二位码维护
                    <i class="Get"></i>
                </a>
            </li>
        </ul>
        <ul> 		 	
            <li>
                <a href="<?php echo U('Help/index');?>">
                    <i class="nav_l_ico nav_l_ico8">
                        <img src="/Public/Wap/images/user.png"/>
                    </i>关于我们
                    <i class="Get"></i>
                </a>
            </li> 	
        </ul>
    </div>
    <!--导航列表 E--> 

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


</body>
</html>