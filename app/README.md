目录结构：

	/js
		/model	存放类、工具（class,model,ajax）
		/m		为页面建立数据模型 m_index.js
		/vm		视图模型 mui,event vm_index.js
		/tpl	模板引擎 new Vue() tpl_index.js	
		/obj	类对象  cart.js,order.js,goods.js
	
	/control主控制函数
	/vm		视图模型Vue,调用vue.js->vm模型，
	/ui		mui事件绑定，



页面加载顺序：

	1、	页面加载 index.html
	2、	创建数据模型（m_index.js）var cart=new Cart;
	3、	创建模板引擎（tpl_index.js）new Vue()
      	数据双向绑定
	4、	ajax获取数据，存入数据模型（m_index.js）
	5、	异步加载mui,处理页面事件（vm_index.js）
	
	index.html
	->  control.js
		->  page	= m.js,
		->	vm		= vm.js,
		->	ui.js
m_index 结构:
1、建立页面数据模型page
2、建立模板tpl
