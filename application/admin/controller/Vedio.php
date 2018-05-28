<?php
// +----------------------------------------------------------------------
// | Charles [ Keep going ]
// +----------------------------------------------------------------------
// | create time : 2018-05-21
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://charles.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: charles <13560336325@163.com>
// +----------------------------------------------------------------------
// | 说明: 影片控制器
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Vedio extends BaseAdmin{
	/**
	 * 影片列表
	 */
	public function index(){
		//获取父级id
		$pid = (int)input('get.pid');
		//定义分页每页显示数量
		$data['pageSize'] = 10;
		//获取当前显示页 默认为1
		$data['cur_page'] = max(1,(int)input('get.page'));
		//拼接where条件
		$where = ['pid'=>$pid];
		//获取wd 搜索内容
		$data['wd'] = trim(input('get.wd'));
		if(!empty($data['wd'])){
			$where['title'] = ['like',"%{$data['wd']}%"];
		}
		//获取影片数据  默认先获取出第一级的影片目录
		$data['data'] = $this->db->table('vedio')->where($where)->order(['id'=>'desc'])->pages($data['pageSize']);
		//获取所有的标签数据
		$data['labels'] = getLabelsCate();
		//如果pid大于0则查询出父级的数据
		if($pid>0){
			$data['parent'] = $this->db->field('title')->where(['id'=>$pid])->item();
		}
		//渲染模板
		$this->assign('data',$data);
		$this->assign('pid',$pid);
		return $this->fetch();
	}

	/**
	 * 添加编辑影片页面
	 */
	public function addEditPage(){
		//接收id
		$id = (int)input('get.id');
		//接收pid 如果有pid则证明是添加子集影片
		$pid = (int)input('get.pid');
		//如果pid有的话则查询出父级的影片数据
		if($pid>0){
			$data['parent'] = $this->db->table('vedio')->where(['id'=>$pid])->item();
		}
		$data['info'] = $this->db->table('vedio')->where(['id'=>$id])->item();
		//获取标签数据
		$data['labels'] = getLabelsCate();
		//渲染模板
		$this->assign('data',$data);
		$this->assign('pid',$pid);
		return $this->fetch();
	}

	/**
	 * 添加编辑影片
	 */
	public function addEditHandle(){
		//接收数据
		$id = (int)input('post.id');//是否有影片的id
		$data['title'] = trim( input('post.title') );//影片标题
		$data['keywords'] = trim( input('post.keywords') );//关键词D
		$data['desc'] = trim( input('post.desc') );//描述  
		$data['channel_id'] = (int)input('post.channel_id');//频道id  
		$data['charge_id'] = (int)input('post.charge_id');//资费id  
		$data['area_id'] = (int)input('post.area_id');//地区id  
		$data['cate_id'] = (int)input('post.cate_id');//分类id  
		$data['url'] = trim( input('post.url') );//影片url 
		$data['cover'] = trim( input('post.cover') );//影片封面图 
		$data['is_sub'] = (int)input('post.is_sub');//是否有子集影片
		$data['is_vip'] = (int)input('post.is_vip');//是否vip可看
		$data['status'] = (int)input('post.status');//状态
		$data['pid'] = (int)input('post.pid');//父级id
		//检查下数据
		//影片标题不能为空
		if( empty($data['title']) )  return ['code'=>1,'msg'=>'影片标题不能为空'];
		//如果没有子集并且url地址为空则不合法
		if( $data['is_sub']==0 && empty($data['url']) ) return ['code'=>1,'msg'=>'没有子集影片url不能为空'];
		//判断标签选择
		if(empty($data['channel_id']) || empty($data['area_id']) || empty($data['charge_id']) || empty($data['cate_id'])){
			return ['code'=>1,'msg'=>'标签分类必须全部选择'];
		}

		//判断是更新还是新增
		if($id>0){
			//更新
			$res = $this->db->table('vedio')->where(['id'=>$id])->update($data);
		}else{
			//新增
			$data['add_time'] = time();//增加添加时间
			$res = $this->db->table('vedio')->insert($data);
		}

		if(!$res){
			//保存失败
			return ['code'=>1,'msg'=>'保存失败'];
		}
		//保存成功返回
		return ['code'=>0,'msg'=>'保存成功'];
	}

	/**
	 * 删除影片
	 */
	public function delete(){
		//接收id
		$id = (int)input('post.id');
		$m = $this->db->table('vedio');
		//查出自己的数据
		$item = $m->where(['id'=>$id])->item();
		$subs = [];//定义一个数组
		//查询看有无该影片的子影片
		$s = $m->where(['pid'=>$id])->lists();
		if(!empty($s)){
			//如果有子集影片，则把数据线赋值到数组
			$subs = $s;
		}
		//将自己的数据追加到数组中
		array_push($subs,$item);
		if(!empty($subs)){
			//删除该影片下所有的子影片
			foreach($subs as $k => $v){
				//删除封面图数据
				@unlink(ROOT_PATH . 'public' . $v['cover']);
				//删除数据库数据
				$m->where(['id'=>$v['id']])->delete();
			}
		}
		return ['code'=>0,'msg'=>'删除成功'];
	}

	/**
	 * 定义一个上传影片封面的方法
	 */
	public function uploadCover(){
		//接收一下传递过来的file
		$file = request()->file('file');
		if(empty($file)){
			return ['code'=>1,'msg'=>'没有获取到上传的文件'];
		}
		//保存图片的目录绝对路径
		$dir_path = ROOT_PATH . 'public' . DS . 'uploads'. DS .'vedioCover';
		//移动保存图片到目录
		$info = $file->move($dir_path);
		if($info){
            //获取后缀名判断是否合法
            $ext = strtolower($info->getExtension());
            if(!in_array($ext,['jpg','jpeg','gif','png'])){
            	return ['code'=>1,'msg'=>'上传图片格式不支持'];
            }
            //拼接图片的相对路径
            $url = '/uploads/vedioCover'. DS . str_replace('\\', '/', $info->getSaveName() );
            return ['code'=>0,'msg'=>'上传成功','url'=>$url];
        }else{
            // 上传失败获取错误信息
            return ['code'=>1,'msg'=>$file->getError()];
        }
	}
}