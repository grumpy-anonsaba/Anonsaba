<html>
	<head>
		<title>{{sitename}} - Management Login</title>
		<link rel="stylesheet" type="text/css" href="/pages/css/manage_login.css">
		<link rel="stylesheet" type="text/css" href="/pages/css/global.css">
		<link rel="apple-touch-icon" sizes="180x180" href="/pages/images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/pages/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/pages/images/favicon-16x16.png">
		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
	</head>
	<body>
		<div id="container">
			<div class="header">
				{{sitename}} Login
			</div>
			<div class="loginformarea">
				{% if errorfound %}
					<div class="alert alert-danger alert-dismissible fade in">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Error!</strong> {{errormsg}}
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