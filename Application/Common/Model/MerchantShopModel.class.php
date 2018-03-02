<?php

namespace Common\Model;

use Think\Model;

/**
 * 店铺模型
 */
class MerchantShopModel extends CommonModel {
    /* 店铺模型自动验证 */
    protected $_validate = array(
        /* 验证店铺名称 */
        array('sname', 'require','店铺名称不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('sname', '1,20', '请输入2到20位的店铺名称', self::EXISTS_VALIDATE, 'length'), //长度不合法
        array('sname', '', '店铺名称已被占用', self::EXISTS_VALIDATE, 'unique',self::MODEL_INSERT), //被占用
    );

    /* 店铺模型自动完成 */
    protected $_auto = array(
        array("reg_ip", "get_client_ip", self::MODEL_INSERT, "function", 1),
        array("reg_time", NOW_TIME, self::MODEL_INSERT),
        array("last_login_ip", 0, self::MODEL_INSERT),
        array("last_login_time", 0, self::MODEL_INSERT),
        array("status", 1, self::MODEL_INSERT),
    );


    /**
     * 创建店铺
     * @param array $data 提交的数据
     * @return boolean  ture-成功，false-失败
     */
    public function createShop($data = array()) {
        if (empty($data['mid'])){
            $this->error = '缺少商家ID';
            return false;
        }

        /* 添加数据 */
        if ($this->create($data)) {
            if($this->add()){
                return true;
            }else{
                $this->error = '创建失败！请联系我们！';
                return false;
            }
        } else {
            $this->error = $this->getError(); //错误详情见自动验证注释
            return false;
        }
    }


    /**
     * 获取店铺配置
     * @param $id int 店铺ID
     * @return mixed
     */
    public function getConfigs($id){
        $configs_info =  M('shop_configs')->where(array('shop_id'=>$id))->find();
        if($configs_info['is_sale'] && $configs_info['sale_configs']){
            //反序列化满减设置数据
            $configs_info['sale_configs_arr'] = @unserialize($configs_info['sale_configs']);
        }
        return $configs_info;
    }

    /**
     * 获取店铺的角色及权限列表
     * @param $id int 店铺ID
     * @return mixed
     */
    public function getRoles($id){
        $configs_info =  M('shop_roles')->where(array('shop_id'=>$id))->find();
        if($configs_info['role_grant'] && $configs_info['role_grant'] != 'all'){
            //反序列化权限设置数据
            $configs_info['role_grant_arr'] = @unserialize($configs_info['role_grant']);
        }
        return $configs_info;
    }

    /**
     * 获取店铺店铺列表
     *
     * @param array $condition 查询条件
     * @param bool $isPage 是否分页
     * @param $order string 排序
     * @param int $page 当前页码
     * @param int $size 每页数量
     * @return bool|mixed 返回店铺列表
     */
    public function getShopList($condition=array(),$isPage = false,$order="id DESC",$page=1,$size=10){
        $where = array('status'=>1);//初始化查询条件数组
        $mid = $condition['mid'];
        if($isPage == false && $mid == 0){ //当查询不分页时必须指定店铺ID
            $this->error = "缺少店铺ID";
            return false;
        }

        if(intval($mid) > 0)
            $where['mid'] = intval($mid);

        if($condition){
            foreach($condition as $key=>$val){
                $where[$key] = $val;
            }
        }
        //总数
        $count = $this->where($where)->count();
        //列表
        if($isPage){
            $list = $this->where($where)->page("{$page},{$size}")->order($order)->select();
        }else{
            $list = $this->where($where)->select();
        }
        return array('list'=>$list,'count'=>$count);
    }

    /**
     * 获取店铺详情
     * @param $shopId integer 店铺ID
     * @param $isAll boolean 是否查询所有数据 默认只查询基础数据
     *
     * @return array 返回店铺详情
     */
    public function getShopInfo($shopId,$isAll = false){
        $where = array('m.id'=>$shopId);//初始化查询条件数组

        if($isAll == false){
            return $this->alias('m')->where($where)->find();
        }else{
            return $this->alias('m')
                ->join('LEFT JOIN __MERCHANT_SHOPINFO__ ms ON ms.sid = m.id')
                ->where($where)
                ->find();
        }
    }


    /**
     * 更新店铺数据
     *
     * @param int $id 店铺ID
     * @param array $data 需要更新的数组
     */
    public function updateShopInfo($id,$data){
        if (empty($id) || empty($data)) {
            $this->error = '参数错误';
            return false;
        }
        //过滤更新的字段
        $base_fields = array('cid','sname','province','city','district','address');
        $info_fields = array('level','balance','points','usetime','person_ident','is_trade',
            'is_offline','logo','sdesc','qq','tel','service_tel','stype','stype_values');
        $base_data = array();
        $info_data = array();
        //数据分组
        foreach($data as $key=>$val){
            if(in_array($key,$base_fields)){
                $base_data[$key] = $val;
            }elseif(in_array($key,$info_fields)){
                $info_data[$key] = $val;
            }
        }
        //更新数据
        if (empty($base_data) && empty($info_data)) {
            $this->error = '字段数据错误';
            return false;
        }
        $res1 = $res2 = true;
        if($base_data){
            $res1 = $this->where(array('id'=>$id))->save($base_data);
        }
        if($info_data){
            $res2 = M('merchant_shopinfo')->where(array('sid'=>$id))->save($info_data);
        }
        if($res1 && $res2){
            return true;
        }else{
            $this->error = '数据更新失败';
            return false;
        }
    }

    //直接获取店铺ID
    public function sid() {
        $shopinfo = session("shop_auth");
        return  $shopinfo ? $shopinfo["id"] : 0;
    }
}
