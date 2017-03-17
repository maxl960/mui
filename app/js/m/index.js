define(function(require){
	//建立数据模型
	/*var Page=require('../model/page');
	var page=new Page;
	console.log(page.show)
	page.show();*/
	var model=require('model');
	var User=model.create();
	var user=User.init();
	console.log(User)
	/*var model=require('model');
	require('main');
	var page=require('../model/page');
	console.log(page.child)
	var Cart=model.create();
	var Goods=model.create();*/
	/*Cart.save({
		id: '123-645',
		name: '枕套',
		price: '12.00'
	})
	//cart.show();
	Goods.save(22);
	//gl.show();
	console.log(Cart.record);
	console.log(Goods.record);*/
	//创建模板引擎
	/*var tabs=require('../tpl/index');
	
	var ajax=require('ajax');
	ajax_post(ajax,'api/category/get_allcategorys',{},function(data){
		var cateorys=data.datas.categorys
		var cateory={}
		for(var i in cateorys){
			cateory[cateorys[i].id]=cateorys[i];
			cateory[cateorys[i].id].goods={};
			console.log(cateorys[i].id)
			getgl(cateorys[i].id)
		}
		tabs.tab=cateory;
	})
	function getgl(id){
		ajax_post(ajax,'api/goods/get_goodslist',{catid: id,page: 1},function(data){
			tabs.tab[id].goods=data.datas.goodslist;
		})
	}*/
})
