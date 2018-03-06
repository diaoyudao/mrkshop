<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
// | Author: 烟消云散 1010422715@qq.com
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// OneThink常量定义
        const ONETHINK_VERSION = '1.1.140825';
        const ONETHINK_ADDON_PATH = './Addons/';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login() {
    $user = session('user_auth');
    if (empty($user)) {
        $cookiename = MD5(C('SITENAME'));
        //根据解密得到用户UID和
        $cookieinfo = Cookie($cookiename);
        if (!empty($cookieinfo)) {
            $cookiearray = explode('|', $cookieinfo);
            $uid = $cookiearray[0];
            $expres = $cookiearray[1];
            $hash = $cookiearray[2];

            $UcenterMember = D("Ucenter_member");
            $userinfo = $UcenterMember->field(true)->find($uid);
            $password = $userinfo['password'];
            $pass5 = substr($password, -5);
            $autologinvalue = sha1($uid . $expres . $pass5 . 'zi5`QkO1AtBPdr}q%Y?T;b&0@Jx/jUE"ea_6l.cM');

            if ($autologinvalue == $hash) {
                $Member = D("Member");
                $Member->login($uid);
                return $uid;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}


/**
 * 检测商家是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_mlogin() {
    $merchant = session('merchant_auth');

    if (empty($merchant)) {
        return 0;
    } else {
        return session('merchant_sign') == data_auth_sign($merchant) ? $merchant['id'] : 0;
    }
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null) {
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ',') {
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ',') {
    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = false) {
    $str = strip_tags($str);
    $strlength = "";
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
        if (function_exists("mb_strlen")) {
            $strlength = mb_strlen($str);
        }
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    $strlength = $strlength? : strlen($str);
    if ($strlength > $length && $suffix) {
        return $slice . '...';
    } else {
        return $slice;
    }
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l)
            $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url) {
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;
        default:
            $url = U($url);
            break;
    }
    return $url;
}

function get_index_url() {
    $damain = $_SERVER['SERVER_NAME'];
    $url = "http://" . $damain . __ROOT__;
    $url = SITE_URL;
    $url = "http://" .$_SERVER['HTTP_HOST'];
    return $url;
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = '') {
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l)
            $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if (!is_array($data)) {
        $data = (array) $data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc') {
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = & $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = & $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = & $refer[$parentId];
                    $parent[$child][] = & $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++)
        $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url) {
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url() {
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = array()) {
    \Think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name) {
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name) {
    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    } else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array()) {
    $url = parse_url($url);
    $case = C('URL_CASE_INSENSITIVE');
    $addons = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        '_addons' => $addons,
        '_controller' => $controller,
        '_action' => $action,
    );
    $params = array_merge($params, $param); //添加额外参数

    return U('Addons/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_regname($uid = 0) {
    $User = new User\Api\UserApi();
    $info = $User->info($uid);

    return $info['username'];
}

function get_regmobile($uid = 0) {
    $User = new User\Api\UserApi();
    $info = $User->info($uid);

    return $info["mobile"];
}

function get_username($uid = 0) {
    if(is_login()){
        $user_info = session('user_auth');
        $name = $user_info['username'];
    }else{
        /* 查找用户信息 */
        $User = new User\Api\UserApi();
        $info = $User->info($uid);
        $name = $info['username'];
    }
    return $name;
}

function get_shipping($warehouse){
    return M("distribution")->where(array('id'=>$warehouse))->getField('title');
}

/**
 * 获取退货单状态
 * @param int $refund_status 售后状态：-2：决绝，0申请状态,1同意退货,2为管理员已处理,3为已完成,默认为1
 */
function get_refund_status($refund_status){
	switch ($refund_status){
		case '-2':
			$r_status_lang = '拒绝';
			break;
		case '0':
			$r_status_lang = '提交申请';
			break;
		case '1':
			$r_status_lang = '通过申请';
			break;
		case '2':
			$r_status_lang = '已处理';
			break;
		case '3':
			$r_status_lang = '已完成';
			break;
		default:
			$r_status_lang = '正在处理中';
	}
	return $r_status_lang;
}

/**
 * 获取投诉建议状态
 * @param int $status 1:处理中 2:已回复
 */
function get_message_status($status){
	switch ($status)
	{
		case '1':
			return '待处理';
			case '2':
				return '已回复';
				default:
					return '未提交订单';
	}
}

/**
 * 获取商品提成状态
 * @param int $status 0：未付款, 1：已付款， 2：已提现, 3:已收货
 */
function get_affiliate_status($status){
	switch ($status)
	{
		case '1':
			return '已付款';
		case '2':
			return '已提现';
			case '3':
				return '已收款';
		default:
			return '未付款';
	}
}

/**
 * 获取商品提成平台处理状态
 * @param int $status  1默认2店家已确认3平台已审核（已打款）4结算完成
 */
function get_affiliate_ob_status($status){
	switch ($status)
	{
		case '2':
			return '已确认';
		case '3':
			return '已审核';
		case '4':
			return '已完成';
		default:
			return '待确认';
	}
}


function set_start_phone($name) {
    if (!empty($name) && is_numeric($name)) {
        $kstart = array(3, 4, 5, 6);
        $namearray = str_split($name);
        foreach ($namearray as $k => $v) {
            if (in_array($k, $kstart)) {
                $namearray[$k] = '*';
            }
        }
        $name = implode('', $namearray);
    }
    return $name;
}

function set_start_card($card_no) {
    return substr_replace($card_no, '*****', 4, 10);
//    $kstart = array(6, 7, 8, 9, 10);
//    $namearray = array();
//    $namearray = str_split($name);
//    foreach ($namearray as $k => $v) {
//        if (in_array($k, $kstart)) {
//            $namearray[$k] = '*';
//        }
//    }
//    $name = implode('', $namearray);
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_qq($uid) {
    $row = M('member')->getbyUid($uid);
    return $row['qq'];
}

function get_address($uid) {
    $row = M('transport')->where("status='1' and uid='$uid'")->find();
    return $row['address'];
}

function get_addressid($uid) {
    $row = M('transport')->where("status='1'")->order("id desc")->limit(1)->getbyUid($uid);
    return $row['id'];
}

function get_realname($uid) {
    $row = M('transport')->order("id desc")->limit(1)->getbyUid($uid);
    return $row['realname'];
}

/* 2015/4/23 16:42 根据订单收货地址ID查询收货人信息 */

function get_order_realname($addressid) {
    $row = M('transport')->where("id='$addressid'")->find();
    return $row['realname'];
}

function get_score($uid) {
    $row = M('member')->getbyUid($uid);
    return $row['score'];
}

function get_cellphone($uid) {
    $row = M('transport')->order("id desc")->limit(1)->getbyUid($uid);
    return $row['cellphone'];
}

function get_lever($uid) {
    $row = M('member')->getbyUid($uid);
    return $row['lever'];
}

function get_nickname($uid = 0) {
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_user_nickname_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('Member')->field('nickname')->find($uid);
        if ($info !== false && $info['nickname']) {
            $nickname = $info['nickname'];
            $name = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 获取文章当前位置信息并缓存分类
 * @param  integer $id    分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_location($id, $field = null, $type = "Article") {
    static $list;
    /* 非法分类ID */
    if (empty($id) || !is_numeric($id)) {
        return '';
    }





    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('sys_category_list');
    }


    $cate = M('Category')->find($id);


    /* 获取分类名称 */
    if (!isset($list[$id])) {

        if (!$cate || 1 != $cate['status']) { //不存在分类，或分类被禁用
            return '';
        }
        $list[$id] = $cate;
        S('sys_category_list', $list); //更新缓存
    }
    if (is_null($field)) {
        return $list[$id];
    } else {
        $a = $list[$id][$field]; //文章的category_id分类
        $NameOne = $list[$id]['id'];
        $UrlOne = U($type . "/lists", array("id" => $NameOne)); //"index.php?s=/Home/Article/index/category/".$NameOne;
        $LeverOne = '<a href="' . $UrlOne . '">' . $a . '</a>';
        if (0 !== $cate['pid']) {//2级分类，第2级
            $pid = $list[$id]['pid'];
            $cat = M('Category');
            if (0 != $pid) {
                //根据pid获取上一级category的标题和标识
                $TitleTWO = $cat->where("id='$pid'")->getField('title');
                //$NameTWO=$cat->where("id='$pid'")->getField('name');
                //设置链接
                $UrlTwo = U($type . "/lists", array("id" => $pid));

                $LeverTWO = '<a href="' . $UrlTwo . '">' . $TitleTWO . '</a>';
                // 获取当前分类的上级分类主键id
                $Id = $cat->where("id='$pid'")->getField('pid');
                if (!empty($Id)) {//判断是否是一级分类,获取标题和标识
                    $TitleThree = $cat->where("id='$Id'")->getField('title');
                    //$NameThree=$cat->where("id='$Id'")->getField('name');
                    //设置链接
                    $UrlThree = U($type . "/lists", array("id" => $Id));
                    $LeverThree = '<a href="' . $UrlThree . '">' . $TitleThree . '</a>';
                    return $LeverThree . " <span class=\"songti\">&gt;</span> " . $LeverTWO . "<span class=\"songti\">&gt;</span>" . $LeverOne;
                } else {
                    //只有2级的分类
                    return $LeverTWO . " <span class=\"songti\">&gt;</span> " . $LeverOne;
                }
            } else {
                //只有1级的分类
                return $LeverOne;
            }
        }
    }
}

function get_location_name($id, $type = "Article") {
    $as = get_location($id, 'title', $type);
    return $as;
}

function get_category($id, $field = null) {
    static $list;

    /* 非法分类ID */
    if (empty($id) || !is_numeric($id)) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('sys_category_list');
    }

    /* 获取分类名称 */
    if (!isset($list[$id])) {
        $cate = M('Category')->find($id);
        if (!$cate || 1 != $cate['status']) { //不存在分类，或分类被禁用
            return '';
        }
        $list[$id] = $cate;
        S('sys_category_list', $list); //更新缓存
    }
    if (is_null($field)) {
        return $list[$id];
    } else {


        return $list[$id][$field];
    }
}

/* 根据ID获取分类标识 */

function get_category_name($id) {
    return get_category($id, 'title');
}

function get_category_icon($id) {
    $row = M('category')->getbyId($id);
    return $row['icon'];
}

/* 封面id调用 */

function get_cover_id($id) {
    $row = M('document')->getbyId($id);
    return $row['cover_id'];
}

/* 优惠券封面id调用 */

function get_icon($id) {
    $row = M('fcoupon')->getbyId($id);
    return $row['icon'];
}


/* 优惠券名称调用 */
function get_coupon_name($id) {
    $row = M('fcoupon')->getbyId($id);
    return $row['title'];
}

/* 优惠券价值调用 */

function get_coupon_price($id) {
    $row = M('fcoupon')->getbyId($id);
    return $row['price'];
}

/* 优惠券代码调用 */

function get_coupon_code($id) {
    $row = M('fcoupon')->getbyId($id);
    return $row['code'];
}

/* 商品重量调用 */

function get_weight($id) {
    $row = M('document_product')->getbyId($id);
    return $row['weight'];
}

/* 商品名称调用 */

function get_good_name($id) {
    $row = M('document')->getbyId($id);
    return $row['title'];
}

function get_good_view($id) {
    $row = M('document')->getbyId($id);
    return $row['view'];
}

/* 商品内容调用 */

function get_docoment_content($id) {
    $row = M('document_article')->getbyId($id);
    return $row['content'];
}

function get_sales($id) {
    $row = M('document')->getbyId($id);
    return $row['sales'];
}

/* 商品内容调用 */

function get_good_content($id) {
    $row = M('document_product')->getbyId($id);
    return $row['content'];
}

function get_good_tuanprice($id) {
    $row = M('document')->getbyId($id);
    return $row['tuan_price'];
}

/* 商品价格调用 */

function get_good_price($id) {
    $row = M('document')->getbyId($id);
    return $row['price'];
}

/* 商品市场价 */

function get_good_yprice($id) {
    $row = M('document_product')->getbyId($id);
    return $row['marketprice'];
}

/* 店铺id调用 */

function get_shop_goodid($id) {
    $row = M('shoplist')->getbyId($id);
    return $row['goodid'];
}

/* 商品价格调用 */

function get_shop_orderid($id) {
    $row = M('shoplist')->getbyId($id);
    return $row['goodid'];
}

function get_good_img($id) {
    $row = M('picture')->getbyId($id);
    return $row['path'];
}

function get_good_shorttitle($id) {
    $row = M('document_product')->getbyId($id);
    return $row['shorttitle'];
}

function get_good_adid($id) {
    $row = M('document_product')->getbyId($id);
    return $row['ads_pic_id'];
}

function get_order_total($id) {
    $row = M('order')->getbyId($id);
    return $row['pricetotal'];
}

/* 根据ID获取分类名称 */

function get_category_title($id) {
    return get_category($id, 'title');
}

////获取ip地址信息，返回操作对象
function get_ip_address() {
    return false;
    $ip = getip();

    $json = @file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip); //根据taobao ip
    $jsonarr = json_decode($json);
    if ($jsonarr->code == 0) {
        $data = $jsonarr->data;
        return $data;
    } else {
        return false;
    }
}

////根据ip138获得本地真实IP
function get_onlineip() {
    $mip = @file_get_contents("http://www.ip138.com/ip2city.asp");
    if ($mip) {
        preg_match("/\[.*\]/", $mip, $sip);
        $p = array("/\[/", "/\]/");
        $iipp = preg_replace($p, "", $sip[0]);
        return preg_replace($p, "", $sip[0]);
    } else {
        return "获取本地IP失败！";
    }
}

//从服务器获取访客ip
function getip() {
    $onlineip = "";
    if (getenv(HTTP_CLIENT_IP) && strcasecmp(getenv(HTTP_CLIENT_IP), unknown)) {
        $onlineip = getenv(HTTP_CLIENT_IP);
    } elseif (getenv(HTTP_X_FORWARDED_FOR) && strcasecmp(getenv(HTTP_X_FORWARDED_FOR), unknown)) {
        $onlineip = getenv(HTTP_X_FORWARDED_FOR);
    } elseif (getenv(REMOTE_ADDR) && strcasecmp(getenv(REMOTE_ADDR), unknown)) {
        $onlineip = getenv(REMOTE_ADDR);
    } elseif (isset($_SERVER[REMOTE_ADDR]) && $_SERVER[REMOTE_ADDR] && strcasecmp($_SERVER[REMOTE_ADDR], unknown)) {
        $onlineip = $_SERVER[REMOTE_ADDR];
    }
    return $onlineip;
}

/* 访问统计 */

function IpLookup($ip = '', $tag, $id) {

    $arr = get_ip_address();
    $ip = $arr->ip;
    $data["ip"] = $arr->ip;
    $data["country"] = $arr->country;
    $data["province"] = $arr->region;
    $data["city"] = $arr->city;
    $data["isp"] = $arr->isp;
    if (is_login()) {
        $member = D("member");
        $data["uid"] = $member->uid();
    }
    if (!empty($tag)) {
        $data["tag"] = $tag;
    }
    if (!empty($id)) {
        $data["page"] = $id;
    }
    $data["time"] = NOW_TIME;
    $data["referer"] = $_SERVER['HTTP_REFERER'];
    $data["url"] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $record = M("records");
    if ($record->where("ip='$ip' and tag='$tag' and page='$id'")->select()) {
        //有访问记录
        $now = NOW_TIME;
        $recordtime = date("YmdH", $now); //当前时间点
        $time = $record->where("ip='$ip' and tag='$tag' and page='$id'")->limit(1)->order("id desc")->getField("time");
        $visittime = date("YmdH", $time); //获取最近一次访问点
        $chazhi = $recordtime - $visittime; //小时差值
        if ($chazhi > C('LAG')) {
            $record->add($data);
        }//每隔5小时记录一次
        else {
            
        }//不记录
    } else {//没有访问记录
        $record->add($data);
    }

    return $tag;
}

/**
 * 获取顶级模型信息
 */
function get_top_model($model_id = null) {
    $map = array('status' => 1, 'extend' => 0);
    if (!is_null($model_id)) {
        $map['id'] = array('neq', $model_id);
    }
    $model = M('Model')->where($map)->field(true)->select();
    foreach ($model as $value) {
        $list[$value['id']] = $value;
    }
    return $list;
}

/**
 * 获取文档模型信息
 * @param  integer $id    模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null) {
    static $list;

    /* 非法分类ID */
    if (!(is_numeric($id) || is_null($id))) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('DOCUMENT_MODEL_LIST');
    }

    /* 获取模型名称 */
    if (empty($list)) {
        $map = array('status' => 1, 'extend' => 1);
        $model = M('Model')->where($map)->field(true)->select();
        foreach ($model as $value) {
            $list[$value['id']] = $value;
        }
        S('DOCUMENT_MODEL_LIST', $list); //更新缓存
    }

    /* 根据条件返回数据 */
    if (is_null($id)) {
        return $list;
    } elseif (is_null($field)) {
        return $list[$id];
    } else {
        return $list[$id][$field];
    }
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function ubb($data) {
    //TODO: 待完善，目前返回原始数据
    return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null) {

    //参数检查
    if (empty($action) || empty($model) || empty($record_id)) {
        return '参数不能为空';
    }
    if (empty($user_id)) {
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);
    if ($action_info['status'] != 1) {
        return '该行为被禁用或删除';
    }

    //插入行为日志
    $data['action_id'] = $action_info['id'];
    $data['user_id'] = $user_id;
    $data['action_ip'] = ip2long(get_client_ip());
    $data['model'] = $model;
    $data['record_id'] = $record_id;
    $data['create_time'] = NOW_TIME;

    //解析日志规则,生成日志备注
    if (!empty($action_info['log'])) {
        if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
            $log['user'] = $user_id;
            $log['record'] = $record_id;
            $log['model'] = $model;
            $log['time'] = NOW_TIME;
            $log['data'] = array('user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => NOW_TIME);
            foreach ($match[1] as $value) {
                $param = explode('|', $value);
                if (isset($param[1])) {
                    $replace[] = call_user_func($param[1], $log[$param[0]]);
                } else {
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
        } else {
            $data['remark'] = $action_info['log'];
        }
    } else {
        //未定义日志规则，记录操作url
        $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->add($data);

    if (!empty($action_info['rule'])) {
        //解析行为
        $rules = parse_action($action, $user_id);

        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self) {
    if (empty($action)) {
        return false;
    }

    //参数支持id或者name
    if (is_numeric($action)) {
        $map = array('id' => $action);
    } else {
        $map = array('name' => $action);
    }

    //查询行为信息
    $info = M('Action')->where($map)->find();
    if (!$info || $info['status'] != 1) {
        return false;
    }

    //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
    $rules = $info['rule'];
    $rules = str_replace('{$self}', $self, $rules);
    $rules = explode(';', $rules);
    $return = array();
    foreach ($rules as $key => &$rule) {
        $rule = explode('|', $rule);
        foreach ($rule as $k => $fields) {
            $field = empty($fields) ? array() : explode(':', $fields);
            if (!empty($field)) {
                $return[$key][$field[0]] = $field[1];
            }
        }
        //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
        if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
            unset($return[$key]['cycle'], $return[$key]['max']);
        }
    }

    return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null) {
    if (!$rules || empty($action_id) || empty($user_id)) {
        return false;
    }

    $return = true;
    foreach ($rules as $rule) {

        //检查执行周期
        $map = array('action_id' => $action_id, 'user_id' => $user_id);
        $map['create_time'] = array('gt', NOW_TIME - intval($rule['cycle']) * 3600);
        $exec_count = M('ActionLog')->where($map)->count();
        if ($exec_count > $rule['max']) {
            continue;
        }

        //执行数据库操作
        $Model = M(ucfirst($rule['table']));
        $field = $rule['field'];
        $res = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));

        if (!$res) {
            $return = false;
        }
    }
    return $return;
}

//根据订单编码，获取会员邮箱
function get_email($uid) {

    $email = M('ucenter_member')->where("id='$uid'")->getField("email");
    return $email;
}

//基于数组创建目录和文件
function create_dir_or_files($files) {
    foreach ($files as $key => $value) {
        if (substr($value, -1) == '/') {
            mkdir($value);
        } else {
            @file_put_contents($value, '');
        }
    }
}

if (!function_exists('array_column')) {

    function array_column(array $input, $columnKey, $indexKey = null) {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }

}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null) {
    if (empty($model_id)) {
        return false;
    }
    $Model = M('Model');
    $name = '';
    $info = $Model->getById($model_id);
    if ($info['extend'] != 0) {
        $name = $Model->getFieldById($info['extend'], 'name') . '_';
    }
    $name .= $info['name'];
    return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id    属性ID
 * @param  string  $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = true, $fields = true) {
    static $list;

    /* 非法ID */
    if (empty($model_id) || !is_numeric($model_id)) {
        return '';
    }

    /* 获取属性 */
    if (!isset($list[$model_id])) {
        $map = array('model_id' => $model_id);
        $extend = M('Model')->getFieldById($model_id, 'extend');

        if ($extend) {
            $map = array('model_id' => array("in", array($model_id, $extend)));
        }
        $info = M('Attribute')->where($map)->field($fields)->select();
        $list[$model_id] = $info;
    }

    $attr = array();
    if ($group) {
        foreach ($list[$model_id] as $value) {
            $attr[$value['id']] = $value;
        }
        $sort = M('Model')->getFieldById($model_id, 'field_sort');

        if (empty($sort)) { //未排序
            $group = array(1 => array_merge($attr));
        } else {
            $group = json_decode($sort, true);

            $keys = array_keys($group);
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    $value[$key] = $attr[$val];
                    unset($attr[$val]);
                }
            }

            if (!empty($attr)) {
                $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
            }
        }
        $attr = $group;
    } else {
        foreach ($list[$model_id] as $value) {
            $attr[$value['name']] = $value;
        }
    }
    return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name, $vars = array()) {
    $array = explode('/', $name);
    $method = array_pop($array);
    $classname = array_pop($array);
    $module = $array ? array_pop($array) : 'Common';
    $callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
    if (is_string($vars)) {
        parse_str($vars, $vars);
    }
    return call_user_func_array($callback, $vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null) {
    if (empty($value) || empty($table)) {
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M(ucfirst($table))->where($map);
    if (empty($field)) {
        $info = $info->field(true)->find();
    } else {
        $info = $info->getField($field);
    }
    return $info;
}

/**
 * 获取链接信息
 * @param int $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author huajie <banhuajie@163.com>
 */
function get_link($link_id = null, $field = 'url') {
    $link = '';
    if (empty($link_id)) {
        return $link;
    }
    $link = M('Url')->getById($link_id);
    if (empty($field)) {
        return $link;
    } else {
        return $link[$field];
    }
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null) {
    if (empty($cover_id)) {
        return false;
    }
    // 2015/5/7 12:00 sheshanhu

    $picture = M('Picture')->where(array('status' => 1))->getById($cover_id);
    if ($field == 'path') {
        if (!empty($picture['url'])) {
            $picture['path'] = $picture['url'];
            //$picture['path'] = substr($setting['rootPath'], 1).$picture['url'];
        } else {
            $picture['path'] = $picture['path'];
            //$picture['path'] = __ROOT__.substr($setting['rootPath'], 1).$picture['path'];
        }
    }
    return empty($field) ? $picture : $picture[$field];
}

/* * *************************************************************
 * created date:2015/6/25 15:48
 * created author:sheshanhu
 * content:根据ID查询是出封面图地址
 * modefiy person:
 * modefiy date:
 * note:
 * ************************************************************** */

function get_cover_picture_url($id, $model = 'document') {

    $row = M($model)->getbyId($id);
    $cover_id = $row['cover_id'];
    $domainid = $row['domainid'];
    if (empty($cover_id)) {
        return false;
    }
    $baseurl = __PICURL__ ;//. $domainid . '/';
    $picture = M('Picture')->where(array('status' => 1))->getById($cover_id);
    if (!empty($picture['url'])) {
        $picture_path = $baseurl . $picture['url'];
    } else {
        $picture_path = $baseurl . $picture['path'];
    }

    return $picture_path;
}

/**
 * 根据图片ID获取图片链接地址
 * @param $id int 图片ID
 * @return string
 */
function get_pic_url($id) {
    $picture_path = "";//默认图
    $picture = M('picture')->field('url,path')->where(array('status' => 1))->getById($id);
    if($picture){
        if (!empty($picture['url'])) {
            $picture_path = $picture['url'];
        } else {
            $picture_path = __PICURL__ . $picture['path'];
        }
    }
    return $picture_path;
}

/**
 * 可使用优惠券最低消费金额
 */
function get_fcoupon_lowpayment($code) {
    $info = M('fcoupon')->where("code='$code'")->find();
    return $info['lowpayment'];
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0) {
    if (empty($pos) || empty($contain)) {
        return false;
    }

    //将两个参数进行按位与运算，不为0则表示$contain属于$pos
    $res = $pos & $contain;
    if ($res !== 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取数据的所有子孙数据的id值
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */
function get_stemma($pids, $model, $field = 'id') {
    $collection = array();

    //非空判断
    if (empty($pids)) {
        return $collection;
    }

    if (is_array($pids)) {
        $pids = trim(implode(',', $pids), ',');
    }
    $result = $model->field($field)->where(array('pid' => array('IN', (string) $pids)))->select();
    $child_ids = array_column((array) $result, 'id');

    while (!empty($child_ids)) {
        $collection = array_merge($collection, $result);
        $result = $model->field($field)->where(array('pid' => array('IN', $child_ids)))->select();
        $child_ids = array_column((array) $result, 'id');
    }
    return $collection;
}

/**
 * 验证分类是否允许发布内容
 * @param  integer $id 分类ID
 * @return boolean     true-允许发布内容，false-不允许发布内容
 */
function check_category($id) {
    if (is_array($id)) {
        $type = get_category($id['category_id'], 'type');
        $type = explode(",", $type);
        return in_array($id['type'], $type);
    } else {
        $publish = get_category($id, 'allow_publish');
        return $publish ? true : false;
    }
}

/**
 * 检测分类是否绑定了指定模型
 * @param  array $info 模型ID和分类ID数组
 * @return boolean     true-绑定了模型，false-未绑定模型
 */
function check_category_model($info) {
    $cate = get_category($info['category_id']);
    $array = explode(',', $info['pid'] ? $cate['model_sub'] : $cate['model']);
    return in_array($info['model_id'], $array);
}

/**
 * 邮件发送函数
 */
function sendMail($to, $title, $content) {

    Vendor('PHPMailer.PHPMailer');
    $mail = new \vendor\PHPMailer\PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host = C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
    $mail->SMTPAuth = true; //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD'); //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($to, "尊敬的客户");
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(TRUE); // 是否HTML格式邮件
    $mail->CharSet = 'utf-8'; //设置邮件编码
    $mail->Subject = $title; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
    return($mail->Send());
}

//发送短信-飞拓掌中传媒接口-备用
function sendsmsbyfeikuo($mobile, $content) {
    Vendor('HttpClient.HttpClient');
    //include_once('HttpClient.php');
    //目标主机的地址，我这里填上测试的地址
    $Client = new \vendor\HttpClient\HttpClient("mssms.cn:8000");
    $url = "http://mssms.cn:8000/msm/sdk/http/sendsms.jsp";
    //请求的页面地址
    //ＰＯＳＴ的参数
    $params = array('username' => "NTY105029", 'scode' => "1111111", 'mobile' => $mobile, 'content' => $content, 'tempid' => "MB-2013102300");
    $pageContents = \HttpClient::quickPost($url, $params);
    return $pageContents;
}

//发送短信验证码-互亿接口,$send_code 随机安全码
function sendsmscode($mobile, $content, $send_code) {
    Vendor('Sms.Sms');
    $sms = new \vendor\Sms\Sms();
    $mobile_code = random(4, 1); //生成手机验证码
    $uid = D("member")->uid();
    if (empty($mobile)) {
        $mobile = get_regmobile($uid);
    }
    $result = $sms->sendcode($mobile, $content, $send_code, $mobile_code);
    return $result;
}

//发送短信通知-互亿接口
function sendsms($mobile, $content) {
    Vendor('Sms.Sms');
    $sms = new \vendor\Sms\Sms();
    $uid = D("member")->uid();
    if (empty($mobile)) {
        $mobile = get_regmobile($uid);
    }
    $result = $sms->send($mobile, $content);
    return $result;
}

//生成短信验证码-互亿接口
function random($length = 6, $numeric = 0) {
    PHP_VERSION < '4.2.0' && mt_srand((double) microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

/* 登录购物车处理函数 ,会员模型函数 */

function addintocart($uid) {
    $table = M("shopcart");
    $cart = $_SESSION["cart"];
    foreach ($cart as $k => $val) {
        $id = $val["goodid"];
        $parameters = $val["parameters"];
        $sort = $val["sort"];
        $type = $val["type"];
        $num = M("shopcart")->where("goodid='$id' and uid='$uid 'and sort='$sort'")->getField("num");
        if ($num) {
            $table->num = $val["num"] + $num;
            $table->where("goodid='$id' and uid='$uid 'and sort='$sort'")->save();
        } else {
            $table->goodid = $id;
            $table->parameters = $parameters;
            //根据商品属性查询商品增量价格，重新计算
            //属性格式27910826-10830 商品ID，属性值
            if (!empty($val["sort"])) {
                $parame = str_replace($id, '', $val["sort"]);
                $paramestr = explode('-', $parame);
                $map = array();
                $map['gid'] = $id;
                $map['id'] = array('in', $paramestr);
                $sumprice = M("goodAttr")->where($map)->sum("price");
                $val["price"] = $val["price"] + $sumprice;
            }

            $table->price = $val["price"];
            $table->sort = $val["sort"];
            $table->uid = $uid;
            $table->num = $val["num"];
            $table->type = $val["type"];
            $table->cart_type = $val["cart_type"];
            $table->proid = $val["proid"];
            $table->promsg = $val["promsg"];
            $table->create_time = NOW_TIME;
            $table->is_system = $val['is_system'];
            $table->add();
        }
    }
}

function int_num_validate($num) {
    $returnnum = $num;
    if (!preg_match("/[0-9]+/i", $num)) {
        $returnnum = 1;
    } elseif ($num <= 0) {
        $returnnum = 1;
    }
    return $returnnum;
}

/* 记录登录历史信息 ,会员模型函数 */

function history($uid,$type=0) {

    $arr = get_ip_address();
    $data["uid"] = $uid;
    $data["type"] = $type;//用户类型 0为普通用户 1为商家用户
    $data["login_ip"] = $arr->ip;
    $data["login_country"] = $arr->country;
    $data["login_province"] = $arr->region;
    $data["login_city"] = $arr->city;
    $data["login_isp"] = $arr->isp;
    $data["login_time"] = NOW_TIME;
    /* 登录方式 */
    $data["login_way"] = isMobil();
    $history = M("history");
    $history->create();
    $history->add($data);
}

/* 判断是电脑还是手机访问 */

function isMobil() {
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
    $mobile_os_list = array
        (
        'Google Wireless Transcoder',
        'Windows CE',
        'WindowsCE',
        'Symbian',
        'Android',
        'armv6l',
        'armv5',
        'Mobile',
        'CentOS',
        'mowser',
        'AvantGo',
        'Opera Mobi',
        'J2ME/MIDP',
        'Smartphone',
        'Go.Web',
        'Palm',
        'iPAQ'
    );
    $mobile_token_list = array
        (
        'Profile/MIDP',
        'Configuration/CLDC-',
        '160×160',
        '176×220',
        '240×240',
        '240×320',
        '320×240',
        'UP.Browser',
        'UP.Link',
        'SymbianOS',
        'PalmOS',
        'PocketPC',
        'SonyEricsson',
        'Nokia',
        'BlackBerry',
        'Vodafone', 'BenQ',
        'Novarra-Vision',
        'Iris',
        'NetFront',
        'HTC_',
        'Xda_',
        'SAMSUNG-SGH',
        'Wapaka',
        'DoCoMo',
        'iPhone',
        'iPod'
    );
    $found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
            CheckSubstrs($mobile_token_list, $useragent);
    if ($found_mobile) {
        $way = '手机登录'; //'手机登录'
    } else {
        $way = '电脑登录'; //'电脑登录'
    }
    return $way;
}

// 加密解密函数，函数encrypt($string,$operation,$key)
//中$string：需要加密解密的字符串；$operation：判断是加密还是解密，E表示加密，D表示解密；$key：密匙。
//echo '加密:'.encrypt($str, 'E', $key); echo '解密：'.encrypt($str, 'D', $key);
function encrypt($string, $operation, $key = '') {
    $key = C('DATA_AUTH_KEY');
    $key = md5($key);
    $key_length = strlen($key);
    $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result.=chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'D') {
        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
            return substr($result, 8);
        } else {
            return'';
        }
    } else {
        return str_replace('=', '', base64_encode($result));
    }
}

function CheckSubstrs($substrs, $text) {

    foreach ($substrs as $substr) {

        if (false !== strpos($text, $substr)) {

            return true;
        }

        return false;
    }
}

//获取频道列表
function get_subdomainbrand($domainid = null) {
    //获取频道
    if ($domainid) {
        $map["domainid"] = intval($domainid);
    }
    $map["status"] = 1;
    $list = M('Subdomain')->where($map)->field("id,domainid,name")->select();
    return $list;
}

function get_subdomain_name($domainid = 0) {
    //获取频道
    $name = '';
    $map = array();
    if ($domainid > 0) {
        $map["id"] = intval($domainid);
        $map["status"] = 1;
        $name = M('Subdomain')->where($map)->getField('name');
    }
    return $name;
}

//根据条件查询频道信息
function check_table_field_name($model, $map = array()) {
    $Subdomain = array();
    if (!empty($map)) {
        //获取频道
        $Subdomain = M($model)->where($map)->select();
    }
    return $Subdomain;
}

//utf-8截取
function getsubstrutf8($string, $start = 0, $sublen, $append = true) {
    $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
    preg_match_all($pa, $string, $t_string);
    if (count($t_string [0]) - $start > $sublen && $append == true) {
        return join('', array_slice($t_string [0], $start, $sublen)) . "...";
    } else {
        return join('', array_slice($t_string [0], $start, $sublen));
    }
}

/**
 * 友好的时间显示
 * @author  wangcheng
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt
 * @return string
 */
function friendlyDate($sTime, $type = 'normal') {
    if (!$sTime)
        return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if ($type == 'normal') {
        if ($dTime < 60) {
            if ($dTime < 10) {
                return '刚刚';    //by yangjs
            } else {
                return intval(floor($dTime / 10) * 10) . "秒前";
            }
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
            //今天的数据.年份相同.日期相同.
        } elseif ($dYear == 0 && $dDay == 0) {
            //return intval($dTime/3600)."小时前";
            return '今天' . date('H:i', $sTime);
        } elseif ($dYear == 0) {
            return date("m月d日 H:i", $sTime);
        } else {
            return date("Y-m-d H:i", $sTime);
        }
    } elseif ($type == 'mohu') {
        if ($dTime < 60) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dDay > 0 && $dDay <= 7) {
            return intval($dDay) . "天前";
        } elseif ($dDay > 7 && $dDay <= 30) {
            return intval($dDay / 7) . '周前';
        } elseif ($dDay > 30) {
            return intval($dDay / 30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    } elseif ($type == 'full') {
        return date("Y-m-d , H:i:s", $sTime);
    } elseif ($type == 'ymd') {
        return date("Y-m-d", $sTime);
    } else {
        if ($dTime < 60) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dYear == 0) {
            return date("Y-m-d H:i:s", $sTime);
        } else {
            return date("Y-m-d H:i:s", $sTime);
        }
    }
}

function highLight($str, $keywords, $color = "red") {
    if (empty($keywords)) {
        return $str;
    }
    $finalrep = "<font color=" . $color . ">" . $keywords . "</font>";
    $str = str_ireplace($keywords, $finalrep, $str);
    return $str;
}

//分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr_info($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

//订单支付方式
function get_site_payment() {
    $result = array();
    $map = array();
    $map["status"] = 1;
    //cookie("current_domainid");
    $result = M('payment')->where($map)->order('weight ASC')->select();
    return $result;
}

//订单配关方式
function get_site_distribution() {
    $result = array();
    $map = array();
    $map["status"] = 1;
    //cookie("current_domainid");
    $result = M('distribution')->where($map)->order('weight DESC')->select();
    return $result;
}

//银行转账汇款
function get_site_banklist() {
    $result = array();
    $map = array();
    $map["status"] = 1;
    //cookie("current_domainid");
    $result = M('bank')->where($map)->order('weight DESC')->select();
    return $result;
}

/* 组合商品价格调用 */

function get_pggood_price($id) {
    $row = M('ProductsGroup')->getbyId($id);
    return $row['price'];
}

/* * *************************************************************
 * created date: 上传图片时判断是否已经存在，存在则不在上传文件，直接使用原图片地址。
 * created author:
 * content:
 * modefiy person:
 * modefiy date:
 * note:
 * ************************************************************** */

function get_file_info($file) {
    /*   Array
      (
      [name] => 559.jpg
      [type] => image/jpeg
      [tmp_name] => C:\wamp\tmp\php4D49.tmp
      [error] => 0
      [size] => 230387
      [key] => download
      [ext] => jpg
      [md5] => df14a594e9ab00275b1c818ae494be0c
      [sha1] => 8b2c87628181cec6dc78a414b718df04a6a36242
      ) */
    $mad5 = $file['md5'];
    $sha1 = $file['sha1'];
    $data = array();
    $data = M('Picture')->where("md5='$mad5' and sha1='$sha1'")->select();
    return $data;
}

/**
 * 生成二维码
 * @param type $data 生成二维码数据
 * @param type $uid 会员ID
 * @param type $outfile 文件名
 * @param string $level
 * @param int $size
 * @return type
 */
function getQrode($data, $uid, $outfile = 'code') {
    Vendor('phpqrcode.index');
    // 生成商品二维码
//    require_once(BASE_RESOURCE_PATH . DS . 'phpqrcode' . DS . 'index.php');
    $PhpQRCode = new PhpQRCode();
    $uid = $uid ? : is_login();
    $rootPath = './Uploads/code/' . $uid . '/';
    $PhpQRCode->set('pngTempDir', $rootPath);
    // 生成商品二维码 urlShop('login', 'register');
    $url = $data ? : '二维码';
    $PhpQRCode->set('date', $url);
    $PhpQRCode->set('pngTempName', $outfile . '.png');
    echo $PhpQRCode->init();
}


/**
 * 价格格式化
 *
 * @param int	$price
 * @return string	$price_format
 */
function ncPriceFormat($price) {
    $price_format = number_format($price, 2, '.', '');
    return $price_format;
}

//在线交易订单支付处理函数
//函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
//返回值：如果订单已经成功支付，返回true，否则返回false；
function checkorderstatus($orderid) {
    $Ord = M('order');
    $ordstatus = $Ord->where(array('tag' => $orderid))->getField('status');
    if ($ordstatus >= 1) {
        return true;
    } else {
        return false;
    }
}

/**
 * //处理订单函数
 * 更新订单状态，写入订单支付后返回的数据
 * @param type $order_id        // 订单号
 * @param type $transaction_id  // 交易单号
 * @param type $pay_type        // 支付类型
 * @return boolean
 */
function orderhandle($order_id, $transaction_id, $pay_type = 0) {
	//记录日志
	\Think\Log::write(var_export("支付成功返回数据【订单ID】:".$order_id, true), 'DEBUG', '', C('LOG_PATH') . 'log_pay.log');
	
    $order = M('order')->where(array('tag' => $order_id))->find();
    $payinfo = M("pay")->where(array('out_trade_no' => $order_id))->find();
    if (!$order) {
        return false;
    }
    if (!$payinfo) {
        return false;
    }
    $data = array();
    $data = array('status' => '2', 'update_time' => time());
    M("pay")->where(array('out_trade_no' => $order_id))->setField($data);
    //M("affiliate_log")->where(array('order_id' => $order['id'], 'status' => 0))->setField(array('status' => 1));
    M("affiliate_log")->where(array('order_id' => $order['id']))->setField(array('status' => 1));
    // 更新发货单为已付款
    M("order_delivery")->where(array('order_id' => $order['id']))->data(array('status' => 1, 'update_time' => NOW_TIME))->save();
    $data = array();
    $data['status'] = 1; // 订单状态
    $data['ispay'] = 2; // 在线支付完成
    $data['backinfo'] = '订单已支付';
    // 支付类型:1、支付宝；2、银联支付；3、微信支付
    $data['pay_type'] = $pay_type; // 支付类型
    $data['transaction_id'] = $transaction_id;  // 交易单号
    $data['payment_time'] = time();
    M('order')->where(array('tag' => $order_id))->setField($data);
    M('order_log')->add(array('order_id' => $order['id'], 'log_msg' => '订单已支付,交易单号：' . $transaction_id, 'log_time' => NOW_TIME, 'log_orderstate' => 1, 'log_role' => is_login(), 'log_user' => is_login()));
    return true;
}




function wap_wx_share($arr) {
    $page_title = empty($arr['meta_title']) ? C('WEB_SITE_TITLE') : $arr['meta_title'];
    $description = empty($arr['meta_description']) ? C('WEB_SITE_DESCRIPTION') : $arr['meta_description'];
    $description = str_replace(array("\r", "\n"), array(","), $description);
    $link = empty($arr['link']) || !isset($arr['link']) ? WAP_SITE_URL . $_SERVER['REQUEST_URI'] : urldecode($arr['link']);
    $is_login = !isset($arr['is_login']) || empty($arr['is_login']) ? 0 : $arr['is_login'];
    $back_act = !isset($arr['back_act']) || empty($arr['back_act']) ? urlencode('./') : trim($arr['back_act']);

    $link_arr = parse_url($link);
    parse_str($link_arr['query'], $query_str);
    $link = $link_arr['scheme'] . '://' . $link_arr['host'] . $link_arr['path'];
    unset($query_str['code']);
    unset($query_str['state']);
    if ($query_str) {
        $link.='?';
        foreach ($query_str AS $key => $value) {
            $link .= $key . '=' . $value . '&';
        }
        $link = trim($link, '&');
    }
    $link.=$link_arr['fragment'];
    vendor("Wxpay.WechatJSAPI");
    vendor('Wxpay.WxPayConfig');
    $imgUrl = empty($arr['imgUrl']) ?  C("TMPL_PARSE_STRING.__IMG__") . '/logo.png' : $arr['imgUrl'];
    
    $wxconfig = new \WxPayConfig();
    $appId = $wxconfig::WEB_APPID; //公众账号ID
    $appSecret = $wxconfig::APPSECRET; //secret
   $jssdk = new \WechatJSAPI($appId, $appSecret);
    $signPackage = $jssdk->GetSignPackage();
    return <<<EOT
<!--div style="">$link <br><br><br><br><br></div-->
	<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script type="text/javascript">
	//分享函数
	var title='{$page_title}',
	desc= '{$description}',
	links='{$link}',
	imgUrl= '{$imgUrl}' ;
	var is_wx=false;
	// 更新分享数据
	function update_share(){
	//alert(links);
	}
	function gotourl(url){
		window.location.href=url;
	}
	function share_wx(){
		// 在这里调用 API
		//获取“分享到朋友圈”按钮点击状态及自定义分享内容接口
		wx.onMenuShareTimeline({
			title: title, // 分享标题
			link: links, // 分享链接
			imgUrl: imgUrl, // 分享图标
			success: function () {
				update_share();
			},
			cancel: function () {
			}
		});
		//获取“分享给朋友”按钮点击状态及自定义分享内容接口
		wx.onMenuShareAppMessage({
			title: title, // 分享标题
			desc:desc, // 分享描述
			link:links, // 分享链接
			imgUrl: imgUrl, // 分享图标
			type: '', // 分享类型,music、video或link，不填默认为link
			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
			success: function () {
				update_share();
			},
			cancel: function () {
			}
		});
		//获取“分享到QQ”按钮点击状态及自定义分享内容接口
		wx.onMenuShareQQ({
			title: title, // 分享标题
			desc: desc, // 分享描述
			link: links, // 分享链接
			imgUrl: imgUrl, // 分享图标
			success: function () {
				update_share();
			},
			cancel: function () {
			}
		});
		//获取“分享到腾讯微博”按钮点击状态及自定义分享内容接口
		wx.onMenuShareWeibo({
			title: title, // 分享标题
			desc: desc, // 分享描述
			link: links, // 分享链接
			imgUrl: imgUrl, // 分享图标
			success: function () {
				update_share();
			},
			cancel: function () {
			}
		});
	}
	$(function(){
	    wx.config({
		    //debug: true,
		    appId: '{$signPackage["appId"]}',
                    timestamp: {$signPackage["timestamp"]},
                    nonceStr: '{$signPackage["nonceStr"]}',
                    signature: '{$signPackage["signature"]}',
		    jsApiList: [
                            'checkJsApi',
                            'onMenuShareTimeline',
                            'onMenuShareAppMessage',
                            'onMenuShareQQ',
                            'onMenuShareWeibo',
                            'chooseImage',
                            'uploadImage',
                            'downloadImage'
                        ]
	    });
	    wx.ready(function () {
		is_wx=true;
		    share_wx();
	    });
	});
</script>
EOT;
}

/**
 * 移除微信中IOS的emoji字符
 * @param sring $text
 * @return mixed
 */
function emojiFilter($text){
	$text = json_encode($text);
	preg_match_all("/(\\\\ud83c\\\\u[0-9a-f]{4})|(\\\\ud83d\\\u[0-9a-f]{4})|(\\\\u[0-9a-f]{4})/", $text, $matchs);
	if(!isset($matchs[0][0])) { return json_decode($text, true); }

	$emoji = $matchs[0];
	foreach($emoji as $ec) {
		$hex = substr($ec, -4);
		if(strlen($ec)==6) {
			if($hex>='2600' and $hex<='27ff') {
				$text = str_replace($ec, '', $text);
			}
		} else {
			if($hex>='dc00' and $hex<='dfff') {
				$text = str_replace($ec, '', $text);
			}
		}
	}

	return json_decode($text, true);
}
/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 *
 * @return      bool
 */
function make_dir($folder){
	$reval = false;

	if (!file_exists($folder)){
		/* 如果目录不存在则尝试创建该目录 */
		@umask(0);

		/* 将目录路径拆分成数组 */
		preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

		/* 如果第一个字符为/则当作物理路径处理 */
		$base = ($atmp[0][0] == '/') ? '/' : '';

		/* 遍历包含路径信息的数组 */
		foreach ($atmp[1] AS $val){
			if ('' != $val){
				$base .= $val;

				if ('..' == $val || '.' == $val){
					/* 如果目录为.或者..则直接补/继续下一个循环 */
					$base .= '/';

					continue;
				}
			}else{
				continue;
			}

			$base .= '/';
			if(strpos(__ROOT__, $base)!==false){
				continue;
			}
			if (!file_exists($base)){
				/* 尝试创建目录，如果创建失败则继续循环 */
				if (@mkdir(rtrim($base, '/'), 0777))
				{
					@chmod($base, 0777);
					$reval = true;
				}
			}
		}
	}else{
		/* 路径已经存在。返回该路径是不是一个目录 */
		$reval = is_dir($folder);
	}
	clearstatcache();
	return $reval;
}

/**
 * 将上传文件转移到指定位置
 *
 * @param string $file_name
 * @param string $target_name
 * @return blog
 */
function move_upload_file($file_name, $target_name = '')
{
	if (function_exists("move_uploaded_file"))
	{
		if (move_uploaded_file($file_name, $target_name))
		{
			@chmod($target_name,0755);
			return true;
		}
		else if (copy($file_name, $target_name))
		{
			@chmod($target_name,0755);
			return true;
		}
	}
	elseif (copy($file_name, $target_name))
	{
		@chmod($target_name,0755);
		return true;
	}
	return false;
}

/**
 * 抓取远程头像
 *
 */
function grabImage($url,$uid, $filename = '') {
	if($url == '') {
		return false; //如果 $url 为空则返回 false;
	}
	if(empty($uid)){
		return false;
	}
	$dir_path = "./Uploads/Face/".$uid;

	if($filename == '') {
		$filename = NOW_TIME.".png"; //以时间戳另起名
	}else{
		$filename = $filename.substr(NOW_TIME,6).".png"; //以时间戳另起名
	}

	if (!make_dir($dir_path))
	{
		/* 创建目录失败 */
		return false;
	}

	//要移动的路径
	$path     = $dir_path.'/'. $filename;

	//抓取图片
	ob_start();
	readfile($url);
	$img_data = ob_get_contents();
	ob_end_clean();
	$size = strlen($img_data);
	$local_file = fopen($filename , 'w');
	fwrite($local_file, $img_data);
	fclose($local_file);

	if (move_upload_file($filename, $path))
	{
		return true;
	}	else	{
		return false;
	}
}

/**
 *  删除过期图片
 * @param string $tmp_dir
 * 删除过期图片的目录地址
 * @param int $expire_time int[optional]
 * 过期时间  单位小时    默认为2
 * @return bool  删除成功 返回true 否则 返回false
 */
function clear_expire_files($tmp_dir = '', $expire_time = 2) {
	$time = NOW_TIME;
	//$tmp_dir .='/';
	$folder = @opendir ( $tmp_dir );

	if ($folder === false) {
		return false;
	}
	$file_log['count']=0;
	$file_log['count_new']=0;

	while ( $file = readdir ( $folder ) ) {
		if ($file == '.' || $file == '..' || $file == 'index.htm' || $file == 'index.html') {
			continue;
		}
		
		if (is_file ( $filename = $tmp_dir . $file )) {
			$filestat = @stat ( $filename );
			if($filestat&&$time-$filestat['mtime']>$expire_time*3600)
			{
				@unlink ( $filename );
				$file_log['count']++;
			}else{
				$file_log['count_new']++;
			}
		}elseif(is_dir($tmp_dir . $file)){
			clear_expire_files($tmp_dir . $file, $expire_time );
		}
		
	}
	closedir ( $folder );
	if(($file_log['count']>0||$file_log['count'])){
		\Think\Log::write('move_refund_cout: ' . var_export("删除过期文件数量{$file_log['count']},还有{$file_log['count']}个文件没有过期\r\n", true), 'DEBUG', '', C('LOG_PATH') . 'log_refund.log');
	}
	return $file;
}


/*
* **************************************************************
* created date:2015/6/26 10:38
* created author:sheshanhu
* content:图片缩略图生成及地址返回
* modefiy person:
* modefiy date:
* note:
* ************************************************************** */
function _image_thumb($url, $width, $height, $iscreted = false, $type = 'Picture', $rootpath = '') {
    // URL /yzjj/Uploads/Picture/2/2015-06-17/5581404d75cc4.jpg
    //对图片分析获取图片名称
    /* Array
      (
      [dirname] => /yzjj/Uploads/Picture/2/2015-06-17
      [basename] => 5581404d75cc4.jpg
      [extension] => jpg
      [filename] => 5581404d75cc4
      ) */

    $path_parts = pathinfo($url);
    $dirname = $path_parts['dirname'];
    $dirarray = explode('Uploads/' . $type . '/', $dirname);
    if (empty($rootpath)) {
        $uploadimage = C('PICTURE_UPLOAD'); //获取上传图片的文件夹地址
        $newdirname = $uploadimage['rootPath'] . $dirarray[1] . '/';
    } else {
        $newdirname = $rootpath . $dirarray[1] . '/';
    }
    $reurl = $newdirname . $path_parts['basename'];

    $newimagename = $path_parts['filename'] . '_' . $width . 'x' . $height . '.' . $path_parts['extension'];

    $newurl = $newdirname . $newimagename;

    if (is_file($reurl)) {
        //判断文件是否存在，如果存在则不生成，
        if (!is_file($newurl) || $iscreted) {
            $image = new \Think\Image();
            $image->open($reurl);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
            $b = $image->thumb($width, $height)->save($newurl);
        }
    }

    //如果创建成功，图片存在则返回图片，如果不存在，则返回原地址。
    if (is_file($newurl)) {
        $newurl = $path_parts['dirname'] . '/' . $newimagename;
        return $newurl;
    } else {
        return $url;
    }
}