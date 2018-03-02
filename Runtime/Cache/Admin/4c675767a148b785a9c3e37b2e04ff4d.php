<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|管理平台</title>
    <link href="/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.mousewheel.js"></script>
 <script type="text/javascript" src="/Public/Admin/js/highcharts.js"></script>
<script type="text/javascript" src="/Public/Admin/js/exporting.js"></script>
<script type="text/javascript" src="/Public/Admin/js/data.js"></script>
    <!--<![endif]-->
    
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <!--<span class="logo"><img src="/Public/Admin/images/logo.png" ></span>-->
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
            

            
    <script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>
		<script type="text/javascript" src="/Public/static/UploadImages.js"></script>
    <div class="main-title">
        <h2><?php echo ($meta_title); ?></h2>
    </div>
    <div class="tab-wrap">
        <!--        <ul class="tab-nav nav">
                    <li data-tab="tab1" class="current"><a href="javascript:void(0);">基 础</a></li>
                </ul>-->
        <div class="tab-content">
            <form action="<?php echo U();?>" method="post" class="form-horizontal">
                <!-- 基础 -->
                <div id="tab1" class="tab-pane in tab1">
                    <div class="form-item">
                        <label class="item-label">
                            活动名称<span class="check-tips">（活动名称将显示在限时折扣活动列表中，方便商家管理使用，最多可输入25个字符。）</span>
                        </label>
                        <div class="controls">
                            <input type="text" name="xianshi_name" class="text input-large" value="<?php echo ((isset($info["xianshi_name"]) && ($info["xianshi_name"] !== ""))?($info["xianshi_name"]):''); ?>">
                        </div>
                    </div>

                    <div class="form-item">
                        <label class="item-label">活动标题<span class="check-tips">（活动标题是商家对限时折扣活动的别名操作，请使用例如“新品打折”、“月末折扣”类短语表现，最多可输入10个字符；
                                非必填选项，留空商品优惠价格前将默认显示“限时折扣”字样。）</span></label>
                        <div class="controls">
                            <input type="text" name="xianshi_title" class="text input-large" value="<?php echo ((isset($info["xianshi_title"]) && ($info["xianshi_title"] !== ""))?($info["xianshi_title"]):''); ?>">
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="item-label">活动描述<span class="check-tips">（活动描述是商家对限时折扣活动的补充说明文字，在商品详情页-优惠信息位置显示；
                                非必填选项，最多可输入30个字符。）</span></label>
                        <div class="controls">
                            <input type="text" name="xianshi_explain" class="text input-large" value="<?php echo ((isset($info["xianshi_explain"]) && ($info["xianshi_explain"] !== ""))?($info["xianshi_explain"]):''); ?>">
                        </div>
                    </div>
                     <div class="form-item">
                          <label class="item-label">活动Banner<span style="margin-left:0;" class="check-tips">（活动Banner）</span></label>
                        <div class="controls">
				<div class="controls">
                                     <input type="file" id="upload_picture_cover_id">
                                            <input type="hidden" id="cover_id_cover_id" name="banner">
                                                <?php if(empty($info["banner"])): ?><div class="upload-img-box"></div>
                                                <?php else: ?> 
                                                 <div class="upload-img-box">
                                                     <div class="upload-pre-item">
                                                     <img style="height: 100px;width: auto;" src="/Uploads/Picture//1/<?php echo (get_cover($info["banner"],'path')); ?>" data-id="<?php echo ($info["banner"]); ?>"/>
                                                      </div>
                                                        <span class='btn-close btn-close-cover_id' title='删除图片'></span>
                                                    </div><?php endif; ?> 
                                            
					    <script type="text/javascript">
					    //上传图片
					    /* 初始化上传插件 */
					    var domainid= '1';
                                            var fieldname = "cover_id";
					    $("#upload_picture_cover_id").uploadify({
						"height"          : 30,
						"swf"             : "/Public/static/uploadify/uploadify.swf",
						"fileObjName"     : "download",
						"buttonText"      : "上传图片",
						"uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
						"width"           : 120,
						'removeTimeout'	  : 1,
						'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
						"onUploadSuccess" : uploadPicturecover_id,
						'formData'      : {'domainid' :domainid,'fields':fieldname,'pagefrom':'activity'},
						'onFallback' : function() {
						    alert('未检测到兼容版本的Flash.');
						}
					    });
					    function uploadPicturecover_id(file, data){
					    var data = $.parseJSON(data);
					    var src = '';
                                            if(data.status){
                                                src = data.url || '/Uploads/Picture//'+domainid+'/'+ data.path;
						upload_img = "<div class='upload-pre-item'><img src=" + src +" title='点击显示大图' data-id="+data.id+"> <span class='btn-close btn-close-banner' title='删除图片'></span></div>";
						picsbox = $("#cover_id_cover_id").parent().find('.upload-img-box');
						picsbox.append(upload_img);
                                                $("#cover_id_cover_id").val(data.id);
					    } else {
						    updateAlert(data.info);
						    setTimeout(function(){
						    $('#top-alert').find('button').click();
						    $(that).removeClass('disabled').prop('disabled',false);
						},1500);
					    }
					}
					</script>
			    </div>
                    </div>
                    <div class="form-item">
                        <?php
 $promote_start_time= $info['start_time'] ? date('Y-m-d H:i',$info['start_time']):date('Y-m-d H:i'); $promote_end_time= $info['end_time'] ? date('Y-m-d H:i',$info['end_time']):date('Y-m-d H:i',time()+3600*24*15); ?>
                        <label class="item-label">开始时间<span class="check-tips">（开始时间不能为空且不能早于2014-01-08 15:46）</span></label>
                        <div class="controls">
                            <input type="text" name="start_time" class="text time input-large" value="<?php echo ($promote_start_time); ?>">
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="item-label">结束时间<span class="check-tips">（结束时间不能为空且不能晚于2019-12-31 00:00）</span></label>
                        <div class="controls">
                            <input type="text" name="end_time" class="text time input-large" value="<?php echo ($promote_end_time); ?>">
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="item-label">默认折扣<span class="check-tips">（名称不能为空）</span></label>
                        <div class="controls">
                            <input type="text" name="discount" class="text input-large" value="<?php echo ((isset($info["discount"]) && ($info["discount"] !== ""))?($info["discount"]):'0.98'); ?>">
                        </div>
                    </div>

                    <div class="form-item">
                        <label class="item-label">活动状态<span class="check-tips">(是否启用)</span> </label>
                        <div class="controls">
                            <select name="status">
                                <?php if(is_array($status)): foreach($status as $key=>$item): ?><option <?php if(($info['status']) == $key): ?>selected="selected"<?php endif; ?> value="<?php echo ($key); ?>"><?php echo ($item); ?></option><?php endforeach; endif; ?>
                            </select>				
                        </div>
                    </div>


                </div>
                <div class="form-item">
                    <input type="hidden" name="xianshi_id" value="<?php echo ((isset($info["xianshi_id"]) && ($info["xianshi_id"] !== ""))?($info["xianshi_id"]):''); ?>">
                    <button type="submit" id="submit" class="btn submit-btn ajax-post" target-form="form-horizontal">确 定</button>
                    <button class="btn btn-return" onclick="javascript:history.back( - 1);
                                return false;">返 回</button>
                </div>
            </form>
            
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
            "ROOT"   : "", //当前网站地址
            "APP"    : "", //当前项目地址
            "PUBLIC" : "/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/Public/static/think.js"></script>

    <script type="text/javascript" src="/Public/Admin/js/common.js"></script>
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
    
                <link href="/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
                <?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>
                <link href="/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
                <script type="text/javascript" src="/Public/static/jquery.dragsort-0.5.1.min.js"></script>
                <script type="text/javascript" src="/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
                <script type="text/javascript" src="/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
                <script>
            $(function() {
            $('.date').datetimepicker({
            format: 'yyyy-mm-dd',
                    language: "zh-CN",
                    minView: 2,
                    autoclose: true
            });
                    $('.time').datetimepicker({
            language: "zh-CN",
//          minView: 2,
                    controlType: 'select',
                    autoclose: true
            });
            });</script>
            
</body>
</html>