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
        .newtj_nav .img img{
            width: 3.0rem;
            height: 3.0rem;
        }
        .recommend_nav .img img{
            width: 3.03rem;
            height: 3.03rem;
        }
        .xsqg_nav .img img{
            height: auto;
        }
        .product_nav .img img{
            height: auto;
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
    <div class="index_ss"> <a href="<?php echo U('Goods/search');?>">搜索您喜欢的商品</a> </div>
    <!--首页幻灯 S-->
<div class="swiper-container sy_hdps" >
  <div class="swiper-wrapper sy_hdp">
    <?php echo hook('Advs',array('mark'=>'wap_index_A1','domainid'=>'','domaintype'=>'site','show_page'=>'banner'));?>
  </div>
  <!-- Add Pagination -->
  <div class="swiper-pagination"></div>
  <div class="banner_zz"></div>
</div>
<!--首页幻灯 E--> 
     
    <!--栏目分类 S-->
    <div class="column_nav">
        <ul>
            <?php if(is_array($menulist)): $i = 0; $__LIST__ = $menulist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li> <a href="<?php echo ($menu["url"]); ?>" title="<?php echo ($menu["icon"]); ?>"> 
                        <img src="/Uploads/Picture//<?php echo ($menu["domainid"]); ?>/<?php echo (get_cover($menu["icon"],'path')); ?>" alt="<?php echo ($menu["icon"]); ?>">
                        <h3><?php echo ($menu["title"]); ?></h3>
                    </a> 
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php if(session('memberinfo.member_type') == 2 || session('memberinfo.member_type') == 1): ?><li> <a href="<?php echo U('Goods/lists',array('type'=>1));?>"> <img src="/Public/Wap/images/nav_6.png" alt="日常商品">
                        <h3>日常商品</h3>
                    </a>
                </li><?php endif; ?>
            <!-- <?php if(session('memberinfo.member_type') == 3 && session('memberinfo.member_agent_id') != 0): ?><li> <a href="<?php echo U('Goods/storegoods',array('store_id'=>session('memberinfo.member_agent_id')));?>"> 
                        <img src="/Public/Wap/images/nav_7.png">
                        <h3>店铺精选</h3>
                    </a> 
                </li><?php endif; ?> -->
            <li> <a href="<?php echo U('Category/index');?>"> <img src="/Public/Wap/images/nav_8.png">
                    <h3>全部分类</h3>
                </a>
            </li>
        </ul>
    </div>
    <!--栏目分类 E-->
    <div class="xsqg_nav" >
        <?php echo hook('Advs',array('mark'=>'wap_index_A3','domainid'=>'','domaintype'=>'site'));?>
        <!--            <li>
                        <div class="img"> <img src="/Public/Wap/images/xsqg_banner.png"> </div>
                        <div class="xsqg_time">
                            <em>剩余<span>0</span><span>0</span>时<span>0</span><span>0</span>分<span>0</span><span>0</span>秒结束</em>
                        </div>
                    </li>-->
    </div>
    <!--最热推荐 S-->
    <div class="nav_nr hot_recommend">
        <div class="h3_bt">
            <a href="#">
                <h3><span>最热推荐</span></h3>
                <p>为您选择第一性价比的商品</p>
            </a>
        </div>
        <div class="recommend_nav">
            <ul>
                <?php if(is_array($hotgoodslist)): $i = 0; $__LIST__ = $hotgoodslist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$hotgood): $mod = ($i % 2 );++$i;?><li>
                        <a href="<?php echo U('Goods/detail',array('channelname'=>$hotgood['channelname'],'id'=>$hotgood['id']));?>" title="<?php echo ($hotgood["title"]); ?>">
                            <div class="img">
                                <img src="<?php echo ($hotgood['pics_img'][$hotgood['cover_id']]); ?>" alt="<?php echo ($hotgood["title"]); ?>"/>
                            </div>
                            <div class="price"><b>&yen;<em><?php echo ($hotgood["show_price"]); ?></em></b><strike>&yen;<?php echo ($hotgood["marketprice"]); ?></strike></div>
                            <h3><?php echo getsubstrutf8($hotgood['title'],0,30);?></h3>

                        </a>
                        <a href="<?php echo U('Goods/detail',array('channelname'=>$hotgood['channelname'],'id'=>$hotgood['id']));?>" class="btn_btn">立即购买</a>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <!--最热推荐 E-->

    <!--每日新品 S-->
    <div class="nav_nr new_product">
        <div class="h3_bt">
            <a href="#">
                <h3><span>每日新品</span></h3>
                <p>我们每日为您推荐最好的</p>
            </a>
        </div>
        <div class="product_nav">
            <?php echo hook('Advs',array('mark'=>'wap_index_A2','domainid'=>'','domaintype'=>'site'));?>
            <!--            <ul>    
                            <li>
                                <a href="#">
                                    <div class="img">
                                        <img src="/Public/Wap/images/banner_new1.jpg">
                                    </div>       
                                </a>
                            </li>
                        </ul>-->
        </div>
    </div>
    <!--每日新品 E-->

    <!--最新推荐 S-->
    <div class="nav_nr new_recommend">
        <div class="h3_bt">
            <a href="#">
                <h3><span>最新推荐</span></h3>
                <p>为您选择第一性价比的商品</p>
            </a>
        </div>
        <div class="newtj_nav">
            <ul>  
                <?php if(is_array($newgoodslist)): $i = 0; $__LIST__ = $newgoodslist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$newgood): $mod = ($i % 2 );++$i;?><li>
                        <a href="<?php echo U('Goods/detail',array('channelname'=>$newgood['channelname'],'id'=>$newgood['id']));?>" title="<?php echo ($newgood["title"]); ?>">
                            <div class="img">
                                <img src="<?php echo ($newgood['pics_img'][$newgood['cover_id']]); ?>" alt="<?php echo ($newgood["title"]); ?>"/>
                            </div>
                            <h3><?php echo getsubstrutf8($newgood['title'],0,30);?></h3>
                            <div class="price"><b>&yen;<em><?php echo ($newgood["show_price"]); ?></em></b><strike>&yen;<?php echo ($newgood["marketprice"]); ?></strike></div>

                        </a>
                        <a href="javascript: addcart('<?php echo ($newgood["id"]); ?>',1,'<?php echo ($newgood["show_price"]); ?>','0',0,0);" class="btn_gwc"></a>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <!--最新推荐 E-->

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

    <link rel="stylesheet" href="/Public/Wap/style/swiper.min.css">
    <script type="text/javascript" src="/Public/Wap/js/swiper.jquery.min.js"></script>
    <script>
        $(function() {
            $('html').css('fontSize', $('body').width() / 640 * 100);
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                speed: 300,
                watchSlidesProgress: true,
                slideToClickedSlide: true,
                autoplay: 3000
            });

//            $(window).scroll(function() {
//                if ($(window).scrollTop() > 200) {
//                    $(".index_ss").addClass("home_bg");
//                }
//                ;
//                if ($(window).scrollTop() < 200) {
//                    $(".index_ss").removeClass("home_bg");
//                }
//            });
        });
    </script>


</body>
</html>