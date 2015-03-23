function login(){
	var data = authenticate();
    if(data.success) {
        alert("Welcome");
        window.location = "registration.html";
    }
    else
        alert("Error logging in.\nPlease check your email/password or create an account.");
}
function authenticate(){
	var result = "";
    $.ajax({
        url: "api/login",
        type: "POST",
        async: false,
        data: {
            "email":$("#username").val(), 
            "password":$("#password").val()
        },
        dataType: "json",
        success:function(data) {
            result = data;
        }
    });
    return result;
}