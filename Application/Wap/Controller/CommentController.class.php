<?php

/**
 * 评论管理
 */

namespace Wap\Controller;

use Api\Api\OrderApi;

class CommentController extends HomeController {

    /**
     * 评价列表
     */
    public function index() {
        $orderapi = new OrderApi();
        $conditon['status'] = 3;
        $conditon['uid'] = is_login();
        $state_type = I('state_type');
        switch ($state_type) {
            case 'state_success': // 已完成
                $conditon["status"] = array('EGT', 3);
                break;
            case 'state_noeval': // 待评价
                $conditon["status"] = array('EGT', 3);
                $conditon['iscomment'] = 0;
                break;
            case 'state_eval': // 已评价
                $conditon["status"] = array('EGT', 3);
                $conditon['iscomment'] = 1;
                break;
            default:
                $state_type = 'all';
                break;
        }
        $conditon['orderid'] = I('orderid');
        $list = $orderapi->getOrderList($conditon);
        $i =0;
        foreach ($list as $key => $itmes) {
            foreach ($itmes['goodslist'] as $k => &$goods) {
                if ($goods['iscomment']) {
                    $comment = M("comment")->where(array('goodid' => $goods['goodid'], 'orderid' => $goods['orderid'], 'uid' => is_login()))->find();
                    
                    if ($comment['pics']) {
//                        if (strpos($comment['pics'], ',')) {
                        $aa = explode(',', $comment['pics']);
                        $bb = array();
                        foreach ($aa as $key => $val) {
                            $pics = M('picture')->where(array('id' => $val))->getField('path');
                            $bb[] = __PICURL__ . $pics;
                            $comment['picss'] = $bb;
                        }
//                        $list['comment'] =$comment;
//                        } else {
//                            $pics = M('picture')->where(array('id' => $comment['pics']))->getField('path');
//                            $comment['picss'] = array(__PICURL__ . $pics);
//                        }
//                        echo $key;
                        $list[$i]['goodslist'][$k]['comment'] = $comment;
                    }
                }
            }
            $i++;
        }

//        dump($list);exit;
        $ordersum = M("order")->where(array('uid' => $conditon['uid'], 'status' => 3))->count();
        $noevalnum = M('order')->where(array('uid' => $conditon['uid'], 'status' => 3, 'iscomment' => 0))->count();  // 待评价
        $evalnum = $ordersum - $noevalnum;  // 已评价
        $this->assign('ordernum', $ordersum);
        $this->assign('noevalnum', $noevalnum);
        $this->assign('evalnum', $evalnum);
        $this->assign('orderlist', $list); // 赋值数据集
        $this->assign('state_type', $state_type);
        $this->meta_title = '评价管理';
        $this->display("comments.index");
    }

    /**
     * 评价页面
     */
    public function evaluation() {
        if (is_login()) {
            $goodsid = I('get.goodsid');
            $orderid = I('get.orderid');
            $shoplist = M('shoplist');
            $map['orderid'] = $orderid;
            $map['goodid'] = $goodsid;
            $goodsinfo = $shoplist->where($map)->find();
//            dump($goodsinfo);
            //没有评论过
            if ($goodsinfo["iscomment"] == 0) {
                $this->assign('goodsinfo', $goodsinfo);
                $title = get_good_name($goodsinfo["goodid"]);
                $this->meta_title = '评价商品_' . $title;
                $this->display();
            } else {
                $this->error('商品评价过');
            }
        } else {
            $this->redirect(U('Member/login'));
        }
//        $this->display();
    }

    public function add() {
        if (!IS_POST) {
            $this->error('商品评价失败', Cookie('__forward__'));
        }
        $post = I('post.');
        $ordergoods = M('shoplist')->where(array('orderid' => $post['orderid'], 'goodid' => $post['goodid']))->find();
        if (empty($ordergoods)) {
            $this->error('商品不存在，或参数错误');
        }
        $orderapi = new OrderApi();
        $comments_data = $post;
        $comments_data['content'] = $post['content'] ? : "这个产品不错，下次还在你家商城购买。";
        $comments_data['category_id'] = $ordergoods['category_id'];
        $comments_data['domainid'] = $ordergoods['domainid'];
        $comments_data['score'] = $post['goodscore'];
        $comments_data['status'] = 1;
        $comments_data['brandid'] = 0;
        $comments_data['create_time'] = NOW_TIME;
        $comments_data['uid'] = is_login();
        $comments_data['pics'] =implode(",", $post['pic_id']);

        $res = $orderapi->addOrderComments($comments_data);
        if ($res) {
            $result = $orderapi->changeOrderStatus('order_comment', $ordergoods);
            if (isset($result['success'])) {
                $this->success($result['msg'], U('comment/index'));
            } else {
                $this->error($result['msg'], U('comment/index'));
            }
        }
    }

    public function addajax() {

        if (IS_POST) {
            $postlist = I('post.');
            $shopid = $postlist['shopid'];
            $goodid = $postlist['goodid'];
            $orderid = $postlist['orderid'];

            $Member = D("member");
            $uid = $Member->uid();
            if ($uid <= 0) {
                $data = array();
                $data["status"] = 0;
                $data["msg"] = "商品评价失败";
                $this->ajaxReturn($data);
            } else {
                $comment = D("comment");
                $comment->create();
                $comment->uid = $uid; // 修改数据对象的status属性
                $comment->create_time = NOW_TIME; // 增加time属性
                $comment->status = 1; // 增加time属性

                $brandid = 0;
                $goodsinfo = M('document')->where("id = $goodid")->field('brandid,category_id,domainid')->select();
                //===================>
                if (!empty($goodsinfo)) {
                    $comment->brandid = $goodsinfo[0]['brandid']; // brandid
                    $comment->category_id = $goodsinfo[0]['category_id']; // category id
                    //2015/5/8 18:19 sheshanhu添加评论时把频道ID和商品的品牌ID保存在评论表中
                    $comment->domainid = $goodsinfo[0]['domainid']; //cookie("current_domainid"); // domanid
                }

                $id = $comment->add();
                if ($id > 0) {
                    //M('shoplist')->where("id='$shopid'")->setField("iscomment","2");
                    //document表中评论数加1 只有评价通过审核后才能加数量
                    //M('document')->where("id='$goodid'")->setInc('comment');

                    $data = array();
                    $data["status"] = 1;
                    $data["msg"] = "商品评价提交成功，请等待审核。";
                    $this->ajaxReturn($data);
                }
            }
        } else {
            $data = array();
            $data["status"] = 0;
            $data["msg"] = "商品评价失败";
            $this->ajaxReturn($data);
        }
    }

    /*
     * 商品详细页面ajax 对应的评论
     * @author wangcheng
     * @date 2015-05-08
     * return array
     */

    public function ajaxlists($gid = 0, $callback = "") {
        $goodid = $gid ? $gid : I('gid');
        $map = array();
//        $map['status'] = 1;
        $map["ifcheck"] = 1;
        $map["goodid"] = $goodid;
        $commentview = D("CommentView");
        $_POST['r'] = 10;
        $lists = $this->_ajaxlists($commentview, $map, 'id DESC', array(), 'id', 'getcomments');

        if ($lists) {
            foreach ($lists as $k => $v) {
                $comment_detail = array(); // S( 'pl_'.$v['id'] );
                if (!$comment_detail) {
                    $comment_detail = $commentview->find($v['id']);
                    if ($comment_detail['pics']) {
                        if (strpos($comment_detail['pics'], ',')) {
                            $aa = explode(',', $comment_detail['pics']);
                            $bb = array();
                            foreach ($aa as $key => $val) {
                                $pics = M('picture')->where(array('id' => $val))->getField('path');
                                $bb[] = __PICURL__ . $pics;
                                $comment_detail['picss'] = $bb;
                            }
                        } else {
                            $pics = M('picture')->where(array('id' => $comment_detail['pics']))->getField('path');
                            $comment_detail['picss'] = array(__PICURL__ . $pics);
                        }
                    }
                    if ($comment_detail["face"]) {
                        $attinfo = M("picture")->find($comment_detail["face"]);
                        $comment_detail["face"] = $attinfo["path"];
                    }
//                      S('pl_'.$v['id'], $comment_detail);
                }
                $lists[$k] = $comment_detail;
            }
        }

//        if ($lists) {
//            foreach ($lists as $k => $v) {
//                $comment_detail = S('pl_' . $v['id']);
//                if (!$comment_detail) {
//                    $comment_detail = $commentview->find($v['id']);
//                    if ($comment_detail["face"]) {
//                        $attinfo = M("picture")->find($comment_detail["face"]);
//                        $comment_detail["face"] = $attinfo["path"];
//                    }
//                    S('pl_' . $v['id'], $comment_detail);
//                }
//                $lists[$k] = $comment_detail;
//            }
//        }
//        dump($lists);exit;
        $this->assign('commentlist', $lists);
        $this->assign('gid', $goodid);
        $re = $this->fetch('Goods/ajaxlist');
        $back = $callback ? $callback : I("callback", "");
        if ($back == 'html') {
            return $re;
        } else {
            $this->success($re);
        }
    }

    public function lists($gid = '') {
        $goodid = $gid ? $gid : I('gid');
        $is_ajax = I('ajax') ? 1 : 0;
        $map = array();
//        $map['status'] = 1;
        $map["ifcheck"] = 1;
        $map["goodid"] = $goodid;
        $order = 'id DESC';
        $page = I('page', 1, 'int');
        $pageSize = 10;
        $start = ($page - 1) * $pageSize;
        $commentview = M('comment');
        $limit = $pageSize;
        $lists = M('comment')->where($map)->order($order)->limit("{$start},{$limit}")->select();
        
        if ($lists) {
            foreach ($lists as $k => $v) {
                $comment_detail = array(); // S( 'pl_'.$v['id'] );
                if (!$comment_detail) {
                    $comment_detail = $commentview->find($v['id']);
                    if ($comment_detail['pics']) {
                        if (strpos($comment_detail['pics'], ',')) {
                            $aa = explode(',', $comment_detail['pics']);
                            $bb = array();
                            foreach ($aa as $key => $val) {
                                $pics = M('picture')->where(array('id' => $val))->getField('path');
                                $bb[] = __PICURL__ . $pics;
                                $comment_detail['picss'] = $bb;
                            }
                        } else {
                            $pics = M('picture')->where(array('id' => $comment_detail['pics']))->getField('path');
                            $comment_detail['picss'] = array(__PICURL__ . $pics);
                        }
                    }
                    if ($comment_detail["face"]) {
                        $attinfo = M("picture")->find($comment_detail["face"]);
                        $comment_detail["face"] = $attinfo["path"];
                    }
//                      S('pl_'.$v['id'], $comment_detail);
                }
                $lists[$k] = $comment_detail;
            }
        }
        if ($is_ajax) {
            $this->assign("lists", $lists);
            $re = $this->fetch('Comment/comments.ajax');
            exit($re);
        }
        $totalRows = M('comment')->where($map)->count('*');
        $totalPages = (int) ceil($totalRows / $pageSize);
        $this->assign('totalPages', $totalPages);

        $this->assign('lists', $lists);
        $this->assign('goodsid', $goodid);
        $this->display('comments');
    }

    /**
     * 上传图片
     */
    public function update_pic() {
        $base64 = I('post.pic', '');
        if (!empty($base64)) {
            $dir = './Uploads/Picture/' . date("Y", time()) . "/" . date("m", time()) . "/" . date("d", time()) . "/";
            $resultDir = $this->mkdirp($dir);
            if (!$resultDir) {
                $this->ajaxReturn(array('result' => 'fail', 'info' => '头像目录生成失败，请重新制作！'));
            }
            $filenameUn = $this->unique_name($dir) . 'p.jpg';
            $filename = $dir . $filenameUn;

            $imgs = base64_decode($base64);
            $reslut = file_put_contents($filename, $imgs);
            /* 记录图片信息 */
            if (!$reslut) {
                $this->ajaxReturn(array('result' => 'fail', 'info' => '上传失败，请重新上传！'));
            }

            $data = array();
            $data['types'] = 5;
            $data['path'] = date("Y", time()) . "/" . date("m", time()) . "/" . date("d", time()) . "/" . $filenameUn;
            $data['status'] = 1;
            $data['create_time'] = time();
            $pictureModel = M('picture');
            $res = $pictureModel->add($data);
            $pic_id = $pictureModel->getLastInsID();
            $this->ajaxReturn(array('result' => 'success', 'info' => '上传成功！', 'pic_id' => $pic_id));
        } else {
            $this->ajaxReturn(array('result' => 'fail', 'info' => '上传失败，请重新上传！'));
        }
    }

    /**
     * 删除图片
     */
    public function remove_pic() {
        $id = I('post.id', 0, 'int');
        $where = array();
        $where['id'] = $id;
        $where['status'] = 1;
        $result = M('picture')->where($where)->find();
        if (!$result) {
            $this->ajaxReturn(array('result' => 'error', 'info' => '参数错误！'));
        }

        $pic_dir = $_SERVER['DOCUMENT_ROOT'] . __PICURL__ . $result['path'];
        $result = unlink($pic_dir);
        $re = M('picture')->where($where)->delete();
        if (!$result) {
            $this->ajaxReturn(array('result' => 'error', 'info' => '删除失败！'));
        }
        $this->ajaxReturn(array('result' => 'success', 'info' => '删除成功！'));
    }

    /**
     * @desc 创建目录
     * @param unknown $dst
     * @return boolean
     */
    protected function mkdirp($dir) {
        if (file_exists($dir)) {
            if (is_dir($dir)) {
                return true;
            } else {
                return false;
            }
        }
        mkdir($dir, 0775, true);
        if (file_exists($dir) and is_dir($dir)) {
            return true;
        }
        return false;
    }

    /**
     * @desc 生成指定目录不重名的文件名
     * @access  public
     * @param   string      $dir        要检查是否有同名文件的目录
     * @return  string      文件名
     */
    protected function unique_name($dir) {
        $filename = '';
        while (empty($filename)) {
            $str = '';
            for ($i = 0; $i < 9; $i++) {
                $str .= mt_rand(0, 9);
            }

            $filename = time() . $str;
            if (file_exists($dir . $filename . '.jpg') || file_exists($dir . $filename . '.gif') || file_exists($dir . $filename . '.png')) {
                $filename = '';
            }
        }
        return $filename;
    }

}
