<?php if (!defined('THINK_PATH')) exit(); switch($show_page): case "banner": if($list['type'] == 2): ?><!-- type eq one is index advs -->
        <?php if(is_array($list["res"])): $k = 0; $__LIST__ = $list["res"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lo): $mod = ($k % 2 );++$k;?><div class="swiper-slide">
                <a href="<?php echo ($lo["link"]); ?>" target="_blank" title="<?php echo ($lo["title"]); ?>">
                    <img src="<?php echo ($lo["path"]); ?>" alt="<?php echo ($lo["title"]); ?>" />
                    <?php if($lo['showtitle'] > 0): ?><span><?php echo ($lo["title"]); ?></span><?php endif; ?>
                </a>
            </div><?php endforeach; endif; else: echo "" ;endif; ?> 
        <?php elseif($list['type'] == 1): ?>
        <?php if(!empty($list['path'])): ?><a href="<?php echo ($list["link"]); ?>" target="_blank" title="<?php echo ($list["title"]); ?>" style="width:<?php echo ($list['width']); ?>px;height:<?php echo ($list['height']); ?>px;" >
                <img alt="<?php echo ($list["title"]); ?>" src="<?php echo ($list["path"]); ?>"  style="width:<?php echo ($list['width']); ?>px;height:<?php echo ($list['height']); ?>px;" />
            </a><?php endif; endif; break;?>
<?php case "pc_banner": ?><!--<script type="text/javascript" src="/Public/Web/js/jdSlide.js"></script>-->
    <?php if($list['type'] == 2): ?><!-- type eq one is index advs -->
        <div class="index_nav">
	<ul class="slides">
            <?php if(is_array($list["res"])): $k = 0; $__LIST__ = $list["res"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lo): $mod = ($k % 2 );++$k;?><li style="background:url('<?php echo ($lo["path"]); ?>') 50% 0 no-repeat;"><a href="<?php echo ($lo["link"]); ?>" title="<?php echo ($lo["title"]); ?>"></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
	</div>
 
        <?php elseif($list['type'] == 1): ?>
        <?php if(!empty($list['path'])): ?><div class="index_nav">
            <ul class="slides">
                <?php if(is_array($list["res"])): $k = 0; $__LIST__ = $list["res"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lo): $mod = ($k % 2 );++$k;?><li style="background:url('<?php echo ($lo["path"]); ?>') 50% 0 no-repeat;"><a href="<?php echo ($lo["link"]); ?>" title="<?php echo ($lo["title"]); ?>"></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            </div>
            <a href="<?php echo ($list["link"]); ?>" target="_blank" title="<?php echo ($list["title"]); ?>" style="width:<?php echo ($list['width']); ?>px;height:<?php echo ($list['height']); ?>px;" >
                <img alt="<?php echo ($list["title"]); ?>" src="<?php echo ($list["path"]); ?>"  style="width:<?php echo ($list['width']); ?>px;height:<?php echo ($list['height']); ?>px;" />
            </a><?php endif; endif; break;?>
<?php case "cate": if($list['type'] == 2): ?><!-- type eq one is index advs -->
       <div class="tui_jian">
        <?php if(is_array($list["res"])): $k = 0; $__LIST__ = $list["res"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lo): $mod = ($k % 2 );++$k;?><div class="">
                <a href="<?php echo ($lo["link"]); ?>" title="<?php echo ($lo["title"]); ?>">
                    <img src="<?php echo ($lo["path"]); ?>" alt="<?php echo ($lo["title"]); ?>"/>
                </a>
            </div><?php endforeach; endif; else: echo "" ;endif; ?> 
        </div> 
        <?php elseif($list['type'] == 1): ?>
        <?php if(!empty($list['path'])): ?><div class="tui_jian">
                <div class="">
                    <a href="<?php echo ($list["link"]); ?>"  target="_blank" title="<?php echo ($list["title"]); ?>">
                        <img src="<?php echo ($list["path"]); ?>" alt="<?php echo ($list["title"]); ?>"/>
                    </a>
                </div>
            </div><?php endif; endif; break;?>
<?php default: ?>
<?php if($list['type'] == 2): ?><!-- type eq one is index advs -->
    <div class="bd">
        <ul>
            <?php if(is_array($list["res"])): $k = 0; $__LIST__ = $list["res"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lo): $mod = ($k % 2 );++$k;?><li>
                    <a href="<?php echo ($lo["link"]); ?>" target="_blank" title="<?php echo ($lo["title"]); ?>">
                        <img src="<?php echo ($lo["path"]); ?>" alt="<?php echo ($lo["title"]); ?>" />
                        <!--style="width:<?php echo ($list["width"]); ?>px;height:<?php echo ($list["height"]); ?>px;"--> 
                        <!--<?php if($lo['showtitle'] > 0): ?><span><?php echo ($lo["title"]); ?></span><?php endif; ?>-->
                    </a>
                </li><?php endforeach; endif; else: echo "" ;endif; ?> 
        </ul>
    </div>
    <?php elseif($list['type'] == 3): ?>
    <div style="width:<?php echo ($list['width']); ?>px;height:<?php echo ($list['height']); ?>px;background: #cecece;">
        <div style="margin:0px auto;width:150px;line-height: 30px;"><?php echo ($list["advstext"]); ?></div></div>
    <?php elseif($list['type'] == 1): ?>
    <?php if(!empty($list['path'])): ?><a href="<?php echo ($list["link"]); ?>" target="_blank" title="<?php echo ($list["title"]); ?>" style="width:<?php echo ($list['width']); ?>px;height:<?php echo ($list['height']); ?>px;" >
            <img alt="<?php echo ($list["title"]); ?>" src="<?php echo ($list["path"]); ?>"  style="width:<?php echo ($list['width']); ?>px;height:<?php echo ($list['height']); ?>px;" />
        </a><?php endif; ?>
    <?php elseif($list['type'] == 4): ?>
    联系管理员<?php endif; endswitch;?>

<!-- add more -->