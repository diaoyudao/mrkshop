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

////在线交易订单支付处理函数
////函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
////返回值：如果订单已经成功支付，返回true，否则返回false；
//function checkorderstatus($ordid) {
//    $Ord = M('Orderlist');
//    $ordstatus = $Ord->where('ordid=' . $ordid)->getField('ordstatus');
//    if ($ordstatus == 1) {
//        return true;
//    } else {
//        return false;
//    }
//}
//
////处理订单函数
////更新订单状态，写入订单支付后返回的数据
//function orderhandle($parameter) {
//    $ordid = $parameter['out_trade_no']; //商户网站订单系统中唯一订单号
//    $data['payment_trade_no'] = $parameter['trade_no']; //支付宝交易号
//    $data['payment_trade_status'] = $parameter['trade_status'];
//    $data['payment_notify_id'] = $parameter['notify_id']; //通知校验ID。
//    $data['payment_notify_time'] = $parameter['notify_time'];
//    $data['payment_buyer_email'] = $parameter['buyer_email']; //买家支付宝帐号；
//    $data['ordstatus'] = 1;
//    $Ord = M('Orderlist');
//    $Ord->where('ordid=' . $ordid)->save($data);
//    $data = array('status' => '1', 'ispay' => '2'); //设置订单为已经支付,状态为已提交
//    M('order')->where('orderid=' . $ordid)->setField($data);
//}

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

/* ===============统计====================== */

/**
 * 获得折线图统计图数据
 * param $statarr 图表需要的设置项
 */
function getStatData_LineLabels($stat_arr) {
    //图表区、图形区和通用图表配置选项
    $stat_arr['chart']['type'] = 'line';
    //图表序列颜色数组
    $stat_arr['colors'] ? '' : $stat_arr['colors'] = array('#058DC7', '#ED561B', '#8bbc21', '#0d233a');
    //去除版权信息
    $stat_arr['credits']['enabled'] = false;
    //导出功能选项
    $stat_arr['exporting']['enabled'] = false;
    //标题如果为字符串则使用默认样式
    is_string($stat_arr['title']) ? $stat_arr['title'] = array('text' => "<b>{$stat_arr['title']}</b>", 'x' => -20) : '';
    //子标题如果为字符串则使用默认样式
    is_string($stat_arr['subtitle']) ? $stat_arr['subtitle'] = array('text' => "<b>{$stat_arr['subtitle']}</b>", 'x' => -20) : '';
    //Y轴如果为字符串则使用默认样式
    if (is_string($stat_arr['yAxis'])) {
        $text = $stat_arr['yAxis'];
        unset($stat_arr['yAxis']);
        $stat_arr['yAxis']['title']['text'] = $text;
    }
    return json_encode($stat_arr);
}

/**
 * 获得Column2D统计图数据
 */
function getStatData_Column2D($stat_arr) {
    //图表区、图形区和通用图表配置选项
    $stat_arr['chart']['type'] = 'column';
    //去除版权信息
    $stat_arr['credits']['enabled'] = false;
    //导出功能选项
    $stat_arr['exporting']['enabled'] = false;
    //标题如果为字符串则使用默认样式
    is_string($stat_arr['title']) ? $stat_arr['title'] = array('text' => "<b>{$stat_arr['title']}</b>", 'x' => -20) : '';
    //子标题如果为字符串则使用默认样式
    is_string($stat_arr['subtitle']) ? $stat_arr['subtitle'] = array('text' => "<b>{$stat_arr['subtitle']}</b>", 'x' => -20) : '';
    //Y轴如果为字符串则使用默认样式
    if (is_string($stat_arr['yAxis'])) {
        $text = $stat_arr['yAxis'];
        unset($stat_arr['yAxis']);
        $stat_arr['yAxis']['title']['text'] = $text;
    }
    //柱形的颜色数组
    $color = array('#7a96a4', '#cba952', '#667b16', '#a26642', '#349898', '#c04f51', '#5c315e', '#445a2b', '#adae50', '#14638a', '#b56367', '#a399bb', '#070dfa', '#47ff07', '#f809b7');

    foreach ($stat_arr['series'] as $series_k => $series_v) {
        foreach ($series_v['data'] as $data_k => $data_v) {
            $data_v['color'] = $color[$data_k];
            $series_v['data'][$data_k] = $data_v;
        }
        $stat_arr['series'][$series_k]['data'] = $series_v['data'];
    }
    //print_r($stat_arr); die;
    return json_encode($stat_arr);
}

/**
 * 获得Column2D统计图数据
 */
function getStatData_Bar2D($stat_arr) {
    //图表区、图形区和通用图表配置选项
    $stat_arr['chart']['type'] = 'bar';
    //去除版权信息
    $stat_arr['credits']['enabled'] = false;
    //导出功能选项
    $stat_arr['exporting']['enabled'] = false;
    //标题如果为字符串则使用默认样式
    is_string($stat_arr['title']) ? $stat_arr['title'] = array('text' => "<b>{$stat_arr['title']}</b>", 'x' => -20) : '';
    //子标题如果为字符串则使用默认样式
    is_string($stat_arr['subtitle']) ? $stat_arr['subtitle'] = array('text' => "<b>{$stat_arr['subtitle']}</b>", 'x' => -20) : '';
    //Y轴如果为字符串则使用默认样式
    if (is_string($stat_arr['yAxis'])) {
        $text = $stat_arr['yAxis'];
        unset($stat_arr['yAxis']);
        $stat_arr['yAxis']['title']['text'] = $text;
    }
    //柱形的颜色数组
    $color = array('#7a96a4', '#cba952', '#667b16', '#a26642', '#349898', '#c04f51', '#5c315e', '#445a2b', '#adae50', '#14638a', '#b56367', '#a399bb', '#070dfa', '#47ff07', '#f809b7');

    foreach ($stat_arr['series'] as $series_k => $series_v) {
        foreach ($series_v['data'] as $data_k => $data_v) {
            $data_v['color'] = $color[$data_k];
            $series_v['data'][$data_k] = $data_v;
        }
        $stat_arr['series'][$series_k]['data'] = $series_v['data'];
    }
    //print_r($stat_arr); die;
    return json_encode($stat_arr);
}

/**
 * 计算环比
 */
function getHb($updata, $currentdata) {
    if ($updata != 0) {
        $mtomrate = round(($currentdata - $updata) / $updata * 100, 2) . '%';
    } else {
        $mtomrate = '-';
    }
    return $mtomrate;
}

/**
 * 计算同比
 */
function getTb($updata, $currentdata) {
    if ($updata != 0) {
        $ytoyrate = round(($currentdata - $updata) / $updata * 100, 2) . '%';
    } else {
        $ytoyrate = '-';
    }
    return $ytoyrate;
}

/**
 * 地图统计图
 */
function getStatData_Map($stat_arr) {
    //$color_arr = array('#f63a3a','#ff5858','#ff9191','#ffc3c3','#ffd5d5');
    $color_arr = array('#fd0b07', '#ff9191', '#f7ba17', '#fef406', '#25aae2');
    $stat_arrnew = array();
    foreach ($stat_arr as $k => $v) {
        $stat_arrnew[] = array('cha' => $v['cha'], 'name' => $v['name'], 'des' => $v['des'], 'color' => $color_arr[$v['level']]);
    }
    return json_encode($stat_arrnew);
}

/**
 * 获得饼形图数据
 */
function getStatData_Pie($data) {
    $stat_arr['chart']['type'] = 'pie';
    $stat_arr['credits']['enabled'] = false;
    $stat_arr['title']['text'] = $data['title'];
    $stat_arr['tooltip']['pointFormat'] = '{series.name}: <b>{point.y}</b>';
    $stat_arr['plotOptions']['pie'] = array(
        'allowPointSelect' => true,
        'cursor' => 'pointer',
        'dataLabels' => array(
            'enabled' => $data['label_show'],
            'color' => '#000000',
            'connectorColor' => '#000000',
            'format' => '<b>{point.name}</b>: {point.percentage:.1f} %'
        )
    );
    $stat_arr['series'][0]['name'] = $data['name'];
    $stat_arr['series'][0]['data'] = array();
    foreach ($data['series'] as $k => $v) {
        $stat_arr['series'][0]['data'][] = array($v['p_name'], $v['allnum']);
    }
    //exit(json_encode($stat_arr));
    return json_encode($stat_arr);
}

/**
 * 获得系统年份数组
 */
function getSystemYearArr() {
    $year_arr = array('2010' => '2010', '2011' => '2011', '2012' => '2012', '2013' => '2013', '2014' => '2014', '2015' => '2015', '2016' => '2016', '2017' => '2017', '2018' => '2018', '2019' => '2019', '2020' => '2020');
    return $year_arr;
}

/**
 * 获得系统月份数组
 */
function getSystemMonthArr() {
    $month_arr = array('1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06', '7' => '07', '8' => '08', '9' => '09', '10' => '10', '11' => '11', '12' => '12');
    return $month_arr;
}

/**
 * 获得系统周数组
 */
function getSystemWeekArr() {
    $week_arr = array('1' => '周一', '2' => '周二', '3' => '周三', '4' => '周四', '5' => '周五', '6' => '周六', '7' => '周日');
    return $week_arr;
}

/**
 * 获取某月的最后一天
 */
function getMonthLastDay($year, $month) {
    $t = mktime(0, 0, 0, $month + 1, 1, $year);
    $t = $t - 60 * 60 * 24;
    return $t;
}

/**
 * 获得系统某月的周数组，第一周不足的需要补足
 */
function getMonthWeekArr($current_year, $current_month) {
    //该月第一天
    $firstday = strtotime($current_year . '-' . $current_month . '-01');
    //该月的第一周有几天
    $firstweekday = (7 - date('N', $firstday) + 1);
    //计算该月第一个周一的时间
    $starttime = $firstday - 3600 * 24 * (7 - $firstweekday);
    //该月的最后一天
    $lastday = strtotime($current_year . '-' . $current_month . '-01' . " +1 month -1 day");
    //该月的最后一周有几天
    $lastweekday = date('N', $lastday);
    //该月的最后一个周末的时间
    $endtime = $lastday - 3600 * 24 * $lastweekday;
    $step = 3600 * 24 * 7; //步长值
    $week_arr = array();
    for ($i = $starttime; $i < $endtime; $i = $i + 3600 * 24 * 7) {
        $week_arr[] = array('key' => date('Y-m-d', $i) . '|' . date('Y-m-d', $i + 3600 * 24 * 6), 'val' => date('Y-m-d', $i) . '~' . date('Y-m-d', $i + 3600 * 24 * 6));
    }
    return $week_arr;
}

/**
 * 获取本周的开始时间和结束时间
 */
function getWeek_SdateAndEdate($current_time) {
    $current_time = strtotime(date('Y-m-d', $current_time));
    $return_arr['sdate'] = date('Y-m-d', $current_time - 86400 * (date('N', $current_time) - 1));
    $return_arr['edate'] = date('Y-m-d', $current_time + 86400 * (7 - date('N', $current_time)));
    return $return_arr;
}