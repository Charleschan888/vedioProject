layui.use(['table','form'],function(){
	var table = layui.table;
	var layer = layui.layer;
	$ = layui.jquery;
	var form = layui.form;

	form.on('checkbox(is_show)',function(data){
		//获取当前幻灯片的id
		var _id = $(this).parents('tr').data('id');
		$.post('/index.php/admin/Slide/changeShow',{'id':_id},function(res){
			if(res.code==0){
				//修改成功
				layer.msg(res.msg);
			}else{
				//修改失败
				layer.alert(res.msg);
			}
			//根据返回来的is_show字段修改是否选中效果
			if(res.is_show==0){
				$(this).attr('checked',false);
			}else{
				$(this).attr('checked',true);
			}
			//重新渲染checkbox
			form.render('checkbox');
			$(this).attr('disabled',false);
		},'json');
	});

	//排序
	$('input.blurOrder').on('blur',function(){
		//获取修改的值
		var _order = parseInt($(this).val());
		if(_order>100) _order = 100;//最大为100
		var _id = $(this).parents('tr').data('id');
		$.post('/index.php/admin/Slide/changeOrder',{'id':_id,'order':_order},function(res){
			if(res.code==0){
				//修改成功
				layer.msg(res.msg);
			}else{
				//修改失败
				layer.alert(res.msg);
			}
		},'json');
	})

});

//定义一个打开页面添加或编辑管理员
function addEditPage(id,type){
	//打开iframe层
	var _t1 = layer.open({
	  type: 2,
	  title: id>0?'编辑幻灯片':'添加幻灯片',
	  shade: 0.3,
	  content: '/index.php/admin/Slide/addEditPage?id='+id+'&type='+type //iframe的url
	});
	//全屏打开
	layer.full(_t1);
}

//定义一个删除管理员的函数
function removeAdmin(id){
	if(id){
		//弹出confirm框确定是否删除
		layer.confirm('确定要删除幻灯片吗？', function(index){
			$.post("/index.php/admin/Slide/delete",{'id':id},function(res){
				if(res.code==0){
					//删除成功
					layer.msg(res.msg,{
						icon:1,
						time:1500,
						shadeClose:false,
					},function(){
						window.location.reload();
					})
				}else{
					//删除失败
					layer.alert(res.msg);
				}
			},'json');
		}); 
	}
}