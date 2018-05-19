<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 使用指针算法把数组整改为带无限极分类children的数组数据
 * @param array $items 带有pid的数组数据
 */
function getTreesCates($items){
	$result = [];
	foreach($items as $item){
		if( isset($items[$item['pid']]) ){
			//如果有数据则把对应的数据增加children键值
			$items[$item['pid']]['children'][] = &$items[$item['id']];
		}else{
			//没有则是一级
			$result[] = &$items[$item['id']];
		}
	}
	return $result;
}

/**
 * 将无限极的权限children数据数组整改为之分两级的数组数据
 * @param array $children 数组的子菜单权限数组数据
 * @param array $result 返回的数据数组使用&直接指向堆区
 */
function formatTrees( $children , &$result = array() ){
	foreach($children as $cc){
		if( !isset($cc['children']) ){
			//如果没有子类则直接整合到result
			$result[] = $cc;
		}else{
			//判断如果有children子类的数据则继续递归该函数把子集整合到一起
			$tmp = $cc['children'];//先临时保存children数组数据
			unset($cc['children']);//删除
			$result[] = $cc;
			formatTrees($tmp,$result);
		}
	}
	return $result;
}

/**
 * 检查权限函数
 */
function checkAuth($current=''){
	if(empty($current)){
		$request = think\Request::instance();
		$current = $request->controller() . '/' . $request->action();
	}
	$auths = session('adminInfo.auths') ? : [];
	//定义不用验证的控制器方法
	$freeToGo = array(
		'Home/index',
		'Home/welcome',
	);
	//合并权限
	$auths = array_merge($auths,$freeToGo);
	if(!in_array($current , $auths)){
		return false;
	}
	return true;
}