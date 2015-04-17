var pantryTable, recipeTable;
$(document).ready(function(){

    $.post("api/checkUser", function(data){
        console.log(data);
        if(data == false)
        {
            window.location = "index.html";
            alert("You are not authorized to view this page")
        }
    });

     $.get("api/getUserInfo",function(data){
            console.log(data);
            var x = JSON.parse(data);
            document.getElementById("uPantry").innerHTML = x.fname + "\'s Pantry";
            

        });

    pantryTable = $("#pantryList").DataTable({
        "scrollY":          "273px",
        "scrollCollapse":   false,
        "paging":           false
    });

    $("#newProductName").keyup(function(event) {
    if(event.keyCode == 13)
        $("#submitNewProduct").click();
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
	$("#recipeLoadIndic").show();
        $.get("api/getRecipes", {query: $("#ingredientName").val()}, function(data) {
            var dataArr = JSON.parse(data);
            console.log(data);
	    $("#recipeLoadIndic").hide();
            $.each(dataArr,function(index, value1) {
                var img = '<img src=\"' + value1[1] + '\">';
                $.each(value1[0], function(key, value) {
                    console.log('key ' + key + ' value ' + value);
                    recipeTable.row.add([img, key.link(value)]);
                });
            });
            recipeTable.draw();
        });
    });

    $("#ingredientName").keyup(function(event) {
	if(event.keyCode == 13)
	    $("#searchForRecipe").click();
    });

    recipeTable = $("#recipesTable").DataTable({
        "scrollY":          "240px",
        "scrollCollapse":   false,
        "paging":           false,
	    "dom":		        "ti",
        "columns":          [{"orderable": false}, null],
        "order":            [[1, "asc"]]
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

    //initialize tabs
    $("#pantryTabs a").click(function(e){
        e.preventDefault();
        $(this).tab('show');
        recipeTable.columns.adjust().draw();
    });
    
});

