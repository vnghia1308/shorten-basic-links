<?php
require_once "../../../server/config.php";

session_start();
header('Content-Type: application/json');

$deleteStatus = array();

if(isset($_SESSION["admin"]) && !empty($_POST["id"]) && isset($_GET["action"]))
{
	$i = (int) $_POST["id"]; $a = (string) $_GET["action"];

	if(mysqli_num_rows(mysqli_query($db, "SELECT * FROM {$a} WHERE id={$i}")) > 0)
	{
		if($a == "link")
		{
			$d = mysqli_query($db, "DELETE FROM {$a} WHERE id={$i}");

			if($d)
				$deleteStatus = array("error" => false, "message" => "Deleted this url success!");
			else
				$deleteStatus = array("error" => true, "message" => "Can't delete this url!", "debug" => mysqli_error($db));

			mysqli_close($db);
		}
		else
		{
			if($a == "admin" && mysqli_num_rows(mysqli_query($db, "SELECT * FROM {$a}")) > 1)
			{
				$d = mysqli_query($db, "DELETE FROM {$a} WHERE id={$i}");

				if($d)
					$deleteStatus = array("error" => false, "message" => "Deleted this admin success!");
				else
					$deleteStatus = array("error" => true, "message" => "Can't delete this admin!", "debug" => mysqli_error($db));

				mysqli_close($db);
			}
			else
				$deleteStatus = array("error" => true, "message" => "Can't delete admin if only one administrator!");
		}
	}
	else
		$deleteStatus = array("error" => true, "message" => ($a == "link") ? "This url" : "This admin" . " not exist in database!");
}
else
	$deleteStatus = array("error" => true, "message" => "Error session or method");

print json_encode($deleteStatus);