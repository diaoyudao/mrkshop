<?php

namespace Common\Model;

/**
 * 活动基础模型
 */
class GroupsModel extends CommonModel{

	/* 自动验证 */
	protected $_validate = array(
		/* 验证手机号码 */
        array('group_name', 'require','活动名不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('group_name', '2,40', '活动名长度不能超过40个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('group_name', 'checkGroupsName', '活动名已经存在', self::MUST_VALIDATE, 'callback', self::MODEL_BOTH),
		array('group_type', 'require','活动类型不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('shop_id', 'require','关联店铺不能空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	/* 自动完成 */
	protected $_auto = array(
		array("create_time", NOW_TIME, self::MODEL_INSERT),
        array("update_time", NOW_TIME, self::MODEL_UPDATE),
		array("status", 1, self::MODEL_INSERT),
	);

    /**
     * 检查名称是否重复
     * @param string $group_name 商品名称
     * @return bool|mixed
     */
    protected function checkGroupsName($group_name = '')
    {
        $name = $group_name ? $group_name :trim(I('post.group_name'));
        $shop_id = intval(I('post.shop_id', 0));
        $id = intval(I('post.id', 0));
        $map = array(
            'group_name' => $name,
            'shop_id' => $shop_id,
            'status' => 1
        );
        if($id > 0){
            $map['id'] = array('neq',$id);
        }

        if ($this->where($map)->find()){
            return false;
        }else{
            return true;
        }
    }


    /**
     * 创建活动
     * @param array $data 提交的数据
     * @return bool
     */
    public function createGroups($data = array()){
        /* 添加数据 */
        if ($this->create($data)) {
            $id = $res = $this->add();
            if($res){
                return $id;//返回创建成功的ID
            }else{
                $this->error = '创建失败';
                return false;
            }
        } else {
            $this->error = $this->getError(); //错误详情见自动验证注释
            return false;
        }
    }

    /**
     * 获取详情
     * @param $id int 活动的ID
     * @return bool|mixed
     */
    public function getInfo($id){
        $res = $this->where(array('id'=>$id))->find();
        if(empty($res) || $res['status'] == 0){
            $this->error = "订单不存在或已被删除";
            return false;
        }

        //根据活动类型获取不同的活动详情
        switch($res['group_type']){
            case "1"://团购
            case "2"://抢购
                return $this->getGroupGoods($id);
                break;
            case "3"://砍价
                return $this->getBargainGoods($id);
                break;
        }
    }

    /**
     * 获取活动列表
     * @param array $map 筛选条件
     * @param bool $isLimit 是否分页
     * @param string $order 排序
     * @param int $page 页码
     * @param int $size 每页数量
     * @return array
     */
    public function getList($map = array(),$isLimit = true,$order = "id desc",$page=1,$size=10){
        $map['g.status'] = 1;
        //总数
        $count = $this->alias('g')->where($map)->count();
        //是否分页
        if($isLimit){
            $list = $this->alias('g')->where($map)->page("{$page},{$size}")->order($order)->select();
        }else{
            $list = $this->alias('g')->where($map)->select();
        }
        return array('list'=>$list,'count'=>$count);
    }

    /**
     * 保存团购或抢购商品数据
     * @param array $data 提交的数据
     * @return bool
     */
    public function saveGroupGoods($data = array()){
        if($data['id']){
            $res = M('groups_goods')->save($data);
        }else{
            $res = M('groups_goods')->add($data);
        }

        if(false === $res){
            $this->error = "数据保存失败";
            return false;
        }else{
            return $res;
        }
    }

    /**
     * 获取团购|抢购活动商品数据
     * @param $id int 活动ID
     * @return mixed
     */
    protected function getGroupGoods($id){
        $groupGoodsModel = M('groups_goods');
        $fields = 'g.group_type,g.group_name,g.group_des,g.group_status,g.start_time,g.end_time,g.create_time,
        g.update_time,gg.*,p.url,p.path';

        $groupInfo = $groupGoodsModel->alias('gg')->field($fields)
            ->join('LEFT JOIN __GROUPS__ g ON gg.group_id=g.id')
            ->join('LEFT JOIN __PICTURE__ p ON p.id=gg.goods_img')
            ->where(array('gg.group_id'=>$id,'gg.status'=>1))
            ->select();

        foreach($groupInfo as $key=>$val){
            //处理下图片
            if($val['goods_img'] > 0){
                $groupInfo[$key]['goods_img_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
            }
        }
        return $groupInfo;
    }

    /**
     * 保存砍价商品数据
     * @param array $data 提交的数据
     * @return bool
     */
    public function saveBargainGoods($data = array()){
        if($data['id']){
            $res = M('bargain_goods')->save($data);
        }else{
            $res = M('bargain_goods')->add($data);
        }

        if(false === $res){
            $this->error = "数据保存失败";
            return false;
        }else{
            return $res;
        }
    }

    /**
     * 获取砍价活动商品数据
     * @param int $id 活动ID
     * @return mixed
     */
    protected function getBargainGoods($id){
        $bargainGoodsModel = M('bargain_goods');
        $fields = 'g.group_type,g.group_name,g.group_des,g.group_status,g.start_time,g.end_time,g.create_time,
        g.update_time,bg.*,p.url,p.path';
        $bargainInfo = $bargainGoodsModel->alias('bg')->field($fields)
            ->join('LEFT JOIN __GROUPS__ g ON bg.group_id=g.id')
            ->join('LEFT JOIN __PICTURE__ p ON p.id=bg.goods_img')
            ->where(array('bg.group_id'=>$id,'bg.status'=>1))
            ->select();

        foreach($bargainInfo as $key=>$val){
            //处理下图片
            if($val['goods_img'] > 0){
                $bargainInfo[$key]['goods_img_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
            }
        }

        return $bargainInfo;
    }

    /**
     * 添加会员砍价申请
     * @param array $data 提交的数据
     * @return mixed
     */
    public function addBargainUsers($data = array()){
        return M('bargain_users')->add($data);
    }

    /**
     * 会员申请砍价活动列表
     * @param int $id 砍价商品活动ID（bargain_goods表ID）
     * @return mixed
     */
    public function getBargainUsersList($id){
        $bargainUsersModel = M('bargain_users');
        $fields = 'bu.bargain_status,bu.bargain_price,bu.help_num,bu.create_time,bu.last_time,
        g.group_type,g.group_name,g.group_des,g.group_status,g.start_time,g.end_time,g.create_time,g.update_time,
        gg.goods_img,gg.goods_name,gg.goods_id,
        m.username,mi.member_level_id,p.url,p.path';
        $list = $bargainUsersModel->alias('bu')->field($fields)
            ->join('LEFT JOIN __GROUPS__ g ON bu.group_id=g.id')
            ->join('LEFT JOIN __BARGAIN_GOODS__ gg ON bu.bargain_id=gg.id')
            ->join('LEFT JOIN __UCENTER_MEMBER__ m m.uid=bu.user_id')
            ->join('LEFT JOIN __MEMBER__ mi mi.uid=m.id')
            ->join('LEFT JOIN __PICTURE__ p ON p.id=bb.goods_img')
            ->where(array('bu.bargain_id'=>$id,'bu.status'=>1))
            ->select();

        foreach($list as $key=>$val){
            //处理下图片
            if($val['goods_img'] > 0){
                $list[$key]['goods_img_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
            }
        }
        return $list;
    }

    /**
     * 获取会员申请砍价活动详情
     * @param $id
     * @return mixed
     */
    public function getBargainUsersInfo($id){
        $bargainUsersModel = M('bargain_users');
        $fields = 'bu.bargain_status,bu.bargain_price,bu.help_num,bu.create_time,bu.last_time,
        g.group_type,g.group_name,g.group_des,g.group_status,g.start_time,g.end_time,g.create_time,g.update_time,
        gg.goods_img,gg.goods_name,gg.goods_id,
        m.username,mi.member_level_id,p.url,p.path';
        $info = $bargainUsersModel->alias('bu')->field($fields)
            ->join('LEFT JOIN __GROUPS__ g ON bu.group_id=g.id')
            ->join('LEFT JOIN __BARGAIN_GOODS__ gg ON bu.bargain_id=gg.id')
            ->join('LEFT JOIN __UCENTER_MEMBER__ m m.uid=bu.user_id')
            ->join('LEFT JOIN __MEMBER__ mi mi.uid=m.id')
            ->join('LEFT JOIN __PICTURE__ p ON p.id=bb.goods_img')
            ->where(array('bu.id'=>$id,'bu.status'=>1))
            ->find();
        if(empty($info)){
            $this->error = "数据不存在";
            return false;
        }

        //处理下图片
        if($info['goods_img'] > 0){
            $info['goods_img_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
        }

        return $info;
    }


    /**
     * 更新会员砍价商品数据（用于更新砍价价格和助力会员数量）
     * @param array $data 提交的数据
     * @return bool
     */
    public function updateBargainUsers($data = array()){
        $res = M('bargain_users')->save($data);
        if(false === $res){
            $this->error = "数据更新失败";
            return false;
        }else{
            return true;
        }
    }

    /**
     * 参与砍价记录数据
     * @param array $data 提交的数据
     * @return mixed
     */
    public function addBargainLogs($data = array()){
        return M('bargain_logs')->add($data);
    }

    /**
     * 获取用户参加商品砍价记录表
     * @param $id int 活动商品ID
     * @return mixed
     */
    public function getBargainLogs($id){
        $bargainLogsModel = M('bargain_logs');

        $list = $bargainLogsModel->alias('bl')
            ->field('bl.bargain_price,bl.create_time,m.username,m.face,p.url,p.path')
            ->join('LEFT JOIN __ucenter_member__ m m.uid=bl.user_id')
            ->join('LEFT JOIN __PICTURE__ p ON p.id=m.face')
            ->where(array('bl.bu_id'=>$id,'bl.status'=>1))
            ->select();

        foreach($list as $key=>$val){
            //处理下头像
            if($val['face'] > 0){
                $list[$key]['face_url'] = !empty($val['url']) ? $val['url'] : __PICURL__ . $val['path'];
            }
        }
        return $list;
    }

}
