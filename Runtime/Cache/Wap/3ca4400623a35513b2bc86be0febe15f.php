<?php if (!defined('THINK_PATH')) exit();?><!--头部 S-->
<div class="herder">
    <a href="javascript:;" class="back back_pre2"><i></i></a>
    <h3>地址管理</h3>
    <!-- <div class="herd_r"><a href="search.html" class="search"><i></i></a></div>-->
</div>
<div class="t_line"></div>
<!--头部 E--> 
<!--地址列表 S--> 
<ul class="add_list" >
    <?php if(is_array($address)): $i = 0; $__LIST__ = $address;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$myaddress): $mod = ($i % 2 );++$i;?><li>
            <div class="info">
                <span><i><img src="/Public/Wap/images/user-name.png" /></i><?php echo ($myaddress["realname"]); ?></span>
                <span><i><img src="/Public/Wap/images/phone.png" /></i><?php echo (set_start_phone($myaddress["cellphone"])); ?></span>
                <span><i><img src="/Public/Wap/images/id.png" /></i><?php echo (set_start_card($myaddress["card_no"])); ?></span>
                <p><?php echo ($myaddress["province"]); ?> <?php echo ($myaddress["city"]); ?> <?php echo ($myaddress["area"]); ?> <?php echo ($myaddress["address"]); ?></p>
            </div>
            <div class="setD bg_fa clear">
                <div class="fl">
                    <a class=" " onclick="chooseaddress(this)" addressid="<?php echo ($myaddress["id"]); ?>"  href="javascript:;"><i class='check  <?php if(($myaddress["isdefault"]) == "1"): ?>checked<?php endif; ?> '></i>选择地址</a>
                </div>
                <div class="fr ">
                    <a href="javascript:;" onclick="delAddress(<?php echo ($myaddress["id"]); ?>)" class="dele">删除</a>
                    <a class="mr-0 edi" href="javascript:;" onclick="open_address(<?php echo ($myaddress["id"]); ?>)" >编辑</a>
                </div>
            </div>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
<div class="fix_bo" style="padding-bottom:0.3rem;background:#fff;bottom: 0;">
    <a href="javascript:;" class="long_btn add_loginbtn" onclick="open_address(0)">新增地址</a>
</div>
<!--地址列表 E-->  
<!--底部 S-->
<div class="b_line"></div>
<!--底部 E-->