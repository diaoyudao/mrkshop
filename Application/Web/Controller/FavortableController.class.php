<?php

/**
 * 收藏管理
 */
namespace Web\Controller;
use Web\Controller\HomeController;
class FavortableController extends HomeController{
    
    protected function _initialize() {
        parent::_initialize();
    }

    public function index() {
        
    }
     /***************************************************************
      *created date:2015/5/6 13:49
      *content:收藏夹添加
      *modefiy person:
      *modefiy date:
    ****************************************************************/
    public function addfavortable() {
        $data = array();
        $postlist = I('post.');
        if(!IS_AJAX){
        	$data["status"] = 0;
        	$data["msg"] = "参数提交错误。";
        	$this->ajaxReturn($data);
        }
        //获取登录账号信息，如果没有登录则提示登录
        if(!is_login()){
        	$data["status"] = -1;
        	$data["msg"] = "请登录网站。";
        	$this->ajaxReturn($data);
        }
        
        $id = intval($postlist["id"]);//收藏ID
        $types = intval($postlist["types"]);//收藏类型
        $uid = D("Member")->uid();
        $fav = M("favortable");
        
        //判断该商品是否已收藏
        $exsit = $fav->where("goodid='{$id}' and uid='{$uid}'")->getField("id");
        
        if (isset($exsit)) {
        	$data["status"] = 1;
        	$data["msg"] = "已收藏";
        	//更新cookie中的值
        	$favor = D("favortable")->getfavor(1);
        	Cookie('favor'.$uid,$favor);
        	$this->ajaxReturn($data);
        } else {
        	$fav->goodid = $id;
        	$fav->uid = $uid;
        	$fav->create_time = NOW_TIME;
        	if($fav->add()){
        		//增加收藏数量
        		M("document")->where("id='{$id}'")->setInc("collectionnum");
        		//更新cookie中的值
        		$favor = D("favortable")->getfavor(1);
        		Cookie('favor'.$uid,$favor);
        		//获取收藏数量
        		$data['cnum'] = M("favortable")->where(array('goodid'=>$id))->count();
        		$data["status"] = 1;
        		$data["msg"] = "已收藏";
        	}else{
        		$data["status"] = 0;
        		$data["msg"] = "数据错误。";
        	}
        	$this->ajaxReturn($data);
        }
    }
        // 删除收藏商品
    public function delcollect() {
        if (is_login()) {
            $Transport = M("favortable"); // 实例化transport对象
            $Member = D("member");
            $uid = $Member->uid();
            $id = intval($_POST["id"]);
            $data = array();
            
            if ($Transport->where("uid='{$uid}' and goodid='{$id}'")->delete()){
            	//减少商品收藏数量
            	M("document")->where("id='{$id}'")->setDec("collectionnum");
            	//获取收藏数量
            	$data['cnum'] = M("favortable")->where(array('goodid'=>$id))->count();
              $data['msg'] = "删除成功";
              $data['status'] = 1;
              $this->ajaxreturn($data);
            }else{
              $data['msg'] = "删除失败";
              $data['status'] = 0;
              $this->ajaxreturn($data);
            }
        }else{
           Cookie('__forward__',$_SERVER['REQUEST_URI']);
           $this->redirect('Member/login');
        }
    }
}

