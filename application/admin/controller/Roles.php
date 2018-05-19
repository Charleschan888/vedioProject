<?php
// +----------------------------------------------------------------------
// | Charles [ Keep going ]
// +----------------------------------------------------------------------
// | create time : 2018-05-18
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://charles.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: charles <13560336325@163.com>
// +----------------------------------------------------------------------
// | 说明: 角色控制器
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Roles extends BaseAdmin{
	/**
	 * 角色列表
	 */
	public function index(){
		//获取出所有的角色
		$data['roles'] = $this->db->table('groups')->lists();
		//渲染模板
		$this->assign('data',$data);
		return $this->fetch();
	}

	/**
	 * 添加或编辑角色的方法
	 */
	public function addEditRole(){
		//接收get过来的角色id
		$id = (int)input('get.id');
		//获取对应角色的数据
		$roleInfo = $this->db->table('groups')->where(['id'=>$id])->item();
		//判断是否有rules
		$roleInfo['rules'] = isset($roleInfo['rules']) ?  json_decode($roleInfo['rules']) : [];

		//获取所有的权限菜单 查询出除了禁用之外的权限数据，id做为键值
		$rules = $this->db->table('menus')->where(['status'=>0])->cates('id');
		//使用无限极分类函数把权限处理成带children的数组数据
		$rules = getTreesCates($rules);
		//把无限极分类整改为二级分类
		$fomatRules = [];
		foreach ($rules as $rule) {
			if(isset($rule['children'])){
				$rule['children'] = formatTrees($rule['children']);
			}else{
				$rule['children'] = false;
			}
			$fomatRules[] = $rule;
		}
		//渲染模板
		$this->assign('roleInfo',$roleInfo);
		$this->assign('rules',$fomatRules);
		return $this->fetch();
	}

	/**
	 * 处理添加或编辑角色方法
	 */
	public function addRoleSave(){
		//接收id
		$id = (int)input('post.id');
		//接收角色名称
		$data['title'] = input('post.title');
		//检测下是否有重复的角色名称
		$db = $this->db->table('groups');
		//当id==0新增数据时才需要判断有误重复角色名称
		$id==0 && $is_have = $db->where(['title'=>$data['title']])->item();
		if(!empty($is_have)){
			return ['code'=>1,'msg'=>'角色名有重复'];
		}
		//接收传递过来的权限
		$rules = input('post.rules/a');
		//把接收到的权限键值json格式化
		isset($rules) && $data['rules'] = json_encode( array_keys($rules) );
		//判断是新增还是修改
		if($id>0){
			//执行修改
			$res = $db->where(['id'=>$id])->update($data);
		}else{
			//执行保存数据
			$res = $db->insert($data);
		}
		if(!$res){
			return ['code'=>1,'msg'=>'保存失败'];
		}
		return ['code'=>0,'msg'=>'保存成功'];
	}


	/**
	 * 删除角色方法
	 */
	public function delete(){
		//接收id
		$id = (int)input('post.id');
		//执行删除
		$res = $this->db->table('groups')->where(['id'=>$id])->delete();
		if($res>0){
			return ['code'=>0,'msg'=>'删除成功'];
		}else{
			return ['code'=>1,'msg'=>'删除失败'];
		}
	}

}