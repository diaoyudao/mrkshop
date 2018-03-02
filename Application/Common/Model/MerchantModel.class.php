<?php

namespace Common\Model;

/**
 * 商家模型
 */
class MerchantModel extends CommonModel {
    /* 商家模型自动验证 */
    protected $_validate = array(
        /* 验证手机号码 */
        array('mobile', 'require','手机号不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('mobile', 'checkMobile', '手机号格式错误', self::EXISTS_VALIDATE,'callback'), //手机格式不正确
        array('mobile', '', '手机号已被占用', self::EXISTS_VALIDATE, 'unique',self::MODEL_INSERT), //手机号被占用

        /* 验证密码 */
        array('passwords', '6,20', '请输入6到20位的密码', self::EXISTS_VALIDATE, 'length'), //密码长度不合法
    );

    /* 商家模型自动完成 */
    protected $_auto = array(
        array("reg_ip", "get_client_ip", self::MODEL_INSERT, "function", 1),
        array("reg_time", NOW_TIME, self::MODEL_INSERT),
        array("last_login_ip", 0, self::MODEL_INSERT),
        array("last_login_time", 0, self::MODEL_INSERT),
        array("status", 1, self::MODEL_INSERT),
    );

    /**
     * 检测手机格式是否正确
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkMobile($mobile) {
        if (!is_numeric($mobile)) {
            return false;
        }

        return preg_match("#^1[3456789]{10}$#",$mobile) ? true : false;
//        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }

    /**
     * 商家注册
     * @param array $data 注册提交的数据
     * @return boolean  ture-注册成功，false-注册失败
     */
    public function register($data = array()) {
        //验证手机
        if (empty($data['mobile'])){
            $this->error = '手机号不能为空！';
            return false;
        }
        //生产安全码
        $data['secret_key'] = think_ucenter_encrypt($data['mobile'],C('DATA_AUTH_KEY'));

        /* 添加数据 */
        if ($this->create($data)) {
            if($this->add()){
                return true;
            }else{
                $this->error = '注册失败！请联系我们！';
                return false;
            }
        } else {
            $this->error = $this->getError(); //错误详情见自动验证注释
            return false;
        }
    }

    /**
     * 商家登录
     * @param  integer $mobile 商家手机号
     * @param  string $passwords 密码
     * @return boolean ture-登录成功，false-登录失败
     */
    public function login($mobile,$passwords) {
        /* 检测是否注册 */
        $merchants = $this->where(array('mobile'=>intval($mobile)))->find();

        if (is_array($merchants) && $merchants['status']) {
            /* 验证密码 */
            if (think_ucenter_md5($passwords, $merchants['secret_key']) === $merchants['password']) {
                /* 登录商家 */
                $this->autoLogin($merchants);
                /* 登录历史 */
                history($merchants);
                //记录行为
                action_log("merchant_login", "merchant", $merchants['id'], $merchants['id']);
                return true;
            } else {
                $this->error = "密码错误！";
                return false;
            }
        } else{
            $this->error = "商家不存在或已禁用！";
            return false;
        }
    }

    /**
     * 注销当前商家
     * @return void
     */
    public function logout() {
        session("merchant_auth", null);
        session("merchant_auth_sign", null);
        unset($_SESSION['merchant_auth'],$_SESSION['merchant_auth_sign']);
        $cookiename = MD5(C('SITENAME'));
        Cookie($cookiename, null);
    }

    /**
     * 自动登录商家
     * @param  integer $merchants 商家信息数组
     */
    private function autoLogin($merchants) {
        /* 更新登录信息 */
        $data = array(
            "id" => $merchants["id"],
            "login" => array("exp", "`login`+1"),
            "last_login_time" => NOW_TIME,
            "last_login_ip" => get_client_ip(1),
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            "id" => $merchants["id"],
            "logn" => $merchants["mobile"],
            "last_login_time" => $merchants["last_login_time"],
            "mtype" => $merchants['mtype'],
            "mstatus" => $merchants['mstatus'],
            "parent_id" => $merchants['parent_id']
        );
        session("merchant_auth", $auth);
        session("merchant_auth_sign", data_auth_sign($auth));
    }

    //直接获取商家ID
    public function mid() {
        $merchants = session("merchant_auth");
        return  $merchants ? $merchants["id"] : 0;
    }

}
