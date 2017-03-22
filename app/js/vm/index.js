define(function(require,exports,module){
	var page=require('../m/index');
	require('vue');
	var vm=new Vue({
		el: '#body',
		data: page
	});
	return vm;
})
