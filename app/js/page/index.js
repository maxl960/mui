define(function(require,exports,module){
	//require('mui');
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
	$.post(base.url+'category/show',{},function(data){
		console.log(data)
		tabs.tab=data;
	},'json')
})