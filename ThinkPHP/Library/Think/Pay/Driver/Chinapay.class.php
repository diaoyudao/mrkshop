<?php

namespace Think\Pay\Driver;

include_once(dirname(__FILE__).'/netpayclient.php');
class Chinapay extends \Think\Pay\Pay {

    protected $gateway = 'https://payment.ChinaPay.com/pay/TransGet';
    //http://payment-test.chinapay.com/pay/transget
    //payment.ChinaPay.com
    protected $config = array(
        'PRI_KEY' => '',
        'PUB_KEY' => '',
        'CHINAPAY_ACCOUNT' => ''
    );

    public function check() {
        if (!$this->config['PRI_KEY'] || !$this->config['CHINAPAY_ACCOUNT']) {
            E("银联支付设置有误！");
        }
        return true;
    }

    public function buildRequestForm(\Think\Pay\PayVo $vo) {


        $MerId = trim($this->config['CHINAPAY_ACCOUNT']);
        $orderno = $vo->getOrderNo();
        $OrdId = $this->ecshopsn2chinapaysn($orderno, $MerId);
        $orderprice = $vo->getFee();
        $TransAmt = $this->formatamount($orderprice);
        $TransTime = date('His',time());
        $CuryId = '156'; 
        $TransDate = date('Ymd',time());
        $TransType = '0001'; 
        $Version = '20070129';//'20070129';
        $GateId = '';
        $Priv1 = "test priv1"; 
        $merkey_file= trim($this->config['PRI_KEY']);

        //导入私钥文件, 返回值即为您的商户号，长度15位
        $merid = buildKey($merkey_file);

        if(!$merid) {
            echo "导入私钥文件失败！";
            exit;
        }
     
        //按次序组合订单信息为待签名串
        $plain = $MerId . $OrdId . $TransAmt . $CuryId . $TransDate .  $TransType.  $Priv1;
        
        //生成签名值，必填
        $chkvalue = sign($plain);
        if (!$chkvalue) {
            echo "签名失败！";
            exit;
        }


        $param = array(
            'MerId' => $MerId,
            'OrdId' => $OrdId,
            'TransAmt' => $TransAmt,
            'CuryId' => $CuryId,
            'TransDate' => $TransDate,
            'TransType' => $TransType,
            'Version' => $Version,
            'BgRetUrl' => $this->config['notify_url'],
            'PageRetUrl' => $this->config['return_url'],
            'GateId' => $GateId,
            'Priv1' => $Priv1,
            'ChkValue'=>$chkvalue,
        );

        $sHtml = $this->_buildForm($param, $this->gateway);

        return $sHtml;
    }



    public function verifyNotify($notify) {


    //order_paid($v_oid);
	//return true;
	//$payment = get_payment(basename(__FILE__, '.php'));
 
	$merid = trim($_POST['merid']);
	$orderno = trim($_POST['orderno']);
	$transdate = trim($_POST['transdate']);
	$amount = trim($_POST['amount']);
	$currencycode = trim($_POST['currencycode']);
	$transtype = trim($_POST['transtype']);
	$status = trim($_POST['status']);
	$checkvalue = trim($_POST['checkvalue']);
	$v_gateid = trim($_POST['GateId']);
	$v_Priv1 = trim($_POST['Priv1']);
	
	/**
	 * 重新计算密钥的值
	 */
	$pubkey = $this->config['PUB_KEY'];
	$PGID = buildKey($pubkey);
	if(!$PGID) 
	{
		echo "导入私钥文件失败！";
		exit;
	}
	$verify = verifyTransResponse($merid, $orderno, $amount, $currencycode, $transdate, $transtype, $status, $checkvalue);
	if (!$verify) 
	{
		echo "验证签名失败！";
		exit;
	}
	
	/* 检查秘钥是否正确 */
	if ($status == '1001') 
	{
		/*$v_ordesn = chinapaysn2ecshopsn($orderno);
		$order_id = get_order_id_by_sn($v_ordesn);

        $info = array();
        //支付状态
        $info['status'] = $notify['respCode'] == '00' ? true : false;
        $info['money'] = $notify['orderAmount'] / 100;
        $info['out_trade_no'] = $notify['orderNumber'];
        $this->info = $info;*/

        $this->setInfo($notify);

        return true;


	}
	else
	{
		return false;
	}
	}

    protected function setInfo($notify) {
        $info = array();
        //支付状态
        $info['status'] = true;
        $info['money'] = $notify['amount']/100;
        $v_ordesn = $this->chinapaysn2ecshopsn($notify['orderno']);

        $info['out_trade_no'] = $v_ordesn;
        $this->info = $info;
    }
 
    /*
     *本地订单号转为银联订单号
     */
    function ecshopsn2chinapaysn($order_sn, $vid)
    {
        if($order_sn > $vid)
        {
            //$sub_vid = substr($vid, 10, 5);
            //$sub_start = substr($order_sn, 2, 4);
            //$sub_end = substr($order_sn, 6);

            $sub_vid = substr($vid, 10, 5);
            $sub_start = substr($order_sn, 5, 4);
            $sub_end = substr($order_sn, 9,7);

            $temp = @$pay_id; //屏蔽错误提示
            return $sub_start . $sub_vid . $sub_end;
        }
    }
     
    /*
     *银联订单号转为本地订单号
     */
    function chinapaysn2ecshopsn($chinapaysn)
    {
        if($chinapaysn)
        { 
            $year = date('Y',time());
            return 'E'.substr($year,0,4) . substr($chinapaysn, 0, 4) . substr($chinapaysn, 9) ;
        }
    }
     
    /*
     *格式化交易金额，以分位单位的12位数字。
     */
    function formatamount($amount)
    {
        if($amount){
            if(!strstr($amount, "."))
            {
                $amount = $amount.".00";
            }
            $amount = str_replace(".", "", $amount);
            $temp = $amount;
            for($i=0; $i< 12 - strlen($amount); $i++)
            {
                $temp = "0" . $temp;
            }
            return $temp;
        }
    }


}
