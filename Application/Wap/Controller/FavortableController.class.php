<?php

/**
 * 收藏管理
 */

namespace Wap\Controller;

use Wap\Controller\HomeController;

class FavortableController extends HomeController {

    protected function _initialize() {
        parent::_initialize();
    }

    public function index() {
        
    }

    /*     * *************************************************************
     * created date:2015/5/6 13:49 
     * content:收藏夹添加
     * modefiy person:
     * modefiy date:
     * ************************************************************** */

    public function addfavortable() {
        $data = array();
        $postlist = I('post.');
        if (IS_AJAX) {
            $id = $postlist["id"]; //收藏ID
            $data["id"] = $id;
            $types = $postlist["types"]; //收藏类型
            $data["types"] = $types;
            //获取登录账号信息，如果没有登录则提示登录
            if (is_login()) {
                $uid = D("Member")->uid();
                $data["uid"] = $uid;
                $fav = M("favortable");
                $exsit = $fav->where("goodid='$id' and uid='$uid'")->getField("id");
                if (isset($exsit)) {
                    $data["status"] = 1;
                    $data["msg"] = "已收藏";
                    //更新cookie中的值
                    $fav = D("favortable");
                    $favor = $fav->getfavor(1);
                    Cookie('favor' . $uid, $favor);
                    $this->ajaxReturn($data);
                } else {
                    $fav->goodid = $id;
                    $fav->uid = $uid;
                    $fav->create_time = time();
                    $fav->add();
                    $data["status"] = 1;
                    $data["msg"] = "已收藏";
                    $res = M("document")->where("id=" . $id)->setInc("collectionnum");
                    //更新cookie中的值
                    $fav = D("favortable");
                    $favor = $fav->getfavor(1);
                    Cookie('favor' . $uid, $favor);

                    $this->ajaxReturn($data);
                }
            } else {
                $data["status"] = -1;
                $data["msg"] = "请登录网站。";
                $this->ajaxReturn($data);
            }
        } else {
            $data["status"] = 0;
            $data["msg"] = "参数提交错误。";
            $this->ajaxReturn($data);
        }
    }

    /**
     * 批量收藏  
     */
    public function betachcollect() {

        $uid = is_login();
        if(empty($uid)){
            $this->error("请先登录才能收藏",U("Member/login"));
        }
        $sorts = I('post.sort');
        if(empty($sorts)){
            $this->error('参数错误！');
        }
        $sorts = explode(',', $sorts);
        $goods_list = M("shopcart")->field("goodid,num,parameters")->where(array('sort' => array('in', $sorts), 'uid' => $uid))->select();
        if ($goods_list) {
            foreach ($goods_list as $key => $goods) {
                $fav = array();
                $fav = M('favortable')->field('goodid,uid')->where(array('goodid' => $goods['goodid'], 'uid' => $uid))->find();
                if (empty($fav)) {
                    M('favortable')->add(array('goodid' => $goods['goodid'], 'uid' => $uid, 'create_time' => NOW_TIME, 'num' => $goods['num']));
                }
            }
            M("shopcart")->field("goodid,num,parameters")->where(array('sort' => array('in', $sorts), 'uid' => $uid))->delete();
        }

        $data["status"] = 1;
        $data["msg"] = "移至收藏成功！";
        $this->ajaxReturn($data);
    }

    // 删除收藏商品
    public function delcollect() {
        if (is_login()) {
            $Transport = M("favortable"); // 实例化transport对象
            $Member = D("member");
            $uid = $Member->uid();
            $id = $_POST["id"];

            if ($Transport->where("uid='$uid' and goodid='$id'")->delete()) {
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
            $this->redirect('Member/login');
        }
    }

}
