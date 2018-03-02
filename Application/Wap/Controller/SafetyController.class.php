<?php

/**
 * 安全中心
 */

namespace Wap\Controller;

use Wap\Controller\HomeController;
//use Common\Util\Validate;
use User\Api\UserApi;
use Api\Api\MemberApi;

class SafetyController extends HomeController {

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
        );
    }

    /**
     * 安全中心
     */
    public function safety() {
        if (is_login()) {
            $Member = D("member");
            $uid = $Member->uid();
            $memberinfo = $Member->where(array("uid" => $uid))->find();
            $info = M('ucenter_member')->where("id='$uid'")->find();
            $memberinfo = array_merge($memberinfo, $info);
            $num = 1 + $memberinfo['email_auth'] + $memberinfo['mobile_auth'];
            $this->assign('num', $num); //安全等级
            $this->assign('info', $info);
            $this->assign('userinfo', $memberinfo);
            $this->meta_title = '安全中心';
            $this->display('safety.index');
        } else {
            $this->redirect('Member/login');
        }
    }

    /**
     * 修改手机
     */
    public function changeMobile() {
        if (IS_AJAX) {
            $phone = I('mobile');
            $code = I('code');

            $old_mobile = I("old_mobile");
            $old_data['phone'] = $old_mobile;
            $old_data['code'] = $code;
            $counts = M("PhoneVerify")->where($old_data)->count("*");
            if ($counts == 0) {
                $this->error('原手机的验证码错误');
            }

            $new_code = I('new_code');
            $code_m["code"] = $new_code;
            $code_m["phone"] = $phone;
            $count = M("PhoneVerify")->where($code_m)->count("*");
            if ($count == 0) {
                $this->error('新手机的验证码错误');
            } else {
                $auth = array(
                    'mobile' => $phone,
//                    'mobile_auth' => 1
                );
                $uid = is_login();
                action_log('binding_mobile', 'member', $uid, $uid, 1);
                $re = M('ucenter_member')->where('id=' . $uid)->setField($auth);
                $re = M('member')->where('uid=' . $uid)->setField(array('mobile_auth' => 1));
                $re = M("PhoneVerify")->where(array('phone' => $phone))->delete();
                $re = M("PhoneVerify")->where(array('phone' => $old_mobile))->delete();
                $this->success('绑定成功', U('Safety/safety'));
            }
        } else {
            $memberapi = new MemberApi();
            $memberInfo = $memberapi->getMemberInfo(array('uid' => is_login()));
            $this->assign('memberInfo', $memberInfo);
            $this->display('safety.phone');
        }
    }

    /**
     * 获取验证码
     */
    function getMobileCode() {
        $mobile = I("post.mobile");
        $type = I("post.type", 3); // 3： 绑定手机  2： 找回密码 0:注册  4：旧手机验证码
        $re = $this->sendPhoneMsg($type, $mobile);
        if ($re) {
            //$this->success("发送成功".$re);
        	$this->success("发送成功");
        } else {
            $this->error("发送失败");
        }
        exit;
    }

    //绑定手机
    public function bind_mobile() {
        if (IS_AJAX) {
            $phone = I('mobile');
            $code = I('code');

            //已綁定的手機號碼
            $old_mobile = I("old_mobile");
            if(!empty($old_mobile)){
            	$old_data['phone'] = $old_mobile;
            	$old_data['code'] = $code;
            	$counts = M("PhoneVerify")->where($old_data)->count("*");
            	if ($counts == 0) {
            		$this->error('原手机的验证码错误');
            	}
            }

            //新手機號碼
            $new_code = I('new_code');
            $code_m["code"] = $new_code;
            $code_m["phone"] = $phone;
            $count = M("PhoneVerify")->where($code_m)->count("*");
            if ($count == 0) {
                $this->error('新手机的验证码错误');
            } else {
                $auth = array(
                    'mobile' => $phone,
//                    'mobile_auth' => 1
                );
                $uid = is_login();
                action_log('binding_mobile', 'member', $uid, $uid, 1);
                $re = M('ucenter_member')->where('id=' . $uid)->setField($auth);
                $re = M('member')->where('uid=' . $uid)->setField(array('mobile_auth' => 1));
                $re = M("PhoneVerify")->where(array('phone' => $phone))->delete();
                $re = M("PhoneVerify")->where(array('phone' => $old_mobile))->delete();
                $this->success('绑定成功', U('Safety/safety'));
            }
        } else {
            $memberapi = new MemberApi();
            $memberInfo = $memberapi->getMemberInfo(array('uid' => is_login()));
            $this->assign('memberInfo', $memberInfo);
            $this->display('safety.phone');
        }
    }

    /**
     * 设置密码
     */
    public function setpassword() {
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
                D("Member")->logout();
                $this->success("修改密码成功！", U('Member/login'));
            } else {
                $this->error($res["info"]);
            }
        } else {
//            $this->error('参数错误');
            $this->display('safety.password');
        }
    }

    /**
     * 第三方账号列表
     */
    public function account() {

        $this->meta_title = "账号绑定";
        $this->display('account_list');
    }

}
