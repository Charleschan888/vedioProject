<?php
// +----------------------------------------------------------------------
// | Charles [ Keep going ]
// +----------------------------------------------------------------------
// | create time : 2018-05-19
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://charles.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: charles <13560336325@163.com>
// +----------------------------------------------------------------------
// | 说明: 后台网站设置控制器
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Site extends BaseAdmin{

	/**
	 * 网站配置设置页面
	 */
	public function index(){
		//获取出所有的网站配置数据
		$tmp = $this->db->table('site')->cates('name');
		$config = [];
		foreach ($tmp as $key => $value) {
			$config[$key] = json_decode($value['value'],true);
		}
		//渲染模板
		$this->assign('config',$config);
		return $this->fetch();
	}

	/**
	 * 保存网站配置
	 */
	public function save(){
		//接收所有post过来的数据
		$post = input('post.');
		if(!empty($post)){
			$siteModel = $this->db->table('site');
			//循环配置 key为name字段值，value为对应的值
			foreach ($post as $key => $value) {
				if($siteModel->where(['name'=>$key])->item()){
					//有则更新操作
					$siteModel->where(['name'=>$key])->update(['value'=>json_encode($value)]);
				}else{
					//如果没有则录入
					$siteModel->insert(['name'=>$key,'value'=>json_encode($value)]);
				}
			}
			return ['code'=>0,'msg'=>'保存成功'];
		}
		return ['code'=>1,'msg'=>'没有接收到配置数据'];
	}

}