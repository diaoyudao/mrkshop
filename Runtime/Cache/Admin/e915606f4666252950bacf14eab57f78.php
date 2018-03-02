<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|管理平台</title>
    <link href="/mrkshop/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/mrkshop/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/mrkshop/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/mrkshop/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/mrkshop/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/mrkshop/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/mrkshop/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/mrkshop/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/mrkshop/Public/Admin/js/jquery.mousewheel.js"></script>
 <script type="text/javascript" src="/mrkshop/Public/Admin/js/highcharts.js"></script>
<script type="text/javascript" src="/mrkshop/Public/Admin/js/exporting.js"></script>
<script type="text/javascript" src="/mrkshop/Public/Admin/js/data.js"></script>
    <!--<![endif]-->
    
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <!--<span class="logo"><img src="/mrkshop/Public/Admin/images/logo.png" ></span>-->
        <span class="logo"><h2 style='color: #fff;'>妙品生活商城系统</h2></span>
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav">
            <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (get_nav_url($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
			<li><a href="<?php echo get_index_url();?>" target="_blank">网站首页</a></li>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
                <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
                <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>
        </div>
    </div>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
        <!-- 子导航 -->
        
            <div id="subnav" class="subnav">
                <?php if(!empty($_extra_menu)): ?>
                    <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
                <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
                    <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
                        <ul class="side-sub-menu">
                            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                                    <a class="item" href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
    <div class="main-title">
        <h2>网站设置</h2>
    </div>
    <div class="tab-wrap">
        <ul class="tab-nav nav">
            <?php if(is_array(C("CONFIG_GROUP_LIST"))): $i = 0; $__LIST__ = C("CONFIG_GROUP_LIST");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><li <?php if(($id) == $key): ?>class="current"<?php endif; ?>><a href="<?php echo U('?id='.$key);?>"><?php echo ($group); ?>配置</a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <div class="tab-content">
            <form action="<?php echo U('save');?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$config): $mod = ($i % 2 );++$i;?><div class="form-item">
                        <label class="item-label"><?php echo ($config["title"]); ?><span class="check-tips">（<?php echo ($config["remark"]); ?>）</span> </label>
                        <div class="controls">
                            <?php switch($config["type"]): case "0": ?><input type="text" class="text input-small" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>"><?php break;?>
                            <?php case "1": if(($config["name"]) == "SITE_LOGO"): ?><input type="file" id="upload_picture">
                                <input type="hidden" name="config[<?php echo ($config["name"]); ?>]" id="icon" value="<?php echo ((isset($config["value"]) && ($config["value"] !== ""))?($config["value"]):''); ?>"/>
                                <div class="upload-img-box">
                                    <?php if(!empty($config["value"])): ?><div class="upload-pre-item"><img src="/mrkshop/Uploads/Picture//logo/<?php echo (get_cover($config["value"],'path')); ?>"/></div><?php endif; ?>
                                </div>
                                <script type="text/javascript" src="/mrkshop/Public/static/uploadify/jquery.uploadify.min.js"></script>
                                <script type="text/javascript">
                                    //上传图片
                                    /* 初始化上传插件 */
                                    $("#upload_picture").uploadify({
                                        "height": 30,
                                        "swf": "/mrkshop/Public/static/uploadify/uploadify.swf",
                                        "fileObjName": "download",
                                        "buttonText": "上传图片",
                                        "uploader": "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
                                        "width": 120,
                                        'removeTimeout': 1,
                                        'fileTypeExts': '*.jpg; *.png; *.gif;',
                                        "onUploadSuccess": uploadPicture,
                                        'onFallback': function() {
                                            alert('未检测到兼容版本的Flash.');
                                        },
                                        'onUploadStart': function(file) {
                                            var domainid = 'logo';
                                            $('#upload_picture').uploadify('settings', "formData", {'domainid': domainid});
                                        }
                                    });
                                    function uploadPicture(file, data) {
                                        var data = $.parseJSON(data);
                                        var src = '';
                                        if (data.status) {
                                            $("#icon").val(data.id);
                                            var domainid = 'logo';
                                            src = data.url || '/mrkshop/Uploads/Picture/' + domainid + '/' + data.path
//					    src = data.url || '/mrkshop' + data.path;
                                            $("#icon").parent().find('.upload-img-box').html(
                                                    '<div class="upload-pre-item"><img src="' + src + '"/></div>'
                                                    );
                                        } else {
                                            updateAlert(data.info);
                                            setTimeout(function() {
                                                $('#top-alert').find('button').click();
                                                $(that).removeClass('disabled').prop('disabled', false);
                                            }, 1500);
                                        }
                                    }
                                </script> 
                                <?php else: ?>
                                <input type="text" class="text input-large" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>"><?php endif; break;?>
                            <?php case "2": ?><label class="textarea input-large">
                                    <textarea name="config[<?php echo ($config["name"]); ?>]"><?php echo ($config["value"]); ?></textarea>
                                </label><?php break;?>
                            <?php case "3": ?><label class="textarea input-large">
                                    <textarea name="config[<?php echo ($config["name"]); ?>]"><?php echo ($config["value"]); ?></textarea>
                                </label><?php break;?>
                            <?php case "4": ?><select name="config[<?php echo ($config["name"]); ?>]">
                                    <?php $_result=parse_config_attr($config['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($config["value"]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select><?php break;?>
                            <?php case "5": ?><div class="controls" style="display: block;">
                                    <input class="file" type="file" name="config[<?php echo ($config["name"]); ?>]">
                                    <input type="hidden" name="<?php echo ($config["value"]); ?>"/>
                                    <div class="upload-img-box">
                                        <?php if(!empty($data[$field['name']])): ?><div class="upload-pre-item"><img src="<?php echo (get_cover($config["value"],'path')); ?>"/></div><?php endif; ?>
                                    </div>
                                </div><?php break; endswitch;?>

                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                <div class="form-item">
                    <label class="item-label"></label>
                    <div class="controls">
                        <?php if(empty($list)): ?><button type="submit" disabled class="btn submit-btn disabled" target-form="form-horizontal">确 定</button><?php else: ?><button type="submit" class="btn submit-btn ajax-post" target-form="form-horizontal">确 定</button><?php endif; ?>

                        <button class="btn btn-return" onclick="javascript:history.back(-1);
                                return false;">返 回</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl"><a href="<?php echo C('DOMAIN');?>" target="_blank"><?php echo C('SITENAME');?></a>商城系统</div>
                <div class="fr">V<?php echo (ONETHINK_VERSION); ?></div>
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "/mrkshop", //当前网站地址
            "APP"    : "/mrkshop/admin.php?s=", //当前项目地址
            "PUBLIC" : "/mrkshop/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/mrkshop/Public/static/think.js"></script>

    <script type="text/javascript" src="/mrkshop/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
			
            $subnav.find("a[href='" + url + "']").parent().addClass("current") ;

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
</body>
</html>