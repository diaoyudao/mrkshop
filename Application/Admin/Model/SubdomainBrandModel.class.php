<?php
namespace Admin\Model;
use Think\Model;
use Think\PYInitials;
/**
 * 子域名频道模型
 * @author wangcheng <253490851@qq.com>
 */

class SubdomainBrandModel extends Model {
    protected $_validate = array(
        array('title', '', '标题已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('name', '/^[a-zA-Z0-9]{1,50}$/', '标识只能为英文字母,数字组成(1-50位)', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('name', '', '标识已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH), 
        array('name', 'checkName', '标识已经存在或用了系统变量(createorder,orderconfirm,feedback,shopcart,comment,rank,baike,product,brand,ask,article,zt)', self::VALUE_VALIDATE, 'callback', self::MODEL_BOTH),
        array('domainid', 'require', '频道不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array(array('domainid','title','status'), '', '同频道下名称已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array(array('domainid','name','status'), '', '同频道下标示已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('jianshu', '1,140', '品牌简述不能超过140个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('hot_pics_info', 'json_encode', self::MODEL_BOTH, 'function'),
        array('hot_pics_info', null, self::MODEL_BOTH, 'ignore'),
        array('re_pics_info', 'json_encode', self::MODEL_BOTH, 'function'),
        array('re_pics_info', null, self::MODEL_BOTH, 'ignore'),
        array('folderdes', 'json_encode', self::MODEL_BOTH, 'function'),
        array('folderdes', null, self::MODEL_BOTH, 'ignore'),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('pyname', 'getPinyin', self::MODEL_BOTH, 'callback'),
        array('firstkey', 'getFirstkey', self::MODEL_BOTH, 'callback'),
        array('firstkeyid', 'getFirstkeyid', self::MODEL_BOTH, 'callback'),
        array('position', 'getPosition', self::MODEL_BOTH, 'callback'),
        array('status', '1', self::MODEL_INSERT),
    );
    
    protected function getPinyin(){
        $py = new PYInitials();
        $str=I("title");
        $pyname= $py->getInitials( $str );
        return $pyname;
    }
    protected function getFirstkey(){
        $py = new PYInitials();
        $str=I("title");
        $firstkey= $py->getInitials( $str );
        $firstkey=strtoupper(substr($firstkey,0,1));//是否只显示首写 
        return $firstkey;
    }
    protected function getFirstkeyid(){
        $py = new PYInitials();
        $str=I("title");
        $firstkey= $py->getInitials( $str );
        $firstkey=strtoupper(substr($firstkey,0,1));//是否只显示首写
        $id=0;
        if( $firstkey ){ 
            if( is_numeric($firstkey) ){
                $id=27;
            }else{ 
                $id = M("SubdomainBrandKey")->where(array("firstkey"=>$firstkey))->getField("id");
                if(!$id){
                    $id=0;
                }
            }
        }
        return $id;
    }
    
    /**
     * 生成推荐位的值
     * @return number 推荐位
     * @author qchlian
     */
    protected function getPosition(){
        $position = I('post.position');
        if(!is_array($position)){
            return 0;
        }else{
            $pos = 0;
            foreach ($position as $key=>$value){
                $pos += $value;		//将各个推荐位的值相加
            } 
            return $pos;
        }
    }
    /**
     * 检查标识是否已存在(只需在同一根节点下不重复)
     * @param string $name
     * @return true无重复，false已存在
     * @author qchlian
     */
    protected function checkName(){
        $name        = I('post.name');
        //$domainid        = I('post.domainid');
        $id = I("post.id",0);
        if( in_array($mark,array("createorder","orderconfirm","feedback","shopcart","comment","rank","baike","product","brand","ask","article","zt")) ){
            return false;
        } 
        $map = array('name' => $name, 'id' => array('neq', $id), 'status' => array('neq', -1));
        //频道标识在频道表，品牌表，专题表中是唯一的
        $subdomain = check_table_field_name('SubdomainBrand',$map);
        $bmap = array('mark' => $name,'status' => array('neq', -1));
      //  $brandinfo = check_table_field_name('Specialtopic',$bmap);
        $zmap = array('mark' => $name,'status' => array('neq', -1));
        $ztinfo = check_table_field_name('Subdomain',$zmap);
        if ($subdomain || $ztinfo) {
            return false;
        }
        return true; 
        return true;
    }
    
}
