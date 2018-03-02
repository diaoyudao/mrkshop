<?php
namespace Admin\Model;
use Think\Model;
/**
 * 专题模型
 * @author wangcheng <253490851@qq.com>
 */

class SpecialtopicModel extends Model {
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('title', 'checkLength', '标题超过最大字符', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('subtitle', 'require', '副标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('mark', 'require', '标示不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('mark', 'checkMark', '标识已经存在或用了系统变量(createorder,orderconfirm,feedback,shopcart,comment,rank,baike,product,brand,ask,article,zt)', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('preview', 'require', '导语不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('des', 'require', '描述不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('uniongood', 'arr2str', self::MODEL_BOTH, 'function'),
        array('uniongood', null, self::MODEL_BOTH, 'ignore'),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );
    
    protected function checkLength(){
        $mark = I('post.mark_ask',0);
         $value = I("post.title" );
        if( $mark==3 ){//问答模板 
            $length  =  mb_strlen($value,'utf-8'); // 当前数据长度
            if( $length > 14 ){
                return false;
            }
        }else if( $mark==2 ){//文章模板 
            $length  =  mb_strlen($value,'utf-8'); // 当前数据长度
            if( $length > 6 ){
                return false;
            }
        }
        return true;
    }
    /**
     * 检查标识是否已存在(只需在同一根节点下不重复)
     * @param string $name
     * @return true无重复，false已存在
     * @author qchlian
     */
    protected function checkMark(){
        $mark        = I('post.mark');
        //$domainid        = I('post.domainid');
        $id = I("post.id",0);
        if( in_array($mark,array("createorder","orderconfirm","feedback","shopcart","comment","rank","baike","product","brand","ask","article","zt")) ){
            return false;
        } 
        $map = array('mark' => $mark, 'id' => array('neq', $id), 'status' => array('neq', -1));
        //频道标识在频道表，品牌表，专题表中是唯一的
        $subdomain = check_table_field_name('Specialtopic',$map);
        $bmap = array('name' => $mark,'status' => array('neq', -1));
        $brandinfo = check_table_field_name('SubdomainBrand',$bmap);
        $zmap = array('mark' => $mark,'status' => array('neq', -1));
        $ztinfo = check_table_field_name('Subdomain',$zmap);
        if ($subdomain || $brandinfo || $ztinfo) {
            return false;
        }
        return true; 
    }
}
