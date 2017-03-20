define(function(require){
	var Model=require('model')
	var Page=function(){}
	Page.prototype={
		child: function(domObj){
			for(var i in domObj){
				this[i]=domObj[i];
			}
		}
	}
	return Page;
})
