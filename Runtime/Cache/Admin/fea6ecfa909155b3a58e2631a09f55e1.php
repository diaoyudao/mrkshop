<?php if (!defined('THINK_PATH')) exit();?><div class="span<?php echo ($addons_config["width"]); ?>">
	<div class="columns-mod">
		<div class="hd cf">
			<h5>软件支持</h5>
			<div class="title-opt">
			</div>
		</div>
		<div class="bd">
			<div class="sys-info">
				<table>
					<tr>
						<th>系统名称</th>
						<td><?php echo C('SITENAME');?></td>
					</tr>
					<tr>
						<th>程序维护</th>
						<td><?php echo C('DOMAIN');?></td>
					</tr>
					<tr>
						<th>官方网址</th>
						<td><a href="<?php echo C('DOMAIN');?>" target="_blank"><?php echo C('DOMAIN');?></a></td>
					</tr>
					<tr>
						<th>核心框架</th>
						<td>ThinkPHP 3.2</td>
					</tr>
					<tr>
						<th>BUG反馈</th>
						<td><a href="<?php echo C('DOMAIN');?>" target="_blank"><?php echo C('SITENAME');?></a></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>