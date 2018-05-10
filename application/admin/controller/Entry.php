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

namespace app\admin\Controller;
use think\Controller;

class Entry extends Controller {

	//登录页面	
	public function login(){
    	//渲染页面
    	return $this->fetch();
		
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
}