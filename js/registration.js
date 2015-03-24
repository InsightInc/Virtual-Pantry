$(document).ready(function(){

    $("#submitReg").click(function() {
        $.post("api/register",{firstname: $("#firstname").val(), lastname: $("#lastname").val(),
                            username: $("#username").val(), password: $("#password").val()},function(data) {

            var status = JSON.parse(data);
            console.log(data);
            if(status.success == true)
            {
                window.location = "home.html";
            }
            else
            {
                alert("Failed to Register");
            }
        });
    });


})