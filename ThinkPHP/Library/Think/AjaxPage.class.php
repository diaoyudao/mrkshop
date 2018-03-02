<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Think;

class AjaxPage {

    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter;
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow;
    // 分页总页面数
    protected $totalPages;
    // 总行数
    protected $totalRows;
    // 当前页数
    protected $nowPage;
    // 分页的栏的总页数
    protected $coolPages;
    // 分页显示定制
    protected $config = array('header' => '条记录', 'prev' => '上一页', 'next' => '下一页', 'first' => '第一页', 'last' => '最后一页', 'theme' => ' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    // 默认分页变量名
    protected $varPage;

    public function __construct($totalRows, $listRows = '', $ajax_func, $parameter = '') {
        $this->totalRows = $totalRows;
        $this->ajax_func = $ajax_func;
        $this->parameter = $parameter;
        $this->varPage = C('VAR_PAGE') ? C('VAR_PAGE') : 'p';
        if (!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
        $this->totalPages = ceil($this->totalRows / $this->listRows);     //总页数
        $this->coolPages = ceil($this->totalPages / $this->rollPage);
        $this->nowPage = !empty($_GET[$this->varPage]) ? intval($_GET[$this->varPage]) : 1;
        if (!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows * ($this->nowPage - 1);
    }

    public function setConfig($name, $value) {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    public function frontshow() {
        if (0 == $this->totalRows)
            return '';
        $p = $this->varPage;
        $nowCoolPage = ceil($this->nowPage / $this->rollPage);
        $url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '' : "?") . $this->parameter;
        $parse = parse_url($url);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $params);
            unset($params[$p]);
            $url = $parse['path'] . '?' . http_build_query($params);
        }
        //上下翻页字符串
        $upRow = $this->nowPage - 1;
        $downRow = $this->nowPage + 1;
        if ($upRow > 0) {
            $upPage = "<a class='prev' href='javascript:" . $this->ajax_func . "(" . $upRow . ")'>" . $this->config['prev'] . "</a>";
        } else {
            $upPage = "";
        }

        if ($downRow <= $this->totalPages) {
            $downPage = "<a class='next' href='javascript:" . $this->ajax_func . "(" . $downRow . ")'>" . $this->config['next'] . "</a>";
        } else {
            $downPage = "";
        }
        // << < > >>
        if ($nowCoolPage == 1) {
            $theFirst = "";
            $prePage = "";
        } else {
            $preRow = $this->nowPage - $this->rollPage;
            $prePage = "<a class='prev' href='javascript:" . $this->ajax_func . "(" . $preRow . ")'>上" . $this->rollPage . "页</a>";
            $theFirst = "<a class='first' href='javascript:" . $this->ajax_func . "(1)' >" . $this->config['first'] . "</a>";
        }
        if ($nowCoolPage == $this->coolPages) {
            $nextPage = "";
            $theEnd = "";
        } else {
            $nextRow = $this->nowPage + $this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<a class='next' href='javascript:" . $this->ajax_func . "(" . $nextRow . ")' >下" . $this->rollPage . "页</a>";
            $theEnd = "<a class='end' href='javascript:" . $this->ajax_func . "(" . $theEndRow . ")' >" . $this->config['last'] . "</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for ($i = 1; $i <= $this->rollPage; $i++) {
            $page = ($nowCoolPage - 1) * $this->rollPage + $i;
            if ($page != $this->nowPage) {
                if ($page <= $this->totalPages) {
                    $linkPage .= "<a class='num'  href='javascript:" . $this->ajax_func . "(" . $page . ")'>" . $page . "</a>";
                } else {
                    break;
                }
            } else {
                if ($this->totalPages != 1) {
                    $linkPage .= '<a class="on" href="javascript:;">' . $page . "</a>";
                }
            }
        }
        $pageStr = str_replace(
                array('%header%', '%nowPage%', '%totalRow%', '%totalPage%', '%upPage%', '%downPage%', '%first%', '%prePage%', '%linkPage%', '%nextPage%', '%end%'), array($this->config['header'], $this->nowPage, $this->totalRows, $this->totalPages, $upPage, $downPage, $theFirst, $prePage, $linkPage, $nextPage, $theEnd), $this->config['theme']);
        return $pageStr;
    }
    
    
    

    public function show() {
        if (0 == $this->totalRows)
            return '';
        $p = $this->varPage;
        $nowCoolPage = ceil($this->nowPage / $this->rollPage);
        $url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '' : "?") . $this->parameter;
        $parse = parse_url($url);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $params);
            unset($params[$p]);
            $url = $parse['path'] . '?' . http_build_query($params);
        }
        //上下翻页字符串
        $upRow = $this->nowPage - 1;
        $downRow = $this->nowPage + 1;
        if ($upRow > 0) {
            $upPage = "<a id='big' href='javascript:" . $this->ajax_func . "(" . $upRow . ")'>" . $this->config['prev'] . "</a>";
        } else {
            $upPage = "";
        }

        if ($downRow <= $this->totalPages) {
            $downPage = "<a id='big' href='javascript:" . $this->ajax_func . "(" . $downRow . ")'>" . $this->config['next'] . "</a>";
        } else {
            $downPage = "";
        }
        // << < > >>
        if ($nowCoolPage == 1) {
            $theFirst = "";
            $prePage = "";
        } else {
            $preRow = $this->nowPage - $this->rollPage;
            $prePage = "<a id='big' href='javascript:" . $this->ajax_func . "(" . $preRow . ")'>上" . $this->rollPage . "页</a>";
            $theFirst = "<a id='big' href='javascript:" . $this->ajax_func . "(1)' >" . $this->config['first'] . "</a>";
        }
        if ($nowCoolPage == $this->coolPages) {
            $nextPage = "";
            $theEnd = "";
        } else {
            $nextRow = $this->nowPage + $this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<a id='big' href='javascript:" . $this->ajax_func . "(" . $nextRow . ")' >下" . $this->rollPage . "页</a>";
            $theEnd = "<a id='big' href='javascript:" . $this->ajax_func . "(" . $theEndRow . ")' >" . $this->config['last'] . "</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for ($i = 1; $i <= $this->rollPage; $i++) {
            $page = ($nowCoolPage - 1) * $this->rollPage + $i;
            if ($page != $this->nowPage) {
                if ($page <= $this->totalPages) {
                    $linkPage .= "&nbsp;<a id='big' href='javascript:" . $this->ajax_func . "(" . $page . ")'>&nbsp;" . $page . "&nbsp;</a>";
                } else {
                    break;
                }
            } else {
                if ($this->totalPages != 1) {
                    $linkPage .= "&nbsp;<span class='current'>" . $page . "</span>";
                }
            }
        }
        $pageStr = str_replace(
                array('%header%', '%nowPage%', '%totalRow%', '%totalPage%', '%upPage%', '%downPage%', '%first%', '%prePage%', '%linkPage%', '%nextPage%', '%end%'), array($this->config['header'], $this->nowPage, $this->totalRows, $this->totalPages, $upPage, $downPage, $theFirst, $prePage, $linkPage, $nextPage, $theEnd), $this->config['theme']);
        return $pageStr;
    }
    
    
    public function new_pc_page() {
        if (0 == $this->totalRows)
            return '';
        $p = $this->varPage;
        $nowCoolPage = ceil($this->nowPage / $this->rollPage);
        $url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '' : "?") . $this->parameter;
        $parse = parse_url($url);
        if (isset($parse['query'])) {
            parse_str($parse['query'], $params);
            unset($params[$p]);
            $url = $parse['path'] . '?' . http_build_query($params);
        }
        //上下翻页字符串
        $upRow = $this->nowPage - 1;
        $downRow = $this->nowPage + 1;
        $this->config['prev'] = '<';
        $this->config['next'] = '>';
        if ($upRow > 0) {
            $upPage = "<a class='prev' href='javascript:" . $this->ajax_func . "(" . $upRow . ")'>" . $this->config['prev'] . "</a>";
        } else {
            $upPage = "";
        }

        if ($downRow <= $this->totalPages) {
            $downPage = "<a class='next' href='javascript:" . $this->ajax_func . "(" . $downRow . ")'>" . $this->config['next'] . "</a>";
        } else {
            $downPage = "";
        }
        // << < > >>
        if ($nowCoolPage == 1) {
            $theFirst = "";
            $prePage = "";
        } else {
            $preRow = $this->nowPage - $this->rollPage;
            $prePage = "<a class='prev' href='javascript:" . $this->ajax_func . "(" . $preRow . ")'>上" . $this->rollPage . "页</a>";
            $theFirst = "<a class='first' href='javascript:" . $this->ajax_func . "(1)' >" . $this->config['first'] . "</a>";
        }
        if ($nowCoolPage == $this->coolPages) {
            $nextPage = "";
            $theEnd = "";
        } else {
            $nextRow = $this->nowPage + $this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<a class='next' href='javascript:" . $this->ajax_func . "(" . $nextRow . ")' >下" . $this->rollPage . "页</a>";
            $theEnd = "<a class='end' href='javascript:" . $this->ajax_func . "(" . $theEndRow . ")' >" . $this->config['last'] . "</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for ($i = 1; $i <= $this->rollPage; $i++) {
            $page = ($nowCoolPage - 1) * $this->rollPage + $i;
            if ($page != $this->nowPage) {
                if ($page <= $this->totalPages) {
                    $linkPage .= "<a class='num'  href='javascript:" . $this->ajax_func . "(" . $page . ")'>" . $page . "</a>";
                } else {
                    break;
                }
            } else {
                if ($this->totalPages != 1) {
                    $linkPage .= '<a class="active" href="javascript:;">' . $page . "</a>";
                }
            }
        }
        $pageStr = str_replace(
                array('%header%', '%nowPage%', '%totalRow%', '%totalPage%', '%upPage%', '%downPage%', '%first%', '%prePage%', '%linkPage%', '%nextPage%', '%end%'), 
                array($this->config['header'], $this->nowPage, $this->totalRows, $this->totalPages, $upPage, $downPage, $theFirst, $prePage, $linkPage, $nextPage, $theEnd), 
                $this->config['theme']);
        return $pageStr;
    }

    function new_pc_page2() {
        if (0 == $this->totalRows)
            return '';
        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
        $this->url = U(ACTION_NAME, $this->parameter);
        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
        if (!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页零时变量 */
        $now_cool_page = $this->rollPage / 2;
        $now_cool_page_ceil = ceil($now_cool_page);
        $this->lastSuffix && $this->config['last'] = $this->totalPages;

        //上一页
        $up_row = $this->nowPage - 1;
        $up_page = $up_row > 0 ? '<a class="prev" href="' . $this->url($up_row) . '"><</a>' : '';

        //下一页
        $down_row = $this->nowPage + 1;
        $down_page = ($down_row <= $this->totalPages) ? '<a class="next" href="' . $this->url($down_row) . '">></a>' : '';

        //第一页
        $the_first = '';
        if ($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1) {
            $the_first = '<a class="first" href="' . $this->url(1) . '">' . $this->config['first'] . '</a>';
        }

        //最后一页
        $the_end = '';
        if ($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages) {
            $the_end = '<a class="end" href="' . $this->url($this->totalPages) . '">' . $this->config['last'] . '</a>';
        }

        //数字连接
        $link_page = "";
        for ($i = 1; $i <= $this->rollPage; $i++) {
            if (($this->nowPage - $now_cool_page) <= 0) {
                $page = $i;
            } elseif (($this->nowPage + $now_cool_page - 1) >= $this->totalPages) {
                $page = $this->totalPages - $this->rollPage + $i;
            } else {
                $page = $this->nowPage - $now_cool_page_ceil + $i;
            }
            if ($page > 0 && $page != $this->nowPage) {

                if ($page <= $this->totalPages) {
                    $link_page .= '<a class="num" href="' . $this->url($page) . '">' . $page . '</a>';
                } else {
                    break;
                }
            } else {
                if ($page > 0 && $this->totalPages != 1) {
                    $link_page .= '<a class="active" href="javascript:;">' . $page . '</a>';
                }
            }
        }

        //替换分页内容
        $page_str = str_replace(
                array('%HEADER%', '%NOW_PAGE%', '%TOTAL_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%'), array($this->config['header'], $this->nowPage, $this->totalPages, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows), $this->config['theme']);
        return "<div class='page'>{$page_str}</div>";
    }

}
