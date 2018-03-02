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
    <form method="post" name="form" action="<?php echo U('Member/update');?>" class="validate" id="addaddress" onsubmit="validateCallback(this, dialogAjaxDone);
             return false;">
        <div class="herder herder2 ">
            <a href="javascript:history.go(-1);" class="back"><i></i></a>
            <h3>账户管理</h3>
            <div class="herd_r"><input type="submit" value="完成"/></div>
        </div>
        <div class="t_line"></div>
        <!--头部 E--> 
        <div class="count_mana">
            <dl>
                <dt>性别</dt>
                <dd class="famle" >
                    <select name="sex">
                        <?php switch($memberInfo["sex"]): case "1": ?><option value='0'>保密</option>
                            <option value='1' selected='selected'> 男</option>
                            <option value='2'> 女</option><?php break;?>
                        <?php case "2": ?><option value='0'>保密</option>
                            <option value='1' > 男</option>
                            <option value='2' selected='selected'> 女</option><?php break;?>
                        <?php default: ?>
                        <option value='0' selected='selected'>保密</option>
                        <option value='1' > 男</option>
                        <option value='2'> 女</option><?php endswitch;?> 

                    </select>
                    <i class="Get"></i>
                </dd>
            </dl>
            <dl><dt>昵称</dt><dd><input type="text" name='nickname' value='<?php echo ($memberInfo["nickname"]); ?>' placeholder="请输入您的昵称" /></dd></dl>
            <dl><dt>真实姓名</dt><dd><input type="text" name='realname' value='<?php echo ($memberInfo["realname"]); ?>' placeholder="请输入您的真实姓名" /></dd></dl>
            <dl><dt>身份证号</dt><dd><input type="text" name='card_no' value='<?php echo ($memberInfo["card_no"]); ?>' placeholder="请输入您的身份证号" /></dd></dl>
            <dl><dt>地址信息</dt><dd><input type="text" name='address' value='<?php echo ($memberInfo["address"]); ?>' placeholder="请输入您的地址信息" /></dd></dl>
        </div>
    </form>

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
    <script src="/Public/Wap/js/jquery.cityselect.js" type="text/javascript"></script>
    <script>
         $(function() {
             var prov = "<?php echo ($memberInfo['province']); ?>";
             if (prov) {
                 $("#selectaddress").citySelect({
                     prov: "<?php echo ($memberInfo['province']); ?>",
                     city: "<?php echo ($memberInfo['city']); ?>",
                     nodata: "<?php echo ($memberInfo['area']); ?>"
                 });
             } else {
                 $("#selectaddress").citySelect({
                     prov: "北京",
                     city: "东城区",
                     nodata: "none"
                 });
             }

             $('#addaddress').validate({
                 rules: {
                     realname: {
                         required: true
                     },
                     card_no: {
                         required: true,
                         rangelength: [18, 18]
                     },
                     nickname: {
                         required: true,
                     },
                     address: {
                         required: true
                     },
//                    youbian: {
//                        rangelength: [6, 6]
//                    }
                 },
                 messages: {
                     realname: {
                         required: '收货人不能为空'
                     },
                     card_no: {
                         required: '身份证不能为空',
                         rangelength: "身份证格式不正确"
                     },
                     nickname: {
                         required: '用户昵称不能为空'
                     },
                     address: {
                         required: '收货详细地址不能为空'
                     }
//                    youbian: {
//                        rangelength: '邮编长度为6位数字'
//                    }
                 },
//                 errorElement: 'i',
//                 errorClass: 'ts2',
                 highlight: function(e) {
                     //   $(e).addClass('ts2');
                 },
                 success: function(e) {
//                     $(e).removeClass('ts2');
                 },
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

         });

    </script>


</body>
</html>