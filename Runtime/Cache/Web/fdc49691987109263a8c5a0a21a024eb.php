<?php if (!defined('THINK_PATH')) exit();?><h2>配送方式 &nbsp;
    <?php if(!empty($lowwest)): ?>(消费满<?php echo ($lowwest); ?>元免邮)
        <?php if($all < $lowwest): ?><a class="toshopcart" target="_blank" href="<?php echo U('Goods/lists');?>">&lt;&lt; 前去凑单</a><?php endif; endif; ?></h2>
<p style="display: none;">客户您好：你所下单得客户为：<span class="where">XXX门店</span>，地址：<span class="ad">XXXXXXXX</span>,联系电话：<span class="tel">138XXXXXXXXXX</span> 请合理根据您自身的情况选择配送方式</p>
<?php if(!empty($distribution)): if(is_array($distribution)): $k = 0; $__LIST__ = $distribution;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dvo): $mod = ($k % 2 );++$k;?><div>
        <input type="radio" id='kd<?php echo ($k); ?>' money="<?php echo ($dvo["money"]); ?>" class="radiodistribution" <?php if(($key) == "0"): ?>checked="checked"<?php endif; ?> value="<?php echo ($dvo['id']); ?>" name="distribution">
        <label for="kd<?php echo ($k); ?>"><?php echo ($dvo['title']); ?></label>
        <?php if(!empty($lowwest)): if($all < $lowwest): $distriprice = $dvo['price']; ?>
                <?php else: ?>
                <?php $distriprice = '0'; endif; ?>    
            <?php else: ?>
            <?php $distriprice = $dvo['price']; endif; ?>

        <!--<span>( <?php if($dvo["price"]): ?>￥<?php echo ($dvo['price']); else: ?> 免运费<?php endif; ?>)</span>-->
        <span>(订单总重量在<?php echo ($dvo["snum"]); ?>千克内<?php echo ($dvo["sprice"]); ?>元,每增加<?php echo ($dvo["xnum"]); ?>千克，增加<?php echo ($dvo["xprice"]); ?>元)</span>
        <span><?php echo ($dvo['description']); ?></span>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
    <div>
        <p>所在地区没有配送方式，不能下单</p>
    </div><?php endif; ?>
<!--<div><a href="javascript:alert('配送规则')">配送规则？</a></div>-->