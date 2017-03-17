define(function(require,exports,module){
	var obj={}
	Object.defineProperties(obj,{
		hello: {
			set: function(val){
				document.getElementById('b').innerHTML=val;
			}
		}
	})
	document.addEventListener('keyup',function(e){
		obj.hello=e.target.value;
	})
})
