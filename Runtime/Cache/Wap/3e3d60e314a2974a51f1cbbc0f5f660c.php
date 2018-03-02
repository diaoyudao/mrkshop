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
        html{
            background-color: #fff;
        }
        .footer{display:none}
        body{background:url(/Public/Wap/images/login/bg.jpg) no-repeat center top;background-size:100% auto;min-height:100%}
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

    <!--article start-->
    <div class="logins">
        <form action="<?php echo U('member/login');?>" method="post" class="validate"  onsubmit="validateCallback(this, dialogAjaxDone);
                return false;">
            <ul class="login">
                <li><input type="text" name="username" id="" class="name" placeholder="请输入用户名/手机号" value=""  /></li><!---错误提示：placeholder="aa"　这样写--->
                <li><input type="password" name="password" class="psw" id="" placeholder="请输入密码"  value="" /></li>
                <li><input type="submit" class="button" name="" id="" value="登录" /></li>
                <li class="clear">
                    <a href="<?php echo U('Member/register');?>">注册账号</a>
                    <a href="<?php echo U('Member/forgotpassword');?>">忘记密码</a>
                </li>
            </ul>
        </form>
    </div>
    <div class="logins_three" style="display:block">
        <h3>其他方式登录</h3>
        <div>
            <!--            <a href="javascript:;" class="i_qq"></a>
                        <a href="javascript:;" class="i_wb"></a>-->
            <a href="<?php echo U('Member/wx_login');?>" class="i_wx"></a>
        </div>
    </div>
    <!--article end-->

<!-- /主体 -->
<!-- 底部 -->

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
 
    <script src="/Public/Wap/js/jquery.validate.js" type="text/javascript"></script>
    <script type="text/javascript">
            $(function() {
                if ($('form.validate').length) {
//	    var mobyzurl = "<?php echo U('member/checkMobile');?>";
                    $('form.validate').validate({
                        rules: {
                            username: {
                                required: true,
//                                        ismobile: true
                                rangelength: [3, 11]
                            },
                            password: {
                                required: true,
                                rangelength: [5, 15]

                            }
                        },
                        messages: {
                            username: {
                                required: '请输入手机号',
                                rangelength: "账号介于 {0} 和 {1} 之间用户名或账号"
                            },
                            password: {
                                required: '请输入密码',
                                rangelength: "密码介于 {0} 和 {1} 之间的字符串"
                            }
                        },
//                        errorPlacement: function(error, element) {
//                            error.appendTo(element);
//                            console.log(error);
//                        }
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


                $(".name,.psw").focus(function() {
                    $(".logins_three").slideUp();
                });
                $(".name,.psw").blur(function() {
                    $(".logins_three").show();
                });


            });



    </script>


</body>
</html>