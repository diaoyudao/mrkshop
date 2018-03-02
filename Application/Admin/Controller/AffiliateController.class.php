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
 */
class AffiliateController extends AdminController {

	public function index(){
		redirect(U('Admin/Affiliate/bill'));
	}
	
    /**
     * 结算单
     */
    public function bill() {
        /* 查询条件初始化 */
        $ob_no = I('ob_no');
        if ($ob_no) {
            $map = array('ob_no' => $ob_no);
        }
        $list = $this->lists('affiliate_bill', $map, 'ob_no desc');

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->meta_title = '结算单';
        $this->display('index');
    }

    /**
     * 日志详情
     */
    public function log() {
        /* 查询条件初始化 */
        $ob_no = I('ob_no');
        if ($ob_no) {
            $map = array('ob_no' => $ob_no);
        }
        $list = $this->lists('affiliate_log', $map, 'ob_no desc');

        $this->assign('list', $list);
        $this->assign('ob_no', $ob_no);

        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->meta_title = '提成明细';
        $this->display('log');
    }

    /**
     * 编辑订单
     */
    public function edit($id = 0) {
        if (IS_POST) {
            $_POST['ob_pay_date'] = strtotime($_POST['ob_pay_date']);
            $_POST['update_time'] = NOW_TIME;
            $Form = D('AffiliateBill');
            if ($_POST["ob_no"]) {
                $id = $_POST["ob_no"];
                $Form->create();
                $result = $Form->where("ob_no='$id'")->save();
                if ($result) {
                    //记录行为
                    action_log('update_AffiliateBill', 'AffiliateBill', $id, UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败' . $id);
                }
            } else {
                $this->error($Form->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('affiliate_bill')->where(array('ob_no' => $id))->find();

            if (false === $info) {
                $this->error('获取订单信息错误');
            }

            $this->assign('info', $info);
            $this->meta_title = '编辑结算单';
            $this->display();
        }
    }

    /**
     * 导出结算
     */
    public function excel_export() {
        $condition = array();
        $list = M('affiliate_bill')->where($condition)->select();
        if ($list) {
            $this->createExcel($list);
        }
        exit;
    }
    /**
     * 导出结算
     */
    public function excel_export_log() {
        $condition = array();
        $list = M('affiliate_log')->where($condition)->select();
        if ($list) {
            $this->createExcel_log($list);
        }
        exit;
    }

    /*
     * 创建excel
     */
    public function createExcel($data) {
        vendor('Excel.Excel');
        $excel_obj = new \Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        //header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '结算单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '结算月份');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '应结金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '实际支付金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员名');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '申请时间');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '状态');
        //data
        foreach ($data as $k => $v) {
            $excel_data[$k + 1][] = array('format' => 'Number', 'data' => $v['ob_no']);
            $excel_data[$k + 1][] = array('format' => 'Number', 'data' => $v['os_month']);
            $excel_data[$k + 1][] = array('format' => 'Number', 'data' => ncPriceFormat($v['ob_result_totals']));
            $excel_data[$k + 1][] = array('format' => 'Number', 'data' => ncPriceFormat($v['ob_pay_total']));
            $excel_data[$k + 1][] = array('data' => get_username($v['ob_store_id']));
            $excel_data[$k + 1][] = array('data' => date("Y-m-d H:i:s", $v['ob_create_date']));
            $excel_data[$k + 1][] = array('data' => $this->state_txt($v['ob_state']));
        }
        define(CHARSET, "utf-8");
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('结算单列表', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('结算单', CHARSET) . date('Y-m-d-H', time()));
    }
    /*
     * 创建excel
     */
    public function createExcel_log($data) {
        vendor('Excel.Excel');
        $excel_obj = new \Excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        //header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '订单号');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '商品名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员价');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '数量');
        //$excel_data[0][] = array('styleid' => 's_title', 'data' => '提成比例');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '提成金额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '提成时间');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '会员名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '状态');
        //data
        foreach ($data as $k => $v) {
            $excel_data[$k + 1][] = array('data' => $v['order_sn']);
            $excel_data[$k + 1][] = array('data' => $v['goods_name']);
            $excel_data[$k + 1][] = array('data' => ncPriceFormat($v['goods_price']));
            $excel_data[$k + 1][] = array('data' => $v['goods_num']);
           // $excel_data[$k + 1][] = array('format' => 'Number', 'data' => ($v['affiliate']));
            $excel_data[$k + 1][] = array('format' => 'Number', 'data' => ncPriceFormat($v['money']));
            $excel_data[$k + 1][] = array('data' => date("Y-m-d H:i:s", $v['add_time']));
            $excel_data[$k + 1][] = array('data' => get_username($v['member_id']));
            $excel_data[$k + 1][] = array('data' => $this->status_txt($v['status']));
        }
        define(CHARSET, "utf-8");
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('提成明细列表', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('提成明细', CHARSET) . date('Y-m-d-H', time()));
    }

    private function state_txt($state) {
        $statetxt = "";
        switch ($state) {
            case 1:
                $statetxt = "门店申请体现";
                break;
            case 2:
                $statetxt = "平台已确认";
                break;
            case 3:
                $statetxt = "平台已打款";
                break;
            case 4:
                $statetxt = "结算已完成";
                break;
            default:
                $statetxt = '';
                break;
        }
        return $statetxt;
    }
    private function status_txt($status) {
        $statetxt = "";
        switch ($status) {
            case 1:
                $statetxt = "未付款";
                break;
            case 2:
                $statetxt = "已付款";
                break;
            case 3:
                $statetxt = "已收货";
                break;
            case 4:
                $statetxt = "已提现";
                break;
            default:
                $statetxt = '';
                break;
        }
        return $statetxt;
    }

}
