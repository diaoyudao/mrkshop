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

class BankController extends AdminController {

    /**
     * 频道列表
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        $pid = I('get.pid', 0);
        /* 获取频道列表 */
        $map  = array('status' => array('gt', -1), 'pid'=>$pid);
        $list = M('Bank')->where($map)->order('weight asc,id asc')->select();

        $this->assign('list', $list);
        $this->assign('pid', $pid);
        $this->meta_title = '银行账号管理';
        $this->display();
    }

    /**
     * 添加频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function add(){
        if(IS_POST){
            $Bank = D('Bank');
            $data = $Bank->create();
            if($data){
                $id = $Bank->add();
                if($id){
                    $this->success('新增成功', U('index'));
                    //记录行为
                    action_log('update_Bank', 'bank', $id, UID);
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Bank->getError());
            }
        } else {
            $pid = I('get.pid', 0);
            //获取父导航
            if(!empty($pid)){
                $parent = M('Bank')->where(array('id'=>$pid))->field('title')->find();
                $this->assign('parent', $parent);
            }
            //获取频道
            $banklist=M('Subdomain')->where("status >=1")->field("id,name")->select(); 
            $this->assign("banklist",$banklist);
            
            $this->assign('pid', $pid);
            $this->assign('info',null);
            $this->meta_title = '新增银行账号';
            $this->display('edit');
        }
    }

    /**
     * 编辑频道
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $Bank = D('Bank');
            $data = $Bank->create();
            if($data){
                if($Bank->save()){
                    //记录行为
                    action_log('update_bank', 'bank', $data['id'], UID);

                    //清空缓存
                    S('bank_'.$data['id'],NULL); 

                    $this->success('编辑成功', U('index'));
                } else {
                    $this->error('编辑失败');
                }

            } else {
                $this->error($Bank->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Bank')->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }

            $pid = I('get.pid', 0);
            //获取父导航
            if(!empty($pid)){
            	$parent = M('Bank')->where(array('id'=>$pid))->field('title')->find();
            	$this->assign('parent', $parent);
            }
            //获取频道
            $banklist=M('Subdomain')->where("status >=1")->field("id,name")->select(); 
            $this->assign("banklist",$banklist);
            
            $this->assign('pid', $pid);
            $this->assign('info', $info);
            $this->meta_title = '编辑银行账号';
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
        if(M('Bank')->where($map)->delete()){
            //记录行为
            action_log('update_bank', 'bank', $id, UID);
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
            $list = M('Bank')->where($map)->field('id,title')->order('weight asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '导航排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('Bank')->where(array('id'=>$value))->setField('weight', $key+1);
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