<?php
namespace Admin\Model;
use Think\Model;

/**
 * 子域名频道模型
 * @author wangcheng <253490851@qq.com>
 */

class SubdomainModel extends Model {
    protected $_validate = array(
        array('name', 'require', '名称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        //array('mark', 'require', '标识不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH), 
        array('mark', 'checkName', '标识已经存在或用了系统变量(createorder,orderconfirm,feedback,shopcart,comment,rank,baike,product,brand,ask,article,zt)', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('url', 'require', 'URL不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('bindgroup', 'require', '绑定分组名不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );
    
    //检查频道标识是否为唯一
    protected function checkName(){
        $mark        = I('post.mark');
        $id          = I('post.id', 0);
        if( in_array($mark,array(createorder,orderconfirm,feedback,shopcart,comment,rank,baike,product,brand,ask,article,zt)) ){
            return false;
        }
        
        $map = array('mark' => $mark, 'id' => array('neq', $id), 'status' => array('neq', -1));
        //频道标识在频道表，品牌表，专题表中是唯一的
        $subdomain = check_table_field_name('Subdomain',$map);
        $bmap = array('name' => $mark,'status' => array('neq', -1));
        $brandinfo = check_table_field_name('SubdomainBrand',$bmap);
        $zmap = array('mark' => $mark,'status' => array('neq', -1));
        $ztinfo = check_table_field_name('Specialtopic',$zmap);
        if ($subdomain || $brandinfo || $ztinfo) {
            return false;
        }
        return true;
    }
}
