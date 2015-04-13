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

});
