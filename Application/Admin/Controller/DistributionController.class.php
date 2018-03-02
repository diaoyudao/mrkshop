<?php

// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台频道控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class DistributionController extends AdminController {

    /**
     * 频道列表
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index() {
        $pid = I('get.pid', 0);
        /* 获取频道列表 */
        $map = array('status' => array('gt', -1), 'pid' => $pid);
        $list = M('Distribution')->where($map)->order('weight asc,id asc')->select();

        $this->assign('list', $list);
        $this->assign('pid', $pid);
        $this->meta_title = '配送方式管理';
        $this->display();
    }

    /**
     * 添加频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function add() {
        if (IS_POST) {
            $Distribution = D('Distribution');
            $data = $Distribution->create();
            if ($data) {
                $id = $Distribution->add();
                if ($id) {
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_Distribution', 'distribution', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Distribution->getError());
            }
        } else {
            $pid = I('get.pid', 0);
            //获取父导航
            if (!empty($pid)) {
                $parent = M('Distribution')->where(array('id' => $pid))->field('title')->find();
                $this->assign('parent', $parent);
            }
            //获取频道
            $distributionlist = M('Subdomain')->where("status >=1")->field("id,name")->select();
            $this->assign("distributionlist", $distributionlist);

            $this->assign('pid', $pid);
            $this->assign('info', null);
            $this->meta_title = '新增配送方式';
            $this->display('edit');
        }
    }

    /**
     * 编辑频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function edit($id = 0) {
        if (IS_POST) {
            $Distribution = D('Distribution');
            $data = $Distribution->create();
            if ($data) {
                if ($Distribution->save()) {
                    //记录行为
                    action_log('update_distribution', 'distribution', $data['id'], UID);

                    //清空缓存
                    S('distribution_' . $data['id'], NULL);

                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }
            } else {
                $this->error($Distribution->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Distribution')->find($id);

            if (false === $info) {
                $this->error('获取配置信息错误');
            }

            $pid = I('get.pid', 0);
            //获取父导航
            if (!empty($pid)) {
                $parent = M('Distribution')->where(array('id' => $pid))->field('title')->find();
                $this->assign('parent', $parent);
            }
            //获取频道
            $distributionlist = M('Subdomain')->where("status >=1")->field("id,name")->select();
            $this->assign("distributionlist", $distributionlist);

            $this->assign('pid', $pid);
            $this->assign('info', $info);
            $this->meta_title = '编辑配送方式';
            $this->display();
        }
    }

    /**
     * 删除频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del() {
        $id = array_unique((array) I('id', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id));
        if (M('Distribution')->where($map)->delete()) {
            //记录行为
            action_log('update_distribution', 'distribution', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 导航排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort() {
        if (IS_GET) {
            $ids = I('get.ids');
            $pid = I('get.pid');

            //获取排序的数据
            $map = array('status' => array('gt', -1));
            if (!empty($ids)) {
                $map['id'] = array('in', $ids);
            } else {
                if ($pid !== '') {
                    $map['pid'] = $pid;
                }
            }
            $list = M('Distribution')->where($map)->field('id,title')->order('weight asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '导航排序';
            $this->display();
        } elseif (IS_POST) {
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key => $value) {
                $res = M('Distribution')->where(array('id' => $value))->setField('weight', $key + 1);
            }
            if ($res !== false) {
                $this->success('排序成功！');
            } else {
                $this->error('排序失败！');
            }
        } else {
            $this->error('非法请求！');
        }
    }

    /**
     * 读取地址页面
     */
    public function shipping() {

        // 保存数据
        if (IS_POST) {
            $post = I('post.');
//            dump($post);
//            exit;
            $shippingModel = M("shipping_extend");
            $shipping_id = $post['shipping_id'];

//            dump($post['special']['kd']);
//            exit;
            foreach ($post['special']['kd'] as $key => $value) {
                if (empty($value['start']) || $value['postage'] == '') {
                    $this->error("配送模板中的，首重，首费不能为空！");
                } else if ($value['plus'] <= 0 || $value['postageplus'] == '') {
                    $this->error("配送模板中的，续重，续费不能为空！");
                }
            }

            if (is_numeric($shipping_id)) {
                //编辑时，删除所有附加表信息
                $shippingModel->where(array('shipping_id' => $shipping_id))->delete();
            } else {
                $this->error('参数错误');
            }
            //保存默认运费
            $bool_i = 0;
            if (is_array($post['default']['kd'])) {
                $a = $post['default']['kd'];
                $trans_list['area_id'] = '';
                $trans_list['area_name'] = '全国';
                $trans_list['snum'] = $a['start'];
                $trans_list['sprice'] = $a['postage'];
                $trans_list['xnum'] = $a['plus'];
                $trans_list['xprice'] = $a['postageplus'];
                $trans_list['is_default'] = 1;
                $trans_list['shipping_id'] = $shipping_id;
                $trans_list['shipping_title'] = $post['title'];
                $trans_list['top_area_id'] = '';
                $bool_i = $shippingModel->add($trans_list);
            }

            //保存自定义地区的运费设置
            $trans_list = array();
            $areas = $post['areas']['kd'];
            $special = $post['special']['kd'];
            if (is_array($areas) && is_array($special)) {
                //$key需要加1，因为快递默认运费占了第一个下标
                foreach ($special as $key => $value) {
                    if (empty($areas[$key])) {
                        continue;
                    }
                    $trans_list['area_id'] = '';
                    $trans_list['area_name'] = $areas[$key];
                    $trans_list['snum'] = $value['start'];
                    $trans_list['sprice'] = $value['postage'];
                    $trans_list['xnum'] = $value['plus'];
                    $trans_list['xprice'] = $value['postageplus'];
                    $trans_list['is_default'] = 2;
                    $trans_list['shipping_id'] = $shipping_id;
                    $trans_list['shipping_title'] = $post['title'];
                    $trans_list['top_area_id'] = '';
                    $bool_i += $shippingModel->add($trans_list);
                }
            }

            if ($bool_i) {
                $this->success($post['title'] . '配送区域设置成功');
            } else {
                $this->success($post['title'] . '配送区域设置失败');
            }
        } else {
            $shipping_id = I('id');
            $shipping = M("distribution")->where(array('id' => $shipping_id))->find();
            $this->assign('shipping', $shipping);
            $shipping_extend = M("shipping_extend")->where(array('shipping_id' => $shipping_id, 'is_default' => 2))->select();
            $index = 1;
            $cur_area = array();
            $tmp_session_area = array();
            foreach ($shipping_extend as $key => $value) {
                $area_name_list = explode(',', $value['area_name']);
                foreach ($area_name_list as $area) {
                    $cur_area['n' . $index][$area] = $area;
                    $tmp_session_area[$area] = $area;
                }
                $index++;
            }
            // 清空session
            session('cur_session_area', null);
            session('tmp_session_area', null);

            session('tmp_session_area', $tmp_session_area);
            session('cur_session_area', $cur_area);

            $shipping_extend_default = M("shipping_extend")->where(array('shipping_id' => $shipping_id, 'is_default' => 1))->find();
            $this->assign('shipping_extend', $shipping_extend);
            $this->assign('default', $shipping_extend_default);

            $this->meta_title = "配送模板";
            $this->display();
        }
    }

    /**
     * 获取配送区域
     */
    public function getshipping_area() {
        $file = C('DOMAIN') . '/Public/' . MODULE_NAME . '/js/city.min.js';
        $json = file_get_contents($file); //根据taobao ip
        $area_list = json_decode($json, TRUE);
        $this->assign("area_list", $area_list['citylist']);
        // 获取当前已选择的地区
        $curIndex = I('curIndex');
        $cur_area = session('cur_session_area');
        $cur_area_list = $cur_area[$curIndex];
        $this->assign('cur_area_list', $cur_area_list);
        // 已经选择的地区
        $check_area = session('tmp_session_area');
        foreach ($cur_area_list as $value) {
            unset($check_area[$value]);
        }
        $this->assign("check_area", $check_area);
        $re = $this->fetch('Distribution/shipping_area');
        $this->success($re);
    }

    // 临时保存选择的地区
    public function temp_savearea() {
        $areas_string = I('area');
        $curIndex = I('curIndex');
        $arr = explode(',', $areas_string);
        $arr_area = array();
        foreach ($arr as $value) {
            $arr_area[$value] = $value;
        }

        // 当前选择的区域
        $cur_area[$curIndex] = $arr_area;
        session('cur_session_area', $cur_area);

        // 已经选择的区域
        $session_area = session('tmp_session_area') ? : array();
        $tmp_arr = array_merge($session_area, $arr_area);
        $tmp_arr = array_filter($tmp_arr);
        session('tmp_session_area', $tmp_arr);
        $this->success("选择地址成功");
    }

    /**
     * 测试用
     */
    public function test() {

        $file = C('DOMAIN') . '/Public/' . MODULE_NAME . '/js/city.min.js';
        $json = file_get_contents($file); //根据taobao ip
        $jsonarr = json_decode($json, TRUE);
        dump($jsonarr);
        echo 11111;
    }

}
