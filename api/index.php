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
	$id = $_GET['uid'];
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
    $jsonProduct['pid'] = $name;
    $jsonProduct['fat'] = $product->product->nutrients[5]->nutrient_fe_level;
	$jsonProduct['chol'] = $product->product->nutrients[1]->nutrient_fe_level;
	$jsonProduct['sodium'] = $product->product->nutrients[3]->nutrient_fe_level;
	$jsonProduct['carb'] = $product->product->nutrients[4]->nutrient_fe_level;
	$jsonProduct['protien'] = $product->product->nutrients[2]->nutrient_fe_level;
	$jsonProduct['barcode'] = $upc
	$jsonProduct['cal'] = $product->product->nutrients[0]->nutrient_fe_level;
	$jsonProduct['name'] = $product->product->product_name;

	$response = $database->query('INSERT INTO PantryList (uid, pid, barcode, pname) VALUES ('.$id.', '.$jsonProduct['pid'].', '.$upc.', '.$jsonProduct['name'].')');

    echo $response;
});

$app->get('/removeProduct', function($name, $id) 
{
	global $database;
	$name = $_GET['name'];
	$id = $_GET['uid'];

	$response = $database->query(DELETE FROM PantryList WHERE pid = $name AND uid = $id);
	echo = $response;


})

$app->get('/getPantryList', function($id) 
{
	global $database;
	$id = $_GET['uid'];

	$response = $database->query(SELECT pname FROM PantryList WHERE uid = $id);
	$response = $response->fetch_assoc();
	$response = json_encode($response);
	echo = $response;


})


#$app->get('/getRecipes', function($query)
$app->get('/', function()
{
	#$query = $_GET['query'];
	$query = 'bacon';
	$jresponse = file_get_contents('http://api.yummly.com/v1/api/recipes?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e&q=&allowedIngredient[]='.$query);
	$recipe_list = json_decode($jresponse);

	$id_1 = $recipe_list->matches[0]->id;
	$id_2 = $recipe_list->matches[1]->id;
	$id_3 = $recipe_list->matches[2]->id;
	$id_4 = $recipe_list->matches[3]->id;
	$id_5 = $recipe_list->matches[4]->id;

	$url_1 = file_get_contents('http://api.yummly.com/v1/api/recipe/'.$id_1.'?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e');
	$url_1a = json_decode($url_1);
	$url_2 = file_get_contents('http://api.yummly.com/v1/api/recipe/'.$id_2.'?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e');
	$url_2a = json_decode($url_2);
	$url_3 = file_get_contents('http://api.yummly.com/v1/api/recipe/'.$id_3.'?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e');
	$url_3a = json_decode($url_3);
	$url_4 = file_get_contents('http://api.yummly.com/v1/api/recipe/'.$id_4.'?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e');
	$url_4a = json_decode($url_4);
	$url_5 = file_get_contents('http://api.yummly.com/v1/api/recipe/'.$id_5.'?_app_id=6e415947&_app_key=5e4133f9b50bb1bf39382a83d84b8d9e');
	$url_5a = json_decode($url_5);

	$url_a = preg_replace('/(\/+)/','/',$url_1a->source->sourceRecipeUrl);
	$url_b = preg_replace('/(\/+)/','/',$url_2a->source->sourceRecipeUrl);
	$url_c = preg_replace('/(\/+)/','/',$url_3a->source->sourceRecipeUrl);
	$url_d = preg_replace('/(\/+)/','/',$url_4a->source->sourceRecipeUrl);
	$url_e = preg_replace('/(\/+)/','/',$url_5a->source->sourceRecipeUrl);

	$recipe_array  = array();
	$recipe_array[$recipe_list->matches[0]->recipeName] = $url_a;
	$recipe_array[$recipe_list->matches[1]->recipeName] = $url_b;
	$recipe_array[$recipe_list->matches[2]->recipeName] = $url_c;
	$recipe_array[$recipe_list->matches[3]->recipeName] = $url_d;
	$recipe_array[$recipe_list->matches[4]->recipeName] = $url_e;

	$recipe_array = json_encode($recipe_array);

	echo $recipe_array;
});
$app->run();
?>
