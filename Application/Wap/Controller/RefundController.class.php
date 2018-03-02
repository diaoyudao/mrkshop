<?php

/**
 * 订单退货/退款
 */

namespace Wap\Controller;

use Wap\Controller\HomeController;
use Api\Api\OrderApi;

class RefundController extends HomeController {

    private $refund_resion = array(
        1 => array('id' => 1, 'resion' => '不想要了'),
        2 => array('id' => 2, 'resion' => '商品已损坏'),
        3 => array('id' => 3, 'resion' => '购买其他商品'),
        4 => array('id' => 4, 'resion' => '价格太贵'),
    );

    protected function _initialize() {
        parent::_initialize();
        $refund_resion = $this->refund_resion;
        $this->assign('refund_resion', $refund_resion);
    }

    public function index() {
        $state_type = I('state_type');
        switch ($state_type) {
            case 'state_success': // 已完成
                $conditon["refund_state"] = 3;
                break;
            case 'state_new': // 待处理
                $conditon["refund_state"] = array('LT', 3);
//                $conditon['iscomment'] = 0;
                break;
            case 'state_eval': // 已评价
                $conditon["refund_state"] = array('EGT', 3);
//                $conditon['iscomment'] = 1;
                break;
            default:
                $state_type = 'all';
                break;
        }
        $conditon['uid'] = is_login();
        $orderapi = new OrderApi();
        $bases = array(1);
        $is_ajax = I('ajax') ? 1 : 0;
        if ($is_ajax) {
            $list = $orderapi->getRefundOrderList($conditon, ' refund_id desc', $bases);
            $this->assign("orderlist", $list);
            $re = $this->fetch('Order/index.ajax');
            exit($re);
        }
        $result = $orderapi->getRefundOrderList($conditon);
        $this->assign('state_type', $state_type);
        $ordersum = M("order_refund")->where(array('uid' => $conditon['uid']))->count();    //所有
        $evalnum = M('order_refund')->where(array('uid' => $conditon['uid'], 'refund_state' => 3,))->count();  // 已完成
        $noevalnum = $ordersum - $evalnum;  // 处理中
        $this->assign('ordersum', $ordersum);
        $this->assign('noevalnum', $noevalnum);
        $this->assign('evalnum', $evalnum);
        $this->assign('orderlist', $result); // 赋值数据集
        $this->meta_title = "售后管理";
        if (isset($result['error'])) {
            $this->error($result['msg'], Cookie('__forward__'));
        }
        $this->display();
    }

    public function add() {
        if (!IS_POST) {
            $this->error('非法访问');
        }
        $post = I('post.');
        
        if (empty($post['goods_num']) || empty($post['order_id'])) {
            $this->error('参数错误');
        }
        $orderapi = new OrderApi();
        $condition['id'] = $post['order_id'];
        $order_info = $orderapi->getOrderInfo($condition);
        if (empty($order_info)) {
            $this->error('订单不存在，或参数错误');
        }
        $_ordergoods = M('shoplist')->where(array('orderid' => $post['order_id'], 'goodid' => $post['goodid'],'id'=>$post['id']))->find();
        if (empty($_ordergoods)) {
            $this->error("订单商品信息不存在");
        }
        //处理上传的图片数据
        if(!empty($post['back_pic'])){
        	$picture = M('picture');
        	$pic_id = array();
        	foreach($post['back_pic'] as $val){
        		$pic_data = array();
        		
        		//移动图片到指定的目录
        		$img_url = "http://".$_SERVER['HTTP_HOST'].$val;
        		$filename = pathinfo($val, PATHINFO_BASENAME );
        		$_filename = str_replace('.//', './', "./".$val);
        		$dir_path = ".".__PICURL__ .date("Y/m/d");
        		$path = date("Y/m/d")."/".$filename;

        		if (make_dir($dir_path)){
        			if(!move_upload_file($_filename, $dir_path.'/'.$filename)){
        				\Think\Log::write('move_refund_pic: ' . var_export("移动售后图片失败", true), 'DEBUG', '', C('LOG_PATH') . 'log_refund.log');
        			}
        		}else{
        			\Think\Log::write('make_refund_file: ' . var_export("创建售后目录失败", true), 'DEBUG', '', C('LOG_PATH') . 'log_refund.log');
        		}
        		
        		$pic_data['types'] = 5;
        		$pic_data['path'] = $path;
        		$pic_data['status'] = 1;
        		$pic_data['create_time'] = NOW_TIME;
        		$pic_data['md5'] = md5_file($img_url);
        		$pic_data['sha1'] = sha1_file($img_url);
        		//插入图片数据
        		if($picture->create($pic_data) && ($pid = $picture->add())){
        			$pic_id[] = $pid;
        		}
        	}
        }
        
        //dump($pic_id);exit;
        // 退款信息
        $ordergoods['id'] = $_ordergoods['orderid'];
        $ordergoods['tag'] = $_ordergoods['tag'];
        $ordergoods['uid'] = $_ordergoods['uid'];
        $ordergoods['total'] = $_ordergoods['total'];
        $ordergoods['goods_id'] = $_ordergoods['goodid'];
        $ordergoods['order_goods_id'] = $_ordergoods['id'];
        $ordergoods['order_type'] = $_ordergoods['cart_type'];
        $ordergoods['goods_num'] = $post['goods_num'] > $_ordergoods['num'] ? $_ordergoods['num'] : intval($post['goods_num']);
        $ordergoods['pics'] = !empty($pic_id) ? join(',',$pic_id) : '';
        $ordergoods['reason_id'] = $post['resion_id'];
        $ordergoods['reason_info'] = $this->refund_resion[$post['resion_id']]['resion'];
        $ordergoods['buyer_message'] = $post['buyer_message'];
        $result = $orderapi->changeOrderStatus('order_refund', $ordergoods);
        if (isset($result['error'])) {
            $this->error($result['msg'], Cookie('__forward__'));
        } else {
            $this->success($result['msg'], U('Refund/index'));
        }
    }

    /**
     * 申请售后
     */
    public function apply_refund() {


        $requstdata = I('get.');
        if (empty($requstdata)) {
            $this->error('非法访问');
        }
        $orderapi = new OrderApi();
        $condition['id'] = $requstdata['orderid'];
        $orderdetail = $orderapi->getOrderDetail($condition);
        $this->assign('orderdetail', $orderdetail);
        if (isset($orderdetail['error'])) {
            $this->error($orderdetail['msg'], Cookie('__forward__'));
        } else {
//            $this->success($orderdetail['msg'], U('Refund/index'));
            $this->display();
        }
    }

    /**
     * 退款/退货详情
     */
    public function detail() {
        $refund_id = I('id');
        if (empty($refund_id)) {
            $this->error('非法访问');
        }
        $condition['refund_id'] = $refund_id;
        $orderapi = new OrderApi();
        $orderdetail = $orderapi->getRefundOrderDetail($condition);
        if ($orderdetail['pic_info']) {
            $orderdetail['picss'] = explode(',', $orderdetail['pic_info']);
        }

        $this->assign('orderdetail', $orderdetail);
        if (isset($orderdetail['error'])) {
            $this->error($orderdetail['msg'], Cookie('__forward__'));
        } else {
            $this->display('refund.detail');
        }
    }

    /**
     * 上传售后图片
     */
    public function update_pic(){
    	$base64 = $_POST['pic'];
    	
    	
    	if(!empty($base64))
    	{
    		vendor('class_image');
    		vendor('class_json');
    		
    		$json  = new \JSON;
    		$image = new \cls_image("#282e46");
    		$dir = "./".$image->images_dir . '/back_pic/' . date('Ym').'/';
    		$tmp_dir = ".".$image->images_dir . '/back_pic/';
    		//删除过期图片
    		//clear_expire_files($tmp_dir,2);
    		
    		if (!make_dir($dir))
    		{
    			$result = array('error' => 1, 'message' => '头像目录生成失败，请重新制作');
    			die($json->encode($result));
    		}
    		
    		$filename = $dir.$image->unique_name($dir).'p.jpg';
    		$s = base64_decode( $base64);
    		file_put_contents($filename, $s);
    		
    		$filename = str_replace('./', '', $filename);
    	
    		$result = array('error' => 0,'content'=>$filename);
    		die($json->encode($result));
    	}else{
    		$result = array('error' => 1, 'message' => '上传失败，请重新上传');
    		die($json->encode($result));
    	}
    }
    
    /**
     * 删除上传的图片
     */
    public function drop_pic(){
    	vendor('class_json');
    	$json  = new \JSON;
    	
    	$filename = './'.trim($_POST['filename']);
    	if(file_exists($filename)){
    		@unlink($filename);
    		$result = array('error' => 0);
    		die($json->encode($result));
    	}
    	$result = array('error' => 0,'message'=>'图片已删除或不存在！');
    	die($json->encode($result));
    }
}
