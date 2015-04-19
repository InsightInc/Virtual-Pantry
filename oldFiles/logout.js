$(document).ready(function(){

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


})