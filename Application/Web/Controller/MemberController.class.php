<?php

/**
 * 会员中心
 */

namespace Web\Controller;

use Web\Controller\HomeController;
//use Common\Util\Validate;
use User\Api\UserApi;
use Api\Api\OrderApi;
use Api\Api\MemberApi;
use Api\Api\GoodsApi;

class MemberController extends HomeController {

    protected function _initialize() {
        parent::_initialize();
        $openActions = array(// 开放方法
            'login',
            'register',
            'forgotpassword',
            'resetpassword',
            'succeedSetPassword',
            'logout',
            'checkMobile',
            'getMobileCode',
            'savememberinfo',
            'weixin_login',
        );
        $pattern = '/' . implode('|', $openActions) . '/i';
        if (preg_match($pattern, ACTION_NAME)) {
            if (strtolower(ACTION_NAME) == 'logout') {
                
            } else if ($this->customerId) {
                $this->redirect(url('member/index'));
            }
        } else {
            if (!$this->customerId) {
                $this->redirect(url('member/login'));
            }
        }
    }

    /**
     * 会员中心首页
     */
    public function index() {

        if (is_login()) {
            //收藏夹
            $uid = is_login();
            $fav = D("favortable");
            $favor = $fav->getfavor();
            $this->assign('favorlist', $favor);
            /* 优惠券数量 */
            $num = M("usercoupon")->where("uid='$uid'")->count();
            $this->assign('num', $num);
            // 获取当前登录账号信息
            $memberapi = new MemberApi();
            $memberInfo = $memberapi->getMemberInfo(array('uid' => is_login()));
            $this->assign('uid', $memberInfo['uid']);
            $this->assign('faceid', $memberInfo['face']);
            $safety_num = 1 + $memberInfo['email_auth'] + $memberInfo['mobile_auth'];
            $this->assign('safety_num', $safety_num); //安全等级
            $this->assign('memberInfo', $memberInfo);
            //dump($memberInfo);
            // 浏览历史
            $goodsapi =new GoodsApi();
            $history_list = $goodsapi->get_history();
            $this->assign('history_list', $history_list);
            // 获取最近购买的订单
            $orderapi = new OrderApi();
            $condition['uid'] = $uid;
            $orderlist = $orderapi->getOrderList($condition);
            $this->assign('orderlist', $orderlist);

            $OrderStautsCount = $orderapi->getOrderStautsCount($uid);
            $this->assign('ordernum', $OrderStautsCount);
            // 自动更新订单状态
            $orderapi->autoDealOrderStatus();

            //站内信数量
            $condition['uid'] = $uid;
            $condition['group'] = 2;
            $condition['status'] = 1;
            $ecount = M("personenvelope")->where($condition)->count();
            $this->assign('ecount', $ecount);
            $this->meta_title = get_username() . '的个人中心';
            //Cookie('__forward__',$_SERVER['REQUEST_URI']);
            $this->display();
        } else {
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->redirect('Member/login');
        }
    }

    public function login($username = "", $password = "", $verify = "") {
        if (IS_POST) {
            /* 调用UC登录接口登录 */
            $user = new UserApi;
            $uid = $user->login($username, $password, 3);
            if ($uid < 0) {
                $uid = $user->login($username, $password, 1);
            }
            if (0 < $uid) { //UC登录成功
                /* 登录用户 */
                $Member = D("Member");
                if ($Member->login($uid)) { //登录用户
                    //TODO:跳转到登录前页面
                    if ($_POST['email']) {
                        $msg = "注册成功!";
                    } else {
                        $msg = "登录成功!";

                        $remember = isset($_POST['remember']) ? $_POST['remember'] : '';
                        if (!empty($remember)) {
                            //1.cookie名：uid。推荐进行加密，比如MD5('站点名称')等。
                            //2.cookie值：登录名|有效时间Expires|hash值。hash值可以由"登录名+有效时间Expires+用户密码（加密后的）的前几位+salt"，salt是保证在服务器端站点配置文件中的随机数。
                            $cookiename = MD5(C('SITENAME'));
                            $expres = time() + 3600 * 24;
                            $UcenterMember = D("Ucenter_member");
                            $userinfo = $UcenterMember->field(true)->find($uid);
                            $password = $userinfo['password'];
                            $pass5 = substr($password, -5);
                            $autologinvalue = sha1($uid . $expres . $pass5 . UC_AUTH_AUTOLOGIN_KEY);
                            Cookie($cookiename, $uid . "|" . $expres . '|' . $autologinvalue);
//随机数保存在数据库中

                            $updateuser = array();
                            $updateuser["autologin"] = $autologinvalue;
                            $updateuser["id"] = $uid;
                            M("ucenter_member")->save($updateuser);
                        }
                        $memberinfo = M('member')->where(array('uid' => $uid))->find();
                        
                        session('memberinfo', $memberinfo);

//登录成功后把用收藏夹数量保存到cookie中。
//                        $fav = D("favortable");
//                        $favor = $fav->getfavor(1);
//                        Cookie('favor' . $uid, $favor);
                    }
                    $url = Cookie('__forward__');

                    if ($url) {
                        //redirect( Cookie('__forward__') );
                         $this->ajaxReturn(array('status'=>true,'info'=>$msg,'url'=>  $url));
                    } else {
                        //redirect( U('Index/index'));
                       $this->ajaxReturn(array('status'=>true,'info'=>$msg,'url'=>  S("temp_url")));
                    }
//                    $this->ajaxReturn(array('status'=>true,'info'=>$msg,'url'=>  S("temp_url")));
                } else {
                    $this->error($Member->getError());
                }
            } else { //登录失败
                switch ($uid) {
                    case -1:
                        $error = "用户不存在或被禁用！";
                        break; //系统级别禁用
                    case -2:
                        $error = "密码错误！";
                        break;
                    default:
                        $error = "未知错误！";
                        break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }
        } else {
             S('temp_url',Cookie('__forward__'));
            if (session("user_auth")) {
                $this->redirect(url('index/index'));
            }
            /* 底部分类调用 */
//            $menulist = R('Service/AllMenu');
//            $this->assign('footermenu', $menulist);
            $this->meta_title = '会员登录';
//显示登录表单
            $this->display();
        }
    }

    public function weixin_login() {

        $appid = C('weixin_config.AppID');
        $secret = C('weixin_config.AppSecret');
        $code = $_GET["code"];
        if (!$code) {
            $this->redirect('Member/login');
        }
        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $get_token_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        $json_obj = json_decode($res, true);
        //查询本地数据库微信用户 根据openid 查询用户信息
        $access_token = $json_obj['access_token'];
        $openid = $json_obj['openid'];
        $unionid = $json_obj["unionid"];
        $wx_info = M("member_wx")->where(array("unionid" => $unionid))->find();
        if ($wx_info) {
            if ($wx_info["uid"] == 0) {
                $member_type = D('Member')->getCate();
                $this->assign("member_type", $member_type);
                $this->assign("openid", $unionid);
                $this->assign("avatar", $wx_info['avatar']);
                $this->assign("nickname", $wx_info["nickname"]);
                $this->display();
            } else {
                $member = D('Member');
                $new_member = $member->getOne($wx_info['uid']);
                if ($new_member) {
                    $where = array('id' => $new_member['id']);
                    $member->where($where)->setInc('num');
                    $member->where($where)->setField('lastIp', get_client_ip());
                    $member->where($where)->setField('lastTime', time());
                    define('UID', $new_member['id']);
                    session('uid', $new_member['id']);
                    session('is_seat', $new_member['is_seat']);
                    $this->setSession($new_member);
                    $request_url = session('request_url');
                    $url = U('Member/index');
                    if (!empty($request_url)) {
                        $url = $request_url;
                    }
                    $this->success('登录成功', $url);
                } else {
                    $member_type = D('Member')->getCate();
                    $this->assign("member_type", $member_type);
                    $this->assign("openid", $unionid);

                    $this->assign("avatar", $wx_info['avatar']);
                    $this->assign("nickname", $wx_info["nickname"]);
                    $this->display();
                }
            }
        } else {
            $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $get_user_info_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $res = curl_exec($ch);
            curl_close($ch);

            //解析json
            $user_obj = json_decode($res, true);
            $data = array(
                "uid" => 0,
                "openid" => $user_obj["openid"],
                "unionid" => $unionid,
                "nickname" => $user_obj["nickname"],
                "avatar" => $user_obj["headimgurl"],
                "create_time" => time()
            );
            $res = M("member_wx")->add($data);
            $this->assign("openid", $unionid);
            $this->assign("avatar", $user_obj['headimgurl']);
            $this->assign("nickname", $user_obj["nickname"]);
            $member_type = D('Member')->getCate();
            $this->assign("member_type", $member_type);
            $this->display();
        }
    }

    public function register() {
        $step = I('step'); // 注册步骤
        $step = $step ? : 'step1';
        if (IS_POST) {
            $postdata = I('post.');
            $code_m["code"] = $postdata['code'];
            $code_m["phone"] = I("mobile");
            // TODO
            $count = M("phone_verify")->where($code_m)->count("*");
            if ($count == 0) {
                $this->error("短信验证校验错误！");
            }
            S('register_pre', $postdata);
            $this->success('操作成功', U('member/register', array('step' => 'step2')));
        } else {
            $reg_step = S('register_pre');
            if ($step == 'step2' && empty($reg_step['mobile'])) {
                redirect(U('member/register'));
            }
            $this->assign('reg_info', $reg_step);
            $this->meta_title = '会员注册';
            $this->display('register.' . $step);
        }
    }

    /**
     * 保存会员信息
     */
    public function savememberinfo() {
        if (IS_POST) {
            /* 检测密码 */
            $password = I('password');
            $repassword = I('repassword');
            if ($password != $repassword) {
                $this->error("密码和重复密码不一致！");
            }
            $reg_step = S('register_pre');
            $mobile = $reg_step['mobile'];
            $agent_code = $reg_step['code']; // 推荐码
            $agent_info = $this->getAgentInfo($agent_code);
            $username = I('nickname');
            if (empty($username)) {
                $username = 'B2C' . $mobile;
            }

            /* 调用注册接口注册用户 */
            $User = new UserApi;
            //返回ucentermember数据表用户主键id
            $uid = $User->register($username, $password, '', $mobile);
            if (0 < $uid) { //注册成功
//		$mapcode["phone"]=$reg_step['mobile'];
//		$re = M("Phone_Verify")->where($mapcode)->delete();
            	//设置用户数据
            	M('ucenter_member')->save(array('id'=>$uid,'reg_time'=>NOW_TIME,'reg_ip'=>get_ip_address(),'status'=>1));
            	
                $user = array('uid' => $uid, 'nickname' => $username, 'status' => 1, 'member_agent_id' => $agent_info['agent_id'], 'member_level_id' => $agent_info['level_id'], 'member_type' => $agent_info['member_type'], 'mobile_auth' => 1);
                if (!M('Member')->add($user)) {
                    $this->error('会员添加失败！');
                } else {
                    // 调用登录
                    S('register_pre', null);
                    $this->login($mobile, $password);
//                    $this->success('操作成功',U('member/register',array('step'=>'step3')));
                    $this->success('注册成功', U('member/regsuccess'));
                }
            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($uid));
            	//$this->error('会员注册失败！');
            }
        } else {
            $this->error('参数错误！！！');
        }
    }

    public function regsuccess() {
        $this->display("register.step3");
    }

    /**
     * 获取代理会员信息
     */
    function getAgentInfo($agent_code) {
        if (!$agent_code) {
            $data['level_id'] = 0;
            $data['agent_id'] = 0;
            $data['member_type'] = 0;
        }
        $agent_member = M("Member")->where(array('code' => $agent_code))->find();
        $data['agent_id'] = $agent_member['uid'] ? : 0;
        if ($agent_member['member_type'] >= 3 || $agent_member['member_type'] == 0) {
            $data['member_type'] = 0;
        } else {
            $data['member_type'] = $agent_member['member_type'] + 1;
        }

        $data['level_id'] = 0;

        return $data;
    }

    /**
     * 找回密码
     */
    public function forgotpassword() {
        $step = I('step') ? I('step') : 'step1';
        if (IS_POST) {
            $postdata = I('post.');
            $code_m["code"] = $postdata['code'];
            $code_m["phone"] = I("mobile");
            // TODO
            $count = M("Phone_Verify")->where($code_m)->count("*");
            if ($count == 0) {
//            if ($postdata['code'] != 1234) {
                $this->error("短信验证校验错误！");
            }
            S('forgot_pre', $postdata);

            $this->success('操作成功', U('member/forgotpassword', array('step' => 'step2')));
        } else {
            $this->meta_title = "找回密码";
            $this->display("forgotpassword." . $step);
        }
    }

    /**
     * 重置密码
     */
    public function resetpassword() {
        if (IS_POST) {
            /* 检测密码 */
            $forget_step = S('forgot_pre');
            $mobile = $forget_step['mobile'];
            $password = I('password');
            $repassword = I('repassword');
            if ($password != $repassword) {
                $this->error("密码和重复密码不一致！");
            }
            $Api = new UserApi();
            $res = $Api->updatePwd($mobile, $password);
            S('forgot_pre', null);
            if ($res) {
                $this->success("您的新密码已经重置成功,请及时修改", U("Member/forgotpassword", array('step' => 'step3')));
            } else {
                $this->error("重置密码更新失败!");
            }
        } else {
            $this->display();
        }
    }

    public function forgetsuccess() {
        $step = I('step');
        $this->assign('jump_url', U('member/login'));
        $this->display("forgotpassword." . $step);
    }

    public function logout() {
        if (is_login()) {
            D("Member")->logout();
            $url = Cookie('__forward__');
            if ($url) {
                redirect(Cookie('__forward__'));
                exit;
            } else {
                redirect(U('Index/index'));
                exit;
            }
            //$this->success("退出成功！",U('Index/index'));
        } else {
            redirect(U("Member/login"));
        }
        $this->display();
    }

    public function mycollection() {
        if (is_login()) {
            $_POST['r'] = 10;
            $favormodel = D("Favortable");
            $map['uid'] = session('user_auth.uid');
            $lists = $this->_lists($favormodel, $map, 'create_time DESC');
            if ($lists) {
                $documentViewModel = D("DocumentView");
                foreach ($lists as $k => $v) {
                    $product_detail = array();
                    $product_detail = S('p_' . $v['goodid']);
                    if (!$product_detail) {
                        $product_detail = $documentViewModel->find($v['goodid']);
                        if ($product_detail["cover_id"] || $product_detail['pics']) {
                            if ($product_detail['pics']) {
                                $arr = explode(",", $product_detail['pics']);
                            }
                            $arr[] = $product_detail["cover_id"];
                            $arrmap["id"] = array("in", $arr);
                            $attinfo = array();
                            $attinfo = M("picture")->where($arrmap)->getField("id,path");

                            //2015/6/25 14:39 sheshanhu 对图片地址进行组装拼接
                            foreach ($attinfo as $ckey => $cvalue) {
                                //$attinfo[$ckey] = __PICURL__ . $product_detail['domainid'] . '/' . $cvalue;
                            	$attinfo[$ckey] = __PICURL__ .$cvalue;
                            }

                            $product_detail["pics_img"] = $attinfo;
                        }
                        S('p_' . $v['goodid'], $product_detail);
                    }
                    //dump($product_detail);exit;
                    $product_detail["create_time"] = date("Y-m-d H:i:s", $v["create_time"]);
                    $lists[$k] = $product_detail;
                }
            }
            
            $this->assign('favorlist', $lists);
            $pv = I('get.p', 1);
            $pv = $pv - 1;

            $this->assign('pv', $pv);
           // $this->assign('page', $show);
            $this->meta_title = '我的收藏';
        } else {
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->redirect('Member/login');
        }
        $this->display();
    }

    /*     * ***个人资料************** */

    public function information() {
        if (is_login()) {
            $memberapi = new MemberApi();
            $memberInfo = $memberapi->getMemberInfo(array('uid' => is_login()));
            $this->assign('uid', $memberInfo['uid']);
            $this->assign('faceid', $memberInfo['face']);
            $safety_num = 1 + $memberInfo['email_auth'] + $memberInfo['mobile_auth'];
            $this->assign('safety_num', $safety_num); //安全等级
            $this->assign('memberInfo', $memberInfo);
//            dump($memberInfo);
            $this->meta_title = get_username() . '个人中心';
            $this->display();
        } else {
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->redirect('User/login');
        }
    }

    /*     * *************************************************************
     * content:个人信息更新提交方法
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    public function update() {
        //判断手机号是否更改，如果更改判断校验码是否正确，如果正确则进行保存，如果不正确提示消息
        //第一步，获取当前登录账号的UID
        $data = array();
        $m = D("member");
        $uid = $m->uid();
        //第二步获取用户手机号，在ucenter_member表中
        $member = M("member");
        $ucenter_member = M("ucenter_member");
        $mobile = $ucenter_member->where("id='$uid'")->getField('mobile');
        //第三步获取页面提交过来的数据
        $datamember = array();
        $datamemberucenter = array();
        $postlist = I('post.');
        //memeber表中
        //$datamember['uid'] = $uid;
        $datamember['nickname'] = isset($postlist['nickname']) ? $postlist['nickname'] : '';

        $datamember['realname'] = isset($postlist['realname']) ? $postlist['realname'] : '';
        $datamember['province'] = isset($postlist['province']) ? $postlist['province'] : '';
        $datamember['city'] = isset($postlist['city']) ? $postlist['city'] : '';
        $datamember['area'] = isset($postlist['area']) ? $postlist['area'] : '';
        $datamember['address'] = isset($postlist['address']) ? $postlist['address'] : '';
        $datamember['card_no'] = isset($postlist['card_no']) ? $postlist['card_no'] : '';
        $datamember['sex'] = $postlist['sex']; // 0:保密；1:男；2：女
        $datamember['update_time'] = NOW_TIME;
//        $datamember['birthday'] = isset($postlist['birthday']) ? $postlist['birthday'] : '';
        //ucenter member表中
        //$datamemberucenter['id'] = $uid;
//        $datamemberucenter['mobile'] = isset($postlist['mobile']) ? $postlist['mobile'] : '';
        if (isset($postlist['nickname']) && !empty($postlist['nickname'])) {
            $datamember['nickname'] = isset($postlist['nickname']) ? $postlist['nickname'] : '';
        }

        if (isset($postlist['email']) && !empty($postlist['email'])) {
            $datamemberucenter['email'] = $postlist['email'];
        }

        $datamemberucenter['username'] = $datamember['nickname'];

        $result1 = $member->where("uid='$uid'")->save($datamember);
        $result2 = $ucenter_member->where("id='$uid'")->save($datamemberucenter);
        if ($result1 || $result2) {
            $data['status'] = 1;
            $data['info'] = '修改成功！';
            //$this->success('修改成功！',U("center/information"));
            //用户信息更新成功后，也要更新
            $userinfo = session('user_auth');
            $userinfo['username'] = $datamember['nickname'];
            session('user_auth', $userinfo);
            session('user_auth_sign', data_auth_sign($userinfo));
            $this->success('修改成功', U('Member/index'));
        } else {
            $data['status'] = 0;
            $data['info'] = '修改失败！';
            $this->error('修改失败！');
        }
    }

    /*     * *************************************************************
     * created date:2015/4/24 17:55
     * created author:sheshanhu
     * content:个人中心头像修改
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    public function updateimage() {
        $m = D("member");
        $uid = $m->uid();
        $member = M("ucenter_member");
        //如果PIC为空就不保存
        $pic = I('pic');
        $face = I('face');

        if (!empty($pic) && !empty($face)) {
            $data = $member->create();
            $result = $member->where("id='$uid'")->save();
            if ($result) {
                $this->success('修改成功！', U("Member/information"));
            } else {
                $this->error('修改失败！');
            }
        } else {
            $this->error('修改失败！');
        }
    }

    public function cut() {
        //$uid=I("get.id");
        $uid = is_login();
        $cut = M("member")->where("uid='$uid'")->select();
        $this->assign('cut', $cut);
        $this->assign('uid', $uid);



        //如果已经有头像则调用存在的图片
        $faceid = M('ucenter_member')->where("id='$uid'")->getField("face");
        $this->assign('faceid', $faceid);
        $images = new \Think\Image();
        if ($_POST['pic']) {
            //$src=C('DOMAIN').$_POST["pic"];
            $src = $_POST['pic'];
            $images->open($src);
            $name = time() . $src;
            $x = $_POST["x"];
            $y = $_POST["y"];
            $w = $_POST["w"];
            $h = $_POST["h"];
            $s = $images->crop(400, 400, 100, 30)->save('./' . $name);
            echo $s;
        }
        $this->meta_title = '修改图像';
        $this->display();
    }

    /**
     * 地址中心
     */
    public function address() {
        if (is_login()) {
            $m = D("member");
            $uid = $m->uid();
            //如果有数据提交就更新操作
            $nowaddress = array();
            if (IS_GET) {
                $id = I('get.id');
                //根据地址ID和当前登录用户ID查询地址信息
                $address = M("transport");
                $list = $address->where("uid='$uid'")->select();
                if (!empty($id)) {
                    foreach ($list as $vb) {
                        if ($vb['id'] == $id) {
                            $nowaddress = $vb;
                        }
                    }
                    if (empty($nowaddress['area'])) {
                        $nowaddress['area'] = 'none';
                    }
//                    dump($nowaddress);
                    $this->assign('nowaddress', $nowaddress);
                }
            } else {
                $address = M("transport");
                $list = $address->where("uid='$uid'")->select();
                $this->assign('nowaddress', $nowaddress);
            }
            $this->assign('list', $list);
            $this->meta_title = get_username() . '的地址管理';
            $this->display('address.index');
        } else {
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->redirect('member/login');
        }
        $this->display('address.index');
    }

    // 删除地址
    public function deleteAddress() {
        if (is_login()) {
            $Transport = M("transport"); // 实例化transport对象
            $Member = D("member");
            $uid = $Member->uid();
            $id = $_POST["id"];
            if ($Transport->where("uid='$uid' and id='$id'")->delete()) {
                $data['msg'] = "删除成功";
                $data['status'] = 1;
                $this->ajaxreturn($data);
            } else {
                $data['msg'] = "删除失败";
                $data['status'] = 0;
                $this->ajaxreturn($data);
            }
        } else {
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->redirect('User/login');
        }
    }

    /*     * *************************************************************
     * created date:2015/4/25 10:19
     * created author:sheshanhu
     * content:新增 修改地址
     * modefiy person:
     * modefiy date:
     * note:
     * ************************************************************** */

    public function save() {
        if (is_login()) {
            $Transport = M("transport"); // 实例化transport对象
            $postinfo = I('post.');
            $data['id'] = $postinfo["id"];
            $data['phone'] = $postinfo["phone"];
            $data['realname'] = $postinfo["realname"];
            $data['isdefault'] = isset($postinfo["isdefault"]) ? 1 : 0;
            $data['province'] = $postinfo["province"];
            $data['city'] = $postinfo["city"];
            $data['area'] = isset($postinfo["area"]) ? $postinfo["area"] : '';
            $data['address'] = $postinfo["address"];
            $data['cellphone'] = $postinfo["cellphone"];
            $data['phone'] = $postinfo["phone"];
            $data['card_no'] = $postinfo["card_no"];
            $data['youbian'] = $postinfo["youbian"];

            if (empty($data['id'])) {
                unset($data['id']);
            }
            $Member = D("member");
            $uid = $Member->uid();
            $data['uid'] = $uid;
            //$data['status'] = 0;
            $data['time'] = NOW_TIME;
            if (!isset($data['id'])) {
                $count = $Transport->where("uid=" . $uid)->count("*");
                if ($count >= 10) {
                    $this->error("最多只能添加10个收货地址哦");
                }
                $id = $Transport->add($data);
                if ($id) {
                    if ($data['isdefault'] == 1) {
                        $map = array();
                        $map["uid"] = $uid;
                        $map["id"] = array("neq", $id);
                        $updatedefault = $Transport->where($map)->setField("isdefault", 0);
                    }
                    $this->success('新增成功！', U("Member/address"));
                } else {
                    $this->error('新增失败！');
                }
            } else {
                $returninfo = $Transport->save($data);
                if ($returninfo) {
                    //清除其他默认地址
                    if ($data['isdefault'] == 1) {
                        $id = $data['id'];
                        $map = array();
                        $map["uid"] = $uid;
                        $map["id"] = array("neq", $id);
                        $updatedefault = $Transport->where($map)->setField("isdefault", 0);
                    }
                    $this->success('修改成功！', U("Member/address"));
                } else {
                    $this->error('修改失败！');
                }
            }
        } else {
            Cookie('__forward__', $_SERVER['REQUEST_URI']);
            $this->redirect('Member/login');
        }
    }

    public function defaultaddress() {
        $id = I('id');
        if (empty($id)) {
            $this->error("参数错误");
        }
        $uid = is_login();
        $Transport = M("transport"); // 实例化transport对象
        if ($Transport->where(array('id' => $id))->setField(array('isdefault' => 1))) {
            $map = array();
            $map["uid"] = $uid;
            $map["id"] = array("neq", $id);
            $updatedefault = $Transport->where($map)->setField("isdefault", 0);
            if($updatedefault){
                $this->success("设置成功");
            }
        }else{
            $this->error('设置失败');
        }
    }

    /**
     * 修改密码提交
     */
    public function profile() {
        if (!is_login()) {
            $this->error("您还没有登录", U("User/login"));
        }

        /** 购物车调用数量* */
        if (!session('user_auth')) {
            $usercartcount = $_SESSION['cart'] ? count($_SESSION['cart']) : 0;
        } else {
            $usercartcount = D("Shopcart")->getNumByuid();
            ;
        }
        $this->assign('usercartcount', $usercartcount);


        if (IS_POST) {
            //获取参数
            $uid = is_login();
            $password = I("post.old");
            $repassword = I("post.repassword");
            $data["password"] = I("post.password");
            empty($password) && $this->error("请输入原密码");
            empty($data["password"]) && $this->error("请输入新密码");
            empty($repassword) && $this->error("请输入确认密码");
            if ($data["password"] !== $repassword) {
                $this->error("您输入的新密码与确认密码不一致");
            }
            $this->assign('uid', $uid);
            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if ($res['status']) {
                $this->success("修改密码成功！");
            } else {
                $this->error($res["info"]);
            }
            return $this->ajaxreturn($rdata);
        } else {
            $uid = is_login();
            $this->assign('uid', $uid);
            $this->meta_title = '修改密码';
            $this->display();
        }
    }

    /**
     * 检查手机号
     */
    public function checkMobile() {
        $mobile = I("mobile");
        $map['mobile'] = $mobile;
        $forgetpwd = I("forgetpwd", 0);
        $count = M("ucenter_member")->where($map)->count("*");
        ob_end_clean();
        if ($count > 0) {
            if ($forgetpwd) {
                echo 'true';
            } else {
                echo 'false';
            }
        } else {
            if ($forgetpwd) {
                echo 'false';
            } else {
                echo 'true';
            }
        }
        exit();
    }

    /**
     * 获取短信验证码
     */
    function getMobileCode() {
        $mobile = I("post.mobile");
        $type = I("post.type", 1);
        $is_mobile = M('phone_verify')->where(array('type' => $type, 'phone' => $mobile, 'create_time' => array('gt', NOW_TIME - 1 * 60 * 60)))->find();
        if ($is_mobile) {
            $this->error("验证码发送重复太频繁");
        }
        $result = $this->_checkMobile($mobile, $type);
        if ($result == true) {
            $re = $this->sendPhoneMsg($type, $mobile);
        } else {
            $this->error("该手机号已注册");
        }
        if ($re) {
            $this->success("发送成功");
        } else {
            $this->error("发送失败");
        }
    }

    /**
     * 检查手机号是否存在
     */
    public function _checkMobile($mobile, $forgetpwd = null) {
        $map['mobile'] = $mobile;
        $count = M("ucenter_member")->where($map)->count("*");
        if ($count > 0) {
            if ($forgetpwd) { // 1 找回密码
                return true;
            } else {
                return FALSE;
            }
        } else {
            if ($forgetpwd) {   // 0 注册
                return FALSE;
            } else {
                return true;
            }
        }
    }

}
