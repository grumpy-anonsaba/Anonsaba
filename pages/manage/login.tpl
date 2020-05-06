<html>
	<head>
		<title>{{sitename}} - Management Login</title>
		<link rel="stylesheet" type="text/css" href="/pages/css/manage_login.css">
		<link rel="stylesheet" type="text/css" href="/pages/css/global.css">
	</head>
	<body>
		<div id="container">
			<div class="header">
				{{sitename}} Login
			</div>
			<div class="loginformarea">
				<form method="post" action='index.php?acti=login&side={{side}}&action={{action}}'>
					<div class="username">
						<input type="text" name="username" placeholder="Username" />
					</div>
					<div class="password">
						<input type="password" name="password" placeholder="Password" />
					</div>
					<div class="submit">
						<input type="submit" name="login" value="Login" />
					</div>
				</form>
			</div>
		</div>
	</body>
</html>