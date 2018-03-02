<?php
namespace Admin\Controller;

/**
 * 后台子频道控制器
 * @author wangcheng <253490851@qq.com>
 */

class SubdomainController extends AdminController {
    
    
    public function _initialize() {
        $this->getMenu(2);
        parent::_initialize();
    }
    /**
     * 频道列表
     * @author wangcheng <253490851@qq.com>
     */ 
    public function _filter(){
        $map  = array('status' => array('gt', -1));
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

    }
    /**
     * 添加频道
     * @author wangcheng <253490851@qq.com>
     */
    public function _before_add(){
        if(IS_POST){
            //记录行为 
        } else { 
            $this->meta_title = '新增频道';
        }
    }
    
    /**
     * 添加频道
     * @author wangcheng <253490851@qq.com>
     */
    public function _after_insert(&$info){
        if(IS_POST){
            //记录行为--后期扩展
            action_log('update_subdomain', 'subdomain', $info['id'], UID);

            //当频道创建成功后，需要创建图片ID
            $domainid = $info['id'];
            $picurl = './Uploads/Picture/'.$info['id'];

            //判断文件夹是否存在，不存在则创建
            if(!file_exists($picurl)){
                mkdir( $picurl, 0775, true );
            }

        } else { 
             
        }
    }

    /**
     * 编辑频道
     * @author wangcheng <253490851@qq.com>
     */
    public function _after_edit( &$data ){
        if(IS_POST){
            //记录行为--后期扩展
            action_log('update_channel', 'channel', $data['id'], UID);
            clearcache('d_'.$data['id']);
        } else { 
            $this->meta_title = '编辑频道';

        }

    } 

    public function del(){
        $id = I('id');
        if(empty($id)){
            $this->error('参数错误!');
        }

        //判断该频道下面有没有分类，有分类则不能删除
        $child = M('Category')->where(array('domainid'=>$id))->field('id')->select();
        if(!empty($child)){
            $this->error('请先删除该频道下的分类');
        }
        //删除该分类信息
        $res = M('Subdomain')->delete($id);
        if($res !== false){
            //记录行为
            action_log('update_Subdomain', 'Subdomain', $cate_id, UID);
            $this->success('删除频道成功！');
        }else{
            $this->error('删除频道失败！');
        }
    }


}