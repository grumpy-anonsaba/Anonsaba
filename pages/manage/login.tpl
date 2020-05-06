<html>
	<head>
		<title>{{sitename}} - Management Login</title>
		<link rel="stylesheet" type="text/css" href="/pages/css/manage_login.css">
		<link rel="stylesheet" type="text/css" href="/pages/css/global.css">
	</head>
	<body>
		<div id="container">
			<div class="login">
				<h1>{{sitename}} Login</h1>
				<form method="post" action='index.php?acti=login&side={{side}}&action={{action}}'>
					<p><input type="text" name="username" value="" placeholder="Username"></p>
					<p><input type="password" name="password" value="" placeholder="Password"></p>
					<p class="submit"><input type="submit" name="login" value="Login"></p>
				</form>
			</div>
		</div>
	</body>
</html>