<?php if (!defined('THINK_PATH')) exit();?><h2>收货地址</h2>
<div class="add2  clear">
    <ul>
        <?php if(is_array($address)): $i = 0; $__LIST__ = $address;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$myaddress): $mod = ($i % 2 );++$i;?><li  <?php if(($myaddress["isdefault"]) == "1"): ?>class="active6"<?php endif; ?> >
                <h3><span class="name"><?php echo ($myaddress["realname"]); ?></span><span class="phone"><?php echo ($myaddress["cellphone"]); ?></span></h3>
                <p><?php echo ($myaddress["province"]); ?> <?php echo ($myaddress["city"]); ?> <?php echo ($myaddress["area"]); ?> <?php echo ($myaddress["address"]); ?></p>
                <i class="line2"></i>
                <p class="user-id">身份证号：<?php echo ($myaddress["card_no"]); ?></p>
                <div class="edit ">
                    <!--<input type="radio" name="mysender" class="ra" value="<?php echo ($myaddress["id"]); ?>" <?php if(($myaddress["isdefault"]) == "1"): ?>checked="checked"<?php endif; ?> />-->
                    <a class="b-btn edit1" href="javascript:open_address(<?php echo ($myaddress["id"]); ?>)">编辑</a>
                    <a  class="b-btn" href="javascript:delAddress(<?php echo ($myaddress["id"]); ?>);">删除</a>
                </div>
                <a  class="setDef bg" addressid='<?php echo ($myaddress["id"]); ?>' onclick='chooseaddress(this)' title="选择收货地址"></a>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
        <li class="toAdd">
            <a href="javascript:open_address(0)">
                <i></i>
                添加地址
            </a>
        </li>
    </ul>
</div>