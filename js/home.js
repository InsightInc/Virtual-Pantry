$(document).ready(function(){
    $("#pantryList").DataTable({
        "scrollY":          "390px",
        "scrollCollapse":   false,
        "paging":           false
    });
    
    $("#submitNewProduct").click(function() {
        $.get("api/addProduct",{name: $("#newProductName").val()},function(data) {
            console.log(data);
        });
    });

    $("#searchForRecipe").click(function() {
        $.get("api/getRecipes", {query: $("#ingredientName").val()}, function(data) {
            console.log(data);
        });
    });

    $("#recipesTable").DataTable({
        "scrollY":          "200px",
        "scrollCollapse":   false,
        "paging":           false
    });
});
