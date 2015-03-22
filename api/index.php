<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();

$database = new mysqli("localhost", "root", "root", "VirtualPantryDB");
if ($database->connect_errno)
    die("Connection failed: " . $database->connect_error);


#get product by searching by name
$app->get('/addProduct', function($name, $id) {

	global $database;

	$name = $_GET['name'];
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

    $pjson = file_get_contents('http://api.foodessentials.com/searchprods?q='.$name.'&sid='.$sid.'&n=5&s=1&f=json&v=2.00&api_key=x4c59ktead886t2urzcdju54');
    $productList = json_decode($pjson);
    $upc = $productList->productsArray[0]->upc;

    $product = file_get_contents('http://api.foodessentials.com/productscore?u='.$upc.'&sid='.$sid.'&f=json&api_key=x4c59ktead886t2urzcdju54');
    $product = json_decode($product);

    $jsonProduct = array();
    $jsonProduct['pid'] = $upc;
    $jsonProduct['fat'] = $product->nutrients[5]->nutrient_fe_level;
	$jsonProduct['chol'] = $product->nutrients[1]->nutrient_fe_level;
	$jsonProduct['sodium'] = $product->nutrients[3]->nutrient_fe_level;
	$jsonProduct['carb'] = $product->nutrients[4]->nutrient_fe_level;
	$jsonProduct['protien'] = $product->nutrients[2]->nutrient_fe_level;
	$jsonProduct['barcode'] = $upc
	$jsonProduct['cal'] = $product->nutrients[0]->nutrient_fe_level;
	$jsonProduct['name'] = $product->product_name;

	$response = $database->query(INSERT INTO PantryList (uid, pid, barcode, pname) VALUES ($id, $upc, $upc, $jsonProduct['name']));

    echo $response;

});


$app->get('/getRecipes', function($query)
{
	$query = $_GET['query'];
	
}

$app->run();

?>