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
// | 说明: 后台基础类控制器
// +----------------------------------------------------------------------

namespace app\admin\controller;
use think\Controller;
use think\Request;
use Util\data\Sysdb;//使用自定义数据库操作类

class BaseAdmin extends Controller{
	protected $_menus;//定义一个存储对应管理员显示菜单的属性
	protected $db;//定义一个操作属性对象用于实例化自定义数据库类

	//构造方法
	public function __construct(){
		parent::__construct();
		//检查是否有登录
		if(empty(session('adminInfo'))){
			header('Location:'.url('admin/Entry/login'));
			exit();
		}

		//赋值菜单
		if(!empty(session('adminInfo.menus'))){
			$this->_menus = session('adminInfo.menus');
		}

		//检查权限控制
		if(!checkAuth()){
			return ['code'=>1001,'msg'=>'您没有该权限'];
		}
		
		//实例化自定义数据库类
		$this->db = new Sysdb();
	}

}