<?php if (!defined('THINK_PATH')) exit();?><div class="comment">
    <?php if($commentlist): ?><h4 class="bgc2">
            <span class="active5">全部评价(6586)</span><span>好评(4576)</span><span>中评(565)</span><span>差评(56)</span><span>晒单（575）</span>
        </h4>
        <ul class="com_list">
            <?php if(is_array($commentlist)): $i = 0; $__LIST__ = $commentlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$commentinfo): $mod = ($i % 2 );++$i;?><li>
                    <div class="head-pro ">
                        <a href="#">
                            <?php $impath = get_face($commentinfo["uid"]); $random = time(); ?>
                            <?php if(!empty($impath)): ?><img src="/Uploads/Face/<?php echo ($commentinfo["uid"]); ?>/face.jpg?r=<?php echo ($random); ?>" alt="<?php echo (get_username($commentinfo['uid'])); ?>">
                                <?php else: ?>
                                <img src="/Public/Wap/images/default.png" alt=""><?php endif; ?>
                            <?php echo (get_username($commentinfo['uid'])); ?>
                        </a>
                    </div>
                    <div class="com-right">
                        <p class="com-nr"><?php echo ($commentinfo["content"]); ?></p>
                        <div class="sai clear">
                            <ul class="clear  saitu">
                                <?php if(is_array($commentinfo['picss'])): foreach($commentinfo['picss'] as $key=>$pic): ?><li><img src="<?php echo ($pic); ?>" width="60" heigth="60" alt="<?php echo ($commentinfo["content"]); ?>"></li><?php endforeach; endif; ?>
<!--                                <li><img src="/Public/Wap/images/pro-Detail/show-pro.jpg" alt=""></li>
                                <li><img src="/Public/Wap/images/pro-Detail/show-pro.jpg" alt=""></li>
                                <li><img src="/Public/Wap/images/pro-Detail/show-pro.jpg" alt=""></li>-->
                            </ul>
                            <div class="ping ">
                                <div class="pingfen pingfen<?php echo ($commentinfo["content"]); ?>"><img src="/Public/Wap/images/commstar.jpg" /> </div>
                                <div class="colo9">
                                    <!--<span class="guigei">规格：150g</span>-->
                                    <span class="date">评论时间：<?php echo (date('Y-m-d H:i:s',$commentinfo['create_time'])); ?></span></div>
                            </div>
                        </div>
                    </div>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>

        <div class="page_box clear">
            <?php echo ($_page); ?>
        </div>
        <script type="text/javascript">
            function getcomments(p) {
                var gid = '<?php echo ($gid); ?>';
                $.get('<?php echo U("Comment/ajaxlists");?>', {p: p, gid: gid}, function(data) {
                    $("#aboutcommentlist").html(data.info);
                    $("html,body").animate({scrollTop: $("#good_other_info").offset().top}, 500);
                });
            }
        </script>
        <?php else: ?>
        <div class="nonecomment" >
            暂无评论
        </div><?php endif; ?>


    <form style="display: none;" action="<?php echo U('Comment/addajax');?>" class="comment_info" method="post" name="commentform" id="commentform" onsubmit="return commentValidate();">
        <h3 class="qbpl-tit-a">发表评价</h3>
        <div id="commentmessage" style="display:none;"></div>
        <div class="textarea-c">
            <input type="hidden" id="score" name="score" value="3">
            <input type="hidden" class="goodid" name="goodid" value="<?php echo ($gid); ?>">
            <input type="hidden" class="shopid" name="shopid" value="">
            <input type="hidden" class="orderid" name="orderid" value="">
            <input type="hidden" class="good" name="goodscore" value="5">
            <input type="hidden" class="service" name="servicescore" value="5">
            <input type="hidden" class="delivery" name="deliveryscore" value="5">
            <textarea rows="4" cols="90" maxlength="200" id="goodscomment" name="content" placeholder="请输入5-200字评论内容"></textarea>
        </div>
        <div class="btn-pinglun">
            <input type="submit" class="sub" name="submit" value="提交">
        </div>
    </form>
</div>