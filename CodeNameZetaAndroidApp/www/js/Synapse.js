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
///Email validation
function isEmail(email)
{
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

///Required Field validation
function required(input)
{
    var expr;
    if (input.trim() == "")
    {
        expr = false;
    }
    else
    {
        expr = true;
    }
    return expr;
}
//Min length validation
function passwordRequiredLength(pwd, len)
{
    var expr;
    if (pwd.trim().length >= len)
    {
        expr = true;
    }
    else
    {
        expr = false;
    }
    return expr;
}
//check two passwords are same or not
function passwordCheck(pwd, confirmpwd)
{
    var expr;
    if (pwd.trim() == confirmpwd.trim())
    {
        expr = true;
    }
    else
    {
        expr = false;
    }
    return expr;
}

function trySilentLogin() {
    window.plugins.googleplus.trySilentLogin(
        {},
        function (obj) {
            document.querySelector("#feedback").innerHTML = "Silent hi, " + obj.displayName + ", " + obj.email;
        },
        function (msg) {
            document.querySelector("#feedback").innerHTML = "error: " + msg;
        }
    );
}

function logout() {
    window.plugins.googleplus.logout(
        function (msg) {
            document.querySelector("#image").style.visibility = 'hidden';
            document.querySelector("#feedback").innerHTML = msg;
        },
        function (msg) {
            document.querySelector("#feedback").innerHTML = msg;
        }
    );
}

function disconnect() {
    window.plugins.googleplus.disconnect(
        function (msg) {
            document.querySelector("#image").style.visibility = 'hidden';
            document.querySelector("#feedback").innerHTML = msg;
        },
        function (msg) {
            document.querySelector("#feedback").innerHTML = msg;
        }
    );
}

function handleOpenURL(url) {
    document.querySelector("#feedback").innerHTML = "App was opened by URL: " + url;
}