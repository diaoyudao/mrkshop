<?php

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * URL组装 支持不同URL模式
 * @param string $url URL表达式，格式：'[模块/控制器/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param string|boolean $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $domain 是否显示域名
 * @return string
 */
function url($url = '', $vars = '', $suffix = true, $domain = false) {
    return U($url, $vars, $suffix, $domain);
}

/**
 * 缓存管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function tycache($name, $value = '', $options = null) {
    return S($name, $value, $options);
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1) {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}


    /**
     *  生成指定目录不重名的文件名
     *
     * @access  public
     * @param   string      $dir        要检查是否有同名文件的目录
     *
     * @return  string      文件名
     */
    function unique_name($dir){
        $filename = '';
        while (empty($filename)){
            $filename = 'face';
            if (file_exists($dir . $filename . '.jpg') || file_exists($dir . $filename . '.gif') || file_exists($dir . $filename . '.png')){
                $filename = '';
            }
        }
        return $filename;
    }

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1) {
    static $count;
    if (!isset($count[$category])) {
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

function get_group_price($unionid) {
    $unionid = explode('、', $unionid);
    $total = "";
    foreach ($unionid as $val) {
        $id = $val;
        $total+=get_good_price($id);
    }

    return $total;
}

function get_group_count($unionid) {
    $array = explode('、', $unionid);
    $number = count($array);

    return $number;
}

function get_face($uid) {
    //get_cover(get_face($gcvo["uid"]),'path');
    //__PICURL__{$gcvo.uid|get_face|get_cover='path'}
    $comment = M("ucenter_member");
    $map['id'] = $uid;
    $count = $comment->where($map)->find();

    return $count["face"];
}

function get_comment_count($id) {
    $comment = M("comment");
    $map['goodid'] = $id;
    $count = $comment->where($map)->count();

    return $count;
}

function get_message_count($id) {
    $message = M("message");
    $map['goodid'] = $id;
    $count = $message->where($map)->count();

    return $count;
}

function get_group_marketprice($unionid) {
    $unionid = explode('、', $unionid);
    $total = "";
    foreach ($unionid as $val) {
        $id = $val;
        $total+=get_good_yprice($id);
    }

    if (!isset($total)) {
        $price = get_group_price($unionid);
    }
    return $total ? $total : $price;
}

/**
 * 返回优惠券可抵用金额
 */
function get_fcoupon_fee($code, $total) {
    $lowfee = get_fcoupon_lowpayment($code); //优惠券最低消费金额
    if ($lowfee < $total) {
        $codeid = M("fcoupon")->where("code='$code' and status='1'")->getField('id'); //获取优惠券主键id
        $fee = get_coupon_price($codeid); //获取优惠券等值金额
        $usercouponid = M("usercoupon")->where("couponid='$codeid' and status='1'")->getField('id'); //获取用户可用优惠券主键id
        if ($usercouponid) {
            $deccode = $fee;
            M("usercoupon")->where("couponid='$codeid'")->setField('status', 2); //设置优惠券已用
        } else {
            $deccode = 0;
        }
    } else {
        $deccode = 0;
    }
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id) {
    static $count;
    if (!isset($count[$id])) {
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

function get_tuan_count($id) {
    $number = M('Tuanid')->where("tuanpid='$id'")->count();
    return $number;
}

function get_shop_mobile($id) {
    $info = M('shop')->where("id='$id'")->find();
    return $info["mobile"];
}

function get_shop_address($id) {
    $info = M('shop')->where("id='$id'")->find();
    return $info["shopaddress"];
}

/**
 * 获取首页幻灯片
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 烟消云散 <1010422715@qq.com>
 */
function get_slide() {
    $slide = M('slide');
    $slidelist = $slide->where('status=1')->select();
    return $slidelist;
}


/* * *************************************************************
 * created date:2015/4/27 9:12
 * created author:sheshanhu
 * content:根据不同页面调用有情链接地址信息 links表操作
 * modefiy person:
 * modefiy date:
 * note:
 * ************************************************************** */

function get_friendlink($position = 0, $type = 1) {
    $map = array();
    //分频道$map["domainid"]=cookie("current_domainid");
    $map["type"] = $type;
    $map["position"] = $position;
    $map["status"] = 1;
    $links = M('links');
    $slinks = $links->where($map)->select();
    return $slinks;
}

//获取对于频道分组名称
function get_domain_bindgroup($value, $key = "mark") {
    $message = M("subdomain");
    $map[$key] = $value;
    $bindgroup = $message->where($map)->getField("bindgroup");
    return $bindgroup;
}
