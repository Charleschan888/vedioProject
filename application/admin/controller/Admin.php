<?php
// +----------------------------------------------------------------------
// | Charles [ Keep going ]
// +----------------------------------------------------------------------
// | create time : 2018-05-15
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://charles.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: charles <13560336325@163.com>
// +----------------------------------------------------------------------
// | 说明: 管理员管理控制器
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Admin extends BaseAdmin{
	/**
	 * 管理员列表主页
	 */
	public function index(){
		//获取出管理员数据
		$data['lists'] = $this->db->table('admin')->lists();
		//获取出键值为id的角色数据数组
		$data['groups'] = $this->db->table('groups')->field('id,title')->cates('id');
		//渲染模板
		$this->assign('data',$data);
		return $this->fetch();
	}

	/**
	 * 添加或编辑管理员页面
	 */
	public function addEditPage(){
		//获取id 如果有id则是编辑 如果是没有则默认为0
		$id = (int)input('get.id');
		//获取对应的管理员数据
		$data['info'] = $this->db->table('admin')->where(['id'=>$id])->item();
		//获取角色数据
		$data['groups'] = $this->db->table('groups')->field('id,title')->lists();
		//渲染模板
		$this->assign('data',$data);
		return $this->fetch();
	}

	/**
	 * 执行添加或编辑管理员
	 */
	public function addEditHandle(){
		//接收数据
		$id = (int)input('post.id');
		$data['username'] = trim(input('post.username'));
		$password = trim(input('post.password'));
		$data['truename'] = trim(input('post.truename'));
		$data['status'] = input('post.status') ? 1 : 0 ;
		$data['gid'] = (int)input('post.gid');

		//检测数据
		if( empty($data['username']) ){
			return ['code'=>1,'msg'=>'用户名不能为空'];
		}
		//当如果是添加则需要验证密码是否为空
		if( $id==0 && empty($password) ){
			return ['code'=>1,'msg'=>'密码不能为空'];
		}
		if( empty($data['truename']) ){
			return ['code'=>1,'msg'=>'真实姓名不能为空'];
		}
		if( empty($data['gid']) ){
			return ['code'=>1,'msg'=>'必须选择所属角色'];
		}

		//判断一下是否有相同的用户名
		$item = $this->db->table('admin')->field('id')->where(['username'=>$data['username']])->item();
		if($id==0 && !empty($item)){
			return ['code'=>1,'msg'=>'已有相同的用户名'];
		}

		//处理录入的密码 md5加密
		if(!empty($password)){
			$data['password'] = MD5($data['username'].$password);
		}
		//判断是录入还是更新
		if($id>0){
			//进行更新
			$res = $this->db->table('admin')->where(['id'=>$id])->update($data);
		}else{
			//进行录入
			$data['add_time'] = time();
			$res = $this->db->table('admin')->insert($data);
		}
		if(!$res){
			return ['code'=>1,'msg'=>'保存失败'];
		}
		return['code'=>0,'msg'=>'保存成功'];
	}

	/**
	 * 删除管理员方法
	 */
	public function delete(){
		//接收id
		$id = (int)input('post.id');
		$res = $this->db->table('admin')->where(['id'=>$id])->delete();
		if($res>0){
			return ['code'=>0,'msg'=>'删除成功'];
		}else{
			return ['code'=>1,'msg'=>'删除失败'];
		}
	}
}