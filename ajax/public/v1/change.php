<?php
require_once "../../../server/config.php";

session_start();
header('Content-Type: application/json');

$changeStatus = array();

if(isset($_SESSION["admin"]) && !empty($_POST["id"]) && !empty($_POST["new"]) && isset($_GET["action"]))
{
	$i = (int) $_POST["id"]; $n = (string) $_POST["new"]; $a = (string) $_GET["action"];

	if(mysqli_num_rows(mysqli_query($db, "SELECT * FROM {$a} WHERE id={$i}")) > 0)
	{
		$c = ($a == "link") ? "realSite" : "password";
		$u = mysqli_query($db, "UPDATE {$a} SET {$c}='{$n}' WHERE id={$i}");

		if($u)
			$changeStatus = array("error" => false, "message" => "Change {$a} success!");
		else
			$changeStatus = array("error" => true, "message" => "Can't change this {a}!", "debug" => mysqli_error($db));

		mysqli_close($db);
	}
	else
		$changeStatus = array("error" => true, "message" =>  "This {$a} not exist in database!");
}
else
	$changeStatus = array("error" => true, "message" => "Error session or method");

print json_encode($changeStatus);