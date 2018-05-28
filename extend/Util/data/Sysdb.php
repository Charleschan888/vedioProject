<?php
// +----------------------------------------------------------------------
// | Charles [ Keep going ]
// +----------------------------------------------------------------------
// | create time : 2018-05-10
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://charles.top All rights reserved.
// +----------------------------------------------------------------------
// | Author: charles <13560336325@163.com>
// +----------------------------------------------------------------------
// | 说明: 自封装db操作类
// +----------------------------------------------------------------------

namespace Util\data;
use think\Db;

class Sysdb{
	/**
	 * 封装指定表的方法
	 * @param string $table 表名称
	 * @return mixed 如果有表名正常则返回自身对象，链式操作需要返回自身对象
	 */
	public function table($table=''){
			//当使用table方法时需要重置where和filed默认值
			$this->where = array();
			$this->field = '*';
			$this->order = '';

			//把表名存入到该类对象属性
			$this->table = $table;
			return $this;
	}

	/**
	 * 封装查询指定字段方法
	 * @param string $fileds 查询的字段字符串，使用,隔开
	 * @return obj 返回自身
	 */
	public function field($field='*'){
		$this->field = $field;
		return $this;
	}

	/**
	 * 封装where查询条件
	 * @param mixed $where 查询条件
	 * @return obj 返回自身
	 */
	public function where($where = array() ){
		$this->where = $where;
		return $this;
	}

	/**
	 * 排序方法
	 */
	public function order($order = '' ){
		$this->order = $order;
		return $this;
	}

	/**
	 * 封装查询单条数据方法
	 * @return array 查询正常应该返回数组数据，查询为空应返回false
	 */
	public function item(){
		//使用TP5的db操作 tp5的find方法默认查询错误则返回null
		$item = Db::name( $this->table )->field( $this->field )->where( $this->where )->find();
		return $item ? $item : false;
	}

	/**
	 * 封装查询多条数据方法
	 * @return array 查询正常应该返回二维数组数据，查询为空应返回false
	 */
	public function lists(){
		//使用TP5的db操作 tp5的select方法默认查询错误则返回null
		$query = Db::name( $this->table )->field( $this->field )->where( $this->where );
		//如果有排序则增加排序
		!empty($this->order) && $query = $query->order($this->order);
		$lists = $query->select();
		return $lists ? $lists : false;
	}

	/**
	 * 封装查询多条数据并把制定字段当键值
	 * @param string $index 做键值用的字段名称，必须是查询出的filed里面有的字段
	 * @return mixed 返回查询结果，如果为null返回false
	 */
	public function cates($index){
		$query = Db::name( $this->table )->field( $this->field )->where( $this->where );
		!empty($this->order) && $query = $query->order($this->order);
		$lists = $query->select();
		if(empty($lists)){
			return false;
		}
		$result = [];
		foreach ($lists as $key => $value) {
			$result[$value[$index]] = $value;
		}
		return $result ? $result : false;
	}

	/**
	 * 封装录入方法
	 * @param array $data 录入的数据
	 * @return int 返回录入后的结果
	 */
	public function insert($data){
		$res = Db::name( $this->table )->insert($data);
		return $res;
	}

	/**
	 * 封装更新方法
	 * @param array $data 需要更新的数据
	 * @return int 返回更新后的结果
	 */
	public function update($data){
		$res = Db::name( $this->table )->where( $this->where )->update($data);
		//当结果为false则更新失败，如果为0则没有更新任何数据也返回true
		return $res===false ? false : true;
	}

	/**
	 * 封装删除数据方法
	 * @return int 返回删除后的结果
	 */
	public function delete(){
		$res = Db::name( $this->table )->where( $this->where )->delete();
		return $res;
	}

	/**
	 * 封装数据自增方法
	 * @param string $colum 需要增加数量的字段名称
	 * @param intval $num 需要增加的数值，默认为1
	 * @return int 返回增值后的结果
	 */
	public function inc( $column , $num = 1){
		$res = Db::name( $this->table )->where( $this->where )->setInc($column , $num);
		return $res;
	}

	/**
	 * 封装数据自减方法
	 * @param string $colum 需要减少数量的字段名称
	 * @param intval $num 需要减少的数值，默认为1
	 * @return int 返回减少后的结果
	 */
	public function dec( $column , $num = 1){
		$res = Db::name( $this->table )->where( $this->where )->setDec($column , $num);
		return $res;
	}

	/**
	 * 分页查询方法
	 * @param intval $perpage 每页显示条数
	 * @return array $return 返回查询到的数据 包括totle总条数 lists数据 pages分页数据
	 */
	public function pages($perpage = 10){
		//查询出对应的总数
		$totle = Db::name($this->table)->where($this->where)->count();
		$query = Db::name($this->table)->where($this->where);
		//如果有排序数据则
		!empty($this->order) && $query->order($this->order);
		//使用paginate方法查询出分页查询
		$lists = $query->paginate($perpage,$totle);

		return $lists ? ['totle'=>$totle,'lists'=>$lists->items(),'pages'=>$lists->render()] : false;
	}
}