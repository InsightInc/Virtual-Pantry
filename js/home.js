$(document).ready(function(){
    $("#pantryList").DataTable({
        "scrollY":          "400px",
        "scrollCollapse":   false,
        "paging":           false
    });
    
    $("#submitNewProduct").click(function() {
        $.get("api/addProduct",{name: $("#newProductName").val()},function(data) {
            console.log(data);
        });
    });
});
