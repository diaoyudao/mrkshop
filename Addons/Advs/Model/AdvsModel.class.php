<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Addons\Advs\Model;
use Think\Model;

/**
 * 分类模型
 */
class AdvsModel extends Model{	
	/* 自动完成规则 */
	protected $_auto = array(
	    array('create_time', 'getCreateTime', self::MODEL_BOTH,'callback'),
	    array('end_time', 'getEndTime', self::MODEL_BOTH,'callback'),
	);
	
	
	protected function _after_find(&$result,$options) {
		$sing = M('advertising')->find($result['position']);
		$result['positiontext'] = $sing['title'];
		$result['statustext'] =  $result['status'] == 0 ? '禁用' : '正常';
		$result['create_time'] = date('Y-m-d H:i', $result['create_time']);
		if($result['end_time']){
			$result['end_time'] = date('Y-m-d H:i', $result['end_time']);
		}else{
			$result['end_time'] = '';
		}
	}
	
	protected function _after_select(&$result,$options){
		foreach($result as &$record){
			$this->_after_find($record,$options);
		}
	}
	/*  展示数据  */
	public function AdvsList($map){ 
		//前台调用
		$data=array();
		//$domainid = cookie('current_domainid');
		$where=array();
		//$where["domainid"]= 1; 
		/*if(isset($map['domainid']) && $map['domainid']=='man' ){
		    $where["domainid"]=2;
		}elseif(isset($map['domainid']) && $map['domainid']=='woman' ){
		    $where["domainid"]=3;
	
		}*/
		
		if(is_array($map) && array_key_exists("domainid", $map) && $map["domainid"] ){
		    $domainid = M("subdomain")->where( array("mark"=>$map["domainid"]) )->getField("id");
		    if($domainid){
			$where["domainid"]=$domainid;
		    }else{
			$where["domainid"]= $map["domainid"];
		    }
		}
        
        //,'advsmark'=>$cateinfo['name']
 
		$advsmark="";
		if( is_array($map) ){
		    //扩展品牌页面的广告，如果没有调用频道里面的数据
		    $where["mark"] = $map["mark"];
		    $where["brandid"] = isset($map["brandid"]) ? intval($map["brandid"]) :0;
		    $advsmark = isset($map["advsmark"])?$map["advsmark"]:'';
		}else{
		    $where["mark"] = $map;
		    $where["brandid"] = 0;
		}

		$list = M('advertising')->where($where)->getField("id,brandid");  
		if($list){
		    foreach($list as $k=>$v){
                if($v >0){
                    $id_onebrand = $k;
                }else{ 
                    $id_allbrand = $k;
                }
		    }
            
		    $sing = array();
		    if($id_onebrand){
			$sing = M('advertising')->find($id_onebrand);//找到当前调用的广告位
			$where = ' and position = '.$id_onebrand; 
		    }else{
			$sing = M('advertising')->find($id_allbrand);//找到当前调用的广告位
			$where = ' and position = '.$id_allbrand; 
		    }
		    if(!empty($advsmark)){
			$where .=" and advsmark = '".$advsmark."'"; 
		    } 


		    if( $sing['type'] == 2 ){
			    $advs = $this->where('status = 1 and create_time < '.time().' and ( end_time=0 or end_time > '.time().' ) '.$where)->order('level asc,id asc')->select();
			    if(!$advs && $id_onebrand){
				$sing = M('advertising')->find($id_allbrand);//找到切换当前调用的广告位
				$where = ' and position = '.$id_allbrand; 
				$advs = $this->where('status = 1 and create_time < '.time().' and ( end_time=0 or end_time > '.time().' ) '.$where)->order('level asc,id asc')->select();
			    }
			    foreach($advs as $key=>$val){		
				$data['res'][$key] = $val;
				$cover = M('picture')->find($val['advspic']);
				//缩略图 start
				$url =  __PICURL__.$val['domainid'].'/'.$cover['path'];
                $newurl = $this->ymk_image_thumb($url,$sing['width'],$sing['height']); 
                $cover['path']= $newurl;
				//缩略图 end 
				$data['res'][$key]['path'] = $cover['path'];
			    }
			    $data['type'] = $sing['type'];
			    $data['domainid'] = $sing['domainid'];
			    $data['width'] = $sing['width'];
			    $data['height'] = $sing['height'];
		    }else{ 
                $data = $this->where('status = 1 and create_time < '.time().' and ( end_time=0 or end_time > '.time().' ) '.$where)->order('level asc,id asc')->find(); 
                if(!$data && $id_onebrand){
                    $sing = M('advertising')->find($id_allbrand);//找到切换当前调用的广告位
                    $where = ' and position = '.$id_allbrand; 
                    $data = $this->where('status = 1 and create_time < '.time().' and ( end_time=0 or end_time > '.time().' ) '.$where)->order('level asc,id asc')->find();
                }
                if($data["advspic"]>0){
                    $data['path'] = M('picture')->where("id=".$data['advspic'])->getField("path");
		    //缩略图 start
		    $url =  __PICURL__.$data['domainid'].'/'.$data['path'];
                $newurl = $this->ymk_image_thumb($url,$sing['width'],$sing['height']); 
                $data['path']= $newurl;

		    //缩略图 end
                }
                $data['type'] = $sing['type'];
                $data['width'] = $sing['width'];
                $data['domainid'] = $sing['domainid'];
                $data['height'] = $sing['height']; 
		    }


		}

        

		return $data;
	}




    public function AdvsListSite($map){
	    //前台调用
	    $data=array();
	    //$domainid = cookie('current_domainid');
	    $where=array();
	    //$where["domainid"]= 1;
    
	    /*if(isset($map['domainid']) && $map['domainid']=='man' ){
		$where["domainid"]=2;
	    }elseif(isset($map['domainid']) && $map['domainid']=='woman' ){
		$where["domainid"]=3;
    
	    }*/

	    if(isset($map['domainid']) && $map["domainid"]){
            $domainid = M("subdomain")->where( array("mark"=>$map["domainid"]) )->getField("id");
            if($domainid){
                $where["domainid"]=$domainid;
            }else{
                $where["domainid"]= $map["domainid"];
            }
	    }
    
    //,'advsmark'=>$cateinfo['name']

	    $advsmark="";
	    if( is_array($map)){
		//扩展品牌页面的广告，如果没有调用频道里面的数据
		$where["mark"] = $map["mark"];
		$where["brandid"] = isset($map["brandid"]) ? intval($map["brandid"]) :0;
		$advsmark = isset($map["advsmark"])?$map["advsmark"]:'';
	    }else{
		$where["mark"] = $map;
		$where["brandid"] = 0;
	    }
	    $list = M('advertising')->where($where)->getField("id,brandid"); 


    
	    $id_onebrand = array();
	    $id_allbrand = array();
	    if($list){
		foreach($list as $k=>$v){
            if($v >0){
                $id_onebrand[] = $k;
            }else{ 
                $id_allbrand[] = $k;
            }
		}
	
		$singarray = array();
		if($id_onebrand){
            $mapb['id'] = array('in',$id_onebrand);
            $singarray = M('advertising')->where($mapb)->select();//找到当前调用的广告位
            $id_onebrand_string = implode(',',$id_onebrand);
            $where = " and position in ($id_onebrand_string) "; 
		}else{
            $mapb['id'] = array('in',$id_allbrand);
            $singarray = M('advertising')->where($mapb)->select();//找到当前调用的广告位
            $id_allbrand_string = implode(',',$id_allbrand);
            $where = " and position in ($id_allbrand_string) "; 
		}
		if(!empty($advsmark)){
			$where .=" and advsmark = '".$advsmark."'"; 
		}
            if(!empty($singarray)){
                $sing = $singarray[0];
            }
            
            if( $sing['type'] == 2 ){
                $advs = $this->where('status = 1 and create_time < '.time().' and ( end_time=0 or end_time > '.time().' ) '.$where)->order('level asc,id asc')->select();
                if(!$advs && $id_onebrand){
                    $mapb = array();
                    $mapb['id'] = array('in',$id_allbrand);
                    $singarray = M('advertising')->where($mapb)->select();//找到切换当前调用的广告位
                    if(empty($singarray)){
                        $sing = $singarray[0];
                    }
                    $id_onebrand_string = implode(',',$id_allbrand);
                    $where = " and position in ($id_onebrand_string) "; 

                    $advs = $this->where('status = 1 and create_time < '.time().' and ( end_time=0 or end_time > '.time().' ) '.$where)->order('level asc,id asc')->select();
                } 
                foreach($advs as $key=>$val){
                    $data['res'][$key] = $val;
                    $cover = M('picture')->find($val['advspic']); 
		    //缩略图 start
		    $url =  __PICURL__.$val['domainid'].'/'.$cover['path'];
                $newurl = $this->ymk_image_thumb($url,$sing['width'],$sing['height']); 
                $cover['path']= $newurl;
		    //缩略图 end
                    $data['res'][$key]['path'] = $cover['path'];
                }
                $data['type'] = $sing['type'];
                $data['domainid'] = $sing['domainid'];
                $data['width'] = $sing['width'];
                $data['height'] = $sing['height'];
            }else{ 
                $data = $this->where('status = 1 and create_time < '.time().' and ( end_time=0 or end_time > '.time().' ) '.$where)->order('level asc,id asc')->select(); 

                if(!$data && $id_onebrand){
                    $mapb = array();
                    $mapb['id'] = array('in',$id_allbrand);
                    $singarray = M('advertising')->where($mapb)->select();//找到切换当前调用的广告位

                    /*if(empty($singarray)){
                        $sing = $singarray[0];
                    }*/
                    $id_onebrand_string = implode(',',$id_allbrand);
                    $where = " and position in ($id_onebrand_string) "; 
                    
                    $data = $this->where('status = 1 and create_time < '.time().' and ( end_time=0 or end_time > '.time().' ) '.$where)->order('level asc,id asc')->find();
                }
                foreach($data as $dkey=>&$ddata){
                    if($ddata["advspic"]>0){
                        $ddata['path'] = M('picture')->where("id=".$ddata['advspic'])->getField("path");
                        //缩略图 start
                        $url =  __PICURL__.$ddata['domainid'].'/'.$ddata['path'];
                            $newurl = $this->ymk_image_thumb($url,$sing['width'],$sing['height']); 
                            $ddata['path']= $newurl;
                        //缩略图 end
                    }else{
                        unset($data[$dkey]);
                    }
                }
                $data['type'] = $sing['type'];
                $data['width'] = $sing['width'];
                $data['domainid'] = $sing['domainid'];
                $data['height'] = $sing['height']; 
            }   
		}

        //首页上广告显示 A1
        if($map["mark"] == 'A1'){//每个频道随机显示一张
            $picres= array();
          $picres = $data['res'];
          $newpicres = array();
          if(!empty($picres)){
            foreach($picres as $pvalue){
                $newpicres[$pvalue['domainid']][] = $pvalue;
            }
            $randpic = array();
            foreach($newpicres as $pdomainid=>$ppvalue){
                srand ((float) microtime() * 10000000);
                $randpicnum = array_rand($ppvalue,1);
                $randpic[] = $ppvalue[$randpicnum];
            }
            $data['res'] = $randpic;
          }
        }

        if($map["mark"] == 'A2'){//所有频道A2总合取三个
            $randpic = array();
            if(!empty($data)){
                $domainid = $data['domainid'];
                $width = $data['width'];
                $height = $data['height'];
                unset($data['type']);
                unset($data['width']);
                unset($data['height']);
                unset($data['domainid']);
                if(count($data)>3){
                    srand ((float) microtime() * 10000000);
                    $randpicnum = array_rand($data,3);
                    $randpic[] = $data[$randpicnum[0]]; 
                    $randpic[] = $data[$randpicnum[1]];
                    $randpic[] = $data[$randpicnum[2]];
                }else{
                    $randpic = $data;
                }
                $data = array();
                $data['res'] = $randpic;
                $data['type'] = 2;
                $data['domainid'] = $domainid;
                $data['width'] = $width;
                $data['height'] = $height;
            }
        }

        if($map["mark"] == 'A3'){//每个频道随机显示4张
          $picres= array();
          $picres = $data['res'];
          $newpicres = array();
          if(!empty($picres)){
            foreach($picres as $pvalue){
                $newpicres[$pvalue['domainid']][] = $pvalue;
            }
            $randpic = array();
            foreach($newpicres as $pdomainid=>$ppvalue){
                if(count($ppvalue)>4){
                    srand ((float) microtime() * 10000000);
                    $randpicnum = array_rand($ppvalue,4);
                    $randpic[] = $ppvalue[$randpicnum[0]];
                    $randpic[] = $ppvalue[$randpicnum[1]];
                    $randpic[] = $ppvalue[$randpicnum[2]];
                    $randpic[] = $ppvalue[$randpicnum[3]];
                }else{
                    $randpic = array_merge ($randpic, $ppvalue);
                }
            }
            $data['res'] = $randpic;
          }
        }

        
        if($map["mark"] == 'A4'){//每个频道随机显示六张
          $picres= array();
          $picres = $data['res'];
          if(!empty($picres)){
            $randpic = array();
            if(count($picres)>6){
                srand ((float) microtime() * 10000000);
                $randpicnum = array_rand($picres,6);
                $randpic[] = $picres[$randpicnum[0]];
                $randpic[] = $picres[$randpicnum[1]];
                $randpic[] = $picres[$randpicnum[2]];
                $randpic[] = $picres[$randpicnum[3]];
                $randpic[] = $picres[$randpicnum[4]];
                $randpic[] = $picres[$randpicnum[5]];
            }else{
                $randpic = $picres;
            }
            $data['res'] = $randpic;
          }
        }

		return $data;
	}


	
	///*  展示数据  */
	//public function AdvsList($param){
	//	if(isset($param)){
	//		$sing = M('advertising')->find($param);//找到当前调用的广告位
	//		$where = ' and position = '.$param;
	//	}
	//	if($sing['type'] == 2){
	//		$advs = $this->where('status = 1 and create_time < '.time().' and end_time > '.time().$where)->order('level asc,id asc')->select();
	//		foreach($advs as $key=>$val){		
	//			$data['res'][$key] = $val;
	//			$cover = M('picture')->find($val['advspic']);
	//			$data['res'][$key]['path'] = $cover['path'];
	//		}
	//		$data['type'] = $sing['type'];
	//		$data['width'] = $sing['width'];
	//		$data['height'] = $sing['height'];
	//	}else{
	//		$data = $this->where('status = 1 and create_time < '.time().' and end_time > '.time().$where)->order('level asc,id asc')->find();
	//		$data['type'] = $sing['type'];
	//		$data['width'] = $sing['width'];
	//		$data['height'] = $sing['height'];
	//	}
	//	return $data;
	//}
	
	/* 获取编辑数据 */
	public function detail($id){
		$data = $this->find($id);
		$cover = M('picture')->find($data['advspic']);
		$sing = M('advertising')->find($data['position']);
		$data['path'] = $cover['path'];
		$data['type'] = $sing['type'];
		return $data;
	}
	
	/* 禁用 */
	public function forbidden($id){
		return $this->save(array('id'=>$id,'status'=>'0'));
	}
	
	/* 启用 */
	public function off($id){
		return $this->save(array('id'=>$id,'status'=>'1'));
	}
	
	/* 删除 */
	public function del($id){
		return $this->delete($id);
	}
	
	/**
	 * 新增或更新一个文档
	 * @return boolean fasle 失败 ， int  成功 返回完整的数据
	 */
	public function update(){
		/* 获取数据对象 */
		$data = $this->create();
		$sing = M('advertising')->where(array('id'=>$data['position']))->find();
		//广告位的禁用判断
		if($sing['status'] == 0){
			$this->error = '这是个禁用的广告位！';
			return false;
		}
		
		if(empty($data)){
			return false;
		}
		/* 添加或新增基础内容 */
		if(empty($data['id'])){ //新增数据
			//单一广告判断
			if($sing['type'] != 2){//判断单图
				$count = $this->where('position = '.$data['position'])->count();
				if($count > 0){
					$this->error = '单图广告、文字广告和代码广告位只允许添加一条广告信息！';
					return false;
				}
			}			
			$id = $this->add(); //添加基础内容
			if(!$id){
				$this->error = '新增广告内容出错！';
				return false;
			}
		} else { //更新数据
			$status = $this->save(); //更新基础内容
			if(false === $status){
				$this->error = '更新广告内容出错！';
				return false;
			}
		}
	
		//内容添加或更新完成
		return $data;
	
	}	
	
	/* 时间处理规则 */
	protected function getCreateTime(){
		$create_time    =   I('post.create_time');
		return $create_time?strtotime($create_time):NOW_TIME;
	}
	
	/* 时间处理规则 */
	protected function getEndTime(){
		$end_time    =   I('post.end_time');
		return $end_time?strtotime($end_time):0;
	}
	
	/***************************************************************
	*created date:2015/6/26 10:38 
	*created author:sheshanhu
	*content:图片缩略图生成及地址返回
	*modefiy person:
	*modefiy date:
	*note:
        ****************************************************************/
	function ymk_image_thumb($url,$width,$height,$iscreted=false,$type='Picture',$rootpath=''){ 
	    $path_parts = pathinfo($url);
	    $dirname = $path_parts['dirname'];
	    $dirarray = explode('Uploads/'.$type.'/',$dirname); 
	    if(empty($rootpath)){
		$uploadimage = C('PICTURE_UPLOAD');//获取上传图片的文件夹地址
		$newdirname = $uploadimage['rootPath'].$dirarray[1].'/';
	    }else{
		$newdirname = $rootpath.$dirarray[1].'/';
	    } 
	    $reurl = $newdirname.$path_parts['basename'];
	
	    $newimagename = $path_parts['filename'].'_'.$width.'x'.$height.'.'.$path_parts['extension'];
	
	    $newurl = $newdirname.$newimagename;
        if(is_file($reurl)){
            //判断文件是否存在，如果存在则不生成，
            if(!is_file($newurl) || $iscreted){
            //$image = new \Think\Image(); 
            //$image->open($reurl);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
            //$b = $image->thumb($width, $height)->save($newurl);
            }
        }

	
	    //如果创建成功，图片存在则返回图片，如果不存在，则返回原地址。
	    if(is_file($newurl)){
		$newurl = $path_parts['dirname'].'/'.$newimagename;
		return $newurl;
	    }else{
	       return $url;
	    }
	}
	
}