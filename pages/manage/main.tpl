<html>
	<head>
		<link href="/pages/css/manage.css" rel="stylesheet" type="text/css" />
		<link href="/pages/css/global.css" rel="stylesheet" type="text/css" />
		<title>{{sitename}} - Management</title>
		<script type="text/javascript">
			<!--
				function logout() {
					window.location.replace("index.php?action=logout");
				}
			//-->
		</script>
	</head>
	<body>
		<div id="content_wrap">
			<div class="main">
				<div class="top_header">
					{{sitename}} Management
				</div>
				<div class="top_bar">
					Text goes brr
					<div class="loginlogout">
						Logged in as 
						<strong>
							{{username}}
						</strong>
						&nbsp;
						<input type="submit" value="Logout" onclick="return logout()" />
					</div>
				</div>
			</div>
		</div>
	</body>
</html>