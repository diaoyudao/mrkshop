<?php

/**
 * 商品相关接口
 */

namespace Api\Api;


use Common\Model\GoodsModel;

class GoodsApi extends Api {
    public $totalPages = 0;

    /**
     * 构造方法，实例化操作模型
     */
    protected function _init() {
        $this->model = new GoodsModel();
    }

    /**
     * 商品列表
     * @param array $map 筛选条件
     * @param string $order 排序
     * @param null $limit 是否分页
     * @param int $size 每页条数
     * @return array
     */
    public function getList($map = array(), $order = "id desc", $islimit = true,$size = 10) {
        $lists = $this->model->getList($map,$islimit,$order,I('p'),$size);
        if($lists){
            foreach($lists as $k=>$v){
                //处理商品的价格和库存显示
                $lists[$k] = $this->disposeGoodsShowData($v);
            }
        }
        return $lists;
    }


    public function getOne($id){
        $goods_info = $this->model->getOne($id);
        if($goods_info){
            $goods_info = $this->disposeGoodsShowData($goods_info);
        }
        return $goods_info;
    }

    /**
     * 处理商品显示数据
     * @param $goods_info
     * @return mixed
     */
    private function disposeGoodsShowData($goods_info){
        $now_time = time();
        /* 处理价格 */
        $shop_price = $goods_info['shop_price'];
        //是否促销打折
        if($goods_info['is_promote']  && ($goods_info['promote_start_date'] <= $now_time && $goods_info['promote_end_date'] >= $now_time)){
            //计算促销价
            $promote_price = ($shop_price * $goods_info['promote_ratio'])/100;
            $goods_info['promote_price'] = number_format($promote_price, 2, '.', '');
        }
        //是否登录
        if(is_login()){
            $user_info = session('user_auth');
            //获取会员价
            if($user_info['level_info'] && ($user_info['level_info']['discount'] > 0)){
                $member_price = ($shop_price*$user_info['level_info']['discount'])/100;
                $goods_info['member_price'] = number_format($member_price, 2, '.', '');
            }
            //判断是否收藏该商品
            if(D('Favortable')->isCollected(1,$goods_info['id'])){
                $goods_info['is_collected'] = 1;
            }
        }

        /* 处理库存 */
        $goods_info['goods_number'] = $goods_info['stock_num'] ? intval($goods_info['stock_num']) : $goods_info['goods_number'];

        return $goods_info;
    }

    /**
     * 获取组合商品数据
     * @param array $map 查询条件
     * @param string $order 排序
     * @param null $limit 分页
     * @return mixed
     */
    public function getGoodsGroup($map = array(), $order = "id desc", $limit = null) {
        $map['status'] = 1;
        if ($limit) {
            $lists = M("goods_group")->field("id,goods_id,goods_price")
                ->where($map)
                ->order($order)
                ->limit($limit)
                ->select();
        } else {
            $lists = M("goods_group")->field("id,goods_id,goods_price")
                ->where($map)
                ->order($order)
                ->select();
        }
        if($lists){
            foreach ($lists as $k => $v) {
                $goods_list =  $this->model->getList(array('g.id'=>array('in',$v['goods_id'])),false);
                $lists[$k]['goods_group_list'] = $goods_list;
            }

            if ($limit == 1) {
                $lists = $lists[0];
            }
        }
        return $lists;
    }

    /**
     * 获取历史浏览记录
     * @return array
     */
    public function get_history() {
        $history_list = array();
        $history = cookie('history_list' . session('uid'));
        $count = count($history); //统计条数
        if ($count > 10) {
            $history_goodid = array_slice($history, $count - 10); //取最后10条记录
            $good_ids = implode(',', $history_goodid);
        } else {
            $good_ids = implode(',', $history);
        }
        if (!empty($good_ids)) {
            $map = array();
            $map['g.id'] = array('in', $good_ids);
            $history_list = $this->model->getList($map,false);
        }
        return $history_list;
    }

    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base    基本的查询条件
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @return array|false
     * 返回数据集
     */
    public function _lists($model, $where = array(), $order = '', $base = array('status' => array('egt', 0)), $field = true) {
        $options = array();
        $REQUEST = array_merge(I('post.'), I('get.')); //(array)I('request.');
        if (is_string($model)) {
            $model = M($model);
        }

        $OPT = new \ReflectionProperty($model, 'options');
        $OPT->setAccessible(true);

        $pk = $model->getPk();
        if ($order === null) {
            //order置空
        } else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array('desc', 'asc'))) {
            $options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
        } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
            $options['order'] = $pk . ' desc';
        } elseif ($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'], $REQUEST['_field']);
        $options['where'] = array_filter(array_merge((array) $base, /* $REQUEST, */ (array) $where), function($val) {
            if ($val === '' || $val === null) {
                return false;
            } else {
                return true;
            }
        });
        if (empty($options['where'])) {
            unset($options['where']);
        }
        $options = array_merge((array) $OPT->getValue($model), $options);
        $total = $model->where($options['where'])->count();

        if (isset($REQUEST['r'])) {
            $listRows = (int) $REQUEST['r'];
        } else {
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $page->rollPage = 5;
        $p = $page->new_pc_page();
//        $p =$page->frontshow();
        $this->assign('_page', $p ? $p : '');

        $minpage = new \Think\Page($total, $listRows);
//        if ($total > $listRows) {
        $minpage->setConfig('theme', '%DOWN_PAGE% %UP_PAGE% %TOTAL_PAGE%');
//        }
        $minpage->rollPage = 5;
        $min_p = $minpage->min_page();

        $this->assign('_minpage', $min_p ? $min_p : '');
        $this->assign('totalPages', $page->totalPages);
        $this->assign('_total', $total);
        $aPage = I("page", 1, "intval");
        $this->assign('p', $aPage);
        $options['limit'] = $page->firstRow . ',' . $page->listRows;
        $model->setProperty('options', $options);
        $list = $model->field($field)->select();
        return $list;
    }

}
