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
        <a href="javascript:history.go(-1);" class="back"><i></i></a>
        <h3> <?php if(!empty($memberInfo["mobile"])): ?>更换<?php else: ?>绑定<?php endif; ?>手机</h3>
        <!-- <div class="herd_r"><a href="search.html" class="search"><i></i></a></div>-->
    </div>
    <div class="t_line"></div>
    <!--头部 E--> 
    <form action="<?php echo U('Safety/bind_mobile');?>" method="get" class="mobile_auth"  onsubmit="validateCallback(this, dialogAjaxDone);
            return false;">
        <!--绑定手机 S--> 
        <?php if(!empty($memberInfo["mobile"])): ?><div class="safe_item safe_item2">
            <p>现绑定手机：<i><?php echo ($memberInfo["mobile"]); ?></i></p>
        </div>
        <div class="safe_item">
            <span>短信验证码</span>
            <input type="text" name='code' id='code' class="txt3" placeholder="请输入验证码" />
            <input type="button" class="c_btn fr"  onclick="getcode2(this)" value="获取短信验证" />
        </div><?php endif; ?>
        
        <!--        <div class="chagePw1 chagePw2">
                    <a class="long_btn " href="bangdin.html" >下一步</a>
                </div>-->
        <!--绑定手机 E-->  
        <!--绑定手机 S--> 
        <div class="safe_item">
            <span>绑定手机</span>
            <input type="text" name='mobile' id='mobile'  class="txt" placeholder="请输入手机号" />
        </div>
        <div class="safe_item">
            <span>短信验证码</span>
            <input type="text"  class="txt3" name='new_code' id='new_code'  placeholder="请输入验证码" />
            <input type="button" class="c_btn fr"  onclick="getcode(this)" value="获取短信验证" />
        </div>
        <div class="chagePw1 chagePw2">
            <input type="hidden" value="<?php echo ($memberInfo["mobile"]); ?>" name='old_mobile' class="a-btn" />
            <input type="submit" class="long_btn " value="确定" />
        </div>
        <!--绑定手机 E-->  
    </form>
    <!--底部 S-->
    <div class="b_line"></div>
    <!--底部 E-->

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

    <script src="/Public/Wap/js/jquery.validate.js" type="text/javascript"></script>
    <script>
                $(function() {
                    if ($('form.mobile_auth').length) {
                        var mobyzurl = "<?php echo U('Member/checkMobile');?>";
                        $('form.mobile_auth').validate({
                            rules: {
                                mobile: {
                                    required: true,
                                    remote: {
                                        url: mobyzurl,
                                        type: "post",
                                        dataType: "json",
                                        data: {
                                            mobile: function() {
                                                return $("#mobile").val();
                                            },
                                            typemobile: 0  //  0注册，1找回密码
                                        }
                                    },
                                    ismobile: true
                                },
                                code: {
                                    required: true
                                },
                                new_code: {
                                    required: true
                                }
                            },
                            messages: {
                                mobile: {
                                    required: '<i class=""> ! </i>请输入手机号',
                                    remote: '<i class=""> ! </i>该手机号已注册'
                                },
                                code: {
                                    required: '<i class=""> ! </i>请输入验证码'
                                },
                                new_code: {
                                    required: '<i class=""> ! </i>请输入验证码'
                                }
                            },
                            errorPlacement: function(error, element) {
                                error.appendTo(element.parents("dl").find("dd.error"));
                            }
                        });
                        jQuery.validator.addMethod('ismobile', function(value, element) {
                            var length = value.length;
                            var mobile = /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0-9]|170|177)\d{8}$/;
                            return this.optional(element) || (length == 11 && mobile.test(value));
                        }, '请正确填写手机号码');
                    }
                });

                function getcode(o) {
                    var obj = $(o).parents("form").find("#mobile");
                    var m = $(obj).val();
                    if (m == "" || $(obj).hasClass("error") > 0) {
                        layer.msg('输入新手机号');
                        return false;
                    }
                    wait = 60;
                    var h = $.ajax({
                        url: "<?php echo U('Safety/getMobileCode');?>",
                        type: "post",
                        dataType: "json",
                        data: {mobile: m},
                        success: function(json) {
                            if (json.status) {
                                djstime(o);
                                layer.msg(json.info);
                            }
                            return false;
                        }
                    });
                    return false;
                }
                function getcode2(o) {
                    var m = "<?php echo ($memberInfo["mobile"]); ?>";
                    wait = 60;
                    var h = $.ajax({
                        url: "<?php echo U('Safety/getMobileCode');?>",
                        type: "post",
                        dataType: "json",
                        data: {mobile: m, type: 4}, // 旧手机验证码
                        success: function(json) {
                            if (json.status) {
                                djstime(o);
                                layer.msg(json.info);
                            }
                            return false;
                        }
                    });
                    return false;
                }
                function djstime(o) {
                    if (wait == 0) {
                        o.removeAttribute("disabled");
                        o.value = "免费获取激活码";
                        wait = 60;
                    } else {
                        o.setAttribute("disabled", true);
                        o.value = "重新发送(" + wait + ")";
                        wait--;
                        setTimeout(function() {
                            djstime(o)
                        }, 1000);
                    }
                }
    </script>


</body>
</html>