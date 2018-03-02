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

class PaymentController extends AdminController {

    /**
     * 频道列表
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        $pid = I('get.pid', 0);
        /* 获取频道列表 */
        $map  = array('status' => array('gt', -1), 'pid'=>$pid);
        $list = M('Payment')->where($map)->order('weight asc,id asc')->select();

        $this->assign('list', $list);
        $this->assign('pid', $pid);
        $this->meta_title = '支付方式管理';
        $this->display();
    }

    /**
     * 添加频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function add(){
        if(IS_POST){
            $Payment = D('Payment');
            $data = $Payment->create();
            if($data){
                $id = $Payment->add();
                if($id){
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_Payment', 'payment', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Payment->getError());
            }
        } else {
            $pid = I('get.pid', 0);
            //获取父导航
            if(!empty($pid)){
                $parent = M('Payment')->where(array('id'=>$pid))->field('title')->find();
                $this->assign('parent', $parent);
            }
            //获取频道
            $paymentlist=M('Subdomain')->where("status >=1")->field("id,name")->select(); 
            $this->assign("paymentlist",$paymentlist);
            
            $this->assign('pid', $pid);
            $this->assign('info',null);
            $this->meta_title = '新增支付方式';
            $this->display('edit');
        }
    }

    /**
     * 编辑频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $Payment = D('Payment');
            $data = $Payment->create();
            if($data){
                if($Payment->save()){
                    //记录行为
                    action_log('update_payment', 'payment', $data['id'], UID);

                    //清空缓存
                    S('payment_'.$data['id'],NULL); 

                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }

            } else {
                $this->error($Payment->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Payment')->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }

            $pid = I('get.pid', 0);
            //获取父导航
            if(!empty($pid)){
            	$parent = M('Payment')->where(array('id'=>$pid))->field('title')->find();
            	$this->assign('parent', $parent);
            }
            //获取频道
            $paymentlist=M('Subdomain')->where("status >=1")->field("id,name")->select(); 
            $this->assign("paymentlist",$paymentlist);
            
            $this->assign('pid', $pid);
            $this->assign('info', $info);
            $this->meta_title = '编辑支付方式';
            $this->display();
        }
    }

    /**
     * 删除频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Payment')->where($map)->delete()){
            //记录行为
            action_log('update_payment', 'payment', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 导航排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort(){
        if(IS_GET){
            $ids = I('get.ids');
            $pid = I('get.pid');

            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }else{
                if($pid !== ''){
                    $map['pid'] = $pid;
                }
            }
            $list = M('Payment')->where($map)->field('id,title')->order('weight asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '导航排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('Payment')->where(array('id'=>$value))->setField('weight', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！');
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
}