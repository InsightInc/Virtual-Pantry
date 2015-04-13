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
    
    $("#changeemail").click(function() {
		var oldemail = $("#oldemail").val();
		var newemail = $("#newemail").val();
		var emailconfirm = $("#emailconfirm").val();
        $.post("api/changeEmail",{email: oldemail, newE: newemail, confirm: emailconfirm},function(data) {
        	alert(data);
            console.log(data);
        });
    });
    
    $("#changeDiet").click(function(){
    	var check = $("#checkbox").val();
    	var n = check.length;
    	console.log(n);
    	for (var i = 0; i < n; i++){
    		console.log(check[i]);
    	}
    	// $.post("api/changeDiet", {checkbox: $("#checkbox").val()}, function(data){
    	// 	alert(data);
    	// 	console.log(data);
    	// });

    });

    $.get("api/getUserInfo",function(data){
			console.log(data);
			var x = JSON.parse(data);
			document.getElementById("fname").innerHTML = x.fname;
			document.getElementById("lname").innerHTML = x.lname;
			document.getElementById("email").innerHTML = x.email;

		});

});
