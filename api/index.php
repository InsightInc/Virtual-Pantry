<?php
require 'vendor/autoload.php';
session_cache_limiter(false);
session_start();
$app = new \Slim\Slim();
 $database = new mysqli("localhost", "root", "root", "VirtualPantryDB");
 if ($database->connect_errno)
     die("Connection failed: " . $database->connect_error);
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
$app->POST('/changeEmail', function(){
	global $database;
	$email = $_POST['email'];
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
		 $database->query("UPDATE User SET email ='$email' WHERE uid = $id");
		 $response = array("success" => true);
   	}
   	else{
   		$response = array("success" => false);
   	}
   	echo json_encode($response);
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
$app->get('/removeProduct', function() 
{
	global $database;
	$name = $_GET['name'];

	//Get User
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
   	}

   	//Delete item from User's Pantry
	$response = $database->query("DELETE FROM PantryList WHERE pname = '$name' AND uid = '$id'");

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

   	//Get all items in User's pantry and return it as JSON 
	$response = $database->query("SELECT pname FROM PantryList WHERE uid = $id");
	$response = $response->fetch_all();
	$response = json_encode($response);
	echo $response;
});

$app->get('/getRecipes', function()
// $app->get('/', function()
{
	$query = $_GET['query'];
	// $query = 'salt, pepper, tilapia';
	$parse_query = 	explode(", ", $query);

	$request_url = 'http://api.yummly.com/v1/api/recipes?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e&q=';

	for($x = 0; $x < count($parse_query); $x++)			
	{
		$request_url .= '&allowedIngredient[]='.$parse_query[$x];
	}


	$jresponse = file_get_contents($request_url);
	$recipe_list = json_decode($jresponse);

	$array_recipes = array();
	for($x = 0; $x < count($recipe_list->matches); $x++)
	{
		$url = json_decode(file_get_contents('http://api.yummly.com/v1/api/recipe/'.$recipe_list->matches[$x]->id.'?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e'));
		$url = preg_replace('~(^|[^:])//+~', '\\1/',$url->source->sourceRecipeUrl);
		// $url = rawurlencode($url->source->sourceRecipeUrl)
		$array_recipes[$recipe_list->matches[$x]->recipeName] = $url;
	}

	$recipe_array = json_encode($array_recipes);
	echo $recipe_array;
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

$app->POST('/addDiet', function() 
{
	global $database;

	//Get User and new diet restriction
	$dietR = $_POST['allergy'];
	$id = $_SESSION['uid'];

	//Get key from Dietary Key table
	$response = $database->query("SELECT * FROM DietaryKey WHERE name = '$dietR'");
	$response = $response->fetch_assoc;
	$response = json_encode($response);

	//Add to DietaryRestriction table
	$did = $response['id'];
	$result = $database->query("INSERT INTO DietaryRestriction VALUES('$id', '$did')");
	echo $result;

});

$app->get('/removeDiet', function() 
{
	global $database;
	
	//Get User and diet restriction
	$dietR = $_POST['allergy'];
	$id = $_SESSION['uid'];

	//Get key from Dietary Key table
	$response = $database->query("SELECT * FROM DietaryKey WHERE name = '$dietR'");
	$response = $response->fetch_assoc;
	$response = json_encode($response);

	//Remove to DietaryRestriction table
	$did = $response['id'];
	$result = $database->query("DELETE FROM DietaryRestriction WHERE uid = '$id' AND restricts = '$did'");
	echo $result;
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

// $app->get('/dietaryRestrictions', function()
// {
// 	$dietary_restrictions = file_get_contents('http://api.yummly.com/v1/api/metadata/allergy?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e');
// 	$dietary_restrictions = substr($dietary_restrictions, 23, -2);
// 	$dietary_restrictions = json_decode($dietary_restrictions);
// 	$allergy_array = array();
// 	for($x = 0; $x < count($dietary_restrictions); $x++)
// 	{
// 		if($dietary_restrictions[$x]->shortDescription != "Sulfite-Free")
// 			$allergy_array[$dietary_restrictions[$x]->shortDescription] = $dietary_restrictions[$x]->id; 
// 	}
// 	echo json_encode($allergy_array);

// });


$app->run();
?>

