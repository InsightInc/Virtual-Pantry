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
   	else
   	{
      		$id = 1;
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
    $jsonProduct['pid'] = $name;
    $jsonProduct['fat'] = $product->product->nutrients[6]->nutrient_value;
	$jsonProduct['chol'] = $product->product->nutrients[1]->nutrient_value;
	$jsonProduct['sodium'] = $product->product->nutrients[3]->nutrient_value;
	$jsonProduct['carb'] = $product->product->nutrients[5]->nutrient_value;
	$jsonProduct['protien'] = $product->product->nutrients[2]->nutrient_value;
	$jsonProduct['barcode'] = $upc;
	$jsonProduct['cal'] = $product->product->nutrients[0]->nutrient_value;
	$jsonProduct['name'] = $product->product->product_name;

	//Put product info into varibles for the database insertions 
	$pid = $jsonProduct['pid'];
	$fat = $jsonProduct['fat'];
	$chol = $jsonProduct['chol'];
	$sodium = $jsonProduct['sodium'];
	$carb = $jsonProduct['carb'];
	$protien = $jsonProduct['protien'];
	$barcode = $jsonProduct['barcode'];
	$cal = $jsonProduct['cal'];
	$pname = $jsonProduct['name'];

	//Insert product into PantryList table for user
	$response = $database->query("INSERT INTO PantryList (uid, pid, barcode, pname) VALUES ('$id', '$name', '$upc', '$pname')");


	//Insert product into Ingredient table
	$database->query("INSERT INTO Ingredient VALUES('$pid', '$fat', '$chol', '$chol', '$sodium', '$protien', NULL, NULL, '$upc', '$cal', NULL, '$pname')");

    echo $response;
});
$app->get('/removeProduct', function() 
{
	global $database;
	$name = $_GET['name'];
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
   	}
   	else
   	{
      		$id = 1;
   	}

	$response = $database->query("DELETE FROM PantryList WHERE pname = '$name' AND uid = '$id'");
	echo $response;
});
$app->get('/getPantryList', function() 
{
	global $database;
	if( isset( $_SESSION['uid'] ) )
   	{
		 $id =  $_SESSION['uid'];
   	}
   	else
   	{
      		$id = 1;
   	}

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
	$email = $_POST['username'];
	$password = $_POST['password'];
	$query = "INSERT INTO User(fname, lname, email, password) values ('$fname', '$lname', '$email', '$password')";
	$database->query($query);
	$query = "SELECT uid from User where email = '$email'";
	$run = $database->query($query);
	$result = $run -> fetch_assoc();
	$_SESSION['uid'] = $result['uid'];
	$response = array("success" => true);
	echo json_encode($response);
});

$app->get('/getProductInfo', function() 
{
	global $database;
	$productname = $_GET['name'];
	$result = $database->query("SELECT * FROM Ingredient WHERE pname = '$productname'");
	if($result) {
		$pinfo = $result->fetch_assoc();
		$pinfo = json_encode($pinfo);
		echo $pinfo;
	}
	else {
		echo $result;
	}

	

});

$app->run();
?>

