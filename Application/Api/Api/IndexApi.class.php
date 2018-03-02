<?php

/**
 * 首页管理
 */

namespace Api\Api;

use Api\Api\Api;
use Api\Api\GoodsApi;
use Common\Model\TreeModel;

class IndexApi extends Api {

    private $goodsapi = '';

    /**
     * 构造方法，实例化操作模型
     */
    protected function _init() {
        $this->model = new TreeModel();
        $this->goodsapi = new GoodsApi();
    }

    /**
     * shoye
     */
    public function home_pc() {
        $xianshi = $this->xianshigoods();
        $data['xianshi_goods'] = $xianshi['xianshigoods'];
        $data['xianshi_active'] = $xianshi['active'];
        $data['web_code'] = $this->web_code();
        $data['new_goods'] = $this->new_goods();

        /*         * 首页统计代码实现* */
        if (1 == C('IP_TONGJI')) {
            $id = "index";
            $record = IpLookup("", 1, $id);
        }
        return $data;
    }

    /**
     * 首页模块
     */
    public function web_code() {
        // 模块信息
//        $webconfig = D("webConfig");
    		$temp_code = array();
        $domainids = M("Subdomain")->where(array('show' => 1))->getField('id', true);
        if(!empty($domainids)){
        	$map['web_id'] = array("IN", $domainids);
        	$web_code = M("WebCode")->where($map)->order("web_id")->select();
       
        	if($web_code){
        		foreach ($web_code as $key => $info) {
        			$temp_code[$info['web_id']][$info['var_name']]['web_id'] = $info['web_id'];
        			$temp_code[$info['web_id']][$info['var_name']]['code_id'] = $info['code_id'];
        			$temp_code[$info['web_id']][$info['var_name']]['var_name'] = $info['var_name'];
        			$temp_code[$info['web_id']][$info['var_name']]['show_name'] = $info['show_name'];
        			$temp_code[$info['web_id']][$info['var_name']]['code_info'] = unserialize($info['code_info']); // $webconfig->get_array($info['code_info'],$info['code_type']);
        			 
        			if ($info['var_name'] == 'mid_goods') {
        				//                dump($temp_code[$info['web_id']][$info['var_name']]['code_info']['goods_list']);exit;
        				foreach ($temp_code[$info['web_id']][$info['var_name']]['code_info']['goods_list'] as $key => &$goods) {
        					$_temp = M('document')->field("id,price,domainid,cover_id,title")->where(array('id' => $goods['id']))->find();
        					$_temp['marketprice'] = M('document_product')->where(array('id' => $goods['id']))->getField('marketprice');
        					$_temp['org_price'] = $_temp['price'];
        					$_temp['member_level_price'] = M("document_product")->where(array('id' => $goods['id']))->getField('member_level_price');
        					if ($_temp['id']) {
        						$onlinegoods = $this->goodsapi->calcGoodsSpecPrice($_temp);
        						$goods['cover_id'] = $_temp['cover_id'];
        						$goods['title'] = $_temp['title'];
        						$goods['price'] = $onlinegoods['price'];
        						$goods['show_price'] = $onlinegoods['member_price'];
        						$goods['marketprice'] = $onlinegoods['marketprice'];
        					} else {
        						unset($temp_code[$info['web_id']][$info['var_name']]['code_info']['goods_list'][$key]);
        					}
        				}
        			}
        			if ($info['var_name'] == 'hot_goods') {
        				foreach ($temp_code[$info['web_id']][$info['var_name']]['code_info']['goods_list'] as $key => &$goods) {
        					$_temp = M('document')->field("id,price,domainid,cover_id,title")->where(array('id' => $goods['id']))->find();
        					$_temp['marketprice'] = M('document_product')->where(array('id' => $goods['id']))->getField('marketprice');
        					$_temp['org_price'] = $_temp['price'];
        					$_temp['member_level_price'] = M("document_product")->where(array('id' => $goods['id']))->getField('member_level_price');
        					if ($_temp['id']) {
        						$onlinegoods = $this->goodsapi->calcGoodsSpecPrice($_temp);
        						$goods['cover_id'] = $_temp['cover_id'];
        						$goods['title'] = $_temp['title'];
        						$goods['price'] = $onlinegoods['price'];
        						$goods['show_price'] = $onlinegoods['member_price'];
        						$goods['marketprice'] = $onlinegoods['marketprice'];
        					} else {
        						unset($temp_code[$info['web_id']][$info['var_name']]['code_info']['goods_list'][$key]);
        					}
        				}
        			}
        		}
        	}
        }
        
        return $temp_code;
    }

    /**
     * 限时商品
     */
    public function xianshigoods() {
        $where['status'] = 1;
        $where['end_time'] = array('gt', NOW_TIME);
        $xianshi = M("p_xianshi")->where($where)->limit(1)->find();
        $condition['xianshi_id'] = $xianshi['xianshi_id'];
        $xianshigoods = M("PXianshiGoods")->where($condition)->order("xianshi_goods_id")->select();
        foreach ($xianshigoods as $key => $value) {
            $xianshigoods[$key]['goods_image'] = M('document')->where(array('id' => $value['goods_id']))->getField('cover_id');
            $xianshigoods[$key]['goods_name'] = M('document')->where(array('id' => $value['goods_id']))->getField('title');
        }
        $data['active'] = $xianshi;
        $data['xianshigoods'] = $xianshigoods;
        return $data;
    }

    /**
     * wap首页
     */
    public function home_wap() {
        /*         * 首页统计代码实现* */
        if (1 == C('IP_TONGJI')) {
            $id = "index";
            $record = IpLookup("", 1, $id);
        }

        $data['newgoodslist'] = $this->new_goods();
        $data['hotgoodslist'] = $this->hot_goods();
        return $data;
    }

    /**
     * 新品推荐商品
     */
    public function new_goods() {
        $newmap = array();
        //$hotmap["domainid"]= $domainid;
        $newmap['position'] = array('in',array(1,9));   // 1 首页新品推荐 8 猜你喜欢
        $goodslist = $this->goodsapi->getDocument($newmap, 'update_time desc', 6);
        return $goodslist;
    }

    /**
     * 热卖商品
     */
    public function hot_goods() {
        $hotmap = array();
        //$hotmap["domainid"]= $domainid;
        $hotmap["ishot"] = 1;
        $hotgoodlist = $this->goodsapi->getDocument($hotmap, 'update_time desc', 2);
        return $hotgoodlist;
    }

}
