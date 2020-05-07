<html>
	<head>
		<title>{{sitename}} - Management Login</title>
		<link rel="stylesheet" type="text/css" href="/pages/css/manage_login.css">
		<link rel="stylesheet" type="text/css" href="/pages/css/global.css">
		<link rel="apple-touch-icon" sizes="180x180" href="/pages/images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/pages/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/pages/images/favicon-16x16.png">
	</head>
	<body>
		<div id="container">
			<div class="header">
				{{sitename}} Login
			</div>
			<div class="loginformarea">
				<form method="post" action='index.php?acti=login&side={{side}}&action={{action}}'>
					<!--{% if error %}
						<div class="errormsg">
							{{errormsg}}
						</div>
					{% endif %}-->
					<div class="errormsg">
						<div class="errormsgtxt">
							This is a test error!
						</div>
					</div>
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