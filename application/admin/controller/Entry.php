<?php
// +----------------------------------------------------------------------
// | Charles [ Keep going ]
// +----------------------------------------------------------------------
// | create time : 2018-05-09
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://charles.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: charles <13560336325@163.com>
// +----------------------------------------------------------------------
// | 说明: 登录控制器
// +----------------------------------------------------------------------

namespace app\admin\controller;
use think\Controller;
use Util\data\Sysdb;

class Entry extends Controller {

	//登录页面	
	public function login(){
		if(session('adminInfo')){
			//如果已经登录了直接跳转到首页
			header('Location:'.url('admin/Home/index'));
			exit();
		}
    	//渲染页面
    	return $this->fetch();
		
    }

    //后台验证登录方法
    public function doLogin(){
    	//接收参数
    	$username = input('post.username');
    	$password = input('post.password');
    	$verify = input('post.verify');
    	//检查数据
    	if( empty($username) ){
    		return ['code'=>1,'msg'=>'请输入用户名'];
    	}
    	if( empty($password) ){
    		return ['code'=>1,'msg'=>'请输入密码'];
    	}
    	if( empty($verify) ){
    		return ['code'=>1,'msg'=>'请输入验证码'];
    	}
    	//先检查验证码是否正确
    	if(!captcha_check($verify)){
    		return ['code'=>1,'msg'=>'验证码错误'];
    	}
    	//检查用户名是否正确
    	$this->db = new Sysdb;
    	$adminInfo = $this->db->table('admin')->where(['username'=>$username])->item();
    	if(!$adminInfo){
    		return ['code'=>1,'用户名或密码错误'];
    	}
    	//用户名正确后验证密码
    	if($adminInfo['password'] != MD5($username.$password) ){
    		return ['code'=>1,'msg'=>'用户名或密码错误'];
    	}
    	//判断管理员用户的状态是否锁定
    	if($adminInfo['status']==1){
    		return ['code'=>1,'msg'=>'管理员账户被锁定'];
    	}
    	//登录通过，存储session
    	session('adminInfo',$adminInfo);
        //获取管理员对应的菜单和权限
        $this->getAdminMenus();
    	return ['code'=>0,'msg'=>'登录成功，2秒后跳转'];
    }

     //登出方法
    public function logOut(){
        session('adminInfo',null);
        header('Location:'.url('admin/Entry/login'));
        exit();
    }

    //tp5验证码方法
    public function getVerify(){
    	$config = [
    		'codeSet'=>'0123456789',
    		'fontSize'=>15,
    		'length'=>4,
    		'useNoise'=>false,
    		'useCurve'=>false,
    		'bg'=>[243, 200, 200],
    	];
		$captcha = new \think\captcha\Captcha($config);
		return $captcha->entry();
    }

    /**
     * 定义一个获取管理员的菜单方法
     */
    private function getAdminMenus(){
        $menus = [];
        //获取session中的管理员角色id
        $gid = session('adminInfo.gid');
        if(!empty($gid)){
            //根据该管理员的角色id查询出对应的菜单权限
            $role = $this->db->table('groups')->where(['id'=>$gid])->item();
            //把角色的名称存入到session
            session('adminInfo.role',$role['title']);
            //把权限中的
            if(!empty($role['rules'])){
                //把对应的权限格式化成逗号隔开
                $rules = implode( ',' , json_decode($role['rules'],true) );
                //如果角色中有对应的权限不为空，则去查询出对应的menus菜单数据
                //获取出所有的非禁用的权限
                $tmp = $this->db->table('menus')->where(['id'=>['in',$rules],'status'=>0])->cates('id');
                //获取出所有不隐藏的权限做菜单
                $menus = $auths = [];
                foreach ($tmp as $key => $value) {
                    if($value['ishidden']==0){
                        $menus[$key] = $value;
                    }

                    if($value['controller'] && $value['method']){
                        //增加把controller和method合并,因为tp5的获取方法名全为小写，则把该权限的方法名也转为小写
                        $auths[] = $value['controller'].'/'.strtolower( $value['method'] );
                    }
                }
                $auths = array_unique($auths);
                $menus = getTreesCates($menus);
                //把对应的菜单和权限存入到session
                session('adminInfo.menus',$menus);
                session('adminInfo.auths',$auths);
            }
        }
    }

}