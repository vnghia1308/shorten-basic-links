<?php
require_once "../config.php";

session_start();
header('Content-Type: application/json');

$adminStatus = array();

if(isset($_SESSION["admin"]) && !empty($_POST["username"]) && !empty($_POST["password"]))
{
	$u = $_POST["username"]; $p = $_POST["password"];
	if(mysqli_num_rows(mysqli_query($db, "SELECT * FROM admin WHERE username='{$u}'")) == 0)
	{
		$q = mysqli_query($db, "INSERT INTO admin (id, username, password) VALUES (NULL, '{$u}', '{$p}')");

		if($q)
			$adminStatus = array("error" => false, "message" => "Added admin success!", "user" => $u, "pass" => $p , "id" => mysqli_insert_id($db));
		else
			$adminStatus = array("error" => true, "message" => "Can't add this user admin!", "debug" => mysqli_error($db));

		mysqli_close($db);
	}
	else
		$adminStatus = array("error" => true, "message" => "This user already exist");
} 
else
	$adminStatus = array("error" => true, "message" => "Error session or method");

print json_encode($adminStatus);