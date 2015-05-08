<?php
require 'vendor/autoload.php';
session_cache_limiter(false);
session_start();
$app = new \Slim\Slim();
 $database = new mysqli("localhost", "root", "root", "VirtualPantryDB");
 if ($database->connect_errno)
     die("Connection failed: " . $database->connect_error);
$app->get('/addProductUPC', function(){
	global $database;
	$id = $_GET['uid'];
	$upc = $_GET['upc'];
	#Connect to foodessentials api ------------------------------------------------
	#Get session id
	$sjson = file_get_contents('http://api.foodessentials.com/createsession?uid=ert&devid=ert&appid=ert&f=json&v=2.00&api_key=x4c59ktead886t2urzcdju54');
	$sobj = json_decode($sjson);
	$sid = $sobj->session_id;
	#Create Profile, so information is outputted correctly
	$profjson = file_get_contents("./profile.json");
	$profjson = json_decode($profjson);
	$profjson[0]['session_id'] = $sid;
	
	#Set Profile
	$options = array(
		'http' => array(
			'method'  => 'POST',
			'content' => json_encode($profjson),
			'header'=>  "Content-Type: application/json\r\n" .
			"Accept: application/json\r\n"
			)
		);
	$context  = stream_context_create( $options );
	$result = file_get_contents('http://api.foodessentials.com/setprofile?api_key=x4c59ktead886t2urzcdju54', false, $context );
	$response = json_decode( $result );
	//Get info for product using the upc code
	$product = file_get_contents('http://api.foodessentials.com/productscore?u='.$upc.'&sid='.$sid.'&f=json&api_key=x4c59ktead886t2urzcdju54');
	$product = json_decode($product);
	
	//Put product info into json array for the response
	$jsonProduct = array();
	$jsonProduct['fat'] = $product->product->nutrients[6]->nutrient_value;
	$jsonProduct['chol'] = $product->product->nutrients[1]->nutrient_value;
	$jsonProduct['sodium'] = $product->product->nutrients[3]->nutrient_value;
	$jsonProduct['carb'] = $product->product->nutrients[5]->nutrient_value;
	$jsonProduct['protien'] = $product->product->nutrients[2]->nutrient_value;
	$jsonProduct['barcode'] = $upc;
	$jsonProduct['cal'] = $product->product->nutrients[0]->nutrient_value;
	$jsonProduct['name'] = $product->product->product_name;
	$jsonProduct['manuf'] = $product->product->manufacturer;
	
	
	//Put product info into varibles for the database insertions 
	$fat = $jsonProduct['fat'];
	$chol = $jsonProduct['chol'];
	$sodium = $jsonProduct['sodium'];
	$carb = $jsonProduct['carb'];
	$protien = $jsonProduct['protien'];
	$barcode = $jsonProduct['barcode'];
	$cal = $jsonProduct['cal'];
	$pname = $jsonProduct['name'];
	$manuf = $jsonProduct['manuf'];
	
	//Insert product into PantryList table for user
	$response = $database->query("INSERT INTO PantryList (uid, pname, barcode) VALUES ('$id', '$pname', '$upc')");
	
	
	//Insert product into Ingredient table
	$database->query("INSERT INTO Ingredient VALUES('$upc', '$fat', '$chol', '$sodium', '$carb', '$protien', NULL, NULL, '$cal', NULL, '$pname', '$manuf')");
	
	$response = $database->query("SELECT '$pname' FROM PantryList WHERE uid = $id");
	$response = $response->fetch_all();
	$response = json_encode($response);
	echo $response;

});
#get product by searching by name
$app->get('/addProduct', function() {
	global $database;
	$name = $_GET['name'];
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
   	}

	#Connect to foodessentials api ------------------------------------------------
	#Get session id
	$sjson = file_get_contents('http://api.foodessentials.com/createsession?uid=ert&devid=ert&appid=ert&f=json&v=2.00&api_key=x4c59ktead886t2urzcdju54');
	$sobj = json_decode($sjson);
	$sid = $sobj->session_id;
	#Create Profile, so information is outputted correctly
	$profjson = file_get_contents("./profile.json");
	$profjson = json_decode($profjson);
	$profjson[0]['session_id'] = $sid;
	
	#Set Profile
	$options = array(
	  'http' => array(
	    'method'  => 'POST',
	    'content' => json_encode($profjson),
	    'header'=>  "Content-Type: application/json\r\n" .
	                "Accept: application/json\r\n"
	    )
	);
	$context  = stream_context_create( $options );
	$result = file_get_contents('http://api.foodessentials.com/setprofile?api_key=x4c59ktead886t2urzcdju54', false, $context );
	$response = json_decode( $result );

	//search for product and get UPC code
	$name = str_replace(' ', '+', $name);
    $pjson = file_get_contents('http://api.foodessentials.com/searchprods?q='.$name.'&sid='.$sid.'&n=5&s=1&f=json&v=2.00&api_key=x4c59ktead886t2urzcdju54');
    $productList = json_decode($pjson);
    $upc = $productList->productsArray[0]->upc;
    $name = str_replace('+', ' ', $name);

    //Get info for product using the upc code
    $product = file_get_contents('http://api.foodessentials.com/productscore?u='.$upc.'&sid='.$sid.'&f=json&api_key=x4c59ktead886t2urzcdju54');
    $product = json_decode($product);

    //Put product info into json array for the response
    $jsonProduct = array();
    $jsonProduct['fat'] = $product->product->nutrients[6]->nutrient_value;
	$jsonProduct['chol'] = $product->product->nutrients[1]->nutrient_value;
	$jsonProduct['sodium'] = $product->product->nutrients[3]->nutrient_value;
	$jsonProduct['carb'] = $product->product->nutrients[5]->nutrient_value;
	$jsonProduct['protien'] = $product->product->nutrients[2]->nutrient_value;
	$jsonProduct['barcode'] = $upc;
	$jsonProduct['cal'] = $product->product->nutrients[0]->nutrient_value;
	$jsonProduct['name'] = $product->product->product_name;
	$jsonProduct['manuf'] = $product->product->manufacturer;


	//Put product info into varibles for the database insertions 
	$fat = $jsonProduct['fat'];
	$chol = $jsonProduct['chol'];
	$sodium = $jsonProduct['sodium'];
	$carb = $jsonProduct['carb'];
	$protien = $jsonProduct['protien'];
	$barcode = $jsonProduct['barcode'];
	$cal = $jsonProduct['cal'];
	$pname = $jsonProduct['name'];
	$manuf = $jsonProduct['manuf'];

	//Insert product into PantryList table for user
	$response = $database->query("INSERT INTO PantryList (uid, pname, barcode) VALUES ('$id', '$pname', '$upc')");


	//Insert product into Ingredient table
	$database->query("INSERT INTO Ingredient VALUES('$upc', '$fat', '$chol', '$sodium', '$carb', '$protien', NULL, NULL, '$cal', NULL, '$pname', '$manuf')");

    echo $response;
});

$app->POST('/changePassword', function(){
	global $database;
	$oldpass = $_POST['password'];
	$newpassword = $_POST['newPass'];
	$confirm = $_POST['confirm'];
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
		 if(strcmp($newpassword, $confirm) == 0){
		 	$run = $database -> query("SELECT password FROM User WHERE uid = $id");
			$result = $run->fetch_assoc();
		 	if (strcmp($oldpass, $result['password']) == 0){
		 		$database->query("UPDATE User SET password ='$newpassword' WHERE uid = $id");
		 		$response = "Your Password has been changed";
		 	}
		 	else{
		 		$response = "You entered the incorrect existing password";
		 	}
		 }
		 else{
		 	$response = "The new passwords did not match";
		 }
   	}
   	else{
   		$response = "You are not a logged in user";
   	}
   	echo json_encode($response);
});

$app->POST('/changeEmail', function(){
	global $database;
	$oldemail = $_POST['email'];
	$newemail = $_POST['newE'];
	$confirm = $_POST['confirm'];

	$test = $database->query("SELECT * FROM User WHERE email = '$newemail'");
	$emailCount = $test->num_rows;
	if($emailCount == 0)
	{
		if( isset( $_SESSION['uid'] ) )
	   	{
			 $id =  $_SESSION['uid'];
			 if(strcmp($newemail, $confirm) == 0){
			 	$run = $database -> query("SELECT email FROM User WHERE uid = $id");
				$result = $run->fetch_assoc();
			 	if (strcmp($oldemail, $result['email']) == 0){
			 		$database->query("UPDATE User SET email ='$newemail' WHERE uid = $id");
			 		$response = "Your Email has been changed";
			 	}
			 	else{
			 		$response = "You entered the incorrect existing email";
			 	}
			 }
			 else{
			 	$response = "The new emails did not match";
			 }
	   	}
	   	else{
	   		$response = "You are not a logged in user";
	   	}
	}
	else
	{
		$response = "That email is already in use";
	}
   	echo json_encode($response);
});


$app->get('/removeProduct', function() 
{
	global $database;
	$name = $_GET['name'];
	$name = addslashes($name);

	//Get User
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
   	}
   	else
		$id = $_GET['uid'];

   	//Delete item from User's Pantry
	$response = $database->query("DELETE FROM PantryList WHERE pname = '$name' AND uid = '$id'");

	echo $response;
});

$app->get('/removeProductByID', function() {
	global $database;
	$prodID = $_GET['productID'];

	//Get User
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
   	}

   	//Delete item from User's Pantry
	$response = $database->query("DELETE FROM PantryList WHERE barcode = '$prodID' AND uid = '$id'");

	echo $response;
});

$app->get('/getPantryList', function() 
{
	global $database;

	//Get User
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
   	}
   	else
		$id = $_GET['uid'];

   	//Get all items in User's pantry and return it as JSON 
	$response = $database->query("SELECT pname, barcode FROM PantryList WHERE uid = $id");
	$response = $response->fetch_all();
	$response = json_encode($response);
	echo $response;
});

$app->get('/getRecipes', function()
// $app->get('/', function()
{

	global $database;
	if(isset($_SESSION['uid']))
		$id = $_SESSION['uid'];
	else
		$id = $_GET['uid'];

	// $id = 2;

	$query = $_GET['query'];
	// $query = 'sugar, bread';
	$parse_query = 	explode(", ", $query);

	$request_url = 'http://api.yummly.com/v1/api/recipes?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e&q=';

	for($x = 0; $x < count($parse_query); $x++)			
	{
		$request_url .= '&allowedIngredient[]='.$parse_query[$x];
	}

	$user_restrictions = $database->query("SELECT * FROM DietaryRestrictions WHERE uid = '$id'");
	if($user_restrictions)
		$user_restrictions = $user_restrictions->fetch_assoc(); //get Dietary Restrictions List		
		$count = ($database->query("SELECT COUNT(*) as numRestrictions FROM DietaryRestrictions WHERE UID = '$id'")->fetch_assoc());
		$numRows = intval($count['numRestrictions']);
		if($numRows > 0)
		{
			$dietary_keys = $database->query("SELECT apicode FROM DietaryKey NATURAL JOIN DietaryRestrictions WHERE DietaryKey.id = DietaryRestrictions.restricts")->fetch_assoc(); //get all associated Keys
			for($x = 0; $x < count($dietary_keys); $x++)
				$request_url .= '&allowedAllergy[]='.$dietary_keys[array_keys($dietary_keys)[$x]];
		}
	$request_url .= '&maxResult=25&start=0';

	$jresponse = file_get_contents($request_url);
	$recipe_list = json_decode($jresponse);

	$array_recipes = array();

	for($x = 0; $x < count($recipe_list->matches); $x++)
	{
		$sub_array = array();
		$picutres = array();
		$array_recipes[$x] = array();
		$url = json_decode(file_get_contents('http://api.yummly.com/v1/api/recipe/'.$recipe_list->matches[$x]->id.'?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e'));
		$url = preg_replace('~(^|[^:])//+~', '\\1/',$url->source->sourceRecipeUrl);
		$sub_array[$recipe_list->matches[$x]->recipeName] = $url;
		$pictures[$x] = $recipe_list->matches[$x]->smallImageUrls[0];
		array_push($array_recipes[$x], $sub_array); 
		array_push($array_recipes[$x], $pictures[$x]); 
		unset($sub_array);
		unset($pictures);
	}

	$recipe_array = json_encode($array_recipes);
	echo $recipe_array;
});
$app -> POST('/loginApp', function() use ($database){

	$email = $_POST['email'];
	$password = $_POST['password'];
	$query = "SELECT uid, fname, lname FROM User WHERE email = '$email' AND password = '$password' LIMIT 1" or die ("Error querying user database");
	$run = $database -> query($query);
	$result = $run->fetch_assoc();
	echo json_encode($result);
});
$app -> POST('/login', function() use ($database){

	$email = $_POST['email'];
    $password = $_POST['password'];
	$query = "SELECT uid FROM User WHERE email = '$email' AND password = '$password' LIMIT 1" or die ("Error querying user database");
	$run = $database -> query($query);
	$result = $run->fetch_assoc();
	if ($result === NULL){
		$response = array("success"=>false);
	}
	else{
		$response = array("success" => true);
		$_SESSION['uid'] = $result['uid'];
	}
	echo json_encode($response);
});
$app -> POST('/logout', function() use ($database){
	session_destroy();
	$result = array("success" => true);
	echo json_encode($result);
});
$app -> POST('/registerApp', function() use ($database){
	$fname = $_POST['firstname'];
	$lname = $_POST['lastname'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	$emailStatus = $database->query("SELECT * FROM User WHERE email = '$email'");
	$emailCount = $emailStatus->num_rows;
	if($emailCount == 0){
		$query = "INSERT INTO User(fname, lname, email, password) values ('$fname', '$lname', '$email', '$password')";
		$database->query($query);
		$query = "SELECT uid from User where email = '$email'";
		$run = $database->query($query);
		$result = $run -> fetch_assoc();
	}
	
	else
		$result = array("uid" => 0);
	echo json_encode($result);
});
$app -> POST('/register', function() use ($database){
	$fname = $_POST['firstname'];
	$lname = $_POST['lastname'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	$emailStatus = $database->query("SELECT * FROM User WHERE email = '$email'");
	$emailCount = $emailStatus->num_rows;
	if($emailCount == 0){
		$query = "INSERT INTO User(fname, lname, email, password) values ('$fname', '$lname', '$email', '$password')";
		$database->query($query);
		$query = "SELECT uid from User where email = '$email'";
		$run = $database->query($query);
		$result = $run -> fetch_assoc();
		$_SESSION['uid'] = $result['uid'];
		$response = array("success" => true);
		echo json_encode($response);
	}
	else if($emailCount > 0){
		$response = array("success" => "exists");
		echo json_encode($response);
	}
	else{
		$response = array("success" => false);
		echo json_encode($response);
	}
});

$app->get('/getProductInfo', function() 
{
	global $database;
	$productname = $_GET['name'];
	$productname = addslashes($productname);

	//Get item info based off its name
	$result = $database->query("SELECT * FROM Ingredient WHERE pname = '$productname'");

	//Return item info as JSON
	if($result) {
		$pinfo = $result->fetch_assoc();
		$pinfo = json_encode($pinfo);
		echo $pinfo;
	}
	else {
		echo $result;
	}

});


$app->POST('/checkUser', function() 
{
	global $database;
	$response = false;

	//Check if uid is set and if the user exists in the database
	if(isset( $_SESSION['uid']))
	{
		$uid = $_SESSION['uid'];
		$result = $database->query("SELECT * FROM User WHERE uid = '$uid'");
		$result = $result->num_rows;
		if($result == 1)
		{
			$response = true;
		}
		else
		{
			$response = false;
		}
	}
	else
	{
		$response = false;
	}
	echo $response;
});

$app->POST('/changeDiet', function(){
	global $database;
	$arr = $_POST['checkbox'];
	$arr = json_decode($arr);


	if (empty($arr)){
		$id = $_SESSION['uid'];
		$database->query("DELETE FROM DietaryRestrictions WHERE uid = $id");
		echo "Removed all restrictions";
	}
	else{
		$id = $_SESSION['uid'];
		$database->query("DELETE FROM DietaryRestrictions WHERE uid = $id");
		$n = count($arr);

		for ($i = 0; $i < $n; $i++){
			$database->query("INSERT into DietaryRestrictions values($id, $arr[$i])");
		}
		echo "successfully added/removed restrictions";
	}
});

$app->get('/getDiet', function()
{
	global $database;
	if(isset($_SESSION['uid']))
		$id=$_SESSION['uid'];
	else
		$id = $_GET['uid'];

	$result = $database->query("SELECT name, id FROM DietaryRestrictions INNER JOIN DietaryKey ON DietaryRestrictions.restricts = DietaryKey.id WHERE uid = '$id'");
	if($result)
   	{
   		$diet = $result->fetch_all();
   		echo json_encode($diet);
   	}
   	else
   	{
   		echo $result;
   	}


});


$app->get('/getUserInfo', function()
{
	global $database;
	// $name = $_GET['name'];
	if( isset( $_SESSION['uid'] ) )
   	{
		$id =  $_SESSION['uid'];
   	}

   	$result = $database->query("SELECT fname, lname, email FROM User WHERE uid = '$id'");

   	if($result)
   	{
   		$user_profile = $result->fetch_assoc();
   		echo json_encode($user_profile);
   	}
   	else
   	{
   		echo $result;
   	}

});

$app->get('/validateEmail', function()
{
	$email = $_GET['email'];
	$result = filter_var($email, FILTER_VALIDATE_EMAIL);
	if($result == false)
	{
		$response = false;
	}
	else
	{
		$response = true;
	}
	echo $response;
});

$app->get('/checkLogIn', function()
{
	if( isset( $_SESSION['uid'] ) )
   	{
   		echo true;
   	}
   	else
   	{
   		echo false;
   	}
});


$app->get('/getProductSearch', function() {
//$app->get('/', function() {
	global $database;
	$name = $_GET['name'];
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
   	}

	#Connect to foodessentials api ------------------------------------------------
	#Get session id
	$sjson = file_get_contents('http://api.foodessentials.com/createsession?uid=ert&devid=ert&appid=ert&f=json&v=2.00&api_key=x4c59ktead886t2urzcdju54');
	$sobj = json_decode($sjson);
	$sid = $sobj->session_id;
	#Create Profile, so information is outputted correctly
	$profjson = file_get_contents("./profile.json");
	$profjson = json_decode($profjson);
	$profjson[0]['session_id'] = $sid;
	
	#Set Profile
	$options = array(
	  'http' => array(
	    'method'  => 'POST',
	    'content' => json_encode($profjson),
	    'header'=>  "Content-Type: application/json\r\n" .
	                "Accept: application/json\r\n"
	    )
	);
	$context  = stream_context_create( $options );
	$result = file_get_contents('http://api.foodessentials.com/setprofile?api_key=x4c59ktead886t2urzcdju54', false, $context );
	$response = json_decode( $result );

	$results = array();
	//search for product and get UPC code
	$name = str_replace(' ', '+', $name);
    $pjson = file_get_contents('http://api.foodessentials.com/searchprods?q='.$name.'&sid='.$sid.'&n=5&s=1&f=json&v=2.00&api_key=x4c59ktead886t2urzcdju54');
    $productList = json_decode($pjson);
    for($x = 0; $x < 5; $x++)
    {
    	$results[$x] = $productList->productsArray[$x];
    }

    echo json_encode($results);

});

$app->get('/addProductSearch', function() {
	global $database;
	$upc = $_GET['upc'];
	$name = $_GET['name'];
	$name = addslashes($name);

	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
   	}

   	#Connect to foodessentials api ------------------------------------------------
	#Get session id
	$sjson = file_get_contents('http://api.foodessentials.com/createsession?uid=ert&devid=ert&appid=ert&f=json&v=2.00&api_key=x4c59ktead886t2urzcdju54');
	$sobj = json_decode($sjson);
	$sid = $sobj->session_id;
	#Create Profile, so information is outputted correctly
	$profjson = file_get_contents("./profile.json");
	$profjson = json_decode($profjson);
	$profjson[0]['session_id'] = $sid;
	
	#Set Profile
	$options = array(
	  'http' => array(
	    'method'  => 'POST',
	    'content' => json_encode($profjson),
	    'header'=>  "Content-Type: application/json\r\n" .
	                "Accept: application/json\r\n"
	    )
	);
	$context  = stream_context_create( $options );
	$result = file_get_contents('http://api.foodessentials.com/setprofile?api_key=x4c59ktead886t2urzcdju54', false, $context );
	$response = json_decode( $result );


    //Get info for product using the upc code
    $product = file_get_contents('http://api.foodessentials.com/productscore?u='.$upc.'&sid='.$sid.'&f=json&api_key=x4c59ktead886t2urzcdju54');
    $product = json_decode($product);

    //Put product info into json array for the response
    $jsonProduct = array();
    $jsonProduct['fat'] = $product->product->nutrients[6]->nutrient_value;
	$jsonProduct['chol'] = $product->product->nutrients[1]->nutrient_value;
	$jsonProduct['sodium'] = $product->product->nutrients[3]->nutrient_value;
	$jsonProduct['carb'] = $product->product->nutrients[5]->nutrient_value;
	$jsonProduct['protien'] = $product->product->nutrients[2]->nutrient_value;
	$jsonProduct['barcode'] = $upc;
	$jsonProduct['cal'] = $product->product->nutrients[0]->nutrient_value;
	$jsonProduct['name'] = $product->product->product_name;
	$jsonProduct['manuf'] = $product->product->manufacturer;

	//Put product info into varibles for the database insertions 
	$fat = $jsonProduct['fat'];
	$chol = $jsonProduct['chol'];
	$sodium = $jsonProduct['sodium'];
	$carb = $jsonProduct['carb'];
	$protien = $jsonProduct['protien'];
	$barcode = $jsonProduct['barcode'];
	$cal = $jsonProduct['cal'];
	$pname = $jsonProduct['name'];
	$manuf = $jsonProduct['manuf'];


	//Insert product into PantryList table for user
	$response = $database->query("INSERT INTO PantryList (uid, pname, barcode) VALUES ('$id', '$name', '$upc')");


	//Insert product into Ingredient table
	$database->query("INSERT INTO Ingredient VALUES('$upc', '$fat', '$chol', '$sodium', '$carb', '$protien', NULL, NULL, '$cal', NULL, '$name', '$manuf')");

    echo $response;

});

// $app->get('/', function()
$app->get('/advancedSearch', function()
{
	global $database;
	$id = $_SESSION['uid'];

	// $id = 2;

	// $names = "sugar, butter, milk";
	// $max_cal = 100;
	// $min_carbs = 0;
	// $max_carbs = 0;
	// $min_protein = 0;
	// $max_protein = 0;

	$names = $_GET['names'];
	$max_cal = $_GET['maxCal'];
	$min_protein = $_GET['minProtein'];
	$max_protein = $_GET['maxProtein'];
	$min_carbs = $_GET['minCarbs'];
	$max_carbs = $_GET['maxCarbs'];

	$names = explode(", ", $names);


	//changed these to ints just in case they were saved as JSON strings.
	$max_cal = intval($max_cal);
	$min_carbs = intval($min_carbs);
	$max_carbs = intval($max_carbs);
	$min_protein = intval($min_protein);
	$max_protein = intval($max_protein);

	// $values = array('max_cal' => $nutrition_values[0], 'min_protein' => $nutrition_values[1], 'max_protein' => $nutrition_values[2]);

	$request_url = 'http://api.yummly.com/v1/api/recipes?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e&q=';


	for($x = 0; $x < count($names); $x++)			
	{
		$request_url .= '&allowedIngredient[]='.$names[$x];
	}

	$user_restrictions = $database->query("SELECT * FROM DietaryRestrictions WHERE uid = '$id'");
	if($user_restrictions)
	{
		$user_restrictions = $user_restrictions->fetch_assoc(); //get Dietary Restrictions List		
		$count = ($database->query("SELECT COUNT(*) as numRestrictions FROM DietaryRestrictions WHERE UID = '$id'")->fetch_assoc());
		$numRows = intval($count['numRestrictions']);
		if($numRows > 0)
		{
			$dietary_keys = $database->query("SELECT apicode FROM DietaryKey NATURAL JOIN DietaryRestrictions WHERE DietaryKey.id = DietaryRestrictions.restricts")->fetch_assoc(); //get all associated Keys
			for($x = 0; $x < count($dietary_keys); $x++)
				$request_url .= '&allowedAllergy[]='.$dietary_keys[array_keys($dietary_keys)[$x]];
		}
	}

	if(!(empty($max_cal)))
		$request_url .= '&nutrition.ENERC_KCAL.max='.$max_cal;	
	if(!(empty($min_carbs)))
		$request_url .= '&nutrition.CHOCDF.min='.$min_carbs;
	if(!(empty($max_carbs)))
		$request_url .= '&nutrition.CHOCDF.max='.$max_carbs;
	if(!(empty($min_protein)))
		$request_url .= '&nutrition.PROCNT.min='.$min_protein;
	if(!(empty($max_protein)))
		$request_url .= '&nutrition.PROCNT.max='.$max_protein;
	// (NA, CHOLE, CHOCDF, PROCNT, FAT) Sodium, cholesterol, carbs, protein, fat 

	$request_url .= '&maxResult=25&start=0'; //gets top 25 items.


	$jresponse = file_get_contents($request_url);
	$recipe_list = json_decode($jresponse);

	$array_recipes = array();

	for($x = 0; $x < count($recipe_list->matches); $x++)
	{
		$sub_array = array();
		$picutres = array();
		$array_recipes[$x] = array();
		$url = json_decode(file_get_contents('http://api.yummly.com/v1/api/recipe/'.$recipe_list->matches[$x]->id.'?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e'));
		$url = preg_replace('~(^|[^:])//+~', '\\1/',$url->source->sourceRecipeUrl);
		$sub_array[$recipe_list->matches[$x]->recipeName] = $url;
		$pictures[$x] = $recipe_list->matches[$x]->smallImageUrls[0];
		array_push($array_recipes[$x], $sub_array); 
		array_push($array_recipes[$x], $pictures[$x]); 
		unset($sub_array);
		unset($pictures);
	}

	$recipe_array = json_encode($array_recipes);
	echo $recipe_array;
});


$app->POST('/createItem', function()
{
	global $database;
	$id = $_SESSION['uid'];

	$name = $_POST['name'];
	$fat = $_POST['fat'];
	$chol = $_POST['chol'];
	$sodium = $_POST['sodium'];
	$carb = $_POST['carb'];
	$protien = $_POST['protien'];
	$calories = $_POST['calories'];
	$brand = $_POST['brand'];

	//Insert product into PantryList table for user
	$response = $database->query("INSERT INTO PantryList (uid, pname) VALUES ('$id', '$name')");


	//Insert product into Ingredient table
	$database->query("INSERT INTO Ingredient VALUES('$name', '$fat', '$chol', '$sodium', '$carb', '$protien', NULL, NULL, '$calories', NULL, '$name', '$brand')");

    echo $response;


});


$app->run();
?>

