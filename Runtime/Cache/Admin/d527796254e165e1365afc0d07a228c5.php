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
    
    <link rel="stylesheet" type="text/css" href="/Public/Admin/js/web_code/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/js/web_code/index-admin.css" media="all">


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
    <div class="web_code">
        <div class="main-title">
            <h2><?php echo ($meta_title); ?> <a href="javascript:;;"><?php echo ($info["name"]); ?></a></h2>
          <div class="banner_box" style="height: 474px; width: 100%;overflow: hidden;">
		<div class="swiper-container">
			  <div class="swiper-wrapper">
                              <?php echo hook('Advs',array('mark'=>'index_A1','domainid'=>'','domaintype'=>'site'));?>
			  </div>
		</div>
		<div class="pagination"></div>
            </div>  
        <div class="form-item cf" style='margin-top:20px;'>
            <a class="btn btn-return" href="<?php echo U('Websetting/index');?>">返 回</a>
            <a class="btn btn-next" href="<?php echo U('AdvertisingPage/imgdetail',array('domainid'=>1,'id'=>15));?>">编辑首页轮播图</a>
            <input type="hidden" name="subdomain_id" value="2">
        </div>
    </div>
 
    <style>
    .towp{margin-left:10px;}
    .classfy1{ margin-top: 0;}
    .web_code{
        border:0px solid red;
    }
    .cla-swi, .cla-pro, .xlph, .yd_parent{
        position: relative;              

    }
    .sideNaiv .yd_parent1{
        width: 214px; 
        height: 226px;   
        position: relative; 
    }
    .sideNaiv .yd_parent2{      

        position: relative; 
    }
    .yd_edit{      
        padding: 4px 6px;
        background-color: #4bbd00;
        position: absolute;
        right: 0;
        z-index: 600;
        cursor: pointer
    }
    .tab-wrap{padding-left: 20px;}
    .cate-sortable{height: 160px; border: 2px solid #ccc;  overflow-y: scroll;}
    .cate-sortable dd{cursor: pointer; float: left;line-height: 20px; padding: 6px 20px;margin: 2px; border:1px solid #ccc; clear: right;position: relative;}
    .cate-sortable dd:hover{
        border-color: #F30;
        box-shadow: 2px 2px 0 rgba(204,204,204,0.5);
    }
    .cate-sortable dd:hover i{
        display: block;
    }
    .cate-sortable dd i{           
        background-color: #FFF;
        display: inline-block;
        margin: 0 0 6px 2px;
        padding:5px;
        border: solid 2px #FFF;
        position: relative;
        z-index: 1;
        cursor: pointer;
        border:1px solid #E06040;
        position: absolute;
        top:0;
        right:0;
        background:#E06040 url('/Public/Admin/js/web_code/colse.png') no-repeat;
        display: none;
    }
    
    .goods-sortable1 dd,.goods-sortable2 dd{overflow: hidden; height: 14px; line-height: 18px;}
    .goods-sortable1, .goods-sortable2{height: 240px; float: left;width: 48%; margin-left: 5px; border: 2px solid #ccc;  overflow-y: scroll;}
    .goods-sortable1 dd, .goods-sortable2 dd{cursor: pointer;line-height: 18px; padding: 6px 10px;margin: 2px; border:1px solid #ccc; clear: right;position: relative;}
    .goods-sortable2 dd:hover{
        border-color: #F30;
        box-shadow: 2px 2px 0 rgba(204,204,204,0.5);
    }
    .goods-sortable2 dd:hover i{
        display: block;
    }
    .goods-sortable2 dd i{           
        background-color: #FFF;
        display: inline-block;
        margin: 0 0 6px 2px;
        padding:5px;
        border: solid 2px #FFF;
        position: relative;
        z-index: 1;
        cursor: pointer;
        border:1px solid #E06040;
        position: absolute;
        top:0;
        right:0;
        background:#E06040 url('/Public/Admin/js/web_code/colse.png') no-repeat;
        display: none;
    }
    .goods-sortable3 dd,.goods-sortable4 dd{overflow: hidden; height: 14px; line-height: 18px;}
    .goods-sortable3, .goods-sortable4{height: 240px; float: left;width: 48%; margin-left: 5px; border: 2px solid #ccc;  overflow-y: scroll;}
    .goods-sortable3 dd, .goods-sortable4 dd{cursor: pointer;line-height: 18px; padding: 6px 10px;margin: 2px; border:1px solid #ccc; clear: right;position: relative;}
    .goods-sortable4 dd:hover{
        border-color: #F30;
        box-shadow: 2px 2px 0 rgba(204,204,204,0.5);
    }
    .goods-sortable4 dd:hover i{
        display: block;
    }
    .goods-sortable4 dd i{           
        background-color: #FFF;
        display: inline-block;
        margin: 0 0 6px 2px;
        padding:5px;
        border: solid 2px #FFF;
        position: relative;
        z-index: 1;
        cursor: pointer;
        border:1px solid #E06040;
        position: absolute;
        top:0;
        right:0;
        background:#E06040 url('/Public/Admin/js/web_code/colse.png') no-repeat;
        display: none;
    }
</style>  
<!--标题管理 S-->
<div id="web-code-title" style='display:none;'>
    <div class="tab-wrap">
        <form action="<?php echo U('websetting/code_update');?>" id="form-title" method="post" class="form-horizontal">
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">楼层编号： </label>
                    <input type="text" name="tit[floor]" class="text input-2x"  value="<?php echo ((isset($code_info["tit"]["code_info"]["floor"]) && ($code_info["tit"]["code_info"]["floor"] !== ""))?($code_info["tit"]["code_info"]["floor"]):'F1'); ?>">
                </div>
            </div>
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">英文名称： </label>
                    <input type="text" name="tit[en_name]" class="text input-4x"  value="<?php echo ((isset($code_info["tit"]["code_info"]["en_name"]) && ($code_info["tit"]["code_info"]["en_name"] !== ""))?($code_info["tit"]["code_info"]["en_name"]):''); ?>">
                </div>
            </div>
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">模块标题： </label>
                    <input type="text" name="tit[name]" class="text input-4x"  value="<?php echo ((isset($code_info["tit"]["code_info"]["name"]) && ($code_info["tit"]["code_info"]["name"] !== ""))?($code_info["tit"]["code_info"]["name"]):''); ?>">
                </div>
            </div>

            <div class="form-item" id="type1">
                <label class="item-label">模块LOGO<span class="check-tips">（请上传模块的lOGO 32px * 32px）</span></label>
                <div class="controls">
                    <input type="file" id="upload_picture_tit_icon">
                    <input type="hidden" name="tit[icon]" id="cover_id_tit_icon" value="<?php echo ($code_info["tit"]["code_info"]["icon"]); ?>"/>
                    <div class="upload-img-box">
                        <?php if(!empty($code_info['tit']['code_info']['icon'])): ?><div class="upload-pre-item"><img src="/Uploads/Picture//1/<?php echo (get_cover($code_info["tit"]["code_info"]["icon"],'path')); echo ($info["path"]); ?>"/></div><?php endif; ?>
                    </div>
                </div>
                <script type="text/javascript">
                    //上传图片
                    /* 初始化上传插件 */
                    $("#upload_picture_tit_icon").uploadify({
                    "height"          : 30,
                            "swf"             : "/Public/static/uploadify/uploadify.swf",
                            "fileObjName"     : "download",
                            "buttonText"      : "上传图片",
                            "uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
                            "width"           : 120,
                            'removeTimeout'	  : 1,
                            'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
                            "onUploadSuccess" : uploadPicture<?php echo ($field["name"]); ?>,
                            'onFallback' : function() {
                            alert('未检测到兼容版本的Flash.');
                            },
                            'onUploadStart':function(file){
                            var domainid = 1;
                                    $('#upload_picture_tit_icon').uploadify('settings', "formData", { 'domainid': domainid });
                            }
                    });
                            function uploadPicture<?php echo ($field["name"]); ?>(file, data){
                    var data = $.parseJSON(data);
                            var src = '';
                            if (data.status){
                    $("#cover_id_tit_icon").val(data.id);
                            var domainid = 1;
                            src = data.url || '/Uploads/Picture/' + domainid + '/' + data.path
                            $("#cover_id_tit_icon").parent().find('.upload-img-box').html(
                            '<div class="upload-pre-item"><img src="' + src + '"/></div>'
                            );
                    } else {
                    updateAlert(data.info);
                            setTimeout(function(){
                            $('#top-alert').find('button').click();
                                    $(that).removeClass('disabled').prop('disabled', false);
                            }, 1500);
                    }
                    }
                </script>
            </div>
            <div class="form-item">
                <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">
                <input type="hidden" name="code_id" value="<?php echo ($code_info["tit"]["code_id"]); ?>">
                <input type="hidden" name="var_name" value="<?php echo ((isset($code_info["tit"]["var_name"]) && ($code_info["tit"]["var_name"] !== ""))?($code_info["tit"]["var_name"]):'tit'); ?>">
                <button class="btn submit-btn ajax-post" onclick="submit_form('form-title')" type="button" target-form="form-horizontal">确 定</button>
                <button class="btn btn-return" onclick="javascript:history.back( - 1); return false;">返 回</button>
            </div>    
        </form>
    </div>
</div>
<!--标题管理 E-->

<!--左边广告管理 S-->
<div id="web-code-left-ads" style='display:none;'>
    <div class="tab-wrap">
        <form action="<?php echo U('websetting/code_update');?>" method="post" id="form-left-ads" class="form-horizontal">
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">广告名称</label>
                    <input type="text" name="left_ad[ad_name]" class="text input-4x"  value="<?php echo ((isset($code_info["left_ad"]["code_info"]["ad_name"]) && ($code_info["left_ad"]["code_info"]["ad_name"] !== ""))?($code_info["left_ad"]["code_info"]["ad_name"]):''); ?>">
                </div>
            </div>
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">广告链接<span class="check-tips">（如果为空则为分类链接）</span></label>
                    <input type="text" placeholder="http://" name="left_ad[ad_url]" class="text input-4x"  value="<?php echo ((isset($code_info["left_ad"]["code_info"]["ad_url"]) && ($code_info["left_ad"]["code_info"]["ad_url"] !== ""))?($code_info["left_ad"]["code_info"]["ad_url"]):''); ?>">
                </div>
            </div>
            <div class="form-item" id="type1">
                <label class="item-label">广告图片<span class="check-tips">（广告图片 220px * 220px）</span></label>
                <div class="controls">
                    <input type="file" id="upload_picture_lad_icon">
                    <input type="hidden" name="left_ad[icon]" id="cover_id_lad_icon" value="$code_info.left_ad.code_info.icon"/>
                    <div class="upload-img-box">
                        <?php if(!empty($code_info['left_ad']['code_info']['icon'])): ?><div class="upload-pre-item"><img src="/Uploads/Picture//1/<?php echo (get_cover($code_info["left_ad"]["code_info"]["icon"],'path')); ?>"/></div><?php endif; ?>
                    </div>
                </div>
                <script type="text/javascript">
                            //上传图片
                            /* 初始化上传插件 */
                            $("#upload_picture_lad_icon").uploadify({
                    "height"          : 30,
                            "swf"             : "/Public/static/uploadify/uploadify.swf",
                            "fileObjName"     : "download",
                            "buttonText"      : "上传图片",
                            "uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
                            "width"           : 120,
                            'removeTimeout'	  : 1,
                            'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
                            "onUploadSuccess" : uploadPicture<?php echo ($field["name"]); ?>,
                            'onFallback' : function() {
                            alert('未检测到兼容版本的Flash.');
                            },
                            'onUploadStart':function(file){
                            var domainid = 1;
                                    $('#upload_picture_lad_icon').uploadify('settings', "formData", { 'domainid': domainid });
                            }
                    });
                            function uploadPicture<?php echo ($field["name"]); ?>(file, data){
                    var data = $.parseJSON(data);
                            var src = '';
                            if (data.status){
                    $("#cover_id_lad_icon").val(data.id);
                            var domainid = 1;
                            src = data.url || '/Uploads/Picture/' + domainid + '/' + data.path
                            $("#cover_id_lad_icon").parent().find('.upload-img-box').html(
                            '<div class="upload-pre-item"><img src="' + src + '"/></div>'
                            );
                    } else {
                    updateAlert(data.info);
                            setTimeout(function(){
                            $('#top-alert').find('button').click();
                                    $(that).removeClass('disabled').prop('disabled', false);
                            }, 1500);
                    }
                    }
                </script>
            </div>
            <div class="form-item">
                <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">
                <input type="hidden" name="code_id" value="<?php echo ((isset($code_info["left_ad"]["code_id"]) && ($code_info["left_ad"]["code_id"] !== ""))?($code_info["left_ad"]["code_id"]):''); ?>">
                <input type="hidden" name="var_name" value="<?php echo ((isset($code_info["left_ad"]["var_name"]) && ($code_info["left_ad"]["var_name"] !== ""))?($code_info["left_ad"]["var_name"]):'left_ad'); ?>">
                <button class="btn submit-btn ajax-post" id="submit"  onclick="submit_form('form-left-ads')" type="button" target-form="form-horizontal">确 定</button>
            </div>    
        </form>
    </div>
</div>
<!--左边广告管理 E-->
<!--左边推荐分类管理 S-->
<div id="web-code-left-category" style='display:none;'>
    <div class="tab-wrap">
        <form action="<?php echo U('websetting/code_update');?>" method="post" id="form-left-category" class="form-horizontal">
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">筛选分类</label>
                    <select name="category_list" class=" select ">
                        <option value="0">请选择模块分类</option>
                        <?php if(is_array($category_list)): $i = 0; $__LIST__ = $category_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cate): $mod = ($i % 2 );++$i;?><option  value="<?php echo ($cate["id"]); ?>"><?php echo ($cate["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">分类列表<span class="check-tips">小提示：双击分类名称可删除不想显示的分类</span></label>
                    <dl class="cate-sortable">
                        <?php if(empty($code_info["cate_list"]["code_info"]["goods_class"])): if(is_array($category_list)): $i = 0; $__LIST__ = $category_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cate): $mod = ($i % 2 );++$i;?><dd gc_id="<?php echo ($cate["id"]); ?>" title="<?php echo ($cate["title"]); ?>" ondblclick="del_goods_class('<?php echo ($cate["id"]); ?>');">
                                    <i onclick="del_goods_class('<?php echo ($cate["id"]); ?>');"></i><?php echo ($cate["title"]); ?>          
                                    <input name="cate_list[goods_class][<?php echo ($cate["id"]); ?>][id]" value="<?php echo ($cate["id"]); ?>" type="hidden">
                                    <input name="cate_list[goods_class][<?php echo ($cate["id"]); ?>][gc_name]" value="<?php echo ($cate["title"]); ?>" type="hidden">
                                </dd>
                                <?php if(is_array($cate['_child'])): $i = 0; $__LIST__ = $cate['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$childcate): $mod = ($i % 2 );++$i;?><dd gc_id="<?php echo ($childcate["id"]); ?>" title="<?php echo ($childcate["title"]); ?>" ondblclick="del_goods_class('<?php echo ($childcate["id"]); ?>');">
                                        <i onclick="del_goods_class('<?php echo ($childcate["id"]); ?>');"></i><?php echo ($childcate["title"]); ?>          
                                        <input name="cate_list[goods_class][<?php echo ($childcate["id"]); ?>][id]" value="<?php echo ($childcate["id"]); ?>" type="hidden">
                                        <input name="cate_list[goods_class][<?php echo ($childcate["id"]); ?>][gc_name]" value="<?php echo ($childcate["title"]); ?>" type="hidden">
                                    </dd><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
                            <?php else: ?> 
                            <?php if(is_array($code_info["cate_list"]["code_info"]["goods_class"])): $i = 0; $__LIST__ = array_slice($code_info["cate_list"]["code_info"]["goods_class"],0,6,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$class): $mod = ($i % 2 );++$i;?><dd gc_id="<?php echo ($class["id"]); ?>" title="<?php echo ($class["gc_name"]); ?>" ondblclick="del_goods_class('<?php echo ($class["id"]); ?>');">
                                    <i onclick="del_goods_class('<?php echo ($class["id"]); ?>');"></i><?php echo ($class["gc_name"]); ?>          
                                    <input name="cate_list[goods_class][<?php echo ($class["id"]); ?>][id]" value="<?php echo ($class["id"]); ?>" type="hidden">
                                    <input name="cate_list[goods_class][<?php echo ($class["id"]); ?>][gc_name]" value="<?php echo ($class["gc_name"]); ?>" type="hidden">
                                </dd><?php endforeach; endif; else: echo "" ;endif; endif; ?> 
                    </dl>
                </div>
            </div>
            <div class="form-item">
                <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">
                <input type="hidden" name="code_id" value="<?php echo ((isset($code_info["cate_list"]["code_id"]) && ($code_info["cate_list"]["code_id"] !== ""))?($code_info["cate_list"]["code_id"]):''); ?>">
                <input type="hidden" name="var_name" value="<?php echo ((isset($code_info["cate_list"]["var_name"]) && ($code_info["cate_list"]["var_name"] !== ""))?($code_info["cate_list"]["var_name"]):'cate_list'); ?>">
                <button class="btn submit-btn ajax-post" id="submit"  onclick="submit_form('form-left-category')" type="button" target-form="form-horizontal">确 定</button>
            </div>    
        </form>
    </div>
</div>
<!--左边推荐分类管理 E-->
<!--中间广告管理 S-->
<div id="web-code-mid-ads" style='display:none;'>
    <div class="tab-wrap">
        <form action="<?php echo U('websetting/code_update');?>" method="post" id="form-mid-ads" class="form-horizontal">
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">广告名称</label>
                    <input type="text" name="mid_ad[ad_name]" class="text input-4x"  value="<?php echo ((isset($code_info["mid_ad"]["code_info"]["ad_name"]) && ($code_info["mid_ad"]["code_info"]["ad_name"] !== ""))?($code_info["mid_ad"]["code_info"]["ad_name"]):''); ?>">
                </div>
            </div>
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">广告链接<span class="check-tips">（如果为空则为分类链接）</span></label>
                    <input type="text" placeholder="http://" name="mid_ad[ad_url]" class="text input-4x"  value="<?php echo ((isset($code_info["mid_ad"]["code_info"]["ad_url"]) && ($code_info["mid_ad"]["code_info"]["ad_url"] !== ""))?($code_info["mid_ad"]["code_info"]["ad_url"]):''); ?>">
                </div>
            </div>
            <div class="form-item" id="type1">
                <label class="item-label">广告图片<span class="check-tips">（广告图片 220px * 220px）</span></label>
                <div class="controls">
                    <input type="file" id="upload_picture_mid_icon">
                    <input type="hidden" name="mid_ad[icon]" id="cover_id_mid_icon" value="<?php echo ($code_info["mid_ad"]["code_info"]["icon"]); ?>"/>
                    <div class="upload-img-box">
                        <?php if(!empty($code_info['mid_ad']['code_info']['icon'])): ?><div class="upload-pre-item"><img src="/Uploads/Picture//1/<?php echo (get_cover($code_info["mid_ad"]["code_info"]["icon"],'path')); ?>"/></div><?php endif; ?>
                    </div>
                </div>
                <script type="text/javascript">
                            //上传图片
                            /* 初始化上传插件 */
                            $("#upload_picture_mid_icon").uploadify({
                    "height"          : 30,
                            "swf"             : "/Public/static/uploadify/uploadify.swf",
                            "fileObjName"     : "download",
                            "buttonText"      : "上传图片",
                            "uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
                            "width"           : 120,
                            'removeTimeout'	  : 1,
                            'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
                            "onUploadSuccess" : uploadPicture<?php echo ($field["name"]); ?>,
                            'onFallback' : function() {
                            alert('未检测到兼容版本的Flash.');
                            },
                            'onUploadStart':function(file){
                            var domainid = 1;
                                    $('#upload_picture_mid_icon').uploadify('settings', "formData", { 'domainid': domainid });
                            }
                    });
                            function uploadPicture<?php echo ($field["name"]); ?>(file, data){
                    var data = $.parseJSON(data);
                            var src = '';
                            if (data.status){
                    $("#cover_id_mid_icon").val(data.id);
                            var domainid = 1;
                            src = data.url || '/Uploads/Picture/' + domainid + '/' + data.path
                            $("#cover_id_mid_icon").parent().find('.upload-img-box').html(
                            '<div class="upload-pre-item"><img src="' + src + '"/></div>'
                            );
                    } else {
                    updateAlert(data.info);
                            setTimeout(function(){
                            $('#top-alert').find('button').click();
                                    $(that).removeClass('disabled').prop('disabled', false);
                            }, 1500);
                    }
                    }
                </script>
            </div>
            <div class="form-item">
                <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">
                <input type="hidden" name="code_id" value="<?php echo ((isset($code_info["mid_ad"]["code_id"]) && ($code_info["mid_ad"]["code_id"] !== ""))?($code_info["mid_ad"]["code_id"]):''); ?>">
                <input type="hidden" name="var_name" value="<?php echo ((isset($code_info["mid_ad"]["var_name"]) && ($code_info["mid_ad"]["var_name"] !== ""))?($code_info["mid_ad"]["var_name"]):'mid_ad'); ?>">
                <button class="btn submit-btn ajax-post" id="submit"  onclick="submit_form('form-mid-ads')" type="button" target-form="form-horizontal">确 定</button>
            </div>    
        </form>
    </div>
</div>
<!--中间广告管理 E-->
<!--中间商品管理 S-->
<div id="web-code-mid-goods" style='display:none;'>
    <div class="tab-wrap">
        <form action="<?php echo U('websetting/code_update');?>" method="post" id="form-mid-goods" class="form-horizontal">
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">输入商品关键字</label>
                    <input type="text" name="mid_goods[kewords]" class="text input-4x" id="goodskeywords"  value="<?php echo ((isset($code_info["mid_goods"]["code_info"]["kewords"]) && ($code_info["mid_goods"]["code_info"]["kewords"] !== ""))?($code_info["mid_goods"]["code_info"]["kewords"]):''); ?>">
                    <button id="searchgoods"  class="btn" type="button">搜索</button>
                </div>
            </div>
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">查询商品列表<span class="check-tips">双击选中商品</span><label class="item-label" style="float:right;">添加商品列表</label></label>
                    <dl class="goods-sortable1">
                            <?php if(is_array($goods_lists)): $i = 0; $__LIST__ = $goods_lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><dd gc_id="<?php echo ($goods["id"]); ?>" title="<?php echo ($goods["title"]); ?>" ondblclick="move_goods_item('<?php echo ($goods["id"]); ?>');">
                                    <i onclick="del_goods_item('<?php echo ($goods["id"]); ?>');"></i><?php echo ($goods["title"]); ?>          
                                    <input name="temp_mid_goods[goods_list][<?php echo ($goods["id"]); ?>][id]" value="<?php echo ($goods["id"]); ?>" type="hidden">
                                    <input name="temp_mid_goods[goods_list][<?php echo ($goods["id"]); ?>][title]" value="<?php echo ($goods["title"]); ?>" type="hidden">
                                    <input name="temp_mid_goods[goods_list][<?php echo ($goods["id"]); ?>][price]" value="<?php echo ($goods["price"]); ?>" type="hidden">
                                    <input name="temp_mid_goods[goods_list][<?php echo ($goods["id"]); ?>][cover_id]" value="<?php echo ($goods["cover_id"]); ?>" type="hidden">
                                    <input name="temp_mid_goods[goods_list][<?php echo ($goods["id"]); ?>][marketprice]" value="<?php echo ($goods["marketprice"]); ?>" type="hidden">
                                </dd><?php endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                     
                    <dl class="goods-sortable2">
                        <?php if(is_array($code_info["mid_goods"]["code_info"]["goods_list"])): $i = 0; $__LIST__ = $code_info["mid_goods"]["code_info"]["goods_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><dd gc_id="<?php echo ($goods["id"]); ?>" title="<?php echo ($goods["title"]); ?>" ondblclick="del_goods_item('<?php echo ($goods["id"]); ?>');">
                                    <i onclick="del_goods_item('<?php echo ($class["id"]); ?>');"></i><?php echo ($goods["title"]); ?>          
                                    <input name="mid_goods[goods_list][<?php echo ($goods["id"]); ?>][id]" value="<?php echo ($goods["id"]); ?>" type="hidden">
                                    <input name="mid_goods[goods_list][<?php echo ($goods["id"]); ?>][title]" value="<?php echo ($goods["title"]); ?>" type="hidden">
                                    <input name="mid_goods[goods_list][<?php echo ($goods["id"]); ?>][price]" value="<?php echo ($goods["price"]); ?>" type="hidden">
                                    <input name="mid_goods[goods_list][<?php echo ($goods["id"]); ?>][cover_id]" value="<?php echo ($goods["cover_id"]); ?>" type="hidden">
                                    <input name="mid_goods[goods_list][<?php echo ($goods["id"]); ?>][marketprice]" value="<?php echo ($goods["marketprice"]); ?>" type="hidden">
                                </dd><?php endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                </div>
            </div>
            <div class="form-item">
                <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">
                <input type="hidden" name="code_id" value="<?php echo ((isset($code_info["mid_goods"]["code_id"]) && ($code_info["mid_goods"]["code_id"] !== ""))?($code_info["mid_goods"]["code_id"]):''); ?>">
                <input type="hidden" name="var_name" value="<?php echo ((isset($code_info["mid_goods"]["var_name"]) && ($code_info["mid_goods"]["var_name"] !== ""))?($code_info["mid_goods"]["var_name"]):'mid_goods'); ?>">
                <button class="btn submit-btn ajax-post" id="submit"  onclick="submit_form('form-mid-goods')" type="button" target-form="form-horizontal">确 定</button>
            </div>    
        </form>
    </div>
</div>
<!--中间商品管理 E-->
<!--右边热卖商品管理 S-->
<div id="web-code-right-hotgoods" style='display:none;'>
    <div class="tab-wrap">
        <form action="<?php echo U('websetting/code_update');?>" method="post" id="form-hot-goods" class="form-horizontal">
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">输入商品关键字</label>
                    <input type="text" name="hot_goods[kewords]" class="text input-4x" id="goodskeywords2"  value="<?php echo ((isset($code_info["hot_goods"]["code_info"]["kewords"]) && ($code_info["hot_goods"]["code_info"]["kewords"] !== ""))?($code_info["hot_goods"]["code_info"]["kewords"]):''); ?>">
                    <button id="searchgoods2"  class="btn" type="button">搜索</button>
                </div>
            </div>
            <div class="form-item">
                <div class="controls">
                    <label class="item-label">查询商品列表<span class="check-tips">双击选中商品</span><label class="item-label" style="float:right;">添加商品列表</label></label>
                    <dl class="goods-sortable3">
                            <?php if(is_array($goods_lists)): $i = 0; $__LIST__ = $goods_lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><dd gc_id="<?php echo ($goods["id"]); ?>" title="<?php echo ($goods["title"]); ?>" ondblclick="move_goods_item2('<?php echo ($goods["id"]); ?>');">
                                    <i onclick="del_goods_item2('<?php echo ($goods["id"]); ?>');"></i><?php echo ($goods["title"]); ?>          
                                    <input name="temp_hot_goods[goods_list][<?php echo ($goods["id"]); ?>][id]" value="<?php echo ($goods["id"]); ?>" type="hidden">
                                    <input name="temp_hot_goods[goods_list][<?php echo ($goods["id"]); ?>][title]" value="<?php echo ($goods["title"]); ?>" type="hidden">
                                    <input name="temp_hot_goods[goods_list][<?php echo ($goods["id"]); ?>][price]" value="<?php echo ($goods["price"]); ?>" type="hidden">
                                    <input name="temp_hot_goods[goods_list][<?php echo ($goods["id"]); ?>][cover_id]" value="<?php echo ($goods["cover_id"]); ?>" type="hidden">
                                    <input name="temp_hot_goods[goods_list][<?php echo ($goods["id"]); ?>][marketprice]" value="<?php echo ($goods["marketprice"]); ?>" type="hidden">
                                </dd><?php endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                     
                    <dl class="goods-sortable4">
                        <?php if(is_array($code_info["hot_goods"]["code_info"]["goods_list"])): $i = 0; $__LIST__ = $code_info["hot_goods"]["code_info"]["goods_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><dd gc_id="<?php echo ($goods["id"]); ?>" title="<?php echo ($goods["title"]); ?>" ondblclick="del_goods_item2('<?php echo ($goods["id"]); ?>');">
                                    <i onclick="del_goods_item2('<?php echo ($class["id"]); ?>');"></i><?php echo ($goods["title"]); ?>          
                                    <input name="hot_goods[goods_list][<?php echo ($goods["id"]); ?>][id]" value="<?php echo ($goods["id"]); ?>" type="hidden">
                                    <input name="hot_goods[goods_list][<?php echo ($goods["id"]); ?>][title]" value="<?php echo ($goods["title"]); ?>" type="hidden">
                                    <input name="hot_goods[goods_list][<?php echo ($goods["id"]); ?>][price]" value="<?php echo ($goods["price"]); ?>" type="hidden">
                                    <input name="hot_goods[goods_list][<?php echo ($goods["id"]); ?>][cover_id]" value="<?php echo ($goods["cover_id"]); ?>" type="hidden">
                                    <input name="hot_goods[goods_list][<?php echo ($goods["id"]); ?>][marketprice]" value="<?php echo ($goods["marketprice"]); ?>" type="hidden">
                                </dd><?php endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                </div>
            </div>
            <div class="form-item">
                <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">
                <input type="hidden" name="code_id" value="<?php echo ((isset($code_info["hot_goods"]["code_id"]) && ($code_info["hot_goods"]["code_id"] !== ""))?($code_info["hot_goods"]["code_id"]):''); ?>">
                <input type="hidden" name="var_name" value="<?php echo ((isset($code_info["hot_goods"]["var_name"]) && ($code_info["hot_goods"]["var_name"] !== ""))?($code_info["hot_goods"]["var_name"]):'hot_goods'); ?>">
                <button class="btn submit-btn ajax-post" id="submit"  onclick="submit_form('form-hot-goods')" type="button" target-form="form-horizontal">确 定</button>
            </div>    
        </form>
    </div>
</div>
<!--→右边热卖商品管理 E-->


    <script type="text/javascript" src="/Public/static/layer/layer.js"></script>
    <script type="text/javascript" charset="utf-8">
 var domainid = "<?php echo ($info["id"]); ?>";
    $("#submit-tit").click(function(){
        $.ajax({
            type: 'POST',
            url: $("#form-title").attr('action'),
            data:$("#form-title").serialize(),
            success: function(res){
                if (res.status){
                    layer.msg(res.info);
                } else{
                    layer.msg(res.info);
                }
            }
        });
    });
            // 提交表单
    function submit_form(form_id){
    $.ajax({
        type: 'POST',
        url: $("#" + form_id).attr('action'),
        data:$("#" + form_id).serialize(),
        success: function(res){
            if (res.status){
                    layer.msg(res.info);
                    setTimeout("window.location.reload(true)", 1000);
            } else{
                layer.msg(res.info);
            }
        }
    });
    }

    // 显示提示框
    function show_dialog(title, width, height, model_id, code_id){
    $("#" + model_id).find("input[name=code_id]").val(code_id);
        var layer_index = layer.open({
            title:"<?php echo ($info["name"]); ?>．" + title,
            type: 1,
            area: [width, height],
            shadeClose: true, //点击遮罩关闭
            content: $("#" + model_id)
        });
    }


    function del_goods_class(gc_id){
        var obj = $("dd[gc_id='" + gc_id + "']");
        obj.remove();
    }
    
    function del_goods_item(gc_id){
        var obj = $(".goods-sortable2").find("dd[gc_id='" + gc_id + "']");
        obj.remove();
    }
    
    function move_goods_item(goods_id){
        var obj = $(".goods-sortable1").find("dd[gc_id='" + goods_id + "']");
        obj.attr("ondblclick").replace("move_goods_item","del_goods_class");
        var _tmp =  obj.html().replace("temp_mid_goods", 'mid_goods').replace("temp_mid_goods", 'mid_goods').replace("temp_mid_goods", 'mid_goods').replace("temp_mid_goods", 'mid_goods').replace("temp_mid_goods", 'mid_goods');
        obj.html(_tmp);
        var t= $(".goods-sortable2").html();
        if(t.length > 45){
           var gc_id = $(".goods-sortable2").find("dd[gc_id='" + goods_id + "']").attr('gc_id');
            if(eval(gc_id) != goods_id){
                   obj.appendTo(".goods-sortable2");
               }else{
                   layer.msg('该商品已经添加'); 
                   $(".goods-sortable1").find("dd[gc_id='" + goods_id + "']").remove();
                    return false;
               }
//            $(".goods-sortable2 dd").each(function(index){
//                var gc_id = $(this).attr("gc_id");
//               if(eval(gc_id) != goods_id){
//                   obj.appendTo(".goods-sortable2");
//               }else{
//                   layer.msg('该商品已经添加'); 
//                   
//                    return false;
////                   $(".goods-sortable1").find("dd[gc_id='" + goods_id + "']").remove();
//               }
//            })
        }else{
           obj.appendTo(".goods-sortable2"); 
        }
    }
    /**
     * 删除热销商品
     */
    function del_goods_item2(gc_id){
        var obj = $(".goods-sortable4").find("dd[gc_id='" + gc_id + "']");
        obj.remove();
    }
    /**
     * 热销商品
     */
    function move_goods_item2(goods_id){
        var obj = $(".goods-sortable3").find("dd[gc_id='" + goods_id + "']");
        obj.attr("ondblclick").replace("move_goods_item2","del_goods_item2");
        var _tmp =  obj.html().replace("temp_hot_goods", 'hot_goods').replace("temp_hot_goods", 'hot_goods').replace("temp_hot_goods", 'hot_goods').replace("temp_hot_goods", 'hot_goods').replace("temp_hot_goods", 'hot_goods');
        obj.html(_tmp);
        var t= $(".goods-sortable4").html();
        if(t.length > 45){
             var gc_id = $(".goods-sortable4").find("dd[gc_id='" + goods_id + "']").attr('gc_id');
             if(eval(gc_id) != goods_id){
                   obj.appendTo(".goods-sortable4");
               }else{
                   layer.msg('该商品已经添加2');
                   $(".goods-sortable3").find("dd[gc_id='" + goods_id + "']").remove();
                   return false;
               }
        }else{
           obj.appendTo(".goods-sortable4"); 
        }
    }
    // 热卖商品
    $("#searchgoods2").click(function(){
        var keywords = $("#goodskeywords2").val();
        if(keywords.length<0){
            layer.msg('请输入关键词！');
            return false;
        }
        $.ajax({
            type:"POST",
            url:"<?php echo U('websetting/getgoods_list');?>",
            data:{keywords:keywords,domainid:domainid},
            success:function(res){
                if(res.status){
                    $.each(res.info,function(index,item){
                        var dd = $(".goods-sortable4").find("dd[gc_id="+item.id+"]").html();
                        var dd1 = $(".goods-sortable3").find("dd[gc_id="+item.id+"]").html();
                        if(typeof(dd1) == 'undefined'){ // typeof(dd) == 'undefined' || 
                            var temp_dd = "<dd gc_id='"+item.id+"' title='"+item.title+"' ondblclick='move_goods_item2("+item.id+")'>"+
                                            "<input name='temp_hot_goods[goods_list]["+item.id+"][id]' value='"+item.id+"' type='hidden' />"+
                                            "<input name='temp_hot_goods[goods_list]["+item.id+"][title]' value='"+item.title+"' type='hidden' />"+
                                            "<input name='temp_hot_goods[goods_list]["+item.id+"][price]' value='"+item.price+"' type='hidden' />"+
                                            "<input name='temp_hot_goods[goods_list]["+item.id+"][cover_id]' value='"+item.cover_id+"' type='hidden' />"+
                                            "<input name='temp_hot_goods[goods_list]["+item.id+"][marketprice]' value='"+item.marketprice+"' type='hidden' />"+
                                            "<i onclick='del_goods_item2("+item.id+")'></i>"+item.title+"</dd>";
                            $(".goods-sortable3").append(temp_dd);
                        }
                    });
                }else{
                    layer.msg("没有数据");
                }
            }
        })
    });
    // 搜索商品
    $("#searchgoods").click(function(){
        var keywords = $("#goodskeywords").val();
        if(keywords.length<0){
            layer.msg('请输入关键词！');
            return false;
        }
        
        $.ajax({
            type:"POST",
            url:"<?php echo U('websetting/getgoods_list');?>",
            data:{keywords:keywords,domainid:domainid},
            success:function(res){
                if(res.status){
                    $.each(res.info,function(index,item){
                        var dd = $(".goods-sortable2").find("dd[gc_id="+item.id+"]").html();
                        var dd1 = $(".goods-sortable1").find("dd[gc_id="+item.id+"]").html();
                        if(typeof(dd1) == 'undefined'){ // typeof(dd) == 'undefined' || 
                            var temp_dd = "<dd gc_id='"+item.id+"' title='"+item.title+"' ondblclick='move_goods_item("+item.id+")'>"+
                                            "<input name='temp_mid_goods[goods_list]["+item.id+"][id]' value='"+item.id+"' type='hidden' />"+
                                            "<input name='temp_mid_goods[goods_list]["+item.id+"][title]' value='"+item.title+"' type='hidden' />"+
                                            "<input name='temp_mid_goods[goods_list]["+item.id+"][price]' value='"+item.price+"' type='hidden' />"+
                                            "<input name='temp_mid_goods[goods_list]["+item.id+"][cover_id]' value='"+item.cover_id+"' type='hidden' />"+
                                            "<input name='temp_mid_goods[goods_list]["+item.id+"][marketprice]' value='"+item.marketprice+"' type='hidden' />"+
                                            "<i onclick='del_goods_item("+item.id+")'></i>"+item.title+"</dd>";
                            $(".goods-sortable1").append(temp_dd);
                        }
                    });
                }else{
                    layer.msg("没有数据");
                }
            }
        })
    });
    
    //筛选分类
    $("select[name=category_list]").change(function(){
        var cate_id = $(this).val();
        $.ajax({
            type: 'POST',
            url: "<?php echo U('websetting/getCategory_list');?>",// "<?php echo ('websetting/getCategory_list');?>",
            data:{domainid:domainid,pid:cate_id},
            success: function(res){
                if (res.status){
                    $.each(res.info,function(index,item){
                        var dd = $(".cate-sortable").find("dd[gc_id="+item.id+"]").html();
                        if(typeof(dd) == 'undefined'){
                            var temp_dd = "<dd gc_id='"+item.id+"' title='"+item.title+"' ondblclick='del_goods_class("+item.id+")'>"+
                                            "<input name='cate_list[goods_class]["+item.id+"][id]' value='"+item.id+"' type='hidden' />"+
                                            "<input name='cate_list[goods_class]["+item.id+"][gc_name]' value='"+item.title+"' type='hidden' />"+
                                            "<i onclick='del_goods_class("+item.id+")'></i>"+item.title+"</dd>";
                            $(".cate-sortable").append(temp_dd);
                        }
                    });
                } else{
                    layer.msg('没有数据');
                }
            }
        });
    });
    </script>


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
    
    <script>
            //导航高亮
        highlight_subnav('<?php echo U("Websetting/banner");?>');
    </script>

</body>
</html>