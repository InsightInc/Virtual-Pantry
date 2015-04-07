var pantryTable, recipeTable;
$(document).ready(function(){
    pantryTable = $("#pantryList").DataTable({
        "scrollY":          "310px",
        "scrollCollapse":   false,
        "paging":           false
    });
    
    $("#submitNewProduct").click(function() {
        var product = $("#newProductName").val();
        $.get("api/addProduct",{name: product},function(data) {
            console.log(data);
        });
        location.reload();
    });


    $("#searchForRecipe").click(function() {
        recipeTable.clear();
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
    
    //pantryList
    $.get("api/getPantryList",function(data) {
        var dataAsArr = JSON.parse(data);
        console.log(dataAsArr);
        $.each(dataAsArr,function(index, value) {
            pantryTable.row.add([value[0], '<a href="#"><span class="glyphicon glyphicon-trash deleteRecipeItem"></span></a>']);
        });
        pantryTable.draw();
    });

    //Get product info
    $('#pantryList tbody').on('click', 'td', function() {
        var cellData = pantryTable.cell(this).data();
        console.log(cellData);

        var cindex = $(this).index();
        if(cindex == 0){
            $.get("api/getProductInfo", {name: cellData},function(data){
                console.log(data);
                alert(data);
            });
        }
        if(cindex == 1)
        {
            cellData = $(this).parent().find("td").first().text();
            $.get("api/removeProduct", {name: cellData},function(data){
                console.log(data);
                if(data == 1){
                    alert("Success");
                }
                if(data == 0){
                    alert("Failure");
                }
            });
            location.reload();
        }
    });

});
