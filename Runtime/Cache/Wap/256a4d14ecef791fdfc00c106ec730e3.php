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
        
        body{
            max-width: 640px;min-width: 320px; position: relative; height:100%; overflow: hidden;
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

    <div class="warp_1">
        <!-- page1 s--->
        <div class="c_page1">
            <!--头部 S-->
            <div class="herder">
                <a href="javascript:history.go(-1);" class="back"><i></i></a>
                <h3>商品结算</h3>
            </div>
            <div class="t_line"></div>
            <!--头部 E-->
            <!--地址信息 S--> 
            <form action='<?php echo U("Checkout/createorder");?>' method="post" name="createorderform" id="createorderform" onsubmit=" return checkaddress();"> 
                <div class="address">
                    <div class="flow_Address_line"></div>
                    <a href="javascript:;">
                        <ul class="clear">
                            <li><?php echo ($address["realname"]); ?></li>
                            <li><?php echo (set_start_phone($address["cellphone"])); ?></li>
                            <li><?php echo (set_start_card($address["card_no"])); ?></li>
                        </ul>
                        <p><?php if(($address["isdefault"]) == "1"): ?><i>默认</i><?php endif; echo ($address["province"]); echo ($address["city"]); echo ($address["area"]); ?>,<?php echo ($address["address"]); ?></p>       
                    </a>
                    <div class="flow_Address_line"></div>
                </div>
                <!--地址信息 E-->
                <?php if(is_array($ordergoodslist)): $i = 0; $__LIST__ = $ordergoodslist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$shoplist): $mod = ($i % 2 );++$i;?><h5><?php echo ($distribution[$key]); ?>包裹 
                        <span class="fr back-shopcar" >
                            <span>重量:<?php echo ($new_shipping[$key]['weight']); ?>千克</span>
                            <span>合计:￥<?php echo ($new_shipping[$key]['total']); ?></span>
                            <span>运费:￥<span id='shipping_fee_ware_<?php echo ($key); ?>'><?php echo ($new_shipping[$key]['shipping_fee']); ?></span></span>
                        </span></h5> 
                <!--商品信息 S-->
                <div class="commodity">
                    <ul>
                        <?php if(is_array($shoplist)): $i = 0; $__LIST__ = $shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['type'] == 0): ?><li>
                                    <a href="<?php echo U('Goods/detail',array('channelname'=>$vo['channelname'],'id'=>$vo['goodid']));?>" target="_blank" >
                                        <dt class="img">
                                        <img src="<?php echo (get_cover_picture_url($vo["goodid"])); ?>">
                                        </dt>
                                        <dd>
                                            <h3><?php echo (get_good_name($vo["goodid"])); ?></h3>
                                            <div class="saixuan_sx">
                                                <div class="jiage">
                                                    ￥<span id="total<?php echo ($vo["sort"]); ?>"><?php echo ($vo["total"]); ?></span>
                                                    <?php if($vo["haiguan_rate"] > 0): ?><span class="taxes">税费<i>￥<?php echo ($vo["haiguan_rate"]); ?></i></span><?php endif; ?>
                                                </div>
                                                <?php echo ($vo["parameters"]); ?><em>X<?php echo ($vo["num"]); ?></em>
                                            </div>
                                        </dd>
                                    </a>
                                </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </ul>	
                </div>
                <!--商品信息 E--><?php endforeach; endif; else: echo "" ;endif; ?>

                <!--配送方式 S-->
                <div id="shippinglist">

                </div>
                <!--配送方式 E-->
                <!--买家留言 S-->
                <div class="psxx">
                    <dl><dt>买家留言</dt><dd><input name="order_message" type="text" placeholder="选填，给商家留言(45字以内)" class="text"></dd></dl>
                </div>
                <!--买家留言 E-->
                <!--商品总额 S--> 
                <div class="spdd_jg">
                    <ul>
                        <li><span>￥<span id="allproduct"><?php echo ($total); ?>元 </span></span>商品总额</li>
                        <?php if($orderType == 2 or $orderType == 3): ?><li><span>￥<span id="haiguan_rate_total"><?php echo ($haiguan_rate_total); ?>元 </span></span>   税费 </li><?php endif; ?>
                        <li>商品总重量<span id="goods_weight"><?php echo ($goodsTotalWeight); ?>千克</span>   </li>
                        <li><span>+ ￥<span id="pricedis"><?php echo ($shipping_fee); ?> </span></span>运费</li>

                    </ul>
                </div>
                <!--商品总额 E--> 
                <!--底部 S-->
                <div class="flow_footer" style="bottom:0;">
                    <input type="hidden" name="senderid" value='<?php echo ($address["id"]); ?>' id="senderid" >
                    <input type="hidden" name="tag" value="<?php echo ($tag); ?>"> 
                    <input type="hidden" name="orderType" value="<?php echo ($orderType); ?>"> 
                    <input type="hidden" name="iscart" value="<?php echo ($iscart); ?>"> 
                    <input type="hidden" name="shipping_fee" value="<?php echo ($shipping_fee); ?>"> 
                    <div class="flow_footer_cz"> 
                        <!--<a href="flow_Payment.html" class="jiesuan">提交订单</a>-->
                        <input type="submit" name="submit" value='提交订单' class='jiesuan' />
                    </div>
                    <h3 class="flow_sfk">实付款：<span><strong id="allprice">￥<?php echo ($all); ?></strong></span></h3>
                </div>
            </form>
            <div class="b_line"></div>
            <!--底部 E--> 
            <style>
                .footer{display: none;}
            </style>
        </div>
        <!-- page1 end--->
    </div>
    <!--地址管理 page2 s-->
    <div class="c_page2" style="display: none;" id="myaddresslist">

    </div>
    <!--地址管理 page2 e-->

    <!-- page3 s-->
    <div class="c_page3" style="display:none">
        <!--头部 S-->
        <div class="herder">
            <a href="javascript:;" class="back back_pre3"><i></i></a>
            <h3>编辑地址</h3>
            <!-- <div class="herd_r"><a href="search.html" class="search"><i></i></a></div>-->
        </div>
        <div class="t_line"></div>
        <!--头部 E--> 
        <!--地址列表 S--> 
        <div class="edit_address">

        </div>
        <!--地址列表 E-->  
        <!--底部 S-->
        <div class="b_line"></div>
        <!--底部 E-->
    </div>
    <!-- page3 e-->
    <div class="tdivbg" style="display:none"></div>


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

    <script>
        function back_pre2() {
            $(".tdivbg").css({"z-index": "100"});
            $(".c_page1,.tdivbg").css({"display": "block"});
            $(".c_page2").addClass("lrmove animated");
            setTimeout(removeClass, 1000);
            function removeClass() {
                $(".c_page2").removeClass("lrmove animated");
                $(".c_page2,.tdivbg").css({"display": "none"});
            }
        }
        $(function() {
            $(".address").click(function() {
                $(".tdivbg").css({"z-index": "100"});
                $(".c_page2,.tdivbg").css({"display": "block"});
                $(".c_page2").addClass("fadeInRightBig animated");
                setTimeout(removeClass, 1000);
                //});
                function removeClass() {
                    $(".c_page2").removeClass("fadeInRightBig animated");
                    $(".c_page1,.tdivbg").css({"display": "none"});
                }
            });
//            $("body").on('click', '.edi,.add_loginbtn', function() {
////            $(".edi,.add_loginbtn").click(function() {
//                $(".tdivbg").css({"z-index": "102"});
//                $(".c_page3,.tdivbg").css({"display": "block"});
//
//                $(".c_page3").addClass("fadeInRightBig animated");
//                setTimeout(removeClass, 1000);
//                //});
//                function removeClass() {
//                    $(".c_page3").removeClass("fadeInRightBig animated");
//                    $(".c_page2,.tdivbg").css({"display": "none"});
//                }
//            });
            $("body").on('click', '.back_pre2', function() {
//            $(".back_pre2").click(function() {
                back_pre2();
            });



            $(".back_pre3").click(function() {
                $(".c_page2,.tdivbg").css({"display": "block"});
                $(".c_page3").addClass("lrmove animated");
                setTimeout(removeClass, 1000);
                function removeClass() {
                    $(".c_page3").removeClass("lrmove animated");
                    $(".c_page3,.tdivbg").css({"display": "none"});
                }
            });
            $(".btn_save").click(function() {
                console.log("...");
                $(".c_page2,.c_page3,.tdivbg").css({"display": "none"});
                $(".c_page1").css({"display": "block"});
            });

        })







        $(".Radio_yhj").click(function() {
            $(".yhjsyye").toggle();
        });

        var address_layer = '';
        //地址编辑
        function open_address(address_id) {

            $.ajax({
                url: "<?php echo U('Checkout/ajaxaddress');?>",
                type: "post",
                dataType: "json",
                data: {id: address_id},
                success: function(json) {
                    if (json.status) {
                        $(".edit_address").html(json.info);
                    }
                    return false;
                }
            });

            $(".tdivbg").css({"z-index": "102"});
            $(".c_page3,.tdivbg").css({"display": "block"});

            $(".c_page3").addClass("fadeInRightBig animated");
            setTimeout(removeClass, 1000);
            //});
            function removeClass() {
                $(".c_page3").removeClass("fadeInRightBig animated");
                $(".c_page2,.tdivbg").css({"display": "none"});
            }

        }

        /**
         * 异步获取配送方式
         * @returns {undefined}
         */
        function shippinglist(address_id) {
             var orderType = "<?php echo ($orderType); ?>";
            $.ajax({
                url: "<?php echo U('Checkout/ajaxshipping');?>",
                type: "post",
                dataType: "json",
                data: {address_id: address_id,orderType:orderType},
                success: function(json) {
                    if (json.status) {
                        $("#shippinglist").html(json.info);
                        $('#pricedis').text(json.money);   // 运费
                        $("input[name=shipping_fee]").val(json.money);
                        if (json.disable == false) {
                            $("input[type=submit]").attr('disabled', 'disabled').css({'background': '#ccc', 'cursor': 'not-allowed'});
                        } else {
                            $("input[type=submit]").removeAttr("disabled").css({'background': '', 'cursor': 'pointer'});
                        }
                        clacOrderFee(); // 计算订单金额
                        return false;
                    }

                    layer.msg('加载地址错误');
                    return false;
                }
            });
        }
        
         function getshippingfee(address_id){
             $.ajax({
                url: "<?php echo U('Checkout/ajaxshippingfee');?>",
                type: "post",
                dataType: "json",
                data: {address_id: address_id},
                success: function(json) {
                    if (json.status) {
//                        $("#shippinglist").html(json.info);
                        $.each(json.fee,function(index, item)
                        { 
                            // alert( "the man's no. is: " + index + ",and " + content.name + " is learning " + content.lang ); 
//                            console.log(item.shipping_fee);
                            $("#shipping_fee_ware_"+index).html(item.shipping_fee);
                        });

                        $("input[name=shipping_fee]").val(json.money);
                        $('#pricedis').text(json.money);   // 运费
                        if (json.disable == false) {
                            $("input[type=submit]").attr('disabled', 'disabled').css({'background': '#ccc', 'cursor': 'not-allowed'});
                        } else {
                            $("input[type=submit]").removeAttr("disabled").css({'background': '', 'cursor': 'pointer'});
                        }
                        clacOrderFee(); // 计算订单金额
                        return false;
                    }

                    layer.msg('加载地址错误');
                    return false;
                }
            });
        }

        /**
         * 获取我的地址列表
         * @returns {undefined}
         */
        function myaddresslist() {
            $.ajax({
                url: "<?php echo U('Checkout/ajaxaddresslist');?>",
                type: "post",
                dataType: "json",
//                data: {id: address_id},
                success: function(json) {
                    if (json.status) {
                        $("#myaddresslist").html(json.info);
                        return false;
                    }
                    layer.msg('加载地址错误');
                    return false;
                }
            });
        }
        function delAddress(aid) {
            //判断新地址是否选中,获取地址id
            var con_index = layer.confirm('确定要删除该地址吗？', {icon: 3, title: '提示'}, function(index) {
                if (index) {
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
                                layer.msg(data.info);
                                myaddresslist();
                                getshippingfee(0);
                            },
                            error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                                alert(XMLHttpRequest + thrownError);
                            }
                        });      
                     }
                    layer.close(con_index);
                }
            });

        }


        $(function() {
            myaddresslist();    // 加载我的地址列表
            getshippingfee(0);     // 加载配送方式
            /*重新计算运费及总金额*/
            $("#shippinglist").delegate(".peisong", "change", function() {
//                var yunfei = parseFloat($(this).attr('money'));
               var yunfei = parseFloat($('.peisong option:selected') .attr('money'));
//               alert(yunfei);
//                yunfei =  isNaN(yunfei) ? yunfei : 0;
                var yfsump = yunfei.toFixed(2);
                $('#pricedis').text(yfsump);   // 运费
                clacOrderFee();
            });

        });

        /**
         * 计算订单价格
         * @returns {undefined}
         */
        function clacOrderFee() {

            var yunfei = parseFloat($('#pricedis').text());  // 运费
            yunfei = yunfei.toFixed(2);
            var allproduct = parseFloat($('#allproduct').text());  // 商品价格
            var haiguan_rate_total = parseFloat($('#haiguan_rate_total').text());  // 海关费
            if (isNaN(haiguan_rate_total)) {
                haiguan_rate_total = 0;
            }
            var allprice = parseFloat(eval(allproduct) + eval(yunfei) + eval(haiguan_rate_total));       // 订单总价
            var sump = allprice.toFixed(2);

            $('#allprice').text(sump);
        }
//        选择地址
        function  chooseaddress(obj) {
            var address_id = $(obj).attr("addressid");
            getshippingfee(address_id);
            $("#senderid").val(address_id);
//            $(obj).parents('li').siblings().addClass('active6').removeClass('active6')
//            $(obj).parents('li').addClass('active6');
            $('.check').removeClass('checked');
            $(obj).find('i').addClass('checked');
            $.ajax({
                type: 'post', //传送的方式,get/post
                url: '<?php echo U("Checkout/getaddressinfo");?>', //发送数据的地址
                data: {addressid: address_id},
                dataType: "json",
                success: function(data)
                {
                    $(".address").html(data.info);
                    back_pre2();
                }
            });      
//          $(obj).parents('li').toggleClass('active6');
        }

        /*
         *检查地址是否选择 
         */
        function checkaddress() {
            //判断默认地址是否选中,获取地址id
            var addressid = $("#senderid").val();
//            $("#senderid").val(addressid);
//            alert(addressid);
            if (typeof (addressid) == "undefined") {
                document.getElementById('myaddresslist').scrollIntoView();
                layer.msg("请选择一个收货地址");
                return false;
            } else {
                return true;
            }
            return false;
        }

    </script>


</body>
</html>