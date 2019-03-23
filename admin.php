<?php
require_once "server/config.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Admin</title>
</head>
<body>
	<?php if(empty($_SESSION['admin'])): ?>
	<form id="login" action="server/auth/login.php" method="post">
		<label for="username">Tên đăng nhập:</label><br /> <input id="username" name="username" value="" type="text" /><br /><br />
		<label for="password">Mật khẩu:</label><br /> <input id="password" name="password" value="" type="password" /> <br /><br />
		<input id="goLogin" name="goLogin" value="Đăng nhập" type="submit" />
	</form>
	<div id="loginStatus"></div>
	<?php else: ?>
	<a href="server/auth/logout.php"><input id="goLogin" name="goLogin" value="Đăng xuất" type="submit" /></a><br />
	<h3>Tạo liên kết</h3>
	<form id="link" action="ajax/public/v1/link.php" method="post">
		<label for="url">Nhập liên kết:</label><br /> <input id="url" name="url" value="" type="text" /><br /><br />
		<input id="goLink" name="goLink" value="Khởi tạo" type="submit" />
	</form>
	<div id="linkStatus"></div>
	<h3>Thông kê và chỉnh sửa liên kết</h3>
	<table id="link-table" style="width: 30%">
		<tr>
			<td>Liên kết rút gọn</td>
			<td>Trang đích</td>
			<td></td>
			<td></td>
		</tr>
		<?php $q = mysqli_query($db, "SELECT * FROM link"); 
		while($o = mysqli_fetch_array($q)): ?>
		<tr link-id="<?= $o["id"] ?>">
			<td id="shortUrl"><?= (isset($_SERVER["HTTP"])) ? "https://" : "http://" . $_SERVER["HTTP_HOST"] . "/file/" . $o["urlCode"] ?></td>
			<td id="realSite"><?= $o["realSite"] ?></td>
			<td id="changeUrl"><a href="#" onclick="change_(<?= $o["id"] ?>, 'link')">Chỉnh sửa</a></td>
			<td id="deleteUrl"><a href="#" onclick="delete_(<?= $o["id"] ?>, 'link')">Xóa</a></td>
		</tr>
		<?php endwhile ?>
	</table>
	<div id="linkActionStatus"></div>
	<h3>Quản lý quản trị viên</h3>
	<form id="admin" action="server/auth/admin.php" method="post">
		<label for="username">Tên đăng nhập:</label><br /> <input id="username" name="username" value="" type="text" /><br /><br />
		<label for="password">Mật khẩu:</label><br /> <input id="password" name="password" value="" type="password" /> <br /><br />
		<input id="addAdmin" name="addAdmin" value="Thêm đăng nhập" type="submit" />
	</form>
	<table id="admin-table" style="width: 30%">
		<tbody>
			<tr>
				<td>Tên đăng nhập</td>
				<td>Mật khẩu</td>
				<td></td>
				<td></td>
			</tr>
			<?php $q = mysqli_query($db, "SELECT * FROM admin"); 
			while($a = mysqli_fetch_array($q)): ?>
			<tr admin-id="<?= $a["id"] ?>">
				<td id="a_username"><?= $a["username"] ?></td>
				<td id="a_password"><?= $a["password"] ?></td>
				<td action="changeInfo"><a href="#" onclick="change_(<?= $a["id"] ?>, 'admin')">Đổi mật khẩu</a></td>
				<td action="deleteAdmin"><a href="#" onclick="delete_(<?= $a["id"] ?>, 'admin')">Xóa</a></td>
			</tr>
			<?php endwhile ?>
		</tbody>
	</table>
	<div id="adminStatus"></div>
	<?php endif ?>
</body>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script type="text/javascript">
<?php if(empty($_SESSION["admin"])): ?>
$("#login").on('submit',(function(e) {
	e.preventDefault();
	$.ajax({
		url: $("#login").attr("action"),
		type: "POST",
		data:  new FormData(this),
		dataType: "json",
		contentType: false,
		cache: false,
		processData:false,
		beforeSend: function () {
			// before send login
		},
		success: function(o) {
			if(o.error != true)
				location.reload()
			else
				$("#loginStatus").text(o.status)
		},
		error: function(e){
			console.log(e)
		}
   })
}))
<?php else: ?>
$("#link").on('submit',(function(e) {
	e.preventDefault();
	if($("#url").val())
	{
		$.ajax({
			url: $("#link").attr("action"),
			type: "POST",
			data:  new FormData(this),
			dataType: "json",
			contentType: false,
			cache: false,
			processData:false,
			beforeSend: function () {
				// before send request create link
			},
			success: function(o) {
				if(o.error != true)
					$("#linkStatus").text("Tạo liên kết thành công: " + o.url)
				else
					$("#linkStatus").text(o.message)
			},
			error: function(e){
				console.log(e)
			}
	   })
	}
}))

function change_(i, a) {
	var s = (a == "link") ? "liên kết" : "mật khẩu";
	t = prompt("Vui lòng nhập " + s + " mới:", "")

	if(t)
	{
		$.ajax({
			url: "ajax/public/v1/change.php?action=" + a,
			type: "POST",
			data:  
			{
				id: i,
				new: t
			},
			dataType: "json",
			beforeSend: function () {
				// before send request change link
			},
			success: function(o) {
				var d = (a == "link") ? "link-id" : "admin-id";
				r = (a == "link") ? "realSite" : "a_password";
				s = (a == "link") ? "linkActionStatus" : "adminStatus"

				if(o.error != true)
					$("tr["+ d +"='" + i + "']").find("#" + r).text(t)
				else
					$("#" + s).text(o.message)
				console.log(o)
			},
			error: function(e){
				console.log(e)
			}
	   })
	}
}

function delete_(i, a) {
	var c = confirm("Are you sure? This is can't undo!");

	if(c)
	{
		$.ajax({
			url: "ajax/public/v1/delete.php?action=" + a,
			type: "POST",
			data:  
			{
				id: i
			},
			dataType: "json",
			beforeSend: function () {
				// before send request delete link
			},
			success: function(o) {
				var d = (a == "link") ? "link-id" : "admin-id";
				s = (a == "link") ? "linkActionStatus" : "adminStatus"

				if(o.error != true)
					$("tr["+ d +"='" + i + "']").remove()
				else
					$("#" + s).text(o.message)
			},
			error: function(e){
				console.log(e)
			}
	   })
	}
}

$("#admin").on('submit',(function(e) {
	e.preventDefault();
	$.ajax({
		url: $("#admin").attr("action"),
		type: "POST",
		data:  new FormData(this),
		dataType: "json",
		contentType: false,
		cache: false,
		processData:false,
		beforeSend: function () {
			// before send request add admin
		},
		success: function(o) {
			if(o.error != true)
			{
				$("#username").val(null)
				$("#password").val(null)

				var s = "'admin'"
				$("#admin-table").find("tbody").append('<tr admin-id="' + o.id + '">' +
					'<td id="a_username">' + o.user + '</td>' +
					'<td id="a_password">' + o.pass + '</td>' +
					'<td action="changeInfo"><a href="#" onclick="change_(' + o.id + ', ' + s + ')">Đổi mật khẩu</a></td>' +
					'<td action="deleteAdmin"><a href="#" onclick="delete_(' + o.id + ', ' + s + ')">Xóa</a></td>' +
					'</tr>')
			}
			else
				$("#adminStatus").text(o.status)
		},
		error: function(e){
			console.log(e)
		}
   })
}))
<?php endif ?>
</script>
</html>