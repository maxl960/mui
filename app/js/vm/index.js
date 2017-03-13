define(function(require,exports,module){
	var ajax=$=require('ajax');
	var base=require('base');
	var tabs = new Vue({
  		el: '#slider',
  		data: {
    		tab: {
    			test: {
					type: 'hh'
				},
				ad: {
					type: 'ad'
				}
    		}
  		}
	})
	$.post(base._url+'api/home/get_infolist',{},function(data){
		console.log(data)
		//tabs.tab=data;
	},'json');
	/*require('main');
	var ajax=$=require('ajax');
	var base=require('base');
	var model=require('model');
	var tabs = new Vue({
  		el: '#slider',
  		data: {
    		tab: {
    			test: {
					type: 'hh'
				},
				ad: {
					type: 'ad'
				}
    		}
  		}
	})
	$.post(base.url+'category/show',{},function(data){
		//console.log(data)
		tabs.tab=data;
	},'json');
	//创建购物车模型
	var Cart=model.create();
	Cart.include({
		save: function(goods){
			var records=this.record;
			
			this.record[goods.id]=goods;
		}
	})
	var cart=Cart.init({
		record: {
			0: 'cord 1',
			1: 'cord 2',
		}
	});
	cart.save({
		id: '123-645',
		name: '枕套',
		price: '12.00'
	})
	cart.show();*/
})