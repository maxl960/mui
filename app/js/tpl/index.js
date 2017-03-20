define(function(require,exports,module){
	require('vue');
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
	Vue.directive('complate',{
		update: function(el,binding,vnode){
			mui('#tabs').scroll({
				scrollX: false,
				scrollY:true,
		 		deceleration:0.0006, 
			});
		}
	})
	var tpl=new Vue({
		el: '#body',
		data: {
			//tab: {},
			nav: nav,
		},
		update: function(){
			mui('#tabs').scroll({
				scrollX: true,
				scrollY:false,
		 		deceleration:0.0006, 
			});
		}
	});
	return tpl;
})