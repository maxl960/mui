define(function(require,exports,module){
	require('vue');
	/*Vue.directive('complate',{
		update: function(el,binding,vnode){
			mui('#tabs').scroll({
				scrollX: false,
				scrollY:true,
		 		deceleration:0.0006, 
			});
		}
	})*/
	var tpl=new Vue({
		el: '#body',
		data: {
			//tab: {},
			nav: nav,
		},
		/*update: {
			mui('#tabs').scroll({
				scrollX: true,
				scrollY:false,
		 		deceleration:0.0006, 
			});
		},*/
		methods: {
			Show: function(){
				console.log(this.$data.nav)
			}
		}
	});
	return tpl;
})