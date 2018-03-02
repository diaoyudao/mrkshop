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
        .footer{display: none;}
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

    <?php if(($flag) == "update"): ?><!--头部 S-->
<div class="herder">
    <a href="<?php echo U('Member/address');?>" class="back"><i></i></a>
    <h3>编辑地址</h3>
    <!-- <div class="herd_r"><a href="search.html" class="search"><i></i></a></div>-->
</div>
<div class="t_line"></div>
<!--头部 E--> 
<!--地址列表 S--> 
<form method="post" name="form" action="<?php echo U('Member/save');?>" class="validate" id="addaddress" onsubmit="validateCallback(this, dialogAjaxDone);
        return false;">
    <div class="edi_add">
        <div class="list">
            <span>收货人</span>
            <input type="text" name="realname" value="<?php echo ($nowaddress['realname']); ?>" id="realname" placeholder="请输入姓名"/>
        </div>	

        <div class="list">
            <span>联系方式</span>
            <input type="text" name="cellphone" value="<?php echo ($nowaddress['cellphone']); ?>" id="" placeholder="请输入手机号"/>
        </div>	
        <!--        <div class="list">
                    <span>联系电话</span>
                    <input type="text" name="phone" value="<?php echo ($nowaddress['phone']); ?>" id="" placeholder="请输入电话号"/>
                </div>-->
        <div class="in list area" id='selectaddress'>
            <span>所在地区</span>
            <select id="cmbProvince"  name="province" class="prov"></select>
            <em>省</em>
            <select id="cmbCity" name="city" class="city"></select>
            <em>市</em>
            <select id="cmbArea" name="area"  class="dist"></select>	
            <em>区/县</em>	
        </div>
        <div class="list">
            <span>详细地址</span>
            <input type="text" name="address" id="address"  value="<?php echo ($nowaddress['address']); ?>" placeholder="请输入街道名称" />
        </div>
        <div class="list">
            <span>邮编</span>
            <input type="text" name="youbian" value="<?php echo ($nowaddress['youbian']); ?>" id="" placeholder="请输入邮政编码"/>
        </div>
        <div class="list">
            <span>身份证号</span>
            <input type="text" name="card_no" id="card_no" value="<?php echo ($nowaddress['card_no']); ?>" placeholder="请输入身份证号码"/>
        </div>
        <div class="setD bg_fa clear">
            <div class="fl">
                <a class="a_adrss" href="javascript:;">
                    <?php if($nowaddress['isdefault'] == 1): ?><i class="checked"></i>
                        <?php else: ?>
                        <i class="check"></i><?php endif; ?>
                    设为默认地址 
                    <!--<input type="radio" name="isdefault" value="1" class="i_radio" style="width: 0px;height: 0px; background-color: #fff;" />-->
                    <input type="hidden" name="isdefault" value="<?php echo ($nowaddress['isdefault']); ?>" class="i_radio" />
                </a>

            </div>
        </div>

        <!--        <div class="setD bg_fa clear">
                    <div class="fl">
                        <a class=" " href="javascript:;"><i class="check checked"></i>默认地址</a>
        
                        <?php if($nowaddress['isdefault'] == 1): ?><label ><input name="isdefault" type="checkbox" checked="checked" class='check checked'><span>设为默认地址</span></label>
                            <?php else: ?>
                            <label > <input  name="isdefault" type="checkbox" class='check checked'><span>设为默认地址</span></label><?php endif; ?>
                    </div>-->
        <!--                <div class="fr ">
                            <a href="javascript:;" class="dele">删除</a>
                            <a class="mr-0 edi" href="javascript:;" >编辑</a>
                        </div>-->
    </div>
</div>

<div class="fix_bo" >
    <input type="hidden" value="<?php echo ($nowaddress['id']); ?>" name="id" class="text">
    <input class="long_btn" type="submit" name="submit-add" id="submit-add" value="保存地址" />
    <!--<a href="user_address_add.html" class="long_btn" >新增地址</a>-->
</div>
</form>
<!--地址列表 E-->  
<!--底部 S-->
<div class="b_line"></div>
<!--底部 E-->
<script>
    $(function() {
        $(".a_adrss").click(function(e) {
            cname = $(this).find("i").attr("class");
            if (cname == "check") {
                $(".i_radio").val("1");
                $(this).find("i").removeClass("check").addClass("checked");
            } else {
                $(".i_radio").val("0");
                $(this).find("i").removeClass("checked").addClass("check");
            }
        })
    });

</script>

    <?php else: ?>
    <!--头部 S-->
    <div class="herder">
        <a href="<?php echo U('Member/index');?>" class="back"><i></i></a>
        <h3>地址管理</h3>
        <!-- <div class="herd_r"><a href="search.html" class="search"><i></i></a></div>-->
    </div>
    <div class="t_line"></div>
    <!--头部 E--> 
    <!--地址列表 S--> 
    <ul class="add_list">
        <?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "没有收货地址" ;else: foreach($__LIST__ as $key=>$ao): $mod = ($i % 2 );++$i;?><li>
                <div class="info">
                    <span><i><img src="/Public/Wap/images/user-name.png" /></i><?php echo ($ao['realname']); ?></span>
                    <span><i><img src="/Public/Wap/images/phone.png" /></i><?php echo ($ao['cellphone']); ?></span>
                    <span><i><img src="/Public/Wap/images/id.png" /></i><?php echo ($ao['card_no']); ?></span>
                    <p><?php echo ($ao['province']); echo ($ao['city']); echo ($ao['area']); echo ($ao['address']); ?></p>
                </div>
                <div class="setD bg_fa clear">
                    <div class="fl">
                        <a  class=" " href="javascript:;"><i class="check  <?php if(($ao["isdefault"]) == "1"): ?>checked<?php endif; ?>"></i>默认地址</a>
                    </div>
                    <div class="fr ">
                        <a href="javascript:;" onclick="delAddress(<?php echo ($ao['id']); ?>)" class="dele">删除</a>
                        <a class="mr-0 edi" href="<?php echo U('member/address',array('id'=>$ao['id'],'flag'=>'update'));?>">编辑</a>
                    </div>
                </div>
            </li><?php endforeach; endif; else: echo "没有收货地址" ;endif; ?>
         <?php else: ?>
                <li class="no_data">
                    <h3>
                        <i class=""> <img src="/Public/Wap/images/ts.png"/> </i> 没有收货地址记录哦
                    </h3>						        	
                </li><?php endif; ?>
    </ul>

    <div class="fix_bo" >
        <a href="<?php echo U('Member/address',array('flag'=>'update'));?>" class="long_btn">新增地址</a>
    </div>
    <!--地址列表 E--><?php endif; ?>

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
                            function delAddress(aid) {
                                //判断新地址是否选中,获取地址id
                                if (confirm("确定要删除该地址吗？"))
                                {
                                      if (aid == null) {
                                          alert("选择地址无效!");
                                        return false;
                                    } else {
                                        $.ajax({
                                            type: 'post', //传送的方式,get/post
                                            url: '<?php echo U("Member/deleteAddress");?>', //发送数据的地址
                                            data: {id: aid},
                                            dataType: "json",
                                            success: function(data)
                                            {
                                                $("#message").html(data.msg).show();
                                                setTimeout(function() {
                                                    $('#message').hide();
                                                    window.location.reload();
                                                }, 1500);
                                            },
                                            error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                                                alert(XMLHttpRequest + thrownError);
                                            }
                                        });      
                                     }
                                }
                            }

                            $(function() {
                                var prov = "<?php echo ($nowaddress['province']); ?>";
                                if (prov) {
                                    $("#selectaddress").citySelect({
                                        prov: "<?php echo ($nowaddress['province']); ?>",
                                        city: "<?php echo ($nowaddress['city']); ?>",
                                        nodata: "<?php echo ($nowaddress['area']); ?>"
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
                                        cellphone: {
                                            required: true,
                                            ismobile: true
                                        },
                                        address: {
                                            required: true
                                        },
                                        youbian: {
                                            rangelength: [6, 6]
                                        }
                                    },
                                    messages: {
                                        realname: {
                                            required: '收货人不能为空'
                                        },
                                        card_no: {
                                            required: '身份证不能为空',
                                            rangelength: "身份证格式不正确"
                                        },
                                        cellphone: {
                                            required: '手机号码不能为空'
                                        },
                                        address: {
                                            required: '收货详细地址不能为空'
                                        },
                                        youbian: {
                                            rangelength: '邮编长度为6位数字'
                                        }
                                    },
                                    errorElement: 'i',
                                    errorClass: 'ts2',
                                    highlight: function(e) {
                                        //   $(e).addClass('ts2');
                                    },
                                    success: function(e) {
                                        $(e).removeClass('ts2');
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