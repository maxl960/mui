define(function(require){
	//
	require('main');
	//建立数据模型
	var Page=require('../model/page');
	var page=new Page;
	
	//创建模板引擎
	var tpl=require('../tpl/index');
	//ajax载入数据
	var ajax=require('ajax');
	
	/*(ajax_post(ajax,'api/category/get_allcategorys',{},function(data){
		var cateorys=data.datas.categorys
		var cateory={}
		for(var i in cateorys){
			cateory[cateorys[i].id]=cateorys[i];
			cateory[cateorys[i].id].goods={};
			//console.log(cateorys[i].id)
			//getgl(cateorys[i].id)
		}
		tpl.tab=cateory;
	})
	function getgl(id){
		ajax_post(ajax,'api/goods/get_goodslist',{catid: id,page: 1},function(data){
			tpl.tab[id].goods=data.datas.goodslist;
		})
	}*/
	//mui事件处理
	var dom=require('../vm/index');
})
