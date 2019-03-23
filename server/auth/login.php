<?php
require_once "../config.php";

session_start();
header('Content-Type: application/json');

$statusLogin = array();

if(!empty($_POST["username"]) && !empty($_POST["password"]))
{
	if(mysqli_num_rows(mysqli_query($db, "SELECT * FROM admin WHERE username='{$_POST["username"]}'")) > 0)
		if(mysqli_num_rows(mysqli_query($db, "SELECT * FROM admin WHERE password='{$_POST["password"]}'")) > 0)
		{
			$statusLogin = array("error" => false, "status" => "Login success!");
			$_SESSION["admin"] = true;

			mysqli_close($db);
		}
		else
			$statusLogin = array("error" => true, "status" => "Password not correct!");
	else
		$statusLogin = array("error" => true, "status" => "Username not exist!");
}
else
	$statusLogin = array("error" => true, "status" => "Username or password not correct!");

print json_encode($statusLogin);