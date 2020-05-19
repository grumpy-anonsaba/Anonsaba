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
				{% if errorfound %}
					<div class="errormsg">
						{{errormsg}}
					</div>
				{% endif %}
				<form method="post" action='index.php?side={{current}}&action={{action}}&acti=login'>
					<div class="username{% if errorfound %}error{% endif %}">
						<input type="text" name="username" value="{% if errorfound %}{{username}}{% endif %}" placeholder="Username" />
					</div>
					<div class="password{% if errorfound %}error{% endif %}">
						<input type="password" name="password" placeholder="Password" />
					</div>
					<div class="submit{% if errorfound %}error{% endif %}">
						<input type="submit" name="login" value="Login" />
					</div>
				</form>
			</div>
		</div>
	</body>
</html>