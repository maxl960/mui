define(function(require,exports,module){
	require('vue');
	Vue.directive('complate',{
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
	return tabs;
})