define(function(require){
	//建立数据模型
	var page=require('../m/index');
	//建立vm模型
	var vm=require('../vm/index');
	//加载ui事件
	require('../ui/index');
	var muiPage=require('../tool/mui_page');
	muiPage.scroll();
})
