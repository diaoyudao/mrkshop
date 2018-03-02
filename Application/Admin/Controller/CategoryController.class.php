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
 * 后台分类管理控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class CategoryController extends AdminController {

    
    public function _initialize() {
        $this->getMenu(2);
        parent::_initialize();
    }
    
    /**
     * 分类管理列表
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index( $ismenu=2 ){
        $tree = D('Category')->getTree(0,'id,name,domainid,title,sort,pid,allow_publish,brandid,ismenu,status',array('ismenu'=>$ismenu));
        $this->assign('tree', $tree);
        C('_SYS_GET_CATEGORY_TREE_', true); //标记系统获取分类树模板
        $this->meta_title = '分类管理';
         $this->assign('ismenu',$ismenu);
        $this->display();
    }

    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function tree($tree = null){
        C('_SYS_GET_CATEGORY_TREE_') || $this->_empty();
        $this->assign('tree', $tree);
        $this->display('tree');
    }

    /* 编辑分类 */
    public function edit($id = null, $pid = 0){
        $Category = D('Category');

        if(IS_POST){ //提交表单

            if(false !== $Category->update()){
                $ismenu=I("ismenu");
                $url=U('index');
                if($ismenu){
                    $url=U('index',array("ismenu"=>$ismenu));
                }
                clearcache('c_'.$id);
                $this->success('编辑成功！', $url); 
            } else {
                $error = $Category->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $cate = '';
            if($pid){
                /* 获取上级分类信息 */
                $cate = $Category->info($pid, 'id,name,title,ismenu,brandid,domainid,status,faqcategoryid');
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级分类不存在或被禁用！');
                }
            }

            /* 获取分类信息 */
            $info = $id ? $Category->info($id) : '';
             //获取所有频道
            $channellist=M('Subdomain')->where("status >=1")->getField("id,name"); 
            $this->assign("channellist",$channellist);
            //获取所有品牌
            $channelbrandlist=M('SubdomainBrand')->where("status >=1")->getField("id,name"); 
            $this->assign("channelbrandlist",$channelbrandlist); 
            $this->assign('info', $info);
            $this->assign('ismenu', $info["ismenu"]); 
            $this->assign('category',   $cate);

            $html = $this->get_faq_category($info['domainid'],$info['faqcategoryid']);
            $this->assign('faqcategoryoption', $html);

            $this->meta_title = '编辑分类';
            $this->display();
        }
    }


/***************************************************************
     *created date:2015/4/20 14:07
     *created author:sheshanhu
     *content:无限极分类菜单调用
     *modefiy person:
     *modefiy date:
     *note:
     ****************************************************************/
    public function menulist($map = array(),$categoryid = 0) {
        $field = 'id,name,pid,title';
        $map["display"] = 1;
        $map["status"] = 1;
        $ismenu = $map['ismenu'];
        if ($categoryid > 0) {
            $domainid = $map["domainid"];
            $mapstr = "(id=$categoryid or pid=$categoryid) and ismenu=$ismenu and display=1 and domainid=$domainid ";
            $categoryq = D('Category')->field($field)->order('sort asc')->where($mapstr)->select();
            //echo D('Category')->getLastSql();
            $catelist = $this->unlimitedForLevel($categoryq);
        } else {
            $categoryq = D('Category')->field($field)->order('sort asc')->where($map)->select();
            $catelist = $this->unlimitedForLevel($categoryq);
        }
        return $catelist;
    }
    /***************************************************************
     *created date:2015/4/20 14:07
     *created author:sheshanhu
     *content:对分类进行等级拆分
     *modefiy person:
     *modefiy date:
     *note:
     ****************************************************************/
    public function unlimitedForLevel($cate, $name = 'child', $pid = 0) {
        $arr = array();
        foreach ($cate as $key => $v) {
            //判断，如果$v['pid'] == $pid的则压入数组Child
            if ($v['pid'] == $pid) {
                //递归执行
                $v[$name] = self::unlimitedForLevel($cate, $name, $v['id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }


    public function get_faq_category($domainid,$faqcategoryid=0){
         static $html;
             //获取问答分类
            $map = array();
            $map['ismenu'] = 5;
            $map["domainid"] = $domainid;
            $faqcate = $this->menulist($map);

            foreach ($faqcate as $k=> $opt){
                $html .= ($faqcategoryid != $opt['id'])? '<option value="' . $opt['id'] . '">' . $opt['title'] . '</option>' : '<option value="' .  $opt['id'] . '" selected="selected">' .  $opt['title'] . '</option>';
                if(!empty($opt['child'])){
                    foreach ($opt['child'] as $ck=> $copt){
                                 $html .= ($faqcategoryid != $copt['id'])? '<option value="' . $copt['id'] . '">&nbsp;&nbsp;--' . $copt['title'] . '</option>' : '<option value="' .  $copt['id'] . '" selected="selected">' .  $copt['title'] . '</option>';
                    }
                }
            }
            return $html;
    }

    /* 新增分类 */
    public function add($pid = 0,$ismenu=1){
        $Category = D('Category');

        if(IS_POST){ //提交表单
                if(false !== $Category->update()){
                    $ismenu=I("ismenu");
                    $url=U('index');
                    if($ismenu){
                        $url=U('index',array("ismenu"=>$ismenu));
                    }
                    $this->success('新增成功！', $url);
                } else {
                    $error = $Category->getError();
                    $this->error(empty($error) ? '未知错误！' : $error);
                }
        } else {
            $cate = array();
            if($pid){
                /* 获取上级分类信息 */
                $cate = $Category->info($pid, 'id,name,title,ismenu,brandid,domainid,status,faqcategoryid');
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级分类不存在或被禁用！');
                }
                //$ismenu = $cate["ismenu"]; 
            }
            //获取所有频道
            $channellist=M('Subdomain')->where("status >=1")->getField("id,name"); 
            $this->assign("channellist",$channellist);
            //获取所有品牌
            $channelbrandlist=M('SubdomainBrand')->where("status >=1")->getField("id,name"); 
            $this->assign("channelbrandlist",$channelbrandlist); 
            
            /* 获取分类信息 */
            $this->assign('info',       null);
            $this->assign('pid',       $pid);
            $this->assign('category', $cate); 
            $this->assign('ismenu', $ismenu);


            $html = $this->get_faq_category($cate['domainid'],0);
            $this->assign('faqcategoryoption', $html);

            $this->meta_title = '新增分类';
            $this->display('edit');
        }
    }

    /**
     * 删除一个分类
     * @author huajie <banhuajie@163.com>
     */
    public function remove(){
        $cate_id = I('id');
        if(empty($cate_id)){
            $this->error('参数错误!');
        }

        //判断该分类下有没有子分类，有则不允许删除
        $child = M('Category')->where(array('pid'=>$cate_id))->field('id')->select();
        if(!empty($child)){
            $this->error('请先删除该分类下的子分类');
        }

        //判断该分类下有没有内容
        $document_list = M('Document')->where(array('category_id'=>$cate_id))->field('id')->select();
        if(!empty($document_list)){
            $this->error('请先删除该分类下的文章（包含回收站）');
        }

        //删除该分类信息
        $res = M('Category')->delete($cate_id);
        if($res !== false){
            //记录行为
            action_log('update_category', 'category', $cate_id, UID);
            clearcache("c_".$cate_id);  
            $this->success('删除分类成功！');
        }else{
            $this->error('删除分类失败！');
        }
    }

    /**
     * 操作分类初始化
     * @param string $type
     * @author huajie <banhuajie@163.com>
     */
    public function operate($type = 'move'){
        //检查操作参数
        if(strcmp($type, 'move') == 0){
            $operate = '移动';
        }elseif(strcmp($type, 'merge') == 0){
            $operate = '合并';
        }else{
            $this->error('参数错误！');
        }
        $from = intval(I('get.from'));
        empty($from) && $this->error('参数错误！');

        //获取分类
        $map = array('status'=>1, 'id'=>array('neq', $from));
        $list = M('Category')->where($map)->field('id,pid,title')->select();


        //移动分类时增加移至根分类
        if(strcmp($type, 'move') == 0){
        	//不允许移动至其子孙分类
        	$list = tree_to_list(list_to_tree($list));

        	$pid = M('Category')->getFieldById($from, 'pid');
        	$pid && array_unshift($list, array('id'=>0,'title'=>'根分类'));
        }

        $this->assign('type', $type);
        $this->assign('operate', $operate);
        $this->assign('from', $from);
        $this->assign('list', $list);
        $this->meta_title = $operate.'分类';
        $this->display();
    }

    /**
     * 移动分类
     * @author huajie <banhuajie@163.com>
     */
    public function move(){
        $to = I('post.to');
        $from = I('post.from');
        $res = M('Category')->where(array('id'=>$from))->setField('pid', $to);
        if($res !== false){
            $this->success('分类移动成功！', U('index'));
        }else{
            $this->error('分类移动失败！');
        }
    }

    /**
     * 合并分类
     * @author huajie <banhuajie@163.com>
     */
    public function merge(){
        $to = I('post.to');
        $from = I('post.from');
        $Model = M('Category');

        //检查分类绑定的模型
        $from_models = explode(',', $Model->getFieldById($from, 'model'));
        $to_models = explode(',', $Model->getFieldById($to, 'model'));
        foreach ($from_models as $value){
            if(!in_array($value, $to_models)){
                $this->error('请给目标分类绑定' . get_document_model($value, 'title') . '模型');
            }
        }

        //检查分类选择的文档类型
        $from_types = explode(',', $Model->getFieldById($from, 'type'));
        $to_types = explode(',', $Model->getFieldById($to, 'type'));
        foreach ($from_types as $value){
            if(!in_array($value, $to_types)){
                $types = C('DOCUMENT_MODEL_TYPE');
                $this->error('请给目标分类绑定文档类型：' . $types[$value]);
            }
        }

        //合并文档
        $res = M('Document')->where(array('category_id'=>$from))->setField('category_id', $to);

        if($res !== false){
            //删除被合并的分类
            $Model->delete($from);
            $this->success('合并分类成功！', U('index'));
        }else{
            $this->error('合并分类失败！');
        }
    }
    
    /**
     * 设置状态
     * @author wangcheng <253490851@qq.com>
    */
    function _before_setStatus(){
	$ids = I('request.ids');
	if( $ids ){
	    clearcache($ids,'c_');
	}
    }





    
}
