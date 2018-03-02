<?php if (!defined('THINK_PATH')) exit();?><h4>最新加入的商品</h4>
<?php if(!empty($cart_list["cart_list"])): ?><ul id='head_cart'>
        <?php if(is_array($cart_list["cart_list"])): $i = 0; $__LIST__ = $cart_list["cart_list"];if( count($__LIST__)==0 ) : echo "购物车空空如也" ;else: foreach($__LIST__ as $key=>$cart): $mod = ($i % 2 );++$i;?><li >
                <div style="width: 20%"><img src="<?php echo (get_cover_picture_url($cart["goodid"])); ?>" alt="<?php echo (get_good_name($cart["goodid"])); ?>" /></div>
                <div style="width: 54%" class="intr "><?php echo (get_good_name($cart["goodid"])); ?></div>
                <div style=" width:18%" class="price"><p>￥<?php echo ($cart[price]); ?> * <?php echo ($cart["num"]); ?></p> 
                    <a href="javascript:;" onclick="delcart2(this);" class="delcart2" rel="<?php echo ($cart["sort"]); ?>">&nbsp;&nbsp;删除</a></div>
            </li><?php endforeach; endif; else: echo "购物车空空如也" ;endif; ?>
    </ul>
    <?php else: ?>
    <div class="g_emty"><img src="/Public/Web/img/add_8.jpg"></div><?php endif; ?>

<div class="count">共<span class="cart_sum"><?php echo ($cart_list["goods_count"]); ?></span>件商品</span> 
<b>共计￥<span id='total' ><?php echo ($cart_list["goods_total"]); ?></span></b> 
<?php if(!empty($cart_list["cart_list"])): ?><a class="topay " href="<?php echo U('cart/index');?>">去购物车结算<i class="bg"></i></a>
    <?php else: ?>
    <a class="topay_empty" href="javascript:;">去购物车结算<i class="bg"></i></a><?php endif; ?>
</div>