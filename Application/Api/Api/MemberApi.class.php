<?php

/**
 * 会员管理
 */

namespace Api\Api;

use Api\Api\Api;
use Common\Model\TreeModel;

class MemberApi extends Api {

    public $memberLevel = array();

    /**
     * 构造方法，实例化操作模型
     */
    protected function _init() {
        $this->model = new TreeModel();
        $this->memberLevel = C('MEMBER_LEVEL_LIST');
    }

    /**
     * 获取会员信息
     * @param type $condidion
     */
    public function getMemberInfo($condidion) {
        //$this->updateMemberLevel();
        $map['uid'] = $condidion['uid'];
        $member = M('member')->where($map)->find();
        $where['id'] = $condidion['uid'];
        $ucentermember = M('ucenter_member')->where($where)->find();
        $info = array_merge($member, $ucentermember);
        $levelinfo = $this->getMemberLevel($info['member_level_id']);
        $info['levelInfo'] = $levelinfo;
        return $info;
    }

    /**
     * 根据会员消费积分修改会员等级
     */
    public function updateMemberLevel() {
        $memberModel = M("member");
        // 获取会员积分
         $memberinfo = M('member')->where(array('uid' => is_login()))->find();
        $points = $memberinfo['points'];
        $member_type = $memberinfo['member_type'];
        // 查询等级表等级
        $where['status'] = 1;
        $where['score'] = array('elt', $points);
        $levelinfo = M('member_level')->where($where)->order("score desc")->find();
        if (3 != $member_type) {
            return FALSE;
        }
        // 更新会员等级
        if ($levelinfo) {
            $memberModel->where(array('uid' => is_login()))->save(array('member_level_id' => $levelinfo['id']));
            $memberinfo['member_level_id'] = $levelinfo['id'];
            session('memberinfo', $memberinfo);
        }
    }

    /**
     * 获取会员列表
     * @param array $condition  查询条件
     * @return array()
     */
    public function getMemberlist($condition) {

        $MemberViewModel = D("MemberView");
        $_listmodel = new \Web\Controller\HomeController();
        $memberlist = $_listmodel->_lists($MemberViewModel, $condition, 'id DESC', array());
        foreach ($memberlist as $key => $memberinfo) {
            $memberlist[$key]['levelInfo'] = $this->getMemberLevel($memberinfo['member_level_id']);
        }
//        $list = $this->lists($MemberViewModel, $condition);
        $membercount = M("member")->where(array('member_agent_id' => $condition['member_agent_id']))->count();
        $_data['count'] = $membercount;
        $_data['memberlist'] = $memberlist;
        return $_data;
    }

    /**
     * 获取会员等级
     * @param type $level_id 等级ID
     * @return type 会员等级信息
     */
    public function getMemberLevel($level_id) {
        if (empty($level_id)) {
            return array();
        }
        $levelInfo = M('member_level')->where(array('id' => $level_id))->find();
        $levelInfo['level_n'] = $this->memberLevel[$levelInfo['level']];
        return $levelInfo;
    }

}
