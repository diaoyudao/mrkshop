<?php

namespace Common\Model;

use Think\Model\ViewModel;

/**
 * 货架格子
 */
class ExpressViewModel extends ViewModel {

    public $viewFields = array(
        'express_cabint' => array('name', 'member_id', 'alias as express_alias', 'status as express_status', 'type as express_type', '_type' => 'LEFT'),
        'express_cabint_grid' => array('id','grid_name', 'alias as grid_alias','type as grid_type','use_times','create_time','update_time', '_on' => 'express_cabint.id=express_cabint_grid.parent_id', '_type' => 'LEFT'),
    );

}
