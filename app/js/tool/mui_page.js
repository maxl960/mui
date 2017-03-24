define(function(require){
	require('../mui');
	function scroll(){
		mui.plusReady(function(){
			var H = plus.screen.resolutionHeight;
			var head=mui('header')[0].offsetHeight;
			var nav=mui('nav')[0].offsetHeight;
			var tab=mui('#tabs')[0].offsetHeight;
			var item=mui('.mui-slider-item')[0].setAttribute('style','height:'+(H-head-nav-tab)+'px;position:relative;');
			/*if(!page.list) return false; 
			if(len(page.list.m.tab)!=1){
				mui('.mui-slider-group')[0].setAttribute('style','height:'+H+'px');
			}else{
				mui('.mui-slider-item')[0].setAttribute('style','height:'+H+'px;position:relative;');
			}*/
		})
	}
	
	return {
		scroll: scroll
	}
})
