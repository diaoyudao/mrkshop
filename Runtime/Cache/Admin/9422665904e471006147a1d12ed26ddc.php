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
            

            
    <!-- 标题 -->
    <div class="main-title">
        <h2>商品评论(<?php echo ($_total); ?>)</h2>
    </div>

    <!-- 按钮工具栏 -->
    <div class="cf">
        <div class="fl">
            <button class="btn ajax-post" target-form="ids" url="<?php echo U("comment/setStatus",array("ifcheck"=>1));?>">启 用</button>
            <button class="btn ajax-post" target-form="ids" url="<?php echo U("comment/setStatus",array("ifcheck"=>0));?>">禁 用</button> 
            <input type="hidden" class="hide-data" name="cate_id" value="<?php echo ($cate_id); ?>"/>
            <input type="hidden" class="hide-data" name="pid" value="<?php echo ($pid); ?>"/>
            <!--<button class="btn ajax-post confirm" target-form="ids" url="<?php echo U("comment/setStatus",array("ifcheck"=>-1));?>">删 除</button>-->
        </div>

        <!-- 高级搜索 -->
        <div class="search-form fr cf">
            <div class="sleft">
                <div class="drop-down">
                    <span id="sch-sort-txt" class="sort-txt" data="<?php echo ($status); ?>"><?php if(get_status_title($status) == ''): ?>所有<?php else: echo get_status_title($status); endif; ?></span>
                    <i class="arrow arrow-down"></i>
                    <ul id="sub-sch-menu" class="nav-list hidden">
                        <li><a href="javascript:;" value="">所有</a></li>
                        <li><a href="javascript:;" value="1">正常</a></li>
                        <li><a href="javascript:;" value="0">禁用</a></li>
                        <li><a href="javascript:;" value="2">待审核</a></li>
                    </ul>
                </div>
                <input type="text" name="title" class="search-input" value="<?php echo I('title');?>" placeholder="请输入标题文档">
                <a class="sch-btn" href="javascript:;" id="search" url="<?php echo U('comment/index','pid='.I('pid',0).'&cate_id='.$cate_id,false);?>"><i class="btn-search"></i></a>
            </div>
            <div class="btn-group-click adv-sch-pannel fl">
                <button class="btn">高 级<i class="btn-arrowdown"></i></button>
                <div class="dropdown cf">
                    <div class="row">
                        <label>更新时间：</label>
                        <input type="text" id="time-start" name="time-start" class="text input-2x" value="" placeholder="起始时间" /> -
                        <input type="text" id="time-end" name="time-end" class="text input-2x" value="" placeholder="结束时间" />
                    </div>
                    <div class="row">
                        <label>创建者：</label>
                        <input type="text" name="nickname" class="text input-2x" value="" placeholder="请输入用户名">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 数据表格 -->
    <div class="data-table">
        <table>
            <thead>
                <tr>
                    <th class="row-selected">
                        <input class="checkbox check-all" type="checkbox">
                    </th>
                    <th>编号</th>
                    <th>审核</th>
                    <th>商品</th>
                    <th>评论内容</th>
                    <th>评论时间</th> 
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$comment): $mod = ($i % 2 );++$i;?><tr>
                        <td><input class="ids row-selected" type="checkbox" name="ids[]" value="<?php echo ($comment['id']); ?>"> </td>
                        <td><?php echo ($comment["id"]); ?></td>
                        <td>
                            <?php if(($comment['ifcheck']) > "0"): ?><a class="ajax-get" title="审核" href="<?php echo U('audit',array('id'=>$comment['id'],'ifcheck'=>0));?>">已审</a>
                    <?php else: ?>
                    <a class="ajax-get" style="color: red;" title="未审" href="<?php echo U('audit',array('id'=>$comment['id'],'ifcheck'=>1));?>">未审</a><?php endif; ?>
                    </td>
                    <td><a href="<?php echo U('Goods/edit',array('id'=>$comment['goodid'],'cate_id'=>$comment['category_id']));?>"><?php echo (get_good_name($comment["goodid"])); ?></a></td>
                    <td><?php echo (msubstr($comment["content"],0,45)); ?></td>
                    <td><?php echo (date('Y-m-d H:i',$comment["create_time"])); ?></td>
                    <td>
                        <a href="<?php echo U('setStatus?ids='.$comment['id'].'&ifcheck='.abs(1-$comment['ifcheck']));?>" class="ajax-get"><?php echo (show_status_op($comment["ifcheck"])); ?></a>
                        <a class="" title="查看" href="<?php echo U('detail?id='.$comment['id']);?>">查看</a>
                        <!--<a class="confirm ajax-get" title="删除" href="<?php echo U('del?id='.$comment['id']);?>">删除</a>-->
                        
                    </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <?php else: ?>
                <td colspan="6" class="text-center"> aOh! 暂时还没有内容! </td><?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- 分页 -->
    <div class="page">
        <?php echo ($_page); ?>
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
    <script type="text/javascript" src="/mrkshop/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="/mrkshop/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script type="text/javascript">
        $(function() {
            //搜索功能
            $("#search").click(function() {
                var url = $(this).attr('url');
                var status = $("#sch-sort-txt").attr("data");
                var query = $('.search-form').find('input').serialize();
                query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
                query = query.replace(/^&/g, '');
                if (status != '') {
                    query = 'status=' + status + "&" + query;
                }
                if (url.indexOf('?') > 0) {
                    url += '&' + query;
                } else {
                    url += '?' + query;
                }
                window.location.href = url;
            });

            /* 状态搜索子菜单 */
            $(".search-form").find(".drop-down").hover(function() {
                $("#sub-sch-menu").removeClass("hidden");
            }, function() {
                $("#sub-sch-menu").addClass("hidden");
            });
            $("#sub-sch-menu li").find("a").each(function() {
                $(this).click(function() {
                    var text = $(this).text();
                    $("#sch-sort-txt").text(text).attr("data", $(this).attr("value"));
                    $("#sub-sch-menu").addClass("hidden");
                })
            });

            //只有一个模型时，点击新增
            $('.document_add').click(function() {
                var url = $(this).attr('url');
                if (url != undefined && url != '') {
                    window.location.href = url;
                }
            });

            //点击排序
            $('.list_sort').click(function() {
                var url = $(this).attr('url');
                var ids = $('.ids:checked');
                var param = '';
                if (ids.length > 0) {
                    var str = new Array();
                    ids.each(function() {
                        str.push($(this).val());
                    });
                    param = str.join(',');
                }

                if (url != undefined && url != '') {
                    window.location.href = url + '/ids/' + param;
                }
            });

            //回车自动提交
            $('.search-form').find('input').keyup(function(event) {
                if (event.keyCode === 13) {
                    $("#search").click();
                }
            });

            $('#time-start').datetimepicker({
                format: 'yyyy-mm-dd',
                language: "zh-CN",
                minView: 2,
                autoclose: true
            });

            $('#time-end').datetimepicker({
                format: 'yyyy-mm-dd',
                language: "zh-CN",
                minView: 2,
                autoclose: true
            });
        })
    </script>

</body>
</html>