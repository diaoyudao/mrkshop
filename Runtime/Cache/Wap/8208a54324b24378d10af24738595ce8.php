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

<link rel="stylesheet" href="/mrkshop/Public/Wap/style/main.css">
<link rel="stylesheet" href="/mrkshop/Public/Wap/style/swiper.min.css">
<script type="text/javascript" src="/mrkshop/Public/Wap/js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="/mrkshop/Public/Wap/js/swiper.jquery.min.js"></script>

    <style>
        .all-naiv{display:none;}
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
        <h3>关于我们</h3>
        <!-- <div class="herd_r"><a href="search.html" class="search"><i></i></a></div>-->
    </div>
    <div class="t_line"></div>
    <!--头部 E--> 
    <!-- 内容 -->
    <div class="wrap" style="background-color: #fafafa;">
        <div class="content_right">
            <h2><?php echo ($article["title"]); ?></h2>
            <div class="r_guide">
                <?php echo ($article["content"]); ?>
            </div>
        </div>

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
        "IMG": "/mrkshop/Public/Wap/images", //项目公共目录地址
        "ROOT": "/mrkshop", //当前网站地址
        "JS": "/mrkshop/Public/Wap/js", //当前项目地址
        "APP": "/mrkshop/wap.php", //当前项目地址
        "PUBLIC": "/mrkshop/Public", //项目公共目录地址
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

    <script src="/mrkshop/Public/Wap/js/jquery-1.11.2.min.js"></script>
    <script src="/mrkshop/Public/Wap/js/ajaxpage.js"></script>
    <script src="/mrkshop/Public/static/layer/layer.js"></script>
    <script src="/mrkshop/Public/Wap/js/mrk.js" type="text/javascript" charset="utf-8"></script>


</body>
</html>