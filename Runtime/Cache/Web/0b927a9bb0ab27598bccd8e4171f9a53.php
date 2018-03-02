<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
    <head>
    <meta charset="UTF-8">
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
<title><?php if(!empty($meta_title)): echo ($meta_title); ?>-<?php echo C('SITENAME'); else: echo C('WEB_SITE_TITLE'); endif; ?></title>
<meta name="keywords" content="<?php if(empty($meta_keyword)): echo C('WEB_SITE_KEYWORD'); else: echo ($meta_keyword); endif; ?>"/>
<meta name="description" content="<?php if(empty($meta_keyword)): echo C('WEB_SITE_DESCRIPTION'); else: echo ($meta_description); endif; ?>">
<link href="favicon.ico" type="image/x-icon" rel="shortcut icon"/>
<link href="/Public/Web/css/base.css" rel="stylesheet" />
<link href="/Public/Web/css/index.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="/Public/Web/css/snapUp.css"/>
<link rel="stylesheet" type="text/css" href="/Public/Web/css/idangerous.swiper.css"/>
<script src="/Public/Web/js/idangerous.swiper.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/Public/Web/js/jquery-1.11.2.min.js"></script>
 
</head>
<body>
    <!-- 头部 -->
<header style="margin-bottom: 40px;">
    <!-- 顶部 -->
    <div class="top">
        <div class="towp">
    <div class="wellcom fl">
        <span>欢迎光临！</span>
        <?php if(is_login()): ?><span> <a title="<?php echo session('user_auth.username');?>" href="<?php echo U('member/index');?>"><?php echo session('user_auth.username');?></a>&nbsp;&nbsp;&nbsp;&nbsp;欢迎您!</span>
            <span> <a title="退出" href="<?php echo U('member/logout');?>">退出</a></span>
            <?php else: ?>
            <span> <a href="<?php echo U('member/login');?>">请登录</a></span>
            <span> <a href="<?php echo U('member/register');?>">免费注册</a></span><?php endif; ?>
    </div>
    <div class="topMenu fr">
        <span class="top-myOrder"><a href="<?php echo U('order/index');?>"><i class="bg"></i>我的订单</a></span>
        <span class="top-user"><a href="<?php echo U('member/index');?>"><i class="bg"></i>我的账号</a></span>
        <span class="topOnlineSevic"><a href="<?php echo U('help/index');?>"><i class="bg"></i>客服中心</a></span>
        <span class="topPhone">
            <a href="javascript:;;"><i class="bg"></i>手机版</a>
            <span class="app">
                <img src="/Public/Web/img/code.png" />
                妙品生活WAP
            </span>
        </span>
        <span class="top-qq"><a title="QQ:<?php echo C('QQ');?>" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo C('QQ');?>&site=qq&menu=yes" target="_blank"><i class="bg"></i>在线客服</a></span>
    </div>
</div>
    </div>
    <!-- 搜索 -->
    <div class="search-box search-box2 my" style="background: none;">
        <div class="towp clear">
            <!-- LOGO -->
            <div class="logo fl">
                 <?php $logo = C('SITE_LOGO') ?>
                <a href="<?php echo U('Index/index');?>"><img src="/Uploads/Picture/logo/<?php echo (get_cover($logo,'path')); ?>" /></a>
            </div>
            <div class="outlet fl clear">
                <h2 class="fl">我的会员中心</h2>— —<?php echo ($meta_title); ?></div>
        </div>
    </div>
</header>
<!-- /头部 -->
<!-- 主体 -->

    <!-- 内容 -->
    <div class="towp yh-wrap content_agency clear user_center">
        <div class="content_left">
            <?php if(session('memberinfo.member_type') == 2): ?><span class="centre_box"><a class="centre" href="javascript:;">管理中心</a></span>
    <div class="menu">
        <!--<div class="tit"><a href="javascript:;">管理中心</a></div>-->
        <dl>
            <dd><a <?php if(ACTION_NAME == 'goodsmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/goodsmanage');?>">商品管理</a></dd>
            <dd><a <?php if(in_array(ACTION_NAME,array('orderlist','orderdetail','ordership','orderpay')) and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/orderlist');?>">订单管理</a></dd>
            <dd><a <?php if(ACTION_NAME == 'membermanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/membermanage');?>">会员管理</a></dd>
            <dd><a <?php if(ACTION_NAME == 'goodsmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/goodsmanage');?>">评价管理</a></dd>
            <dd><a <?php if(ACTION_NAME == 'refundmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/refundmanage');?>">退货管理</a></dd>
            <dd><a <?php if(in_array(ACTION_NAME,array('statics','statclasses','statorder','statmember')) and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?> href="<?php echo U('Agent/statics');?>">数据统计</a></dd>
            <dd><a <?php if(ACTION_NAME == 'storeinfo' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/storeinfo');?>">门店资料</a></dd>
            <dd><a <?php if(ACTION_NAME == 'billmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/billmanage',array('type'=>'log'));?>">提成管理</a></dd>
            <!--<dd <?php if((CONTROLLER_NAME) == "Express"): ?>class="current"<?php endif; ?>><a href="<?php echo U('express/index');?>">快件管理</a></dd>-->
        </dl>
    </div><?php endif; ?>
<?php if(session('memberinfo.member_type') == 1): ?><span class="centre_box"><a class="centre" href="javascript:;">管理中心</a></span>
    <div class="menu">
        <!--<div class="tit"><a href="javascript:;">管理中心</a></div>-->
        <dl>
            <dd><a <?php if(ACTION_NAME == 'membermanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?>  href="<?php echo U('Agent/membermanage');?>">会员管理</a></dd>
            <dd><a <?php if(ACTION_NAME == 'billmanage' and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?> href="<?php echo U('Agent/billmanage',array('type'=>'log'));?>">提成管理</a></dd>
            <dd><a <?php if(in_array(ACTION_NAME,array('statics','statclasses','statorder','statmember')) and CONTROLLER_NAME == Agent): ?>class="current"<?php endif; ?> href="<?php echo U('Agent/statics');?>">数据统计</a></dd>
        </dl>
    </div><?php endif; ?>


<span class="centre_box"><a class="centre" href="<?php echo U('member/index');?>">会员中心</a></span>
<div class="menu">
    <!--<div class="tit"><a href="javascript:;">管理中心</a></div>-->
    <dl>
        <dd><a  <?php if(ACTION_NAME == 'index' and CONTROLLER_NAME == Member): ?>class="current"<?php endif; ?> href="<?php echo U('member/index');?>">会员中心</a></dd>
        <dd><a <?php if((ACTION_NAME) == "mycollection"): ?>class="current"<?php endif; ?> href="<?php echo U('member/mycollection');?>">我的收藏</a></dd>
        <!--        <?php if(session('memberinfo.member_type') == 1): ?><dd <?php if((CONTROLLER_NAME) == "Agent"): ?>class="current"<?php endif; ?>><a href="<?php echo U('agent/index');?>">代理管理</a></dd><?php endif; ?>-->
        <!--        <?php if(session('memberinfo.member_type') == 2): ?><dd <?php if((CONTROLLER_NAME) == "Agent"): ?>class="current"<?php endif; ?>><a href="<?php echo U('agent/index');?>">代理管理</a></dd>
                    <dd <?php if((CONTROLLER_NAME) == "Express"): ?>class="current"<?php endif; ?>><a href="<?php echo U('express/index');?>">快件管理</a></dd><?php endif; ?>-->
        <dd><a <?php if((CONTROLLER_NAME) == "Order"): ?>class="current"<?php endif; ?> href="<?php echo U('order/index');?>">订单管理</a></dd>
        <dd><a <?php if((CONTROLLER_NAME) == "Comment"): ?>class="current"<?php endif; ?> href="<?php echo U('Comment/index');?>">评价管理</a></dd>
        <dd><a <?php if((ACTION_NAME) == "address"): ?>class="current"<?php endif; ?> href="<?php echo U('member/address');?>">地址管理</a></dd>
        <dd><a <?php if((CONTROLLER_NAME) == "Safety"): ?>class="current"<?php endif; ?> href="<?php echo U('Safety/safety');?>">安全验证</a></dd>
        <dd><a <?php if((CONTROLLER_NAME) == "Refund"): ?>class="current"<?php endif; ?> href="<?php echo U('refund/index');?>">售后服务</a></dd>
        <dd><a <?php if((CONTROLLER_NAME) == "Message"): ?>class="current"<?php endif; ?> href="<?php echo U('Message/index');?>">建议投诉</a></dd>
    </dl>
</div>
        </div>
        <div class="content stores right-con2 order-mana my-members">
            <div class="con-tit clear">
                <h2 class="">商品管理</h2></div>
            <div class="tab_box">				
                <div class="conditions search-form">
                    <ul class="clear">
                        <li><label for="">商品名称</label><input class="searchinput" type="text" name="goods_name" id="" value="<?php echo I('goods_name');?>" /></li>
                        <li>
                            <label for="">商品分类</label>
                            <select name="domainid" class="searchinput" style='width: 160px;background:rgba(0, 0, 0, 0) url("/Public/Web/img/bottoms.png") no-repeat scroll 98% center'>
                                <option value='0'>请选择</option>
                                <?php if(is_array($domainlist)): $i = 0; $__LIST__ = $domainlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><option <?php if($domainid == $item['id']): ?>selected='selected'<?php endif; ?>  value="<?php echo ($item["id"]); ?>"><?php echo ($item["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </li>
                        <!--                        <li>
                                                    <label for="">状态</label>
                                                    <select name="">
                                                        <option value="">4</option>
                                                        <option value="">1</option>
                                                        <option value="">4</option>
                                                        <option value="">7</option>
                                                    </select>					
                                                </li>
                                                <li><label for="">订单状态</label><input type="" name="" id="" value="" /></li>-->
                        <li class="clear">
                            <a class="active marl_60" id="search" url="<?php echo U('Agent/goodsmanage');?>" href="javascript:;">搜索</a>
                            <a href="<?php echo U('Agent/goodsmanage');?>">清空</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="store_table2">
                <div class="tables">
                    <table width="100%">
                        <colgroup span="8">
                            <col width="33%" />
                            <col width="8%" />
                            <col width="8%" />
                            <col width="8%" />
                            <col width="8%" />
                            <col width="10%" />
                            <col width="10%" />
                            <col width="15%" />
                        </colgroup>
                        <tr>
                            <th>商品名称</th>
                            <th>采购价格</th>
                            <th>会员价</th>
                            <th>销售数量</th>
                            <th>我的库存</th>
                            <th>最新采购时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                    </table>
                </div>
                <div class="tables_1">

                </div>
                <table class="cp" width="100%">
                    <colgroup span="8">
                        <col width="33%" />
                        <col width="8%" />
                        <col width="8%" />
                        <col width="8%" />
                        <col width="8%" />
                        <col width="10%" />
                        <col width="10%" />
                        <col width="15%" />
                        <col width="15%" />
                    </colgroup>
                    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><tr>
                            <td class="clear">
                                <!--<input type="checkbox" name="" id=""/>-->
                                <a class="im" href="jacascript:;">
                                    <!--<img src="<?php echo (get_good_img($goods["goodid"])); ?>"/>-->
                                </a>
                                <div class="xinx" style="height: auto;line-height: normal;">
                                    <p><a href="javascript:;"><?php echo (get_good_name($goods["goods_id"])); ?></a></p>
                                </div>
                            </td>
                            <td><?php echo ($goods["buy_price"]); ?></td>
                            <td><?php echo ($goods["goods_marketprice"]); ?></td>
                            <td><?php echo ((isset($goods["sales_num"]) && ($goods["sales_num"] !== ""))?($goods["sales_num"]): 0); ?></td>
                        <?php if($goods["stock"] <= $goods.stock_warning): ?><td style='color: red;'><?php echo ((isset($goods["stock"]) && ($goods["stock"] !== ""))?($goods["stock"]):0); ?></td>
                            <?php else: ?>
                            <td ><?php echo ((isset($goods["stock"]) && ($goods["stock"] !== ""))?($goods["stock"]):0); ?></td><?php endif; ?>
                            <td><?php echo (date('Y/m/d H:i:s',$goods["update_time"])); ?></td>
                            <td><?php if($goods["storegoodsstatus"] == 1): ?>在售
                            <?php else: ?>
                            已下架<?php endif; ?></td>		
                            <td>	
                                <a href="<?php echo U('Goods/detail',array('id'=>$goods['goods_id']));?>">查看</a>
                                <a href="<?php echo U('Goods/detail',array('id'=>$goods['goods_id']));?>">采购</a>
                                <a class="link set_stock" href="javascript:;" storegoodsid="<?php echo ($goods['storegoodsid']); ?>">库存预警设置</a>
                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </table>

            </div>

            <div class="pagelist_c">
                <?php echo ($_page); ?>
            </div>
        </div>
    </div>
    <style>
        .searchinput{   border: 1px solid #e5e5e5;
                        height: 30px;
                        line-height: 30px;
                        margin-left: 8px;
                        margin-top: 8px;
                        padding: 0 5px;
                        width: 160px;
        }
        #setstock .active{
            background-color: #feb535;
            border: 1px solid #feb535;
            color: #fff;
            font-size: 12px;
            height: 30px;
            line-height: 30px;
            margin-left: 100px;
            text-align: center;
            width: 100px;
            display: block;
            margin-top: 20px;
        }
    </style>
    <div id="setstock" class="tab_box" style='display:none;'>
        <div  class="conditions">
            <ul class="clear">
                <li><label for="">商品名称</label><input class="searchinput" type="text" name="stock" id="" value="10" /></li>
                <li>
                    <a class="active marl_60" id="submit-stock" href="javascript:;">确定</a>
                </li>
            </ul>
        </div>
    </div>

<!-- /主体 -->
<!-- 底部 -->
<a href="javascript:;" class="i_btntop"></a>
<footer>
    <div class="promise2">
        <div class="towp">
            <ul>
                <li>
                    <i class="bg  prom1"></i>
                    <p>快捷物流</p>
                    <p>最快捷得送达方式</p>
                </li>
                <li>
                    <i class="bg prom2"></i>
                    <p>优质产区</p>
                    <p>来自全球最优质的产区</p>
                </li>
                <li>
                    <i class="bg prom3"></i>
                    <p>服务保证</p>
                    <p>保证产品的购买、配送质量</p>
                </li>
                <li>
                    <i class="bg prom4"></i>
                    <p>国际正品保障</p>
                    <p>绝对正品保证</p>
                </li>
            </ul>
        </div>
    </div>
    <div class="promise3" style="display:none;">
        <div class="towp">
            <?php if(is_array($helpcatelist)): $i = 0; $__LIST__ = $helpcatelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><dl><dt><?php echo ($list["title"]); ?></dt>
                    <?php if(is_array($list["child"])): $i = 0; $__LIST__ = $list["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($i % 2 );++$i;?><dd><a href="<?php echo U('help/detail',array('id'=>$child['name']));?>"><?php echo ($child["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?>
                </dl><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <?php echo hook('ReturnTop');?>
    </div>
    <div class="copy"><?php echo C('WEB_SITE_ICP');?></div>
    <div class="footer-ico"><img src="/Public/Web/img/foter-ico.jpg" /> </div>
    <p style="display: block;"><div class="sss"></div></p>
<a href="javascript:;"><div class="eee">

    </div></a>
</footer>
<script type="text/javascript">
    var ThinkPHP = window.Think = {
        "MID": "<?php echo ($guid); ?>",
        "UID": "<?php echo ($uid); ?>",
        "IMG": "/Public/Web/img", //项目公共目录地址
        "ROOT": "", //当前网站地址
        "JS": "/Public/Web/js", //当前项目地址
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
            website = website + url[1] + '/lists/keywords/' + keywords + '.html';
//        alert(website); return false;

            window.location.href = website;

        }
        return false;
    }

    //返回顶部
    $('.i_btntop').click(function() {
        $('html, body').animate({scrollTop: 0}, '500');
    });
    var poxr = ($(window).width() - 1400) / 2;
    var vtop_b = $(document).scrollTop();
    if (vtop_b > 200) {
        $('.i_btntop').show();
    }
    $(window).scroll(function() {
        var vtop = $(document).scrollTop();
        if (vtop > 200) {
            $('.i_btntop').show();
        } else {
            $('.i_btntop').hide();
        }
    })

    //结束返回

</script>
<div id="cart_message_box" style="display: none;">
    <style>
        .add-goods2 {
            background: #fff none repeat scroll 0 0;
            border: none;
            display: block;
            height: 100px;
            /*margin: -75px 0 0 -220px;*/
            /*padding: 20px;*/
            position: relative;
            text-align: center;
            width: 400px;
            /*z-index: 1001;*/
        }
        .layui-layer-mrk{
            border:  4px solid #f16f71;
        }
        .layui-layer-mrk .layui-layer-title{display: none;}
    </style>
    <div class="add-goods add-goods2" >
        <a href="javascript:javascript:layer.closeAll();" class="t_close"></a>
        <div  class="f14 fb f3 con">您选购的商品已经添加到购物车</div>
        <p class="tc">
            <a class="" href="javascript:layer.closeAll();"><span class="go-shop">&lt;&lt; 继续购物</span></a>
            <a href="<?php echo U('Cart/index');?>" class="t_btn1">立即结算</a>
        </p>
    </div>

</div>
<!-- /底部 -->

<div class="tdivbg" id="tdivbg"></div>
<div class="browser" id="browser">
    <img src="/Public/Web/img/msg.png" class="pic">
    <p>
        <a href="https://www.google.com/intl/cn/chrome/browser/" class="chrome">Chrome</a>
        <a href="https://www.mozilla.org/zh-CN/firefox/new/" class="firefox">Firefox</a>
        <a href="http://windows.microsoft.com/zh-CN/internet-explorer/download-ie" class="ie">IE</a>
    </p>
</div>
<script src="/Public/Web/js/jquery-1.11.2.min.js"></script>
<script src="/Public/static/layer/layer.js"></script>
<script src="/Public/Web/js/mrk.js" type="text/javascript" charset="utf-8"></script>

    <script type="text/javascript">
        //搜索功能
        $("#search").click(function() {
            var url = $(this).attr('url');
            var query = $('.search-form').find('.searchinput').serialize();
            query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            query = query.replace(/^&/g, '');
            if (url.indexOf('?') > 0) {
                url += '&' + query;
            } else {
                url += '?' + query;
            }
            window.location.href = url;
        });
        //回车搜索
        $(".search-input").keyup(function(e) {
            if (e.keyCode === 13) {
                $("#search").click();
                return false;
            }
        });
       var storegoodsid =0;
        $('.set_stock').click(function() {
             storegoodsid = $(this).attr('storegoodsid');
            address_layer = layer.open({
                title: '库存预警设置',
                type: 1,
                skin: 'layui-layer-rim', //加上边框
                area: ['260px', '140px'], //宽高
                content: $('#setstock')
            });
        });
        
        $('body').on("click",'#submit-stock',function(){
           var stock = $("input[name=stock]").val();
           $.ajax({
                url: "<?php echo U('Agent/setgoodsstock');?>",
                type: "post",
                dataType: "json",
                data: {storegoodsid: storegoodsid,stock:stock},
                success: function(json) {
                    if (json.status) {
                        window.location = "<?php echo U('Agent/goodsmanage');?>"
                        return false;
                    }
                    layer.msg(json.info);
                    return false;
                }
            });
        })
    </script>

<script>
    $('.allClassfy').click(function() {
        $('.all-naiv').fadeToggle();
    });

    var _CART_URL = "<?php echo U('Cart/addItem');?>";
    var _FAVORTABLE_URL = "<?php echo U('Favortable/addfavortable');?>";
    var LOGIN_URL = "<?php echo U('member/login');?>";
    /*判断浏览器版本是否过低*/
    var Sys = {};

    var ua = navigator.userAgent.toLowerCase();

    if (window.ActiveXObject) {

        Sys.ie = ua.match(/msie ([\d.]+)/)[1];
        //$(".tdivbg,.browser").css({"dislay":"block"});
        if (Sys.ie <= 7) {

            document.getElementById("tdivbg").style.display = "block";
            document.getElementById("browser").style.display = "block";
            document.body.style.overflow = "hidden";

        }
    }
</script>
<style>
    .all-naiv{ display: none;}
</style>
</body>
</html>