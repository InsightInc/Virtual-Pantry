$(document).ready(function(){

	


	$("#changepassword").click(function() {
		var oldpass = $("#oldpassword").val();
		var newpass = $("#newpassword").val();
		var passconfirm = $("#passwordconfirm").val();
        $.post("api/changePassword",{password: oldpass, newPass: newpass, confirm: passconfirm},function(data) {
        	alert(data);
            console.log(data);
        });
    });

    $.get("api/getUserInfo",function(data){
			console.log(data);
			var x = JSON.parse(data);
			document.getElementById("fname").innerHTML = x.fname;
			document.getElementById("lname").innerHTML = x.lname;
			document.getElementById("email").innerHTML = x.email;

		});

});
