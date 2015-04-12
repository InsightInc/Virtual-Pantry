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

});
