<?php
namespace app\index\controller;
use think\Controller;
use Util\data\Sysdb;

class Index extends Controller
{
	/**
	 * 构造方法
	 * @return [type] [description]
	 */
	public function __construct(){
		parent::__construct();
		//new一个自封装的db类
		$this->db = new Sysdb();
	}

	/**
	 * 首页
	 * @return [type] [description]
	 */
    public function index()
    {
    	//获取首屏的幻灯片数据
    	$data['index_slide'] = $this->db->table('slide')->where(['type'=>0,'is_show'=>1])->lists();
    	//获取标签
    	/*$labelModel = $this->db->table('labels')->order(['order','id'=>'asc']);
    	//获取出8个频道标签
    	$data['channel_list'] = $labelModel->where(['flag'=>'channel','status'=>0])->pages(8)['lists'];
    	//获取出地区标签
    	$data['area_list'] = $labelModel->where(['flag'=>'area','status'=>0])->pages(8)['lists'];
    	//获取分类标签
    	$data['cate_list'] = $labelModel->where(['flag'=>'cate','status'=>0])->pages(12)['lists'];*/
    	//获取所有的标签数据
    	$data['labels'] = getLabelsCate();
    	//获取影片数据
    	$vm = $this->db->table('vedio');
    	//分别获取点击量最高 根据pv值排序取出
    	$data['vedio_hot'] = $vm->where(['status'=>1,'pid'=>0])->order('pv desc')->pages(6)['lists'];
    	//取出最新的5条 默认为id最新的
    	$data['vedio_new'] = $vm->where(['status'=>1,'is_vip'=>0,'pid'=>0])->order('id desc')->pages(5)['lists'];
    	//取出vip的最新6条视频
    	$data['vedio_vip'] = $vm->where(['status'=>1,'is_vip'=>1,'pid'=>0])->order('id desc')->pages(6)['lists'];
    	//默认获取综艺 电影类数据
    	$data['vedio_zy'] = $vm->where(['status'=>1,'pid'=>0,'channel_id'=>3])->order('id desc')->pages(3)['lists'];
    	//电影影片
    	$data['vedio_movie'] = $vm->where(['status'=>1,'pid'=>0,'channel_id'=>2])->order('id desc')->pages(3)['lists'];
    	//高评分5部
    	$data['vedio_score'] = $vm->where(['status'=>1,'pid'=>0])->order('score desc')->pages(5)['lists'];
    	//取出10个排序最前的标签
    	$data['tags'] = $this->db->table('labels')->where(['status'=>0])->order(['order'=>'desc'])->pages(10)['lists'];
    	// dump($data['vedio_hot']);die;
    	$this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 分类页
     */
    public function cate(){
    	$data['page'] = max(1,(int)input('param.page'));
    	//获取影片数据 实例化数据表模型
    	$vm = $this->db->table('vedio');

    	//默认where条件 状态为正常并且是一级的
    	$where['status'] = 1;
    	$where['pid'] = 0;

    	//拼接where条件
    	$data['channel_id'] = (int)input('param.channel_id');
    	$data['charge_id'] = (int)input('param.charge_id');
    	$data['area_id'] = (int)input('param.area_id');
    	$data['cate_id'] = (int)input('param.cate_id');

    	!empty($data['channel_id']) && $where['channel_id'] = $data['channel_id'];
    	!empty($data['charge_id']) && $where['charge_id'] = $data['charge_id'];
    	!empty($data['area_id']) && $where['area_id'] = $data['area_id'];
    	!empty($data['cate_id']) && $where['cate_id'] = $data['cate_id'];
    	
    	//分页一页显示多少条
    	$data['pageSize'] = 5;
    	$data['data'] = $vm->where($where)->order(['id'=>'desc'])->pages($data['pageSize']);

    	//获取所有的标签数据
    	$data['labels'] = getLabelsCate();

    	//侧边栏数据
    	//默认获取综艺 电影类数据
    	$data['vedio_zy'] = $vm->where(['status'=>1,'pid'=>0,'channel_id'=>3])->order('id desc')->pages(3)['lists'];
    	//电影影片
    	$data['vedio_movie'] = $vm->where(['status'=>1,'pid'=>0,'channel_id'=>2])->order('id desc')->pages(3)['lists'];
    	//取出10个排序最前的标签
    	$data['tags'] = $this->db->table('labels')->where(['status'=>0])->order(['order'=>'desc'])->pages(10)['lists'];

    	//把标签的id使用数组记录下来
    	$data['channel_ids'] = array_keys($data['labels']['channel']);
    	$data['charge_ids'] = array_keys($data['labels']['charge']);
    	$data['area_ids'] = array_keys($data['labels']['area']);
    	$data['cate_ids'] = array_keys($data['labels']['cate']);
    	// dump($data);die;
    	//渲染模板
    	$this->assign('data', $data);
    	return $this->fetch();
    }

    /**
     * 播放详情页
     */
    public function play(){
    	//接收影片的id
    	$data['id']= (int)input('param.id');
    	//获取当前播放子集id
    	$data['cur_id'] = (int)input('param.cur_id');
    	//实例化数据库
    	$vm = $this->db->table('vedio');
    	//获取对应影片的数据
    	$data['info'] = $vm->where(['id'=>$data['id']])->item();
    	//查询下有无子集
    	if($data['info']['is_sub']==1){
    		//获取出子集的视频数据id
    		$data['sub_vedio'] = $vm->where(['pid'=>$data['id']])->order(['id'=>'asc'])->cates('id');
    		$data['sub_ids'] = array_column($data['sub_vedio'], 'id');
	    	//保存当前需要播放的影片数据 
	    	//如果传过来的子集id不为空 则对应播放子集内容影片 否则默认播放第一集
	    	if(!empty($data['cur_id']) ){
	    		$data['cur_vedio'] = $data['sub_vedio'][$data['cur_id']];
	    	}else{
	    		//没有子集id 则默认播放第一个数据
	    		$data['cur_vedio'] = reset($data['sub_vedio']);
	    	}
    	}else{
    		//非有子集影片 默认播放影片则是自己
    		$data['cur_vedio'] = $data['info'];
    	}
    	//更新当前播放影片的pv值
    	$vm->where(['id'=>$data['cur_vedio']['id']])->inc( 'pv', 1);
    	// dump($data);die;
    	//渲染模板
    	$this->assign('data', $data);
    	return $this->fetch();
    }

}
