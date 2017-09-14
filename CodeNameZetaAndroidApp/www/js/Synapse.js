/************************************************************************
Synapse v1.0

************************************************************************/

//AJAX Function
function ajax(datatype, neuron, parameters, callback, error) {
    var neuron = typeof neuron == 'string' ? neuron : Object.keys(neuron).map(
        function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(neuron[k]) }
    ).join('&');

    var params = typeof parameters == 'string' ? parameters : Object.keys(parameters).map(
        function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(parameters[k]) }
    ).join('&');

    $.ajax({
        type: "POST",
        dataType: datatype,
        url: "http://technostan.com/zeta/neuron.php",
        data: neuron + '&' + params,
        success: callback,
        error: error
    })
}
function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}