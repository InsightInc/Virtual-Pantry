function login(){
	
        alert("Welcome");
        window.location = "home.html";
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
