define(function(){
	function creXHR(){
		if(typeof XMLHttpRequest!="undefined"){
			return new XMLHttpRequest();
		}
	}
	var $=$||{};
	$.ajax=function (type,url,data,success){
		var data=data||null;
		var type=type||'get'
		var xhr=new creXHR();
		xhr.onreadystatechange=function(){
			console.log('change');
		}
		xhr.open(type,url,true);
		//xhr.open('post','https://www.banmaibansong.com/api/goods/get_goodslist',true);
		//xhr.setRequestHeader('Origin','http://10.0.0.130');
		xhr.send(data);
		
		xhr.onreadystatechange=function(){
			if(xhr.readyState==4){
				if(xhr.status>=200&&xhr.status<300||xhr.status==304){
					var js=JSON.parse(xhr.responseText);
					if(success) success(js);
				}
			}
		}
	}
	$.post=function(url,data,success){
		$.ajax('post',url,data,success)
	};
	$.get=function(url,data,success){
		$.ajax('get',url,data,success)
	};
	return $
})
