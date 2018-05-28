<?php
// +----------------------------------------------------------------------
// | Charles [ Keep going ]
// +----------------------------------------------------------------------
// | create time : 2018-05-24
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://charles.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: charles <13560336325@163.com>
// +----------------------------------------------------------------------
// | 说明: 幻灯片控制器
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Slide extends BaseAdmin{
	/**
	 * 首屏幻灯片列表页  type 0
	 */
	public function index_slide(){
		//获取出首屏幻灯页面
		$data['data'] = $this->db->table('slide')->where(['type'=>0])->lists();
		//渲染页面
		$this->assign('data', $data);
		return $this->fetch();
	}

	/**
	 * 幻灯片操作添加编辑页面
	 */
	public function addEditPage(){
		//接收id
		$id = (int)input('get.id');
		//接收过来的typeid
		$data['type'] = (int)input('get.type');
		$data['info'] = $this->db->table('slide')->where(['id'=>$id])->item();
		//渲染模板
		$this->assign('data', $data);
		return $this->fetch();
	}


	/**
	 * 幻灯片保存操作
	 */
	public function addEditHandle(){
		//接收数据
		$id = (int)input('post.id');
		$data['title'] = trim(input('post.title'));
		$data['img'] = trim(input('post.img'));
		$data['url'] = trim(input('post.url'));
		$data['order'] = (int)(input('post.order'));
		$data['is_show'] = (int)(input('post.is_show')) ? 1 : 0 ;
		//检查数据
		if(empty($data['title']) || empty($data['img']) || empty($data['url'])){
			return ['code'=>1,'msg'=>'标题、图片、url都不能为空'];
		}

		//判断是录入还是修改
		if($id>0){
			//执行更新数据
			$res = $this->db->table('slide')->where(['id'=>$id])->update($data);
		}else{
			//执行录入
			$res = $this->db->table('slide')->insert($data);
		}
		if(!$res){
			return ['code'=>1,'msg'=>'保存失败'];
		}
		//返回成功
		return ['code'=>0,'msg'=>'保存成功'];
	}

	/**
	 * 修改幻灯片的是否显示字段
	 */
	public function changeShow(){
		//接收id
		$id = (int)input('post.id');
		$m = $this->db->table('slide');
		$cur_show = $m->where(['id'=>$id])->item();
		$change = $cur_show['is_show'] == 0 ? 1 : 0;
		//更新
		$res = $m->where(['id'=>$id])->update(['is_show'=>$change]);
		if(!$res){
			//修改失败则返回原来的is_show
			return ['code'=>1,'is_show'=>$cur_show['is_show'],'msg'=>'修改失败'];
		}
		//修改成功
		return ['code'=>0,'is_show'=>$change,'msg'=>'修改成功'];
	}

	/**
	 * 修改幻灯片的排序
	 */
	public function changeOrder(){
		//接收id
		$id = (int)input('post.id');
		//order
		$order = (int)input('post.order');
		$order>=100 && $order = 100;
		//更新操作
		$res = $this->db->table('slide')->where(['id'=>$id])->update(['order'=>$order]);
		if(!$res){
			return ['code'=>1,'msg'=>'修改失败'];
		}
		return ['code'=>0,'msg'=>'修改成功'];
	}
}