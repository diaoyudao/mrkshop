<?php
require_once "WxPayApi.php";
require_once "WxPayNotify.php";
require_once "log.php";
//初始化日志
$logHandler= new CLogFileHandler( dirname(__FILE__)."/logs/".date('Y-m-d').'.log' );
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
        //查询订单
        public function Queryorder($transaction_id)
        {
                $input = new WxPayOrderQuery();
                $input->SetTransaction_id($transaction_id);
                $result = WxPayApi::orderQuery($input);
                  //记录日志
                \Think\Log::write(var_export($input, true), 'DEBUG', '', C('LOG_PATH') . 'log_query.log');
                \Think\Log::write(var_export($result, true), 'DEBUG', '', C('LOG_PATH') . 'log_query.log');
                Log::DEBUG("query:" . json_encode($result));
                if(array_key_exists("return_code", $result)
                        && array_key_exists("result_code", $result)
                        && $result["return_code"] == "SUCCESS"
                        && $result["result_code"] == "SUCCESS")
                {
                        $out_trade_no=$result['out_trade_no'];//私有订单号，你就用这个订单号来进行你自己订单的各种更新吧
                        $mch_id=$result['mch_id'];//商户号
                        $total_fee=$result['total_fee'];//支付金额，出来的金额要除以100
                        $transaction_id=$result['transaction_id'];//微信内部的订单流水号
                        $openid=$result['openid'];//微信加密的用户身份识别,app支付的话其实意义不大了
                        /*以下两行用做调试，会自动生成in_test.txt文件而且后期内容会自动追加到这个文件*/
                        //$fp = fopen('in_test.txt','a+');
                        //fwrite($fp,date("Y-m-d H:i:s").json_encode($result) . "|".$transaction_id." 成功了！rn");
                        /**/
                        return true;//这个很重要，微信的异步请求，当你执行完了你的内部处理以后给他返回true，微信就认为你的内部处理完成了，就不会再次请求你了，否则他会一直请求你这个文件，知道超时。
                }
                return false;
        }
        
        //重写回调处理函数
        public function NotifyProcess($data, &$msg)
        {
                $file = dirname(__FILE__) . "/log_pay.txt";
                @file_put_contents($file, "NotifyProcess start notifyprocess ------------------------" . var_export($data, true) . "\r\n" . "------------------------\r\n", FILE_APPEND);
        
                 //记录日志
                \Think\Log::write(var_export($data, true), 'DEBUG', '', C('LOG_PATH') . 'log_pay.log');
                
                Log::DEBUG("call back:" . json_encode($data));
                $notfiyOutput = array();
                
                if(!array_key_exists("transaction_id", $data)){
                        $msg = "输入参数不正确";
                        return false;
                }
                //查询订单，判断订单真实性
                if(!$this->Queryorder($data["transaction_id"])){
                        $msg = "订单查询失败";
                        return false;
                }
                $order_sn = $data['attach'];
                if(!checkorderstatus($order_sn)){
                    orderhandle($order_sn,$data["transaction_id"],3); 
                }  
                        //$sdata = array();
                //$sdata = array('status'=>'2','update_time' => time());
                //M("pay")->where(array('out_trade_no' => $order_sn))->setField($sdata);
                //$sdata = array();
                //$sdata = array('status'=>'1','ispay'=>'2',);//设置订单为已经支付,状态为已提交
                //M('order')->where(array('tag' => $order_sn))->setField($sdata);
                // 
                return true;
        }
} 
