$(document).ready(function(){

    $.get("api/checkLogIn", function(data)
    {
        console.log(data);
        if(data == true)
        {
            window.location = "home.html";
        }

    });

    $("#password").keyup(function(event) {
    if(event.keyCode == 13)
        $("#submitLogin").click();
    });

	$("#submitLogin").click(function() {
        $.post("api/login",{email: $("#email").val(), password: $("#password").val()},function(data) {
        	var status = JSON.parse(data);
            console.log(data);
            if(status.success == true)
            {
            	window.location = "home.html";
            }
            else
            {
            	alert("Failed to Login");
            }
        });
    });


})
