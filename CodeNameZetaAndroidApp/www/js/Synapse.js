/*--------------------------------Sign up------------------------------------------*/
function user_ajax(method,user_type,parameters)
{
    var res = "";
    $.ajax({
		type: "POST",
        url: "http://technostan.com/Zeta/Neuron.php",
        data: { "method": method, "user_type": user_type, "parameters": parameters }
	  })
		.done(function( msg ) {
			res = $.trim(msg);
		})
		.fail(function() {
            res = "Fail";
        });
    return res;
}
