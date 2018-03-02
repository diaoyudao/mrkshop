<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | author 烟消云散 <1010422715@qq.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台订单控制器
  * @author 烟消云散 <1010422715@qq.com>
 */
class OrdercompleteController extends AdminController {

    /**
     * 订单管理
     * author 烟消云散 <1010422715@qq.com>
     */
    public function index(){
        /* 查询条件初始化 */
	
       $map  = array('status' => 3);
       $orderid = I("orderid");
        if($orderid){
            $map['orderid'] = array('like','%'.$orderid.'%');
        }
        if ( isset($_GET['time-start']) ) {
            $map['create_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['create_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
        if (isset($_GET['uid'])) {
            $map['uid'] = I('uid');
        }
       $list = $this->lists('order', $map,'id DESC');

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        $this->meta_title = '订单管理';
        $this->display();
    }

    /**
     * 新增订单
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function add(){
        if(IS_POST){
            $Config = D('order');
            $data = $Config->create();
            if($data){
                if($Config->add()){
                    S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->assign('info',null);
            $this->display('edit');
        }
    }

    /**
     * 编辑订单
     * @author 烟消云散 <1010422715@qq.com>
     */
    public function edit($id = 0){

        if(IS_POST){
            $Form = D('order');
        $user=session('user_auth');
          $uid=$user['uid'];
            if($_POST["id"]){ 
				$id=$_POST["id"];
			  $Form->create();
            $Form->status="3";
			$Form->assistant = $uid;
			$Form->update_time = NOW_TIME;
			 $Form->backinfo=$_POST["backinfo"];
           $result=$Form->where("id='$id'")->save();
                if($result){
                    //记录行为
                    action_log('update_order', 'order', $data['id'], UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败55'.$id);
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('order')->find($id);
$detail= M('order')->where("id='$id'")->select();
$list=M('shoplist')->where("orderid='$id'")->select();

            $documentViewModel = D("Document"); 
            foreach($list as $k => $v){
                $product_detail = array();
                $product_detail = S('p_'.$v['goodid']); 
                if(!$product_detail){
                $product_detail = $documentViewModel->find($v['goodid']); 
                if( $product_detail["cover_id"] || $product_detail['pics'] ){
                    $arr=array();
                    if( $product_detail['pics'] ){
                    $arr=explode(",",$product_detail['pics']);
                    }
                    $arr[]=$product_detail["cover_id"];
                    $arrmap["id"]=array("in",$arr); 
                    $product_detail["pics_img"] = M("picture")->where($arrmap)->getField("id,path"); ;
                }
                S('p_'.$v['goodid'], $product_detail);
                } 
                $list[$k] = array_merge($product_detail,$v); 
            }


            //对组合商品重新设计数组结构
            $goodsgroup = array();
            foreach($list as $okey=>$ovalue){
                if($ovalue['groupid']>0){
                    $goodsgroup[$ovalue['groupid']][] = $ovalue;
                }
                if($ovalue['groupid']>0 && $ovalue['price'] == 0){
                   unset($list[$okey]);
                }
            }
            foreach($list as $pkey=>$pvalue){
                if($pvalue['groupid']>0){
                    $list[$pkey]['goodsinfo'] = $goodsgroup[$pvalue['groupid']];
                }

            }
            //对组合商品重新设计数组结构END
            //<td align="center">{$vo.id|get_good_price}</td>

            $addressid=M('order')->where("id='$id'")->getField("addressid");
            $address=M('transport')->where("id='$addressid'")->select();
             $this->assign('alist', $address);

            if(false === $info){
                $this->error('获取订单信息错误');
            }
$this->assign('list', $list);
            $this->assign('detail', $detail);
			 $this->assign('info', $info);
			 $this->assign('a', $orderid);
            $this->meta_title = '编辑订单';
            $this->display();
        }
    }

  
   /**
     * 删除订单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del(){
       if(IS_POST){
             $ids = I('post.id');
            $order = M("order");
			
            if(is_array($ids)){
                             foreach($ids as $id){
		
                             $order->where("id='$id'")->delete();
							 $shop=M("shoplist");
							 $shop->where("orderid='$id'")->delete(); 
                }
            }
           $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("order");
            $status = $db->where("id='$id'")->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        } 
    }


 public function complete($id = 0){
        if(IS_POST){
            $Form = D('shoplist');
        $user=session('user_auth');
         $uid=$user['uid'];
     if($_POST["id"]){ 
				$id=$_POST["id"];
				
             $Form->create();
			
            $Form->status="3";
            $result=$Form->where("id='$id'")->save();
	
//根据订单id获取购物清单,设置商品状态为已完成.，status=3
$del=M("shoplist")->where("orderid='$id'")->select();

foreach($del as $k=>$val)
	{
//获取购物清单数据表产品id，字段id
$byid=$val["id"];

$data['status']=3;
$shop=M("shoplist");
 $shop->where("id='$byid'")->save($data);
}
                if($result){
                    //记录行为
                    action_log('update_order', 'order', $data['id'], UID);
                    $this->success('确认收货成功', Cookie('__forward__'));
                } else {
                    $this->error('确认收货失败'.$id);
                }
            } 
			
			
       else {
                $this->error($Config->getError());
            }
 } 
		
		else {
         
            $this->meta_title = '订单发货';
            $this->display();
        }
}

}