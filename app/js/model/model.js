define(function(require){
	require('class');
	var Model=new Class;
	Model.extend({
		create: function(){
			return new Model
		}
	})
	Model.include({
		init: function(){
			this.record={};
		},
		save: function(data){
			var records=data;
			var guid=data.id?data.id:Math.guid();
			this.record[guid]=data;
		}
	});
	return Model;
	/*var m=function(){
		//this.record={}
	}
	m.prototype={
		//record: {},
		init: function(){
			t
		},
		save: function(){
			var records=this.record;
			var guid=Math.guid();
			this.record[guid]=records;
		}
	}
	return m;*/
	/*if(typeof Object.create!='function'){
		Object.create=function(o){
			var F=function(){};
			F.prototype=o;
			return new F();
		}
	}
	var Model={
		prototype: {
			init: function(){}
		},
		create: function(){
			var obj=Object.create(this);
			obj.parent=this;
			obj.prototype=Object.create(this.prototype);
			return obj;
		},
		init: function(){
			var instance=Object.create(this.prototype);
			instance.parent=this;
			instance.init.apply(instance,arguments);
			return instance;
		},
		include: function(o){
			for(var i in o){
				this.prototype[i]=o[i];
			}
		}
	}
	Model.include({
		//命名空间存放模型数据
		record: {},
		//
		init: function(atts){
			if(atts) this.load(atts);
		},
		//
		load: function(attributes){
			for(var name in attributes){
				this[name]=attributes[name]
			}
		},
		//查询记录
		find: function(id){
			var record=this.record[id];
			if(record){
				console.log(record);
			}else{
				console.log('没有这条记录');
			}
		},
		show: function(){
			console.log(this.record)
		},
		//保存记录
		save: function(record){
			var records=this.record;
			var guid=Math.guid();
			this.record[guid]=record;
		}
	})
	return Model;*/
})
