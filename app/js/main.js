var base={
	_url	: 'http://10.0.0.130/apps/bmbs/',
	//_url	: 'http://bmbs.xnbshop.com/',
	url	: 'http://10.0.0.130/test/mui-vue/bin/',
	//_url	: 'https://www.banmaibansong.com/'
}
function openPage(_){
	mui.plusReady(function(){
		var id=_;//.id;
		if(!id) return;
		var path=id.split('-');
		var type=path[1]||null;
		var name=path[0].split('_')
		var webviewId=name.length==1?path[0]:(name[0].substr(0,1).toUpperCase()+name[1].substr(0,1));
		toPage();
		function toPage(){
			var options={
				id: webviewId,
				url: 'page/'+path[0]+'.html?type='+type,
			}
			mui.openWindow(options);
		}
	})
}
Math.guid=function(){
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {    
	 	var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);    
	 	return v.toString(16);  
	}).toUpperCase();
}
