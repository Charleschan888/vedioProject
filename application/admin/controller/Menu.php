<?php
// +----------------------------------------------------------------------
// | Charles [ Keep going ]
// +----------------------------------------------------------------------
// | create time : 2018-05-17
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://charles.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: charles <13560336325@163.com>
// +----------------------------------------------------------------------
// | 说明: 菜单控制器
// +----------------------------------------------------------------------


namespace app\admin\controller;

class Menu extends BaseAdmin{
	/**
	 * 菜单列表
	 */
	public function index(){
		//接收一下pid
		$pid = (int)input('get.pid') ? :0;//默认为0 一级菜单
		//获取出所有的菜单数据
		$data['menus'] = $this->db->table('menus')->where(['pid'=>$pid])->lists();

		//如果pid大于0则是显示子菜单，则需要取出返回上级的pid
		$parent_id = 0;
		if($pid>0){
			//获取出对应主键id为这个pid的数据的pid
			$parent = $this->db->table('menus')->where(['id'=>$pid])->item();
			$parent_id = $parent['pid'];
		}
		//渲染模板
		$this->assign('parent_id',$parent_id);
		$this->assign('pid',$pid);
		$this->assign('data',$data);
		return $this->fetch();
	}

	/**
	 * 菜单的添加修改删除方法
	 */
	public function save(){
		//接收数据
		$pid = (int)input('post.pid');//父级id
		//接收的数据为数组时需要在input函数后面增加/a做处理
		$orders = input('post.orders/a');//排序
		$titles = input('post.titles/a');//菜单名称
		$controllers = input('post.controllers/a');//控制器名称
		$methods = input('post.methods/a');//方法名称
		$isHiddens = input('post.isHiddens/a');//是否隐藏
		$status = input('post.status/a');//是否禁用

		//进行处理
		$obj = $this->db->table('menus');
		foreach ($orders as $key => $value) {
			//组装数据
			$data['order'] 		= $value ? : 0;
			$data['title'] 		= $titles[$key];
			$data['controller'] = $controllers[$key];
			$data['method'] 	= $methods[$key];
			$data['ishidden'] 	= isset($isHiddens[$key]) ? 1 : 0;
			$data['status'] 	= isset($status[$key]) ? 1 : 0;
			$data['pid']		= $pid;

			//如果是默认为key值0并且菜单名称不为空的则直接添加
			if($key==0 && !empty($data['title'])){
				$obj->insert($data);
			}
			//如果key大于0则需要修改或者删除
			if($key > 0){
				//判断下如果当数据都为空则执行删除
				if( empty($data['title']) && empty($data['controller']) && empty($data['method']) ){
					//执行删除
					$obj->where(['id'=>$key])->delete();
				}else{
					//执行更新
					$obj->where(['id'=>$key])->update($data);
				}
			}
		}
		//返回
		return ['code'=>0,'msg'=>'保存成功'];
	}

}