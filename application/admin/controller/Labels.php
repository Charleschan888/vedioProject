<?php
// +----------------------------------------------------------------------
// | Charles [ Keep going ]
// +----------------------------------------------------------------------
// | create time : 2018-05-20
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://charles.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: charles <13560336325@163.com>
// +----------------------------------------------------------------------
// | 说明: 标签主页控制器
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Labels extends BaseAdmin{
	/**
	 * 频道标签管理
	 */
	public function channel(){
		$data = $this->db->table('labels')->where(['flag'=>'channel'])->lists();
		//渲染页面
		$this->assign('data', $data);
		return $this->fetch();
	}

	/**
	 * 资费标签管理
	 */
	public function charge(){
		$data = $this->db->table('labels')->where(['flag'=>'charge'])->lists();
		//渲染页面
		$this->assign('data', $data);
		return $this->fetch();
	}

	/**
	 * 地区标签管理
	 */
	public function area(){
		$data = $this->db->table('labels')->where(['flag'=>'area'])->lists();
		//渲染页面
		$this->assign('data', $data);
		return $this->fetch();
	}

	/**
	 * 分类标签管理
	 */
	public function cate(){
		$data = $this->db->table('labels')->where(['flag'=>'cate'])->lists();
		//渲染页面
		$this->assign('data', $data);
		return $this->fetch();
	}

	/**
	 * 保存标签操作
	 */
	public function save(){
		//接收数据
		$flag = trim( input('post.flag') );
		$orders = input('post.orders/a');
		$titles = input('post.titles/a');
		$status = input('post.status/a');
		$obj = $this->db->table('labels');
		//循环数据
		foreach ($orders as $key => $value) {
			$data['flag']   = $flag;
			$data['order']  = $orders[$key]; 
			$data['title']  = $titles[$key]; 
			$data['status']  = isset($status[$key]) ? 1 : 0 ;

			//判断如果key为0则为新增
			if($key==0 && !empty($data['title'])){
				//进行录入
				$obj->insert($data);
			}
			if($key>0){
				//检查如果数据为空则删除
				if(empty($data['title']) ){
					//进行删除操作
					$obj->where(['id'=>$key])->delete();
				}else{
					//进行更新操作
					$obj->where(['id'=>$key])->update($data);
				}
			}
		}
		//返回
		return ['code'=>0,'msg'=>'保存成功'];
	}

}