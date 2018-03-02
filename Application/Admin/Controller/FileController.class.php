<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends AdminController {

    /* 文件上传 */
    public function upload(){
		$return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
		/* 调用文件上传组件上传文件 */
		$File = D('File');
		$file_driver = C('DOWNLOAD_UPLOAD_DRIVER');
		$info = $File->upload(
			$_FILES,
			C('DOWNLOAD_UPLOAD'),
			C('DOWNLOAD_UPLOAD_DRIVER'),
			C("UPLOAD_{$file_driver}_CONFIG")
		);

        /* 记录附件信息 */
        if($info){
            $return['data'] = think_encrypt(json_encode($info['download']));
            $return['info'] = $info['download']['name'];
        } else {
            $return['status'] = 0;
            $return['info']   = $File->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

    /* 下载文件 */
    public function download($id = null){
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误！');
        }

        $logic = D('Download', 'Logic');
        if(!$logic->download($id)){
            $this->error($logic->getError());
        }

    }

    /**
     * 上传图片
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPicture(){
        //TODO: 用户登录检测

        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');
        $setting = C('PICTURE_UPLOAD');
        $pagefrom = I("pagefrom","");
        $fields = I("fields","");
        $domainid = I("domainid","");
        $setting["domainid"]=$domainid;
        if( $domainid ){
            $setting["rootPath"]=$setting["rootPath"].$domainid."/";
        }

	//处理图片保存的分类方便前台调用
	$setting["types"]=0;//图片保存设置默认为0
	if($pagefrom=="goods"){//商品图片设置
	    if($fields=="cover_id"){
		$setting["types"]=1;//商品封面设置1
	    }else if($fields=="pics"){
		$setting["types"]=2;//商品图集设置2
	    }
	}elseif($pagefrom=="specialtopic"){//专题图片设置
	    if($fields=="cover_id"){
		$setting["types"]=3;//专题封面设置3
	    }
	}elseif($pagefrom=="baike"){//百科图片设置
	    if($fields=="cover_id"){
		 $setting["types"]=4;//百科封面设置4
	    }
	}elseif($pagefrom=="article"){//文章图片设置
	    if($fields=="cover_id"){
		 $setting["types"]=5;//文章封面设置5
	    }
	}elseif($pagefrom=="brand"){//品牌图片设置
	    if($fields=="cover_id"){
		$setting["types"]=6;//品牌封面设置6
	    }else if($fields=="pics"){
		$setting["types"]=7;//品牌背景或热门或推荐设置7
	    }
	}elseif($pagefrom=="activity"){//活动图片设置
	    if($fields=="cover_id"){
		$setting["types"]=8;//封面
	    }else if($fields=="pics"){
		$setting["types"]=9;//背景图片
	    }
	}

	$setting['savePath']=$setting["types"]."/";
        $info = $Picture->upload(
            $_FILES,
            $setting,
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        ); //TODO:上传到远程服务器

        /* 记录图片信息 */
        if($info){
            $return['status'] = 1;
            $return = array_merge($info['download'], $return);
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }

        /* 返回JSON数据 */
	ob_end_clean();
        $this->ajaxReturn($return);
    }
}