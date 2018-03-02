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

    <link rel="stylesheet" href="/Public/Wap/style/swiper.min.css">
    <style>
        .footer{ display: none;}
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
    <div class="herder herder_product"> 
        <a href="javascript:history.go(-1);" class="back"><i></i></a>
    </div>
    <!--头部 E--> 
    <!--首页幻灯 S-->
    <div class="swiper-container product_hdp" >
        <div class="swiper-wrapper ">
            <?php if(is_array($info["pics_img"])): $i = 0; $__LIST__ = array_slice($info["pics_img"],0,5,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$picimg): $mod = ($i % 2 );++$i;?><div class="swiper-slide"><img src="<?php echo ($picimg); ?>"></div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
    </div>
    <!--首页幻灯 E-->
    <div class="product_center">
        <h3><?php echo ($info["title"]); ?></h3>
        <h4><?php echo strip_tags($info['description']);?></h4>
        <div class="price">

            <b>￥<em><span class="zzprice zzmemberprice" id="zzmemberprice"><?php echo ($info["member_price"]); ?></span></em></b>
            <strike>销售价：￥<span id="zzprice"><?php echo ($info["price"]); ?></span></strike>
            <strike>市场价：￥<span id="zzmarketprice"><?php echo ($info["marketprice"]); ?></span></strike>
            <!--税费 S-->
            <span class="taxes" style="display:none;">
                税费<i>￥30</i>
            </span>
            <!--税费 -->
        </div>
        <!--促销 S-->
        <div class="spcx" style="display:none;">
            <dt>促销</dt>
            <dd>
                <ul>
                    <li><span>满减</span>满200.00元减50.00元</li>      
                </ul>
            </dd>
        </div>
        <!--促销 E-->
        <!--限时抢购 S-->
        <?php if($promotion): ?><div class="qgtime_time">
                <div class="synumber"><span><a href="<?php echo U('Goods/detail',array('active'=>'pomotion','id'=>$promotion['goods_id']));?>">立即参加</a></span></div>
                <div class="xsqg_ioc"><i></i><?php echo ($promotion["xianshi_title"]); ?></div>
                <div class="sytime">结束时间<?php echo (date('Y-m-d H:i:s',$promotion["end_time"])); ?></div>
            </div><?php endif; ?>
        <!--限时抢购 E-->
    </div>
    <div class="gongneng">
        <dl  class="btn_sppj">
            <a href="<?php echo U('Comment/lists',array('gid'=>$info['id']));?>"><dt><span><i class="Score"></i><?php echo ($info["comment"]); ?>条</span>商品评价<i class="Get"></i></dt></a>
        </dl>
    </div>
    <?php if(!empty($contentgoodattr)): ?><div class="gongneng">
        <dl class="btn_ggcs">
            <dt>规格参数<i class="Get"></i></dt>
        </dl>
    </div><?php endif; ?>
    <div class="product_twxq">
        <?php echo ($info["content"]); ?>
    </div>
    <!--底部 S-->
    <div class="gd-footerMenu"> 

        <?php if(($info["favor"]) == "1"): ?><a href="javascript:;;" class="wdsc"><i class="gwc_sc gwc_sc_hover"></i><span>已收藏</span></a>
        <?php else: ?>
        <a href="javascript:addfavortable(this ,<?php echo ($info["id"]); ?>);" class="wdsc"><i class="gwc_sc"></i><span>收藏</span></a><?php endif; ?>


        <a href="<?php echo U('Cart/index');?>"><i class="gwc_gwc"></i><span>购物车</span><em class="cart_sum"><?php echo ($cart_list["goods_count"]); ?></em></a>
        <div class="btns"> <a href="javascript:;" class="addToCart ">加入购物车</a> </div>
        <div class="btns"> <a href="javascript:;" class="Shop_btn">立即购买</a></div>
    </div>
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
    <div class="b_line"></div>
    <!--底部 E-->
    <!--提示 S-->
    <div class="prompt_nr" style="display:none">
        收藏成功
    </div>
    <!--提示 E-->
    <!--弹出商品参数 S-->
    <div class="flow_tcsx spcs" style="display: none;">
        <div class="tcsx_nr" style="bottom: 0px;">
            <div class="tcsx_spxx">
                <h3 style='background-color:#fff;z-index: 20'>商品参数</h3> 
                <ul class="tcsx_sxnav">
                    <?php if(is_array($contentgoodattr)): $i = 0; $__LIST__ = $contentgoodattr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attrs): $mod = ($i % 2 );++$i;?><li>
                        <dt style='width:0.8rem'><?php echo ($attrs["name"]); ?></dt><dd style='width:4.8rem'><?php echo ($attrs["value"]); ?></dd>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
            <a href="javascript:;" class="tcsx_btn">确定</a>
        </div>
    </div>
    <!--弹出商品参数 E-->
    <!--弹出商品规格 S-->
    <div class="flow_tcsx spgg" style="display:none;">
        <div class="tcsx_nr">
            <div class="tcsx_spxx">
                <dl>
                    <dt class="img"><img src="<?php echo ($info['pics_img'][$info['cover_id']]); ?>" id="midimg" alt="<?php echo ($info["title"]); ?>"></dt>
                    <dd>
                        <div class="xx_bottom">
                            <div class="tcsx_price">￥<span class="zzprice zzmemberprice" id="zzmemberprice22"><?php echo ($info["member_price"]); ?></span></div>
                            <p class="spsx_nr">请选择商品属性</p>
                        </div>
                        <div class="close"><span class="close-top"></span> <span class="close-bottom"></span> </div>
                    </dd>
                </dl>
                <ul class="tcsx_sxnav four">
                    <?php if(is_array($info["goodattr"])): $i = 0; $__LIST__ = $info["goodattr"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$attr): $mod = ($i % 2 );++$i; if($attr['attr_type'] == 1): ?><li>
                                <h3><?php echo ($attr["name"]); ?></h3>
                                <div class="sxxz sxxz_1">
                                    <?php if(is_array($attr["sub"])): $i = 0; $__LIST__ = $attr["sub"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subs): $mod = ($i % 2 );++$i;?><span class="pro_taocan <?php if(($key) == "0"): ?>xz_hover<?php endif; ?>" onclick="selecradiotattr(this);" attrid="<?php echo ($subs["id"]); ?>" price="<?php echo ((isset($subs["price"]) && ($subs["price"] !== ""))?($subs["price"]):0); ?>"><?php echo ($subs["value"]); ?>(+&yen;<?php echo ((isset($subs["price"]) && ($subs["price"] !== ""))?($subs["price"]):0); ?>)</span><?php endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                            </li>
                            <?php else: ?>
                            <li>
                                <h3><?php echo ($attr["name"]); ?></h3>
                                <div class="sxxz sxxz_1">
                                    <?php if(is_array($attr["sub"])): $i = 0; $__LIST__ = $attr["sub"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subs): $mod = ($i % 2 );++$i;?><span class="pro_checkbox_taocan <?php if(($key) == "0"): ?>xz_hover<?php endif; ?>" onclick="selectcheckboxattr(this);" attrid="<?php echo ($subs["id"]); ?>" price="<?php echo ($subs["price"]); ?>"><?php echo ($subs["value"]); ?>(+&yen;<?php echo ($subs["price"]); ?>)</span><?php endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                            </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    <li>
                        <h3>数量</h3>
                        <div class="number">
                            <span class="add_ioc coun-min">-</span>
                            <input type="text" value="1" class='goods_number'  id="coun-num" onkeyup="this.value = this.value.replace(/\D/g, '')"  onafterpaste="this.value=this.value.replace(/\D/g,'')" maxlength="3">
                            <span class="minus_ioc coun-plu">+</span> 
                        </div>
                    </li>
                </ul>
            </div>
            <a href="javascript:;" class="tcsx_btn addcartbutton">确定</a>
        </div>
    </div>
    <!--弹出商品规格 E--> 

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


    <script type="text/javascript" src="/Public/Wap/js/swiper.jquery.min.js"></script>
    <script>
                                $(function() {
                                    // 加入购物车
//                                    $(".addcartbutton").click(function() {
                                    $("body").on('click', ".addcartbutton", function() {
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
                                    $("body").on('click', ".nowbuybutton", function() {
                                        var is_login = "<?php echo is_login();?>";
                                        if (is_login <= 0) {
                                            layer.msg('请先登录');
                                            location.href = "<?php echo U('Member/login');?>";
                                            return false;
                                        }
                                        $("input[name=number]").val(parseInt($("#coun-num").val()));
                                        $("#buynow").submit();
////                                    $(".nowbuybutton").click(function() {
//                                        var stock = "<?php echo ($info["stock"]); ?>";
//                                        var goods_number = parseInt($("#coun-num").val());
//                                        if (goods_number > stock) {
//                                            layer.msg("商品库存不足 ！");
//                                            return false;
//                                        }
//                                        var parameters = $("#parametersid").val();//参数
//                                        var go_url = "<?php echo U('Cart/index');?>";
//                                        now_buy('<?php echo ($info["id"]); ?>', goods_number, '<?php echo ($info["member_price"]); ?>', parameters, 0, 0, 0, go_url);
//                                        // 跳转到购物车页面
////                                    window.location.href='<?php echo U("cart/index");?>';
//                                        return false;
                                    });




                                    // 增加商品数量
                                    $('.coun-plu').on('click', function() {
                                        var _temp_num = 0;
                                        if (isNaN(parseInt($('#coun-num').val()))) {
                                            _temp_num = 0;
                                        } else {
                                            _temp_num = parseInt($('#coun-num').val());
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
            $(obj).parent().find(".pro_taocan").removeClass("xz_hover");
            $(obj).addClass('xz_hover');
            picArr = [];
            var objo = $("ul.four");
            for (var i = 0; i < objo.find("span.xz_hover").length; i++) {
                picArr.push(objo.find('.xz_hover:eq(' + i + ')').attr('attrid'));
            }
            picStr = picArr.join(',');
            $("#parametersid").val(picStr);
            resetproductprice();
        }

        function resetproductprice() {
            var attrprice = 0;
            var objo = $("ul.four");
            var pi = 0;
            var txt = '';
            picArr = [];

            for (var i = 0; i < objo.find("span.xz_hover").length; i++) {
                pi = objo.find('.xz_hover:eq(' + i + ')').attr('price');
                picArr.push(objo.find('.xz_hover:eq(' + i + ')').attr('attrid'));
                txt += objo.find('.xz_hover:eq(' + i + ')').text();
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
            $('.zzmemberprice').html(memberpirce.toFixed(2)); //会员价
            $('#zzmarketprice').html(marketprice.toFixed(2));// 市场价
            $(".spsx_nr").html("已选择：" + txt);
        }

        function selectcheckboxattr(obj) {
            console.log(obj);
            if ($(obj).hasClass('xz_hover')) {
                $(obj).removeClass('xz_hover');
            } else {
                $(obj).addClass('xz_hover');
            }
            picArr = [];
            var objt = $("ul.four");
            for (var i = 0; i < objt.find("span.xz_hover").length; i++) {
                picArr.push(objt.find('.xz_hover:eq(' + i + ')').attr('attrid'));
            }
            ;
            picStr = picArr.join(',');
            $("#parametersid").val(picStr);
            resetproductprice();
        }
    </script>
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
        });
        var fxtime;
    </script>
    <script>

        //显示商品参数筛选
        $(".btn_ggcs").click(function() {
            $(".tcsx_btn").removeClass('addcartbutton');
            $(".tcsx_btn").removeClass('nowbuybutton');
            $(".spcs ").finish().show();
            $(".flow_tcsx .tcsx_nr").finish().css("bottom", '-6rem').eq(0).animate({bottom: '0px', });

        });
        //隐藏商品参数筛选
        $(".close,.flow_tcsx .tcsx_btn").click(function() {
            $(".flow_tcsx .tcsx_nr").finish().animate({bottom: '-6rem'}, function() {
                $(".flow_tcsx").finish().hide();
            });
        });

        //显示商品规格
        $(".addToCart").click(function() {
            $(".tcsx_btn").removeClass('nowbuybutton').addClass("addcartbutton");
            $(".spgg ").finish().show();
            $(".spgg .tcsx_nr").finish().css("bottom", '-6rem').eq(0).animate({bottom: '0px', });
        });
//        //商品规格筛选
//        $(".sxxz_1 span").click(function() {
//            $(".sxxz_1 span").removeClass("xz_hover");
//            $(this).addClass("xz_hover");
//            var txt_a = $(".sxxz_1 span.xz_hover").html();
//            $(".spsx_nr").html("已选择：" + txt_a);
//        });


        $(".Shop_btn").click(function() {
            $(".tcsx_btn").removeClass('addcartbutton').addClass("nowbuybutton");
            $(".spgg ").finish().show();
            $(".spgg .tcsx_nr").finish().css("bottom", '-6rem').eq(0).animate({bottom: '0px', });
        });





    </script>


</body>
</html>