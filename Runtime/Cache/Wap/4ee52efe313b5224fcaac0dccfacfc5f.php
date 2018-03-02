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

    <style type="text/css">
        html{
            background-color: #fff;
        }
       #senmobilemsg {
            border: 1px solid #e65e5f;
            border-radius: 0.12rem;
            color: #e65e5f;
            display: block;
            float: right;
            height: 0.56rem;
            line-height: 0.56rem;
            margin-top: 0.12rem;
            text-align: center;
            width: 1.9rem;
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
    <div class="login-4">
        <div class="herder">
            <a href="javascript:history.go(-1);" class="back"><i></i></a>
            <h3>找回密码</h3>
        </div>
        <div class="main">
            <form action="<?php echo U('member/resetpassword');?>" method="post" class="validate" onsubmit="validateCallback(this, dialogAjaxDone);
                    return false;">
                <ul>
                    <li class="clear"><label for="">绑定手机号</label><input type="tel" name="mobile" id="mobile" placeholder="请输入绑定手机号" /></li>
                    <li class="clear"><label for="">短信验证码</label><input type="text" name="code" id="code" placeholder="请输入验证码" />
                        <!--<a  onclick="getcode(this)">获取短信验证</a>-->
                        <input type="button" id="senmobilemsg" onclick="getcode(this)" class="a-btn3" value="获取短信校验码"  />
                    </li>
                    <li class="clear"><label for="">设置新密码</label><input type="password" name="password" id="password" placeholder="请输入新的密码" /></li>
                    <li class="clear"><label for="">确认新密码</label><input type="password" name="repassword" id="repassword" placeholder="请再次输入新密码" /></li>
                    <li class="clear"><input type="submit" name="submit" id="submit-btn" value="提交" /></li>
                </ul>
            </form>
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
 
    <script type="text/javascript">
        $(function() {
            if ($('form.validate').length) {
                var mobyzurl = "<?php echo U('Member/checkMobile');?>";
                $('form.validate').validate({
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
                                    forgetpwd: 1
                                }
                            },
                            ismobile: true
                        },
                        code: {
                            required: true,
                            rangelength: [4, 6]
                        },
                        password: {
                            required: true,
                            rangelength: [6, 12]
                        },
                        repassword: {
                            required: true,
                            rangelength: [6, 12]
                        }
                    },
                    messages: {
                        mobile: {
                            required: '请输入手机号',
                            remote: '该手机号还未注册'
                        },
                        code: {
                            required: '请输入短信验证码',
                            rangelength: "请输入验证码介于{0}和{1}之间的字符串"
                        },
                        password: {
                            required: '请输入新密码',
                            rangelength: "密码介于{0}和{1}之间的字符串"
                        },
                        repassword: {
                            required: '请输入确认密码',
                            rangelength: "密码介于{0}和{1}之间的字符串",
                            equalTo: '#password'
                        }
                    },
//                    errorPlacement: function(error, element) {
//                        error.appendTo(element.parent().find("em"));
//                    }
 /* 重写错误显示消息方法,以alert方式弹出错误消息 */  
                            showErrors : function(errorMap, errorList) {  
                             var msg = "";  
                             $.each(errorList, function(i, v) {  
                              msg += (v.message +'!' + "\r\n");  
                             });  
                             if (msg != "")  
                              layer.msg(msg);  
                            },
                });
                jQuery.validator.addMethod('ismobile', function(value, element) {
                    var length = value.length;
                    var mobile = /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0-9]|170|177)\d{8}$/;
                    return this.optional(element) || (length == 11 && mobile.test(value));
                }, '请正确填写手机号码');
            }
        });
        var wait = 60;
        function djstime(o) {
            if (wait == 0) {
                o.removeAttribute("disabled");
                o.value = "免费获取验证码";
                wait = 60;
            } else {
                o.setAttribute("disabled", true);
                o.value = "重新发送(" + wait + ")";
                wait--;
                setTimeout(function() {
                    djstime(o)
                },
                        1000);
            }
        }
        function getcode(o) {
            var obj = $(o).parents("form").find("#mobile");
            var m = $(obj).val();
            if (m == "" || $(obj).hasClass("error") > 0) {
                return false;
            }
            var wait = 60;
            var h = $.ajax({
                url: "<?php echo U('Member/getMobileCode');?>",
                type: "post",
                dataType: "json",
                data: {mobile: m, type: 2},
                success: function(json) {
                    if (json.status) {
                        djstime(o);
                        return false;
                    } else {
                        alert(json.info);
                        return false;
                    }
                }
            });
            return false;
        }
    </script>
    <script src="/Public/Wap/js/jquery.validate.js" type="text/javascript"></script>


</body>
</html>