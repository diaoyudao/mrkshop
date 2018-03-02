<?php if (!defined('THINK_PATH')) exit(); if($recordlist): ?><h4 class="bgc2">
            <span class="active5">购买记录</span>
     </h4>
<table border="0" cellpadding="0" cellspacing="0" class="jy-list"  >
    <thead>
        <tr>
            <th width="136">姓名</th><th width="160">成交金额</th><th width="136">数量</th><th width="240">时间</th><th class="border_none">规格</th>
        </tr>
        <tr  class="bb">
            <th colspan="5"></th>
        </tr>
    </thead>
    <tbody class="border-top">
    <?php if(is_array($recordlist)): $i = 0; $__LIST__ = $recordlist;if( count($__LIST__)==0 ) : echo "没有数据" ;else: foreach($__LIST__ as $key=>$shopinfo): $mod = ($i % 2 );++$i;?><tr>
            <td> <?php echo (get_username($shopinfo['uid'])); ?></td><td> ￥5<?php echo ($shopinfo['total']); ?>   </td><td> <?php echo ($shopinfo['num']); ?> </td><td>  <?php echo (date('Y-m-d H:i',$shopinfo['create_time'])); ?> </td><td> <?php echo ($shopinfo["parameters"]); ?> </td>
        </tr><?php endforeach; endif; else: echo "没有数据" ;endif; ?>
</tbody>
</table>
<div class="page_box clear">
    <?php echo ($_page); ?>
</div>
<script type="text/javascript">
    function getshoplist(p) {
        var gid = '<?php echo ($gid); ?>';
        $.get('<?php echo U("Cart/ajaxlists");?>', {p: p, gid: gid}, function(data) {
            $("#recordlist").html(data.info);
        });
    }
</script>
<?php else: ?>
   <div class="empty" style="display: none;">
	暂无成交记录
    </div><?php endif; ?>