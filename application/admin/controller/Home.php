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
// | 说明: 后台主页控制器
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Home extends BaseAdmin{

	/**
	 * 后台首页
	 */
	public function index(){
		//渲染模板
		$this->assign('menus',$this->_menus);
		return $this->fetch();
	}

	/**
	 * 后台首页欢迎页面
	 */
	public function welcome(){
		//渲染模板
		return $this->fetch();
	}

	
}