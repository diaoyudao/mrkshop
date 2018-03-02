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
    <div class="herder ">
        <a href="javascript:history.go(-1);" class="back"><i></i></a>
        <h3>我的收藏</h3>   

    </div>
    <div class="t_line"></div>
    <!--头部 E--> 
    <!--订单列表 S--> 
    <?php if(!empty($favorlist)): ?><div class="order_list order_list2 colled">
            <ul id="conten_id">
                <?php if(is_array($favorlist)): $k = 0; $__LIST__ = $favorlist;if( count($__LIST__)==0 ) : echo "没有收藏商品" ;else: foreach($__LIST__ as $key=>$fo): $mod = ($k % 2 );++$k;?><li rel='<?php echo ($fo["id"]); ?>'>
                        <input name="" type="checkbox" class="chk flow_checkbox" value="<?php echo ($fo['id']); ?>" >
                        <a href=" <?php echo U('Goods/detail',array('channelname'=>$fo['channelname'],'id'=>$fo['id']) );?>">
                            <img src="<?php echo ($fo['pics_img'][$fo['cover_id']]); ?>" />
                            <div class="pro_info">
                                <h3><a href="<?php echo U('Goods/detail',array('channelname'=>$fo['channelname'],'id'=>$fo['id']) );?>" ><?php echo ($fo["title"]); ?></a></h3>
                                <p class="pric">
                                    <span>￥<span class="font36"><?php echo ($fo["price"]); ?></span>
                                    </span>
                                    <i>&yen;<?php echo ($fo["marketprice"]); ?></i>
                                    <a href="javascript:addfavortable(this ,<?php echo ($fo["id"]); ?>);" class="addfavortable"></a>
                                    <a href="javascript:addcart('<?php echo ($fo["id"]); ?>',1,'<?php echo ($fo["price"]); ?>','');" class="addcartbutton"></a>
                                </p>
                            </div>
                            <!--<div class="to_shop_car">
                                    <a href="flow.html" class=""><img src="/Public/Wap/images/shopcar.png" /></a>
                            </div>-->
                        </a>
                    </li><?php endforeach; endif; else: echo "没有收藏商品" ;endif; ?>
            </ul>
        </div>
        <div class="flow_footer flow_footer2 " style="bottom:0; margin-bottom: 1rem">
            <input name="" type="checkbox" class="chk_all flow_checkbox" id="checkall" value=""  />
            <label for="checkall ">全选</label>    
            <div class="flow_footer_cz">
                <a href="javascript:batchDelete();" class="jiesuan yd_tc_btn">删除</a> </div>    
        </div>
        <?php else: ?>
        <div class="no_data">
            <h3>
                <i class=""> <img src="/Public/Wap/images/ts.png"/> </i> 没有收藏记录哦
            </h3>						        	
        </div><?php endif; ?>
    <!--订单列表 E--> 
    <div  class="yd_tc">
        <div class="tijiao_true">	
            <h3>确定删除该商品</h3>
            <div><a href="javascript:;" class="cancel" >取消</a><a href="javascript:;" >确定</a></div>
        </div>
    </div>
    <div id="loding_ajax" style=" text-align:center;height:.88rem;line-height:.88rem;font-size: .44rem;font-weight: bold;color: #000; display: none;">加载中····</div>

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
            $(window).scroll(function() {
                if ($(window).height() + 200 + $(window).scrollTop() > $('body').outerHeight() && AjaxPage.canpage) {
                    AjaxPage.canpage = false;
                    AjaxPage.gotoPageNext();
                }
            });
        });
    </script>
    <script type="text/javascript">
//        $(function() {
//            layer.confirm('确定要删除该收藏商品吗?', {icon: 3, title: '提示'}, function(index) {
//                //do something
//                console.log(index);
//
//                layer.close(index);
//            });
//        })

        function regoodsprice() {
            var option = $(".chk");
            var checkall = true;
            option.each(function(i) {
                if (!$(this).prop("checked")) {
                    checkall = false;
                }
            });
            $(".chk_all").prop("checked", checkall);
        }
        $(".chk_all").click(function() {
            $(".chk").prop("checked", this.checked);
            regoodsprice();
        });
        // 批量删除
        function batchDelete() {
            if ($('input.chk:checked').length > 0) {
                var laylerindex = layer.confirm('确定要批量删除购物车的商品吗？', {icon: 3, title: '提示'}, function(index) {
                    if (index) {
                        var sortArr = [];
                        var objo = $('input.chk:checked');
                        for (var i = 0; i < objo.length; i++) {
                            sortArr.push(objo[i].value);
                        }
                        var sorts = sortArr.join(',');
//                            alert(sorts);return false;
                        $.ajax({
                            type: 'post', //传送的方式,get/post
                            url: '<?php echo U("Member/delcollect");?>', //发送数据的地址
                            data: {goodsids: sorts},
                            dataType: "json",
                            success: function(data) {
                                $.each(sortArr, function(i, item) {
                                    $("li[rel=" + item + "]").slideUp().remove();
                                });
                                layer.close(laylerindex);
                                regoodsprice();
                                var a = data.goodsNum;
//                                if (a == "0") {
                                window.location.reload();
//                                }

                            },
                            error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                                layer.msg(XMLHttpRequest + thrownError);
                            }
                        });
                    } else {
                        return false;
                    }
                });
            } else {
                layer.msg("请勾选商品");
                return false;
            }
        }
    </script>



</body>
</html>