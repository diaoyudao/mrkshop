<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use User\Api\UserApi;

/**
 * 后台用户控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class MemberController extends AdminController {

    private $_type = array(1 => '小', 2 => '中', 3 => '大');

    /**
     * 用户管理首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index() {
        $nickname = I('nickname');
        $map['status'] = array('egt', 0);
        $map['is_admin'] = array('eq', 0);
        $member_type =I('member_type',10);
        if($member_type <= 3){
            $map['member_type']= $member_type;
        }
        if ($nickname) {
            if (is_numeric($nickname)) {
                $map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
            } else {
                $map['nickname'] = array('like', '%' . (string) $nickname . '%');
            }
        }


        //$list   = $this->lists('Member', $map);

        $MemberViewModel = D("MemberView");
        $list = $this->lists($MemberViewModel, $map);

        int_to_string($list);
//        dump($list);
        $this->assign('_list', $list);
        $this->meta_title = '用户信息';
        $this->display();
    }

    /**
     * 修改昵称初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updateNickname() {
        $nickname = M('Member')->getFieldByUid(UID, 'nickname');
        $this->assign('nickname', $nickname);
        $this->meta_title = '修改昵称';
        $this->display('updatenickname');
    }

    /**
     * 修改昵称提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitNickname() {
        //获取参数
        $nickname = I('post.nickname');
        $password = I('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');

        //密码验证
        $User = new UserApi();
        $uid = $User->login(UID, $password, 4);
        ($uid == -2) && $this->error('密码不正确');

        $Member = D('Member');
        $data = $Member->create(array('nickname' => $nickname));
        if (!$data) {
            $this->error($Member->getError());
        }

        $res = $Member->where(array('uid' => $uid))->save($data);

        if ($res) {
            $user = session('user_auth');
            $user['username'] = $data['nickname'];
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            $this->success('修改昵称成功！');
        } else {
            $this->error('修改昵称失败！');
        }
    }

    /**
     * 修改密码初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updatePassword() {
        $this->meta_title = '修改密码';
        $this->display('updatepassword');
    }

    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitPassword() {
        //获取参数
        $password = I('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if ($data['password'] !== $repassword) {
            $this->error('您输入的新密码与确认密码不一致');
        }

        $Api = new UserApi();
        $res = $Api->updateInfo(UID, $password, $data);
        if ($res['status']) {
            $this->success('修改密码成功！');
        } else {
            $this->error($res['info']);
        }
    }

    /**
     * 用户行为列表
     * @author huajie <banhuajie@163.com>
     */
    public function action() {
        //获取列表数据
        $Action = M('Action')->where(array('status' => array('gt', -1)));
        $list = $this->lists($Action);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('_list', $list);
        $this->meta_title = '用户行为';
        $this->display();
    }

    /**
     * 新增行为
     * @author huajie <banhuajie@163.com>
     */
    public function addAction() {
        $this->meta_title = '新增行为';
        $this->assign('data', null);
        $this->display('editaction');
    }

    /**
     * 编辑行为
     * @author huajie <banhuajie@163.com>
     */
    public function editAction() {
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $data = M('Action')->field(true)->find($id);

        $this->assign('data', $data);
        $this->meta_title = '编辑行为';
        $this->display('editaction');
    }

    /**
     * 更新行为
     * @author huajie <banhuajie@163.com>
     */
    public function saveAction() {
        $res = D('Action')->update();
        if (!$res) {
            $this->error(D('Action')->getError());
        } else {
            $this->success($res['id'] ? '更新成功！' : '新增成功！', Cookie('__forward__'));
        }
    }

    /**
     * 会员状态修改
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function changeStatus($method = null) {
        $id = array_unique((array) I('id', 0));
        if (in_array(C('USER_ADMINISTRATOR'), $id)) {
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['uid'] = array('in', $id);
        $rmap['id'] = array('in', $id);
        $adminmap['member_id'] = array('in', $id);
        switch (strtolower($method)) {
            case 'forbiduser':
                $this->forbid('Member', $map);
                $this->forbid('Ucenter_member', $rmap);
                $this->forbid('Ucenter_admin', $adminmap);
                break;
            case 'resumeuser':
                $this->resume('Member', $map);
                $this->resume('Ucenter_member', $rmap);
                $this->resume('Ucenter_admin', $adminmap);
                break;
            case 'deleteuser':
                $this->delete('Member', $map);
                $this->delete('Ucenter_member', $rmap);
                $this->delete('Ucenter_admin', $adminmap);
                break;
            default:
                $this->error('参数非法');
        }
    }

    public function add($username = '', $password = '', $repassword = '', $email = '', $mobile = '') {
        if (IS_POST) {
            /* 检测密码 */
            if ($password != $repassword) {
                $this->error('密码和重复密码不一致！');
            }
            $member_type = I('post.member_type');
            $member_level_id = I('post.member_level_id');
            $member_agent_id = I('post.member_agent_id');
            /* 调用注册接口注册用户 */
            $User = new UserApi;
            $uid = $User->register($username, $password, $email, $mobile);
            if (0 < $uid) { //注册成功
                $user = array('uid' => $uid, 'nickname' => $username, 'status' => 1, 'member_agent_id' => $member_agent_id, 'member_level_id' => $member_level_id, 'member_type' => $member_type);
                if (!M('Member')->add($user)) {
                    $this->error('会员添加失败！');
                } else {
                    //是否是测试用户
                    $istestuser = I('post.istestuser', 0);
                    $data = array();
                    $data = array('istestuser' => $istestuser);
                    M('ucenter_member')->where('id=' . $uid)->save($data);
                    $code =I('code');
                    if (!empty($code)) {
                        $url = WAP_SITE_URL."/index.php?s=Member/register/code/{$code}.html";// U("Member/register",array('code'=>$code)); //
                        getQrode($url, $uid, 'code');
//                        getQrode("您的邀请码：" . $code, $uid, 'code');
                    }
                    $this->success('会员添加成功！', U('index'));
                }
            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($uid));
            }
        } else {
            $level_list = C('MEMBER_LEVEL_LIST');
            $member_type = $this->lists('Member_type');
            $this->assign('member_type', $member_type);
            $this->meta_title = '新增会员';
            $this->display();
        }
    }

    public function edit() {
        if (IS_POST) {
            $password = I('password');
            $repassword = I('repassword');
            if ($password != $repassword) {
                $this->error('密码和重复密码不一致！');
            }

//            $data = array('mobile'=>I('mobile'));
            $post = I('post.');
//            if (empty($password)) {
//            }
            unset($post['repassword']);

            $data = array_filter($post);
            $uid = I('post.uid');
            $User = new UserApi;
            $id = $User->updateUcenterInfo($uid, $data);

            if (!empty($post['code'])) {
               $url = WAP_SITE_URL."/index.php?s=Member/register/code/{$post['code']}.html";// U("Member/register",array('code'=>$code));
                getQrode($url, $uid, 'code');
            }

            if (0 < $id) { // 修改成功
                D("Member")->updateMember($uid, $data);
                $this->success('会员更新成功！', U('index'));
            } else {
                $this->error('会员更新失败');
            }
            exit;
        } else {
            $member_id = I('uid');
            $member_info = D("MemberView")->info(array('uid' => $member_id));
            $level_list = C('MEMBER_LEVEL_LIST');
            $member_type = $this->lists('Member_type');
            $this->assign('member_type', $member_type);
            $this->assign('member_info', $member_info);
            $this->meta_title = '编辑会员';
            $this->display();
        }
    }

    public function test() {
        $path = __UPLOADS__ . '/member/22.png';
        dump(getQrode(22, $path));
    }

    /**
     * 设置银行信息
     */
    public function bank() {
        $model_bank = D("MemberBank");
        if (IS_POST) { //提交表单
            if (false !== $model_bank->update()) {
                $this->success('操作成功！', U('Member/index'));
            } else {
                $error = $model_bank->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $member_id = I('get.uid');
            $member_info = M("Member")->where(array('uid' => $member_id))->find();
            $member_bank = M("Member_bank")->where(array('member_id' => $member_id))->find();
            $this->assign('member_bank', $member_bank);
            $this->assign('member_info', $member_info);
            $this->display('member.bank');
        }
    }

    /**
     * 会员上级和会员等级
     */
    public function getMemberAgent() {
        $member_type = I('member_type');
        if (empty($member_type)) {
            $data = array('status' => 1, 'info' => '', 'error' => '参数错误！');
            $this->ajaxReturn($data);
        }
        $where['member_type'] = $member_type - 1;
        $where['status'] = array('egt', 0);
        $where['is_admin'] = array('eq', 0);
        $agentlist = $this->lists('member', $where);
        $levellist = $this->lists('member_level', array('member_type' => $member_type, 'status'=>array('egt',0)));
        $option = '<option value="0">请选择...</option>';
        foreach ($agentlist as $key => $agent) {
            if($agent['uid'] == I('member_agent_id')){
                $option .= "<option selected value=" . $agent['uid'] . ">会员名：{$agent['nickname']}</option>";
            }else{
                $option .= "<option value=" . $agent['uid'] . ">会员名：{$agent['nickname']}</option>";
            }
        }
        $htmlagentlist = '<select name="member_agent_id">' . $option . '</select>';

        if($member_type == 1){
            $htmlagentlist = '';
        }
        $option1 = '<option value="0">请选择...</option>';
        foreach ($levellist as $key => $level) {
            if ($level['id'] == I('level_id')) {
                $option1 .= "<option selected value=" . $level['id'] . ">{$level['level_name']}</option>";
            } else {
                $option1 .= "<option value=" . $level['id'] . ">{$level['level_name']}</option>";
            }
        }
        $htmllevellist = "&nbsp;<label>会员等级</label>&nbsp;<select name='member_level_id'>{$option1}</select>";
        $data = array('status' => 0, 'info' => $htmlagentlist . $htmllevellist, 'error' => '');
        $this->ajaxReturn($data);
        exit;
    }

    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0) {
        switch ($code) {
            case -1: $error = '用户名长度必须在16个字符以内！';
                break;
            case -2: $error = '用户名被禁止注册！';
                break;
            case -3: $error = '用户名被占用！';
                break;
            case -4: $error = '密码长度必须在6-30个字符之间！';
                break;
            case -5: $error = '邮箱格式不正确！';
                break;
            case -6: $error = '邮箱长度必须在1-32个字符之间！';
                break;
            case -7: $error = '邮箱被禁止注册！';
                break;
            case -8: $error = '邮箱被占用！';
                break;
            case -9: $error = '手机格式不正确！';
                break;
            case -10: $error = '手机被禁止注册！';
                break;
            case -11: $error = '手机号被占用！';
                break;
            case -12: $error = '用户名必须填写！';
                break;
            case -13: $error = '手机号必须填写！';
                break;
            default: $error = '未知错误';
        }
        return $error;
    }

    /**
     * 会员类型
     */
    public function member_type() {
        $this->meta_title = "会员类型列表";
        $map = array();
        $list = $this->lists('Member_type', $map);
        int_to_string($list);
        foreach ($list as $k => &$v) {
            $v["ismembertype"] = true;
            $level_list = M('member_level')->where("status >=0 and member_type=" . $v["id"])->select();
            $v["_"] = $level_list;
        }
//        dump($list);
        //// 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('tree', $list);
        $this->assign('_list', $list);
        $level_list = C('MEMBER_LEVEL_LIST');
        $this->assign('level_list', $level_list);
        $this->display();
    }

    // 更新会员等级状态
    public function setStatus() {
        parent::setStatus('member_level');
    }

    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function tree($tree = null) {
        $this->assign('tree', $tree);
        $this->display('tree');
    }

    /**
     * 用户等级设置
     */
    public function level() {
        $this->meta_title = '会员等级列表';

        $list = D('MemberLevel')->lists();
        int_to_string($list);
        $this->assign('_list', $list);
        $this->display('level.index');
    }

    public function level_add($id = null) {

        $this->meta_title = '设置会员等级';
        $model_level = D('MemberLevel');

        if (IS_POST) { //提交表单
            if (false !== $model_level->update()) {
                $this->success('操作成功！', U('Member/member_type'));
            } else {
                $error = $model_level->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $level_list = C('MEMBER_LEVEL_LIST');
            $list = $this->lists('Member_type', $map);
//            dump($list);
            $info = $id ? $model_level->info($id) : '';
            I("get.member_type") ? $info['member_type'] = I("get.member_type") : '';
            $this->assign('info', $info);
//            dump($info);
            $this->assign('_list', $list);
            $this->assign('level_list', $level_list);
            $this->display('level.add');
        }
    }

}
