$(document).ready(function(){

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
        else
            $("#passMismatch").empty();
        if(!hasError)
        {
            $.post("api/register",{firstname: $("#firstname").val(), lastname: $("#lastname").val(),
                                email: $("#email").val(), password: $("#password").val()},function(data) {

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
        }
    });

})
