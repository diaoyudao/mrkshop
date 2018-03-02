<?php

namespace Admin\Controller;

use Admin\Model\AuthGroupModel;
use Think\Page;

/**
 * 限时抢购
 */
class PromotionxianshiController extends AdminController {

    /**
     * 限时抢购列表
     */
    public function index() {
        $this->meta_title = "限时促销活动";
       $list =  $this->lists('P_xianshi');
       int_to_string($list);
       $this->assign('list', $list);
        $this->display();
    }

    /**
     * 
     */
    public function add() {
        $xianshi = D('PromotionXianshi');
        $_POST['start_time'] = strtotime(I('start_time'));
        $_POST['end_time'] = strtotime(I('end_time'));
        if (IS_POST) { //提交表单
            if (false !== $xianshi->update()) {
                $this->success('操作成功！', U('index'));
            } else {
                $error = $xianshi->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {
            $xianshi_id = I('xianshi_id');
            if($xianshi_id){
            $info = M("PXianshi")->where(array('xianshi_id'=>$xianshi_id))->find();
            }
            $this->assign('info', $info ? $info : null);
            $this->meta_title = '新增限时促销活动';
            $this->display();
        }
    }
    
    
    
    public function del(){
        $ids    =   I('request.ids');
        $status =   I('request.status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        $Model = "PXianshi";
        $map['xianshi_id'] = array('in',$ids);
        switch ($status){
            case -1 :
                $this->delete($Model, $map, array('success'=>'删除成功','error'=>'删除失败'));
                break;
            case 0  :
                $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                break;
            case 1  :
                $this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    

    public function getgoods(){
        // 获取详细数据 
       $goods_id = I('goods_id');
       $data2 =  M("Document")->where(array('id'=>$goods_id))->find();
       $data1 =  M("DocumentProduct")->where(array('id'=>$goods_id))->find();
       $data = array_merge($data1,$data2);
        $this->assign('info', $data);
        $xianshi_id = I('xianshi_id');
        $xianshi_info = M("PXianshi")->where(array('xianshi_id'=>$xianshi_id))->find();
        $this->assign('xianshi_info', $xianshi_info);
        $img = __PICURL__. get_cover($data['cover_id'], 'path');
        $result['images'] = $img;
        $result['title'] = $data['title'];
        $result['cover_id'] = $data['cover_id'];
        $result['price'] = $data['price'];
        $result['stock'] = $data['stock'];
        $result['id'] = $data['id'];
        $result['discount'] = $xianshi_info['discount'];
        $result['xianshi_id'] = $xianshi_info['xianshi_id'];
        $result['domainid'] = $data['domainid'];
        $res['info'] = $this->get_html($result);
        $res['status'] = 0;
        $res['error'] = '';
        $this->ajaxReturn($res);
    }
     public function editgoods(){
        // 获取详细数据 
       $goods_id = I('goods_id');
       $data2 =  M("Document")->where(array('id'=>$goods_id))->find();
       $data1 =  M("DocumentProduct")->where(array('id'=>$goods_id))->find();
       $data = array_merge($data1,$data2);
        $xianshi_goods_id = I('xianshi_goods_id');
        $xianshi_info = M("PXianshiGoods")->where(array('xianshi_goods_id'=>$xianshi_goods_id))->find();
        $img = __PICURL__.  get_cover($data['cover_id'], 'path');
        $result['images'] = $img;
        $result['title'] = $data['title'];
        $result['cover_id'] = $data['cover_id'];
        $result['price'] = $data['price'];
        $result['stock'] = $data['stock'];
        $result['id'] = $data['id'];
        $result['discount'] = $xianshi_info['discount'];
        $result['xianshi_id'] = $xianshi_info['xianshi_id'];
        $result['xianshi_stock'] = $xianshi_info['xianshi_stock'];
        $result['xianshi_price'] = $xianshi_info['xianshi_price'];
        $result['xianshi_goods_id'] = $xianshi_info['xianshi_goods_id'];
        $result['domainid'] = $xianshi_info['domainid'];
        $res['info'] = $this->get_html($result);
        $res['status'] = 0;
        $res['error'] = '';
        $this->ajaxReturn($res);
    }
    
    public function savexianshigoods(){
        $model_pg = D("PXianshiGoods");
        $model_xianshi = D("PromotionXianshi");
        if(IS_POST){
            $xianshi_id = I('xianshi_id');
            $goods_id = I('goods_id');
            if(I('xianshi_price') <= 0 || I('xianshi_stock') <=0 ){
                $this->error("参数错误");
            }
            if(I('xianshi_price') > I('goods_org_price')){
                $this->error("促销价格不能大于销售价格");
            }
            if(I('xianshi_stock') > I('stock')){
                $this->error("促销数量不能大于商品库存");
            }
            $xianshi_info = $model_xianshi->info($xianshi_id);
            $_POST['xianshi_name'] = $xianshi_info['xianshi_name'];
            $_POST['xianshi_explain'] = $xianshi_info['xianshi_explain'];
            $_POST['xianshi_title'] = $xianshi_info['xianshi_title'];
            $_POST['discount'] = $xianshi_info['discount'];
            if(empty($xianshi_info)){
                $this->error('促销活动不存在');
            }
            if(!I('xianshi_goods_id')){
               $goods_info =  M('PXianshiGoods')->where(array('xianshi_id'=>$xianshi_id,'goods_id'=>$goods_id))->find();
                if($goods_info){
                    $this->error('该商品已经添加');
                } 
            }
            
            if (false !== $model_pg->update()) {
                $this->success(array("goods_id"=>$goods_id,'msg'=>'操作成功'));
            } else {
                $error = $model_pg->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        }
    }

        public function get_html($data){
      $html = ' <div class="image">
            <img style="width:80px; height: auto;" src="'.$data['images'].'" data-id="{$info.cover_id}"/>
        </div>
        <div class="tab-wrap">
            <form id="xianshi_goods" action="'.U('savexianshigoods').'" method="post" class="form-inline">
        <div class="form-group" >
            <div class="controls-row">
                <label class="item-label">商品名称:</label>'.$data['title'].'
                    <input type="hidden" name="goods_name" value="'.$data['title'].'" >
                    <input type="hidden" name="goods_image" value="'.$data['cover_id'].'" >
            </div>
        </div>
        <div class="form-group">
            <div class="controls-row">
                <label class="item-label">销售价格： </label>'.$data['price'].'
                <input type="hidden" name="goods_org_price" value="'.$data['price'].'" >
            </div>
        </div>
        <div class="form-group">
            <div class="controls-row">
                <label class="item-label">促销价格:<span class="check-tips"></span> </label>';
                $price = $data['xianshi_price']? $data['xianshi_price'] :($data['price']*$data['discount']);
     $html =$html. '<input type="text" name="xianshi_price" class="text input-2x" value="'.$price.'">
            </div>
            <div>默认促销价= 销售价 * 默认折扣 '.$data['discount'].'</div>
        </div>
        <div class="form-group">
            <div class="controls-row">
                <label class="item-label">促销数量:<span class="check-tips"></span> </label>';
     $stock = $data['xianshi_stock'] ? $data['xianshi_stock'] : $data['stock'];
     $html = $html.'<input type="text" name="xianshi_stock" class="text input-2x"  value="'.$stock.'">
                <input type="hidden" name="stock" class="text input-2x"  value="'.$data['stock'].'">
            </div>
            <div>当前商品库存为 '.$data['stock'].'</div>
        </div>
        <input type="hidden" name="goods_id" value="'.$data['id'].'">
        <input type="hidden" name="xianshi_id" value="'.$data['xianshi_id'].'">
        <input type="hidden" name="domainid" value="'.$data['domainid'].'">
        <input type="hidden" name="xianshi_goods_id" value="'.$data['xianshi_goods_id'].'">
        <button type="button" id="save_submit" class="btn submit-btn ajax-post" target-form="form-horizontal">添 加</button>
                </form>
        </div>';
        
        return $html;
        
    }

    /**
     * 添加限时折扣商品
     */
    public function add_goods(){
        $this->meta_title = '新增促销活动商品';
        $xianshi_id = I("xianshi_id");
        $this->assign('xianshi_id',$xianshi_id);
        $xianshi_goods = $goods_info =  M('PXianshiGoods')->where(array('xianshi_id'=>$xianshi_id))->select();
        $this->assign('xianshi_goods',$xianshi_goods);
        $this->display();
    }
    
    /**
     * 删除限时抢购商品
     */
    public function delxinshigoods(){
        $xianshi_goods_id = I("xianshi_goods_id");
        $bool = M('PXianshiGoods')->where(array('xianshi_goods_id'=>$xianshi_goods_id))->delete();
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

        public function searchgoods(){
        //$domainid=I("pgdomainid");
        $keywords=I("keywords");
        $map = array();// && $domainid
//        if($keywords){ 
            $map["status"] = 1;
            $map["model_id"] = 5;//商品模型
            //$map["domainid"]=$domainid;
            $map["title"]=array("like",'%'.$keywords.'%');
            //$uniongood=M("document")->where($map)->field("id,title,price")->limit(0,20)->order("id desc")->select(); 
            $xianshi_id = I('xianshi_id');
            
            $xianshi_goods_ids =  M('PXianshiGoods')->where(array('xianshi_id'=>$xianshi_id))->field('goods_id')->select();
           
            $temp_ids = array();
            foreach ($xianshi_goods_ids as $key => $value) {
                $temp_ids[] = $value['goods_id'];
            }
            $xianshi_goods_ids = implode(',', $temp_ids);
            $Model = new \Think\Model();
            if($xianshi_goods_ids){
                $sql = "select d.id,d.title,d.price,dp.marketprice from __PREFIX__document d,__PREFIX__document_product dp where d.id=dp.id and d.status=1 and d.model_id=5 and d.id NOT IN (".$xianshi_goods_ids.") and d.title like '%".$keywords."%';";
//                $sql = "select d.id,d.title,d.price,dp.marketprice from __PREFIX__document d,__PREFIX__document_product dp where d.id=dp.id and d.status=1 and d.model_id=5 and dp.product_type = 2  and d.id NOT IN (".$xianshi_goods_ids.") and d.title like '%".$keywords."%';";
            }  else {
                $sql = "select d.id,d.title,d.price,dp.marketprice from __PREFIX__document d,__PREFIX__document_product dp where d.id=dp.id and d.status=1 and d.model_id=5 and d.title like '%".$keywords."%';";
            }
            
            $uniongood = $Model->query($sql);


            $this->success($uniongood);
//        }
        $this->error(array());
    }

}
