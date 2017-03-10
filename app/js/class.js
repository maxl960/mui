var Class=function(parent){
	var klass=function(){
	}
	if(parent){
		var subclass=function(){};
		subclass.prototype=parent.prototype;
		klass.prototype=new subclass;
	}
	klass.fn=klass.prototype;
	//klass.fn.parent=klass;
	klass.extend=function(obj){
		for(var i in obj){
			klass[i]=obj[i];
		}
	}
	
	klass.include=function(obj){
		for(var i in obj){
			klass.fn[i]=obj[i]
		}
	}
	return klass;
}
/*var Person=new Class;

var person=new Person;
person.init();*/
/*if (typeof define === 'function' && define.amd) {
	define('Class', [], function() {
		return Class;
	});
}*/