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
            

            
    <script type="text/javascript" src="/mrkshop/Public/static/uploadify/jquery.uploadify.min.js"></script>
    <script type="text/javascript" src="/mrkshop/Public/static/UploadImages.js"></script>
    <div class="main-title cf">
	<h2>
	    编辑<?php echo (get_document_model($data["model_id"],'title')); ?> [
	    <?php if(is_array($rightNav)): $i = 0; $__LIST__ = $rightNav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i;?><a href="<?php echo U('goods/index','cate_id='.$nav['id']);?>"><?php echo ($nav["title"]); ?></a>
	    <?php if(count($rightNav) > $i): ?><i class="ca"></i><?php endif; endforeach; endif; else: echo "" ;endif; ?>
	    <?php if(isset($article)): ?>：<a href="<?php echo U('goods/index','cate_id='.$data['category_id'].'&pid='.$article['id']);?>"><?php echo ($article["title"]); ?></a><?php endif; ?>
	    ]
	</h2>
    </div>
	<!-- 标签页导航 -->
    <div class="tab-wrap">
	<ul class="tab-nav nav">
	    <?php $_result=parse_config_attr($model['field_group']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><li data-tab="tab<?php echo ($key); ?>" <?php if(($key) == "1"): ?>class="current"<?php endif; ?>><a href="javascript:void(0);"><?php echo ($group); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	    <li data-tab="tab<?php echo (1+count(parse_config_attr($model['field_group'])));?>" class=""><a href="javascript:void(0);">自定义属性</a></li>
	    <!--<li data-tab="tab<?php echo (2+count(parse_config_attr($model['field_group'])));?>" class=""><a href="javascript:void(0);">关联文章</a></li>-->
	</ul>
	<div class="tab-content">
	    <!-- 表单 -->
	    <form id="form" action="<?php echo U('update');?>" method="post" class="form-horizontal">
		<!-- 基础文档模型 -->
		<?php $_result=parse_config_attr($model['field_group']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><div id="tab<?php echo ($key); ?>" class="tab-pane <?php if(($key) == "1"): ?>in<?php endif; ?> tab<?php echo ($key); ?>">
                        <?php if(($key) == "1"): ?><div class="form-item">
			    <label class="item-label item-label_f">所属分类</label>
			    <div class="controls">
				<select name="domainid" class="select required" onchange="getcategory (this.options[this.options.selectedIndex].value,'ke_child','请选择')">
				    <!--<option value="" >请选择分类</option>-->
				    <?php if(is_array($domainlist)): foreach($domainlist as $zhong_key=>$c_zhong): ?><option value="<?php echo ($c_zhong['id']); ?>" <?php if(($data['domainid'] == $c_zhong['id']) || ($ke_id == $c_zhong['id'])): ?>selected<?php endif; ?> ><?php echo ($c_zhong['name']); ?></option><?php endforeach; endif; ?>
				</select>
				<select name="pid" class="select required" id="ke_child" onchange="getchildren (this.options[this.options.selectedIndex].value,'shu_child','请选择')">
				   <?php if(is_array($category_list)): foreach($category_list as $shu_key=>$c_shu): ?><option value="<?php echo ($c_shu['id']); ?>" <?php if(($data['pid'] == $c_shu['id']) || ($shu_id == $c_shu['id'])): ?>selected<?php endif; ?> ><?php echo ($c_shu['title']); ?></option><?php endforeach; endif; ?>
				</select>
				<select name="category_id" class="select required" id="shu_child">
                                    <?php if(!empty($subcategory_list)): if(is_array($subcategory_list)): foreach($subcategory_list as $zhong_key=>$c_zhong): ?><option value="<?php echo ($c_zhong['id']); ?>" <?php if(($data[category_id] == $c_zhong['id']) || ($zhong_id == $c_zhong['id'])): ?>selected<?php endif; ?> ><?php echo ($c_zhong['title']); ?></option><?php endforeach; endif; ?>
                                     <?php else: ?>
                                     <option>请选择</option><?php endif; ?>
				</select>
			    </div>
			</div><?php endif; ?>
                        
			<?php if(is_array($fields[$key])): $i = 0; $__LIST__ = $fields[$key];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($i % 2 );++$i; if($field['is_show'] == 1 || $field['is_show'] == 3): ?><div class="form-item cf">
				<label class="item-label item-label_f"><?php echo ($field['title']); ?></label>
				<div class="controls">
				    <?php switch($field["type"]): case "pictures": ?><!-- 多图上传 --> 
					    <input type="file" id="upload_picture_<?php echo ($field["name"]); ?>">
					    <input type="hidden" name="<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>" class="icon <?php echo ($field["name"]); ?>" />
					    <?php $valArr= $data[$field['name']] ? explode(',',$data[$field['name']]):array(); ?>
					    <div class="upload-img-box">
						<?php if(!empty($valArr)): if(is_array($valArr)): $i = 0; $__LIST__ = $valArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="upload-pre-item">
							    <img src="/mrkshop/Uploads/Picture/<?php echo (get_cover($v,'path')); ?>" data-id="<?php echo ($v); ?>"/>
							    <span class='btn-close btn-close-<?php echo ($field["name"]); ?>' title='删除图片'></span>
							</div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
					    </div>
					    <script type="text/javascript">
						//多图上传图片
						$(function(){
						    /* 初始化上传插件*/
                var fieldname = "<?php echo ($field['name']); ?>";
						    $("#upload_picture_<?php echo ($field["name"]); ?>").uploadify({
							"height"          : 30,
							"swf"             : "/mrkshop/Public/static/uploadify/uploadify.swf",
							"fileObjName"     : "download",
							"buttonText"      : "上传图片",
							"uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id(),'fields'=>$field['name'],'pagefrom'=>'goods'));?>",
							"width"           : 120,
							'removeTimeout'   : 1,
							'fileTypeExts'    : '*.jpg; *.png; *.gif;',
							"onUploadSuccess" : uploadPicture<?php echo ($field["name"]); ?>,
							'formData'      : {'fields':fieldname,'pagefrom':'goods'},
							'onFallback' : function() {
							    alert('未检测到兼容版本的Flash.');
							}
						    });
						    $('.btn-close-<?php echo ($field["name"]); ?>').click(function(event) {
							event.preventDefault();
							$(this).parent().remove();
							picsbox = $("#upload_picture_<?php echo ($field["name"]); ?>").siblings('.upload-img-box');
							picArr = [];
							for (var i = 0; i < picsbox.children().length ; i++) {
							    picArr.push(picsbox.children('.upload-pre-item:eq('+i+')').find('img').attr('data-id'));
							};
							picStr = picArr.join(',');
							$('.icon.<?php echo ($field["name"]); ?>').val(picStr);
						    });
						});
						function uploadPicture<?php echo ($field["name"]); ?>(file, data){
						    var data = $.parseJSON(data);
						    var src = '';
						    if(data.status){
							src = data.url || '/mrkshop/Uploads/Picture/'+ data.path;
							upload_img = "<div class='upload-pre-item'><img src=" + src +" title='点击显示大图' data-id="+data.id+"> <span class='btn-close btn-close-<?php echo ($field["name"]); ?>' title='删除图片'></span></div>";
							picsbox = $("#upload_picture_<?php echo ($field["name"]); ?>").siblings('.upload-img-box');
							picsbox.append(upload_img)
							picArr = [];
							for (var i = 0; i < picsbox.children().length ; i++) {
							    picArr.push(picsbox.children('.upload-pre-item:eq('+i+')').find('img').attr('data-id'));
							};
							picStr = picArr.join(',');
							$('.icon.<?php echo ($field["name"]); ?>').val(picStr);
						    } else {
							updateAlert(data.info);
							setTimeout(function(){
							    $('#top-alert').find('button').click();
							    $(that).removeClass('disabled').prop('disabled',false);
							},1500);
						    }
						}
					    </script><?php break;?>
	    
					<?php case "num": ?><input type="text" class="text input-mid" name="<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>"><?php break;?>
					<?php case "string": ?><input type="text" class="text input-large" name="<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>">
					    <?php if($field['name'] == 'price'): ?><!--价格后面添加点击计算市场价格-->
					    <a class="market_price_setted" href="javascript:;" style="cursor: pointer;">点击按市场价计算</a>
                                            &nbsp;&nbsp;
                                            <a href="javascript:;;" class="set_member_level_price">设置会员等级价格</a>
                                            <div id='member_level_price_box'>
                                                <?php if(!empty($member_level_price)): if(is_array($level_list)): $i = 0; $__LIST__ = $level_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i; switch($item["member_type"]): case "1": ?><div class="form-item cf">
                                                                <label class="item-label item-label_f">代理</label>
                                                                <label class="item-label item-label_f"><?php echo ($item["level_name"]); ?></label>
                                                                <div class="controls">
                                                                    <input type="text" class="text input-large" name="member_price[<?php echo ($item["id"]); ?>]" value="<?php echo ($member_level_price[$item['id']]); ?>">
                                                                    <span class="check-tips" style="margin-left:0;"></span>
                                                                </div>
                                                            </div><?php break;?>
                                                        <?php case "2": ?><div class="form-item cf">
                                                                <label class="item-label item-label_f">门店</label>
                                                                <label class="item-label item-label_f"><?php echo ($item["level_name"]); ?></label>
                                                                <div class="controls">
                                                                    <input type="text" class="text input-large" name="member_price[<?php echo ($item["id"]); ?>]" value="<?php echo ($member_level_price[$item['id']]); ?>">
                                                                    <span class="check-tips" style="margin-left:0;"></span>
                                                                </div>
                                                            </div><?php break;?>
                                                        <?php case "3": ?><div class="form-item cf">
                                                                <label class="item-label item-label_f">消费者</label>
                                                                <label class="item-label item-label_f"><?php echo ($item["level_name"]); ?></label>
                                                                <div class="controls">
                                                                    <input type="text" class="text input-large" name="member_price[<?php echo ($item["id"]); ?>]" value="<?php echo ($member_level_price[$item['id']]); ?>">
                                                                    <span class="check-tips" style="margin-left:0;"></span>
                                                                </div>
                                                            </div><?php break;?>
                                                        <?php default: ?>default<?php endswitch; endforeach; endif; else: echo "" ;endif; endif; ?>
                                                
                                            </div><?php endif; break;?>
					<?php case "textarea": ?><label class="textarea input-large">
					    <textarea name="<?php echo ($field["name"]); ?>"><?php echo ($data[$field['name']]); ?></textarea>
					    </label><?php break;?>
					<?php case "date": ?><input type="text" name="<?php echo ($field["name"]); ?>" class="text date" value="<?php echo (date('Y-m-d',$data[$field['name']])); ?>" placeholder="请选择日期" /><?php break;?>
					<?php case "datetime": ?><input type="text" name="<?php echo ($field["name"]); ?>" class="text time" value="<?php echo (time_format($data[$field['name']])); ?>" placeholder="请选择时间" /><?php break;?>
					<?php case "bool": ?><select name="<?php echo ($field["name"]); ?>">
						<?php $_result=parse_field_attr($field['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($data[$field['name']]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
					    </select><?php break;?>
					<?php case "select": ?><select name="<?php echo ($field["name"]); ?>">
						<?php if($field['name'] == 'product_type'): ?><!--商品类型修改 更换成选择商品类型  普通商品 跨境商品-->
						    <?php if(is_array($goodtypelist)): $i = 0; $__LIST__ = $goodtypelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($data[$field['name']]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                                    <?php elseif($field['name'] == 'warehouse'): ?>
                                                        <?php if(is_array($distribution)): $i = 0; $__LIST__ = $distribution;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($data[$field['name']]) == $vo["id"]): ?>selected<?php endif; ?>><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                                    <?php elseif($field['name'] == 'brandid'): ?>
                                                       <option value="0" <?php if(($data[$field['name']]) == "0"): ?>selected<?php endif; ?>>--请选择--</option>
						    <?php if(is_array($channelbrandlist)): $i = 0; $__LIST__ = $channelbrandlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($data[$field['name']]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						<?php else: ?>
						    <?php $_result=parse_field_attr($field['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($data[$field['name']]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; ?>
					    </select><?php break;?>
					<?php case "radio": $_result=parse_field_attr($field['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label class="radio">
						<input type="radio" value="<?php echo ($key); ?>" name="<?php echo ($field["name"]); ?>" <?php if(($data[$field['name']]) == $key): ?>checked="checked"<?php endif; ?>><?php echo ($vo); ?>
						    </label><?php endforeach; endif; else: echo "" ;endif; break;?>
					<?php case "checkbox": $_result=parse_field_attr($field['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label class="checkbox">
						<input type="checkbox" value="<?php echo ($key); ?>" name="<?php echo ($field["name"]); ?>[]" <?php if(check_document_position($data[$field['name']],$key)): ?>checked="checked"<?php endif; ?>><?php echo ($vo); ?>
						    </label><?php endforeach; endif; else: echo "" ;endif; break;?>
					<?php case "editor": ?><label class="textarea">
					    <textarea name="<?php echo ($field["name"]); ?>"><?php echo ($data[$field['name']]); ?></textarea>
					    <?php echo hook('adminArticleEdit', array('name'=>$field['name'],'value'=>$data[$field['name']]));?>
					    </label><?php break;?>
					<?php case "picture": ?><div class="controls">
						<input type="file" id="upload_picture_<?php echo ($field["name"]); ?>">
						<input type="hidden" name="<?php echo ($field["name"]); ?>" id="cover_id_<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>"/>
						<div class="upload-img-box">
						<?php if(!empty($data[$field['name']])): ?><div class="upload-pre-item"><img src="/mrkshop/Uploads/Picture/<?php echo (get_cover($data[$field['name']],'path')); ?>"/></div><?php endif; ?>
						</div>
					    </div>
					    <script type="text/javascript">
						//上传图片
						/* 初始化上传插件 */
                                                var fieldname = "<?php echo ($field['name']); ?>";
						$("#upload_picture_<?php echo ($field["name"]); ?>").uploadify({
						    "height"          : 30,
						    "swf"             : "/mrkshop/Public/static/uploadify/uploadify.swf",
						    "fileObjName"     : "download",
						    "buttonText"      : "上传图片",
						    "uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id(),'field'=>$field['name'],'pagefrom'=>'goods'));?>",
						    "width"           : 120,
						    'removeTimeout'	  : 1,
						    'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
						    "onUploadSuccess" : uploadPicture<?php echo ($field["name"]); ?>,
						    'formData'      : {'fields':fieldname,'pagefrom':'goods'},
						    'onFallback' : function() {
							alert('未检测到兼容版本的Flash.');
						    }
						});
						function uploadPicture<?php echo ($field["name"]); ?>(file, data){
						    var data = $.parseJSON(data);
						    var src = '';
						    if(data.status){
							$("#cover_id_<?php echo ($field["name"]); ?>").val(data.id);
							src = data.url || '/mrkshop/Uploads/Picture/'+ data.path
							$("#cover_id_<?php echo ($field["name"]); ?>").parent().find('.upload-img-box').html(
								'<div class="upload-pre-item"><img src="' + src + '"/></div>'
							    );
						    } else {
							    updateAlert(data.info);
							    setTimeout(function(){
							    $('#top-alert').find('button').click();
							    $(that).removeClass('disabled').prop('disabled',false);
							},1500);
						    }
						}
					    </script><?php break;?>
					<?php case "file": ?><div class="controls">
										    <input type="file" id="upload_file_<?php echo ($field["name"]); ?>">
										    <input type="hidden" name="<?php echo ($field["name"]); ?>" value="<?php echo think_encrypt(json_encode(get_table_field($data[$field['name']],'id','','File')));?>"/>
										    <div class="upload-img-box">
											    <?php if(isset($data[$field['name']])): ?><div class="upload-pre-file"><span class="upload_icon_all"></span><?php echo (get_table_field($data[$field['name']],'id','name','File')); ?></div><?php endif; ?>
										    </div>
									    </div>
									    <script type="text/javascript">
									    //上传图片
									/* 初始化上传插件 */
									    $("#upload_file_<?php echo ($field["name"]); ?>").uploadify({
									    "height"          : 30,
									    "swf"             : "/mrkshop/Public/static/uploadify/uploadify.swf",
									    "fileObjName"     : "download",
									    "buttonText"      : "上传附件",
									    "uploader"        : "<?php echo U('File/upload',array('session_id'=>session_id()));?>",
									    "width"           : 120,
									    'removeTimeout'	  : 1,
									    "onUploadSuccess" : uploadFile<?php echo ($field["name"]); ?>,
									    'onFallback' : function() {
										alert('未检测到兼容版本的Flash.');
									    }
									});
									    function uploadFile<?php echo ($field["name"]); ?>(file, data){
										    var data = $.parseJSON(data);
									    if(data.status){
										    var name = "<?php echo ($field["name"]); ?>";
										    $("input[name="+name+"]").val(data.data);
										    $("input[name="+name+"]").parent().find('.upload-img-box').html(
											    "<div class=\"upload-pre-file\"><span class=\"upload_icon_all\"></span>" + data.info + "</div>"
										    );
									    } else {
										    updateAlert(data.info);
										    setTimeout(function(){
										    $('#top-alert').find('button').click();
										    $(that).removeClass('disabled').prop('disabled',false);
										},1500);
									    }
									}
									    </script><?php break;?>
					<?php default: ?>
					<input type="text" class="text input-large" name="<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>"><?php endswitch;?>
				    <span class="check-tips" style="margin-left:0;"><?php if(!empty($field['remark'])): ?>（<?php echo ($field['remark']); ?>）<?php endif; ?></span>
				</div>
			    </div><?php endif; ?>
                            <?php if(($stock) == "-2"): if($field['name'] == 'stock'): ?><!--仓储下面紧跟优惠价格-->
				<?php if($goodvolumepricelist): if(is_array($goodvolumepricelist)): $volumeid = 0; $__LIST__ = $goodvolumepricelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$volumeprice): $mod = ($volumeid % 2 );++$volumeid;?><div class="form-item cf">
					<?php if(($volumeid) == "1"): ?><label class="item-label item-label_f">优惠价格</label>
					<a class="volume_add_spec" style="float: left;line-height: 30px" href="javascript:;">[+]</a>
					<div class="controls"> 
					     数量 <input type="text"  style="width: 60px" value="<?php echo ($volumeprice["num"]); ?>" name="volume_number[]" class="text input-mid">
					     价格 <input type="text"  style="width: 60px" value="<?php echo ($volumeprice["price"]); ?>" name="volume_price[]" class="text input-mid">
					    <span class="check-tips">（购买数量达到优惠数量时享受的优惠价格）</span>
					</div>
					<?php else: ?>
					<label class="item-label item-label_f"></label>
					<a class="volume_remove_spec" style="float: left;line-height: 30px" href="javascript:;">[-]</a>
					<div class="controls"> 
					     数量 <input type="text"  style="width: 60px" value="<?php echo ($volumeprice["num"]); ?>" name="volume_number[]" class="text input-mid">
					     价格 <input type="text"  style="width: 60px" value="<?php echo ($volumeprice["price"]); ?>" name="volume_price[]" class="text input-mid"> 
					</div><?php endif; ?>
				    </div><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php else: ?>
				    <div class="form-item cf">
					<label class="item-label item-label_f">优惠价格</label>
					<a class="volume_add_spec" style="float: left;line-height: 30px" href="javascript:;">[+]</a>
					<div class="controls"> 
					    数量<input type="text"  size="8" value="" name="volume_number[]" class="text input-mid">
					    价格<input type="text"  size="8" value="" name="volume_price[]" class="text input-mid">
					    <span class="check-tips">（购买数量达到优惠数量时享受的优惠价格）</span>
					</div>
				    </div><?php endif; ?>
				<div class="form-item cf">
				    <label class="item-label item-label_f">优惠价格描述</label>
				    <div class="controls">
					<label class="textarea input-large">
					    <textarea name="volumedes"><?php echo ($data['volumedes']); ?></textarea>
					</label>
					<span style="margin-left:0;" class="check-tips">(如有数量优惠价，显示在前端商品详细页)</span>
				    </div>
				</div><?php endif; ?>
			    <?php if($field['name'] == 'promote_price'): ?><!--仓储下面紧跟优惠价格-->
				<div class="form-item cf">  
				    <label class="item-label item-label_f">促销日期</label>
				    <?php
 $promote_start_time= $data['promote_start_time'] ? date('Y-m-d',$data['promote_start_time']):date('Y-m-d'); $promote_end_time= $data['promote_end_time'] ? date('Y-m-d',$data['promote_end_time']):date('Y-m-d'); ?>
				    <div class="controls"> 
					<input type="text" name="promote_start_time" style="width: 120px;" class="text date" value="<?php echo ($promote_start_time); ?>" placeholder="请选择日期" />--
					<input type="text" name="promote_end_time" style="width: 120px;" class="text date"   value="<?php echo ($promote_end_time); ?>" placeholder="请选择日期" />
					<span class="check-tips">（促销活动时间，仅在填写促销价格后生效）</span>
				    </div>
				</div><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
		    </div><?php endforeach; endif; else: echo "" ;endif; ?> 
		<div id="tab<?php echo (1+count(parse_config_attr($model['field_group'])));?>" class="tab-pane tab<?php echo (1+count(parse_config_attr($model['field_group'])));?>">
		    <?php echo ($attributehtml); ?>
		</div>
		<div id="tab<?php echo (2+count(parse_config_attr($model['field_group'])));?>" class="tab-pane tab<?php echo (2+count(parse_config_attr($model['field_group'])));?>">
		    <div class="form-item cf"> 
			<!-- 搜索 --> 
			<label class="item-label" style="margin-bottom: 10px">
			    <span class="fl cf">搜索&nbsp;</span>
			    <div class="search-form">
				<div class="sleft"> 
				    <input type="text" id="searchunionkeywords" name="keywordss" class="search-input" value="<?php echo I('title');?>" placeholder="请输入搜索关键字">
				    <a class="sch-btn" href="javascript:;" id="search"><i class="btn-search"></i></a>
				</div> 
			    </div>
			    <span class="check-tips">（直接拖动进行关联）</span>
			</label>
			<div class="form-item cf edit_sort edit_sort_l form_unionarticle" >
			    <span>文章列表</span>
			    <ul class="dragsort needdragsort" data-group="0" id="dragsortsearch"> 
				
			    </ul>
			</div>
			<div class="form-item cf edit_sort edit_sort_l form_unionarticle">
			    <span>关联文章&nbsp;&nbsp;<a  href="javascript:;" id="clearunionarticle">[清空]</a></span>
			    <ul class="dragsort needdragsort" data-group="1" id="dragsortunionarticle">
				<?php if(is_array($unionarticle)): $i = 0; $__LIST__ = $unionarticle;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$uarticle): $mod = ($i % 2 );++$i;?><li class="getSort">
					<em data="<?php echo ($uarticle["id"]); ?>"><?php echo ($uarticle["title"]); ?></em>
					<input type="hidden" name="unionarticle[1][]" value="<?php echo ($uarticle["id"]); ?>"/>
				    </li><?php endforeach; endif; else: echo "" ;endif; ?>
			    </ul>
			</div>
		    </div> 
		</div>
		
		<div class="form-item cf">
		    <button class="btn submit-btn ajax-post hidden" id="submit" type="submit" target-form="form-horizontal">确 定</button>
		    <a class="btn btn-return" href="<?php echo (cookie('__forward__')); ?>">返 回</a>
		    <?php if(C('OPEN_DRAFTBOX') and (ACTION_NAME == 'add' or $data['status'] == 3)): ?><button class="btn save-btn" url="<?php echo U('article/autoSave');?>" target-form="form-horizontal" id="autoSave">
			    存草稿
		    </button><?php endif; ?>
		    <input type="hidden" name="id" value="<?php echo ((isset($data["id"]) && ($data["id"] !== ""))?($data["id"]):''); ?>"/>
		    <!-- <input type="hidden" name="pid" value="<?php echo ((isset($data["pid"]) && ($data["pid"] !== ""))?($data["pid"]):''); ?>"/> -->
		    <input type="hidden" name="model_id" value="<?php echo ((isset($data["model_id"]) && ($data["model_id"] !== ""))?($data["model_id"]):''); ?>"/>
		    <!--<input type="hidden" name="category_id" value="<?php echo ((isset($data["category_id"]) && ($data["category_id"] !== ""))?($data["category_id"]):''); ?>">-->
		    <!--<input type="hidden" name="domainid" id="domainid" value="<?php echo ((isset($data["domainid"]) && ($data["domainid"] !== ""))?($data["domainid"]):''); ?>">-->
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
    
<link href="/mrkshop/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
<?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/mrkshop/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>
<link href="/mrkshop/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/mrkshop/Public/static/jquery.dragsort-0.5.1.min.js"></script>
<script type="text/javascript" src="/mrkshop/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="/mrkshop/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
<script type="text/javascript">
Think.setValue("type", <?php echo ((isset($data["type"]) && ($data["type"] !== ""))?($data["type"]):'""'); ?>);
Think.setValue("display", <?php echo ((isset($data["display"]) && ($data["display"] !== ""))?($data["display"]):0); ?>);
$('#submit').click(function(){
	$('#form').submit();
});
$(function(){
    
        // 设置会员等级价格
    $(".set_member_level_price").click(function(){
        var obj =$("#member_level_price_box");
        var price = $("input[name=price]").val();
        if(price <= 0){
            alert('请输入价格'); return false;
        }
        var url='/mrkshop/admin.php?s=/Goods/getMemberPricelist'; 
        $.ajax({
	    type: 'POST', 
	    url: url , 
	    data:{price:price},
	    success: function(json){
		var data=json.info;
		var html="";
		if(data){
                    obj.html(data);
		}
		return false; 
	    } 
	});
    });
    
    $('.date').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
    $('.time').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
    showTab();
    $(".needdragsort").dragsort({
	dragSelector:'li',
	placeHolderTemplate: '<li class="draging-place">&nbsp;</li>',
	dragBetween:true,	//允许拖动到任意地方
	dragEnd:function(){
	    var self = $(this);
	    self.find('input').attr('name', 'unionarticle[' + self.closest('ul').data('group') + '][]');
	}
    });
    
    //搜索功能
    $("#clearunionarticle").click(function(){
	$("#dragsortunionarticle").html("");
    });
    $("#search").click(function(){
	var url='/mrkshop/admin.php?s=/Goods/searcharticle'; 
	var sk=$("#searchunionkeywords").val();
	$.ajax({
	    type: 'POST', 
	    url: url , 
	    data:{domainid:'<?php echo ($data["domainid"]); ?>',keywords:sk},
	    success: function(json){
		var data=json.info;
		var html="";
		if( data ){
		    for(i=0;i< data.length;i++){
			html+='<li class="getSort"><em data="'+data[i].id+'">'+data[i].title+'</em><input type="hidden" name="unionarticle[0][]" value="'+data[i].id+'"/></li>'; 
		    }
		}
		$("#dragsortsearch").html(html);
		return false; 
	    } 
	});
	return false;
    });
    
	<?php if(C('OPEN_DRAFTBOX') and (ACTION_NAME == 'add' or $data['status'] == 3)): ?>//保存草稿
	var interval;
	$('#autoSave').click(function(){
        var target_form = $(this).attr('target-form');
        var target = $(this).attr('url')
        var form = $('.'+target_form);
        var query = form.serialize();
        var that = this;

        $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
        $.post(target,query).success(function(data){
            if (data.status==1) {
                updateAlert(data.info ,'alert-success');
                $('input[name=id]').val(data.data.id);
            }else{
                updateAlert(data.info);
            }
            setTimeout(function(){
                $('#top-alert').find('button').click();
                $(that).removeClass('disabled').prop('disabled',false);
            },1500);
        })

        //重新开始定时器
        clearInterval(interval);
        autoSaveDraft();
        return false;
    });

	//Ctrl+S保存草稿
	$('body').keydown(function(e){
		if(e.ctrlKey && e.which == 83){
			$('#autoSave').click();
			return false;
		}
	});

	//每隔一段时间保存草稿
	function autoSaveDraft(){
		interval = setInterval(function(){
			//只有基础信息填写了，才会触发
			var title = $('input[name=title]').val();
			var name = $('input[name=name]').val();
			var des = $('textarea[name=description]').val();
			if(title != '' || name != '' || des != ''){
				$('#autoSave').click();
			}
		}, 1000*parseInt(<?php echo C('DRAFT_AOTOSAVE_INTERVAL');?>));
	}
	autoSaveDraft();<?php endif; ?>
	//数量优惠价格
	$("a.volume_add_spec").click(function(){
	    addVolumeSpec(this);
	});
	$("a.volume_remove_spec").click(function(){
	    removeSpec(this);
	});
	//属性添加
	$("a.attr_add_spec").click(function(){
	    addSpec(this);
	});
	$("a.attr_remove_spec").click(function(){
	    removeSpec(this);
	});
	$("a.market_price_setted").click(function(){
	    marketPriceSetted();
	});
});


function getcategory(id,children_id,defaultmsg){
    var url='/mrkshop/admin.php?s=/Goods/getcategory';
    $.ajax({
	type: 'POST', 
	url: url , 
	data:{pid:id}, 
	success: function(json){
	    var status = json.status;
	    var html="<option value=''>"+defaultmsg+"</option>";
	    if (status==1) { 
                $("#attributehtml").html(json.attributehtml);
		var data=json.info; 
		if( data ){
		    for(i=0;i< data.length;i++){
			html+="<option value='"+data[i].id+"'>"+data[i].title+"</option>";
		    }
		}
	    }
	    $("#"+children_id).html(html);
	    if ( children_id=='ke_child' ) {
		$("#shu_child").html("<option value=''>请选择</option>");
	    }
	    return false;
	} 
    });
}
function getchildren( id,children_id,defaultmsg){ 
    var url='/mrkshop/admin.php?s=/Goods/getchildren';
    $.ajax({
	type: 'POST', 
	url: url , 
	data:{pid:id}, 
	success: function(json){
	    var status = json.status;
	    var html="<option value=''>"+defaultmsg+"</option>";
	    if (status==1) { 
		var data=json.info; 
		if( data ){
		    for(i=0;i< data.length;i++){
			html+="<option value='"+data[i].id+"'>"+data[i].title+"</option>";
		    }
		}
	    }
	    $("#"+children_id).html(html);
	    if ( children_id=='ke_child' ) {
		$("#shu_child").html("<option value=''>请选择种</option>");
	    }
	    return false;
	} 
    });
}
function getchildrentags( id ){ 
    var url='/mrkshop/admin.php?s=/Goods/gettagsby_catid';
    $.ajax({
	type: 'POST', 
	url: url , 
	data:{cid:id}, 
	success: function(json){
	    var status = json.status;
	    var html="";
	    if (status==1) { 
		var data=json.info; 
		if( data ){
		    for(i=0;i< data.length;i++){
			html+="<a href=\"javascript:;\" class=\"goodstags\">"+data[i].keywords+"</a> &nbsp;&nbsp;"; 
		    }
		}
	    }
	    $("#tags").html(html);
	    $(".goodstags").click(function(){
		var h=$(this).text();
		$('#keywordstag').tagsinput('add', h);
		var s = $('#keywordstag').tagsinput('items');
		$('#keywordstag_val').val(s);
		return false;
	    });
	    return false;
	} 
    });
}



function addVolumeSpec(obj) {
    var h=$(obj).parent().clone();
    $(h).find("a.volume_add_spec").replaceWith("<a class=\"volume_remove_spec\" style=\"float: left;line-height: 30px\" href=\"javascript:;\">[-] </a>");
    $(h).find("input[name='volume_number[]']").val("");
    $(h).find("input[name='volume_price[]']").val("");
    $(h).find("label").text("");
    $(h).find("span.check-tips").remove();
    $("a.volume_remove_spec",h).click(function(){
	removeSpec(this);
    });
    $(obj).parent().after(h);
}
function addSpec(obj) {
    var h=$(obj).parent().clone();
    $(h).find("a.attr_add_spec").replaceWith("<a class=\"attr_remove_spec\" href=\"javascript:;\">[-] </a>");
    $(h).find("input[name='attr_goodattr_id_list[]']").val("");
    $("a.attr_remove_spec",h).click(function(){
	removeSpec(this);
    });
    $(obj).parent().after(h);
}
function removeSpec(obj) {
    var h=$(obj).parent().remove(); 
}
function marketPriceSetted(){
    var url='/mrkshop/admin.php?s=/Goods/getpricebymarkprice';
    var price = $('input[name=marketprice]').val();
    $.ajax({
	type: 'GET', 
	url: url ,
	data:{mprice:price},
	success: function(json){
		var data=json.info;
		$('input[name=price]').val(data); 
	} 
    });
    return false;
}
</script>

</body>
</html>