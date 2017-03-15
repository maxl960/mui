define(function(require){
	require('mui');
	console.log(mui('.mui-scroll-wrapper'))
	mui('.mui-scroll-wrapper').scroll({
		scrollX: false,
		scrollY:true,
 		deceleration:0.0006, 
	});
})
