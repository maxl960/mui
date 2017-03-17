define(function(require,exports,module){
	require('vue');
	/*Vue.directive('complate',{
		update: function(el,binding,vnode){
			mui('.mui-scroll-wrapper').scroll({
				scrollX: false,
				scrollY:true,
		 		deceleration:0.0006, 
			});
		}
	})
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
	});
	return tabs;*/
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
	Vue.component('navbar',{
		props: ['nav','test'],
		template: '#nav-tpl',
	})
	var page=new Vue({
		el: '#page',
		data: {
			cateory: {},
			nav: nav,
			test: 'index'
		}
	});
	console.log(JSON.parse(JSON.stringify(nav)))
})