define(function(require){
	require('mui');
	mui('#tabs').scroll({
		scrollX: true,
		scrollY:false,
 		deceleration:0.0006, 
	});
	//console.log(mui('#tabs'))
	/*mui('.mui-scroll-wrapper').scroll({
		scrollX: false,
		scrollY:true,
 		deceleration:0.0006, 
	});*/
	//console.log('mui')
})
