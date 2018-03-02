<?php if (!defined('THINK_PATH')) exit();?>﻿
<?php if(!empty($config['type'])){ ?>

<span>第三方帐号登录：</span>
<ul class="clear">
    <?php if(in_array('Qq',$config['type'])){ ?>
        <li><a href="<?php if(in_array('Qq',$config['type'])){ echo addons_url('SyncLogin://Base/login',array('type'=>'qq'));} ?>" class="lqq" title="用QQ账号登录"></a></li>
    <?php } ?>
    <?php if(in_array('Sina',$config['type'])){ ?>
    <li><a href="<?php if(in_array('Sina',$config['type'])){ echo addons_url('SyncLogin://Base/login',array('type'=>'sina'));} ?>" class="wb" title="用微博账号登录"></a></li>
    <?php } ?>
    <?php if(in_array('Wx',$config['type'])){ ?>
     <li>
         <?php  $appid = $config['WxKEY']; $urls = urlencode("http://miaork.m1ju.com/index.php/Member/weixin_login.html"); ?>
         <!--<a href="https://open.weixin.qq.com/connect/qrconnect?appid=<?php echo ($appid); ?>&redirect_uri=<?php echo ($urls); ?>&response_type=code&scope=snsapi_login#wechat_redirect" class="wx" title="用微信账号登录"></a>-->
         <a href="https://open.weixin.qq.com/connect/qrconnect?appid=<?php echo ($appid); ?>&redirect_uri=http://miaork.m1ju.com/index.php/Member/weixin_login&response_type=code&scope=snsapi_login#wechat_redirect" class="wx" title="用微信账号登录"></a>
        <!--https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxf0e81c3bee622d60&redirect_uri=http%3A%2F%2Fnba.bluewebgame.com%2Foauth_response.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect-->
         
         <!--<a href="<?php if(in_array('Wx',$config['type'])){ echo addons_url('SyncLogin://Base/login',array('type'=>'Wx'));} ?>" class="wx" title="用微信账号登录"></a></li>-->
    <!--<li><a href="javascript:;" class="wx"></a></li>-->
    <?php } ?>
</ul>

<div class="login" style="display: none;">
    <div class="legend">第三方账号登录</div>
    <div style="text-align:left;padding-top:25px;">
        <a class="" href="<?php if(in_array('Qq',$config['type'])){ echo addons_url('SyncLogin://Base/login',array('type'=>'qq'));} ?>"><img src="/Public/Web/img/qq.png">用QQ账号登录</a></br></br>
        <a class="" href="<?php if(in_array('Sina',$config['type'])){ echo addons_url('SyncLogin://Base/login',array('type'=>'sina'));} ?>"><img src="/Public/Web/img/weibo.png">用微博账号登录</a>
        <!--<a class="btn btn-warning btn-block" href="<?php if(in_array('Douban',$config['type'])){ echo addons_url('SyncLogin://Base/login',array('type'=>'duban'));} ?>">用豆瓣账号登录</a>
        <a class="btn btn-primary btn-block" href="<?php if(in_array('Renren',$config['type'])){ echo addons_url('SyncLogin://Base/login',array('type'=>'renren'));} ?>">用人人账号登录</a>-->
    </div>
</div>
<?php } ?>