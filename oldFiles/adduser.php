<?php
	session_start();
	if(isset($_POST['firstname'] && $_POST['lastname'] && $_POST['username'] && $_POST['password'] && $_POST['passwordconfirm'])){
		if ($_POST['password'] == $_POST['passwordconfirm']){
			$fname = $_POST['firstname'];
			$lname = $_POST['lastname'];
			$email = $_POST['username'];
			$password = $_POST['password'];
		}
		else{
			header('Location: registration.html');
			exit;
		}
	}
	$link = mysqli_connect("localhost","root", "root", "VirtualPantryDB") or die("Error connecting to user database");
	$query = "INSERT INTO User values (\"" . $fname . "\", \"" . $lname . "\", \"" . $email . "\",\"" . $password . "\", 0, 0)";
	$result = mysqli_query($link, $query);
	$query = "SELECT uid from User where email = \"" . $email . "\"";
	$result = mysqli_query($link, $query);
	$_SESSION['uid'] = $result;
	header('Location: home.html');
	exit;
?>