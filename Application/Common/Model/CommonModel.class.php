<?php
namespace Common\Model;

use Think\Model;

/**
 * 公共基础模型
 */
class CommonModel extends Model{
    /**
     * 数据表前缀
     * @var string
     */
    protected function __initialize(){
        $this->tablePrefix = C('DB_PREFIX');
    }

    /**
     * 删除数据
     * @param $model 实例化模型
     * @param $id 数据主键ID,多个id用逗号隔开
     */
    protected function delData($model,$ids){
        return $model->where(array('id'=>array('in',$ids)))->setField('status',0);
    }

}
