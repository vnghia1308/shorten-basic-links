<?php
session_start();

if(is_array($_SESSION))
{
	session_destroy();
	header("Location: /admin.php");
}
