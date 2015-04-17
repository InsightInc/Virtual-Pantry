$(document).ready(function(){

    $.post("api/checkUser", function(data){
        console.log(data);
        if(data == false)
        {
            window.location = "index.html";
            alert("You are not authorized to view this page")
        }
    });

    $("#submitLogout").click(function() {
        $.post("api/logout",{logout: true},function(data) {
            var status = JSON.parse(data);
            console.log(data);
            if(status.success == true)
            {
                window.location = "index.html";
            }
            else
            {
                alert("Failed to Logout");
            }
        });
    });
    
    $("#viewProfile").click(function(){
        window.location = "profile.html";
    });

    $("#changepassword").click(function() {
        jQuery.ajaxSetup({async:false});
		var oldpass = $("#oldpassword").val();
		var newpass = $("#newpassword").val();
		var passconfirm = $("#passwordconfirm").val();

        var hasError = false;
        $("div.form-group1").each(function(index,val) {
            var formid = "#" + $(this).find("input").attr("id");
            if($(formid).val() == "") {
                $(this).find(".errorMsgP").html("This field is required.");
                if(!$(this).hasClass("has-error"))
                    $(this).addClass("has-error");
                hasError = true;
            }
            else {
                $(this).removeClass("has-error");
                $(this).find(".errorMsgP").empty();
            }
        });
        if(newpass != passconfirm) {
            $("#passMismatch").html("Passwords must match.");
            $(".passForm").addClass("has-error");
            hasError = true;
        }
        else{
            $("#passMismatch").empty();
        }

        if(hasError == false){
            $.post("api/changePassword",{password: oldpass, newPass: newpass, confirm: passconfirm},function(data) {
            	alert(data);
                console.log(data);
            });
        }
        jQuery.ajaxSetup({async:true});
        location.reload();
    });
    
    $("#changeemail").click(function() {
        jQuery.ajaxSetup({async:false});

        var hasError = false;
        $("div.form-group2").each(function(index,val) {
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

        var emailCheck = true;
		var oldemail = $("#oldemail").val();

		var newemail = $("#newemail").val();
        $.get("api/validateEmail",{email: newemail}, function(data){
            console.log(data);
            if(data == false)
            {
                $("#errorMsg2").html("Not a valid email.");
                $(".emailForm1").addClass("has-error");
                emailCheck = false;
                hasError = true;
            }
            else
            {
                $("#errorMsg2").empty();
                emailCheck = true;
            }
            
        });


		var emailconfirm = $("#emailconfirm").val();
        $.get("api/validateEmail",{email: emailconfirm}, function(data){
            console.log(data);
            if(data == false)
            {
                $("#errorMsg3").html("Not a valid email.");
                $(".emailForm2").addClass("has-error");
                emailCheck = false;
                hasError = true;
            }
            else
            {
                $("#errorMsg3").empty();
                emailCheck = true;
            }
        });

        if(emailCheck == true && hasError == false){
            $.post("api/changeEmail",{email: oldemail, newE: newemail, confirm: emailconfirm},function(data) {
            	alert(data);
                console.log(data);
            });
        }
        jQuery.ajaxSetup({async:true});
        location.reload();
    });
    
    $("#changeDiet").click(function(){

        var allergies = [];
        $.each($("input[name='checkbox']:checked"), function(){            
             allergies.push($(this).val());
        });

        console.log(allergies);
        allergies = JSON.stringify(allergies);

        $.post("api/changeDiet", {checkbox: allergies}, function(data){
            console.log(data);
        });

	location.reload();

    });

    $.get("api/getUserInfo",function(data){
			console.log(data);
			var x = JSON.parse(data);
			document.getElementById("fname").innerHTML = x.fname;
			document.getElementById("lname").innerHTML = x.lname;
			document.getElementById("email").innerHTML = x.email;
            document.getElementById("profile1").innerHTML = x.fname + "\'s Profile";

		});

    $.get("api/getDiet",function(data){
            console.log(data);
            var x = JSON.parse(data);

            for(var i = 0; i < x.length; i++){
                var ul = document.getElementById("alist");
                var li = document.createElement("li");
                li.appendChild(document.createTextNode(x[i][0]));
                ul.appendChild(li);
                $("input[name='checkbox']")[x[i][1]].checked = true;
            }   
        });

});

