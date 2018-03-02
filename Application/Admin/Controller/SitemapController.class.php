<?php
namespace Admin\Controller;
class SitemapController extends AdminController {
    public function index( $ismenu=1 ){
        $map['status']= array('gt', -1);
        $list = D('Sitemap')->where($map)->order('sort')->select(); 
        $list = list_to_tree($list, 'id', 'pid', '_', 0); 
        $this->assign('tree', $list); 
        $this->meta_title = '网址导航管理'; 
        $this->display();
    }
    
    public function tree($tree = null){ 
        $this->assign('tree', $tree);
        $this->display('tree');
    }
    
    
    /* 编辑导航*/
    public function edit($id = null, $pid = 0){
        $Sitemap = D('Sitemap'); 
        if(IS_POST){ //提交表单 
            if(false !== $Sitemap->update()){ 
                $url=U('index'); 
                $this->success('编辑成功！', $url); 
            } else {
                $error = $Sitemap->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cate = '';
            if($pid){
                /* 获取上级分类信息 */
                $cate = $Sitemap->find( $pid ); 
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级导航不存在或被禁用！');
                }
            } 
            /* 获取分类信息 */
            $info = $id ? $Sitemap->find($id) : ''; 
            $this->assign('info', $info); 
            $this->assign('category',   $cate); 
            $this->meta_title = '编辑网址导航';
            $this->display();
        }
    }
    
    /* 新增导航 */
    public function add($pid = 0,$ismenu=1){
        $Sitemap = D('Sitemap'); 
        if(IS_POST){ //提交表单
                if(false !== $Sitemap->update()){ 
                    $url=U('index'); 
                    $this->success('新增成功！', $url);
                } else {
                    $error = $Sitemap->getError();
                    $this->error(empty($error) ? '未知错误！' : $error);
                }
        } else {
            $cate = array();
            if($pid){
                /* 获取上级分类信息 */
                $cate = $Sitemap->find( $pid ); 
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级导航不存在或被禁用！');
                } 
            } 
            /* 获取分类信息 */
            $this->assign('info',       null);
            $this->assign('pid',       $pid);
            $this->assign('category', $cate); 
            $this->meta_title = '新增网址导航';
            $this->display('edit');
        }
    }
}
