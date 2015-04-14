$(document).ready(function(){

    var emailCheck = false;
    jQuery.ajaxSetup({async:false});
    $("#submitReg").click(function() {
        var hasError = false;
        $("div.form-group").each(function(index,val) {
            var formid = "#" + $(this).find("input").attr("id");
            if($(formid).val() == "") {
                $(this).find(".errorMsg").html("This field is required.");
                if(!$(this).hasClass("has-error"))
                    $(this).addClass("has-error");
                hasError = true;
            }
            else {
                $(this).removeClass("has-error");
                $(this).find(".errorMsg").empty();
            }
        });
        if($("#password").val() != $("#passwordconfirm").val()) {
            $("#passMismatch").html("Passwords must match.");
            $(".passForm").addClass("has-error");
            hasError = true;
        }
        else{
            $("#passMismatch").empty();
        }
        $.get("api/validateEmail",{email: $("#email").val()}, function(data){
            console.log(data);
            if(data == false)
            {
                $("#emailValid").html("Not a valid email.");
                $(".emailForm").addClass("has-error");
                hasError = true;
                emailCheck = false;
            }
            else
            {
                $("#emailValid").empty();
                emailCheck = true;
            }
        });
        console.log(hasError);
        console.log(emailCheck);

        if(!hasError && emailCheck == true)
        {
            $.post("api/register",{firstname: $("#firstname").val().substr(0,1).toUpperCase() + $("#firstname").val().substr(1).toLowerCase(), lastname: $("#lastname").val().substr(0,1).toUpperCase() + $("#lastname").val().substr(1).toLowerCase(),
                                email: $("#email").val(), password: $("#password").val()},function(data) {

                var status = JSON.parse(data);
                console.log(data);
                if(status.success == true)
                {
                    window.location = "home.html";
                }
                else if(status.success == "exists")
                {
                    alert("The email you entered is already associated with an account");
                }
                else
                {
                    alert("Failed to register");
                }
            });
        }
        jQuery.ajaxSetup({async:true});
    });

})
