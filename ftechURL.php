<?php require_once "server/config.php" ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Go to URL</title>
</head>
<body>
<?php
if(isset($_GET["o"]))
{
	$q = mysqli_query($db, "SELECT * FROM link WHERE urlCode = '{$_GET["o"]}'");
	if(mysqli_num_rows($q) > 0)
	{
		$o = mysqli_fetch_array($q);
		echo '<center><a href="'. $o["realSite"] .'"><button type="button">Tiếp tục</button></a>';
	}
	else
		echo "<p>Liên kết không tồn tại!</p>";
}
else
	echo "<p>Không dữ liệu liên kết được cung cấp!</p>";

mysqli_close($db);
?>
</body>
</html>