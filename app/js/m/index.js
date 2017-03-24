define(function(require){
	//建立页面入口数据page
	var Page=require('../model/page');
	var page=new Page;
	var nav={
		index: {
			label: '首页'
		},
		active: {
			label: '活动'
		},
		shop: {
			label: '购物'
		},
		my: {
			label: '我的'
		}
	}
	
	var d={
		title: '首页',
		catory: {},
		nav: nav,
	}
	page.child(d);
	
	//ajax载入数据
	require('main');
	var ajax=require('ajax');
	//加载商品分类
	ajax_post(ajax,'api/category/get_allcategorys',{},function(data){console.log(data)
		var cateorys=data.datas.categorys
		var catory={}
		for(var i in cateorys){
			catory[cateorys[i].id]=cateorys[i];
			catory[cateorys[i].id].goods={};
			getgl(cateorys[i].id)
		}
		page.catory=catory;
	})
	//按分类加载商品
	function getgl(id){
		ajax_post(ajax,'api/goods/get_goodslist',{catid: id,page: 1},function(data){
			page.catory[id].goods=data.datas.goodslist;
		})
	}
	return page
	/*
	
	
	//创建模板引擎
	var tpl=require('../tpl/index');
	//ajax载入数据
	var ajax=require('ajax');*/
	
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
	*/
})
