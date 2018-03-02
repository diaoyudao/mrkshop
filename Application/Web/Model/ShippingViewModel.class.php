<?php

namespace Web\Model;
use Think\Model\ViewModel;

/**
 * 配送方式
 */
class ShippingViewModel extends ViewModel {

    public $viewFields = array(
        'distribution' => array('id'=>'dis_id','title', 'price', 'description', 'weight', 'status','create_time', '_type' => 'LEFT'),
        'shipping_extend' => array('id'=>'id','area_name', 'snum','sprice','xnum','xprice','is_default','shipping_id','shipping_title', '_on' => 'distribution.id=shipping_extend.shipping_id', '_type' => 'LEFT'),
    );

}
