<html>
	<head>
		<link href="/pages/css/manage.css" rel="stylesheet" type="text/css" />
		<link href="/pages/css/global.css" rel="stylesheet" type="text/css" />
		<link rel="apple-touch-icon" sizes="180x180" href="/pages/images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/pages/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/pages/images/favicon-16x16.png">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
					<div class="mainbutton">
						Main
					</div>
					<div class="sitebutton">
						Site Administration
					</div>
					<div class="boardbutton">
						Board Administration
					</div>
					<div class="modbutton">
						Moderation
					</div>
					<div class="loginlogout">
						Logged in as 
						<strong>
							{{username}}
						</strong>
						&nbsp;
						<input type="submit" value="Logout" onclick="return logout()" />
					</div>
				</div>
				<div class="left_side">
					<div class="title">
						{{sectionname}}
					</div>
				</div>
			</div>
		</div>
	</body>
</html>