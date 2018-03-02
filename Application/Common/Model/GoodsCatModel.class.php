<?php
namespace Common\Model;

/**
 * 商品分类模型
 */
class GoodsCatModel extends CommonModel{

    protected $_validate = array(
        array('cat_name', 'require', '分类名不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('cat_name', 'checkCatName', '分类名已经存在', self::VALUE_VALIDATE , 'callback', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('status', '1', self::MODEL_BOTH),
    );


    /**
     * 检查商品分类名称是否重复
     * @param string $cat_name 商品名称
     * @return true无重复，false已存在
     */
    protected function checkCatName($cat_name = '')
    {
        $name = $cat_name ? $cat_name :trim(I('post.cat_name'));
        $id = intval(I('post.id', 0));
        $map = array(
            'cat_name' => $name,
            'status' => 1
        );
        if($id > 0){
            $map['id'] = array('neq',$id);
        }

        if ($this->where($map)->find()){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 获取分类详细信息
     * @param  milit   $id 分类ID或分类名称
     * @param  boolean $field 查询字段
     * @return array     分类信息
     */
    public function info($id, $field = true){
        /* 获取分类信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['cat_name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param int $id 指定的分类ID
     * @param boolean $field 查询字段,查询所有
     * @param array $map 筛选条件
     * @return array，返回当前分类及其子分类数据表所有数据，分类树，多维数组
     *
     */
    public function getTree($id, $field = true,$map = array()){
        /* 获取当前分类信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }

        /* 获取所有商品分类 */
        $map['status']  = 1;
        $list = $this->field($field)->where($map)->order('sort desc')->select();
        $list = list_to_tree($list, $pk = 'id', $pid = 'parent_id', $child = '_', $root = $id);
        
        /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }

        return $info;
    }

    /**
     * 获取指定分类的同级分类
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array
     */
    public function getSameLevel($id, $field = true){
        $info = $this->info($id, 'parent_id');
        $map = array('parent_id' => $info['parent_id'], 'status' => 1);
        return $this->field($field)->where($map)->order('sort')->select();
    }

    /**
     * 保存分类信息
     * @return boolean 更新状态
     */
    public function saveData(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }
        /* 添加或更新数据 */
        return empty($data['id']) ? $this->add() : $this->save();
    }

    /**
     * 获取指定分类子分类ID
     * @param  string $cate 分类ID
     * @param  int $ismenu  分类类型 默认2是商品分类
     * @return string       id列表
     */
    public function getChildrenId( $id ){
        //by wangcheng 循环找出子分类的id
        $cate_ids_array   = get_stemma( $id,M("goods_cat"), 'id');
        $ids = $id; 
        if($cate_ids_array){
            $cate_ids=array();
            foreach($cate_ids_array as $k=>$v){
                $cate_ids[]=$v["id"];
            }
            $cate_ids[]=$id;
            $ids= implode(',', $cate_ids); 
        } 
        return $ids; 
    }

    /**
     * 获取前台分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param int $tree_id 指定的分类ID
     * @return array
     */
    public function get_child_tree($tree_id = 0)
    {
        $three_arr = array();
        $where = array(
            'parent_id'=>$tree_id,
            'status'=>1,
            'is_show'=>1
        );
        $count = $this->where($where)->count();

        if ($count || $tree_id == 0)
        {
            $res = $this->where($where)->order('sort ASC,id ASC')->select();
            foreach ($res AS $row)
            {
                if ($row['is_open']){
                    $three_arr[$row['id']]['id']   = $row['id'];
                    $three_arr[$row['id']]['name'] = $row['cat_name'];
                    $ext_arr = !empty($row['ext_arr']) ? unserialize($row['ext_arr']) : "";
                    if(isset($ext_arr['ad_img'])){
                        $ext_arr['ad_img']= get_pic_url($ext_arr['ad_img']);
                    }
                    $three_arr[$row['id']]['ext_arr'] = $ext_arr;

                    if (isset($row['id']) != NULL)
                    {
                        $three_arr[$row['id']]['id'] = get_child_tree($row['id']);
                    }
                }
            }
        }
        return $three_arr;
    }
}
