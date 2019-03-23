<?php
require_once "../../../server/config.php";

session_start();
header('Content-Type: application/json');

$linkStatus = array();

if(isset($_SESSION["admin"]) && !empty($_POST["url"]))
{
	/* REAL POST URL */
	$URL = (string) $_POST["url"];

	/* CREATE LINK CODE */
	$linkCode = time();

	/* CREATE LINK URL */
	$link = (string) "";
	$link .= (isset($_SERVER["HTTPS"])) ? "https://" : "http://";
	$link .= $_SERVER["HTTP_HOST"];
	$link .= "/url/";
	$link .= $linkCode;

	$inputLink = mysqli_query($db, "INSERT INTO link (id, urlCode, realSite) VALUES (NULL, '{$linkCode}', '{$URL}')");
	mysqli_close($db);

	if($inputLink)
		$linkStatus = array("error" => false, "message" => "Create url success!", "url" => $link);
	else
		$linkStatus = array("error" => true, "message" => "Can't create url!", "debug" => mysqli_error($db));
}
else
	$linkStatus = array("error" => true, "message" => "Error session or method");

print json_encode($linkStatus);
