<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Wap\Controller;
use Think\Controller;
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */

class FileController extends HomeController {
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
        ob_end_clean();

        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');

        $setting = C('PICTURE_UPLOAD');
        $setting['rootPath']='./Uploads/Picture/';

        $setting["types"] = 5;//5评论图片类型

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
        $this->ajaxReturn($return);
    }


    public function uploadPictureFace(){
        //TODO: 用户登录检测
        ob_end_clean();

        /* 返回标准数据 */
        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');

        /* 调用文件上传组件上传文件 */
        $Picture = D('Picture');
        $pic_driver = C('PICTURE_UPLOAD_DRIVER');

        $setting = C('PICTURE_UPLOAD');
        $setting['rootPath']='./Uploads/Face/';

        
        $uid = I("uid","");

        //$setting['savePath'] = $uid."/"; 
        $setting['subName'] = $uid;//子目录
        $setting['saveName'] = 'face';//头像名称
        $setting['replace'] = TRUE;//存在同名是否覆盖
        //$setting['exts'] = array('jpg');//允许上传头像的后缀
        $setting['saveExt'] = 'jpg';//允许上传头像的后缀
        $setting['maxSize'] = '2000000';//允许上传头像的后缀
        

        $info = $Picture->uploadface(
            $_FILES,
            $setting,
            C('PICTURE_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        ); //TODO:上传到远程服务器
        
        //生成缩略图　图片大小格式165X165；75X75
        $touurl = __PICURLFACE__.$uid.'/face.jpg';
        $a = $this->_image_thumb($touurl,165,165,true,'Face',$setting['rootPath']);
        $b = $this->_image_thumb($touurl,75,75,true,'Face',$setting['rootPath']);


        /* 记录图片信息 */
        if($info){
            $return['status'] = 1;
            $return = array_merge($info['download'], $return);

            //如果用户上传图片成功则把用户表face设置为１
            $updateuser = array();
            $updateuser["face"] = 1;
            $updateuser["id"] = $uid;
            M("ucenter_member")->save($updateuser);
            //更新SESSION内容
            /*$userinfo  =  session('user_auth');
            if(!empty($userinfo)){
                $userinfo['face'] = 1;
                session('user_auth', $userinfo);
                session("user_auth_sign", data_auth_sign($userinfo));
            }*/


        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }

}
