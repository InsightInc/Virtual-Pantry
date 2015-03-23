var pantryTable, recipeTable;
$(document).ready(function(){
    pantryTable = $("#pantryList").DataTable({
        "scrollY":          "310px",
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
            var dataArr = JSON.parse(data);
            console.log(data);
            $.each(dataArr,function(key, value) {
                recipeTable.row.add([key, value]);
            });
            recipeTable.draw();
        });
    });

    recipeTable = $("#recipesTable").DataTable({
        "scrollY":          "200px",
        "scrollCollapse":   false,
        "paging":           false
    });

    //pantryList
    $.get("api/getPantryList",function(data) {
        var dataAsArr = JSON.parse(data);
        console.log(dataAsArr);
        $.each(dataAsArr,function(index, value) {
            pantryTable.row.add([value[0], '<a href="#"><span class="glyphicon glyphicon-trash deleteRecipeItem"></span></a>']);
        });
        pantryTable.draw();
    });
});
