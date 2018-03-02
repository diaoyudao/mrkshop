<?php if (!defined('THINK_PATH')) exit();?><div class="tools" style="margin-bottom:10px;">
    <a class="btn" href="<?php echo addons_url('Links://Links/add');?>">新 增</a>
    <button class="btn ajax-post" target-form="ids" url="<?php echo addons_url('Links://Links/savestatus',array('status'=>1));?>">启 用</button>
    <button class="btn ajax-post" target-form="ids" url="<?php echo addons_url('Links://Links/savestatus',array('status'=>0));?>">禁用</button>
	<select name="position" style="float: right;" onchange="refleshoption(this.options[this.options.selectedIndex].value)">
	    <option value="" <?php if(!$_GET['position']): ?>selected<?php endif; ?>>频道全部</option>
	    <option value="5" <?php if(5 == $_GET['position']): ?>selected<?php endif; ?>>频道首页热门搜索</option>
	    <option value="4" <?php if(4 == $_GET['position']): ?>selected<?php endif; ?>>频道首页推荐阅读</option>
	    <option value="1" <?php if(1 == $_GET['position']): ?>selected<?php endif; ?>>频道首页友情链接</option>
	    <option value="6" <?php if(6 == $_GET['position']): ?>selected<?php endif; ?>>频道首页合作伙伴</option>
	    <option value="2" <?php if(2 == $_GET['position']): ?>selected<?php endif; ?>>频道百科页面</option>
	    <option value="3" <?php if(3 == $_GET['position']): ?>selected<?php endif; ?>>频道问答页面</option>
	    <option value="7" <?php if(7 == $_GET['position']): ?>selected<?php endif; ?>>频道品牌页面</option>
	    <option value="8" <?php if(8 == $_GET['position']): ?>selected<?php endif; ?>>频道排行榜页面</option> 
	</select> 
</div>
<table>
	<thead>
		<tr>
			<th class="row-selected row-selected"><input class="check-all" type="checkbox"></th>
			<th>序号</th>
			<?php if(is_array($listKey)): $i = 0; $__LIST__ = $listKey;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><th><?php echo ($vo); ?></th><?php endforeach; endif; else: echo "" ;endif; ?>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if(is_array($_list)): $vo = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv): $mod = ($vo % 2 );++$vo;?><tr>
			<td><input class="ids" type="checkbox" name="id[]" value="<?php echo ($lv["id"]); ?>"></td>
			<td><?php echo ($lv["id"]); ?></td>
			<?php if(is_array($listKey)): $i = 0; $__LIST__ = $listKey;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lk): $mod = ($i % 2 );++$i;?><td><?php echo ($lv["$key"]); ?></td><?php endforeach; endif; else: echo "" ;endif; ?>
			<td>
				<a href="<?php echo addons_url('Links://Links/edit',array('id'=>$lv['id']));?>">编辑</a>
				<?php if($lv["status"] == 1): ?><a class="confirm ajax-get" href="<?php echo addons_url('Links://Links/forbidden',array('id'=>$lv['id']));?>">禁用</a>
				<?php else: ?>
				<a class="confirm ajax-get" href="<?php echo addons_url('Links://Links/off',array('id'=>$lv['id']));?>">启用</a><?php endif; ?>
				<a class="confirm ajax-get" href="<?php echo addons_url('Links://Links/del',array('id'=>$lv['id']));?>">删除</a>
			</td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	</tbody>
</table>

<script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>
<script>
    function refleshoption(pp){
	var t="<?php echo I('title');?>";
	var s="";
	if (t) {
	   s="&title="+t;
	}
	window.location.href = '<?php echo U("Admin/Addons/adminList/name/Links");?>'+s+'&position='+pp;
    }
</script>