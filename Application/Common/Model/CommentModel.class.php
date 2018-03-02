<?php
namespace Common\Model;

/**
 * 商品评论模型
 */
class CommentModel extends CommonModel{

    protected $_validate = array(

    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_BOTH),
    );

    /**
     * 获取详细信息
     * @param  milit  $id 评论的ID
     * @param  boolean $field 查询字段
     * @return array  信息
     */
    public function info($id, $field = true){
        $info = $this->field($field)->find($id);
        return $info;
    }

    /**
     * 获取列表
     * @param $field string 查询字段
     * @param $order string 排序
     * @param $map array 筛选条件
     * @return array
     */
    public function getList($field = true,$order="sort desc",$map = array()){
        $map['status']  = 1;
        $list = $this->field($field)->where($map)->order($order)->select();

        //处理logo
        if($list){
            foreach($list as $key=>$val){
                //获取该评论所有的图片数据
                $pic_list = M('picture')->field('url,path')->where(array('data_id'=>$val['id'],'types'=>0,'status'=>1))->select();
                if($pic_list){
                    foreach($pic_list as $v){
                        $list[$key]['pic_list'][] = !empty($v['url']) ? $v['url'] : __PICURL__ . $v['path'];
                    }
                }
            }
        }
        return $list;
    }

    /**
     * 保存数据
     * @return boolean 更新状态
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function saveData(){
        $data = $this->create();
        if(!$data){ //数据对象创建错误
            return false;
        }
        /* 添加或更新数据 */
        return empty($data['id']) ? $this->add() : $this->save();
    }

}
