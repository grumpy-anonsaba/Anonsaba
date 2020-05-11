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
					<a href="index.php?side=main&action={{action}}">
						<div class="mainbutton{% if current == 'main' %}selected{% endif %}">
							Main
						</div>
					</a>
					{% if level == 1 %}
						<a href="index.php?side=site&action={{action}}">
							<div class="sitebutton{% if current == 'site' %}selected{% endif %}">
								Site Administration
							</div>
						</a>
					{% endif %}
					<a href="index.php?side=boards&action={{action}}">
						<div class="{% if level > 1 %}mod{% endif %}boardbutton{% if current == 'boards' %}selected{% endif %}">
							Board Administration
						</div>
					</a>
					<a href="index.php?side=mod&action={{action}}">
						<div class="{% if level > 1 %}mod{% endif %}modbutton{% if current == 'mod' %}selected{% endif %}">
							Moderation
						</div>
					</a>
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
					<div class="actions">
						{% for i in 0..arraynum %}
							<a href="index.php?side={{current}}{{urls[i]}}">
								<div class="action">
									{{names[i]}}
								</div>
							</a>
						{% endfor %}
					</div>
				</div>
				<div class="modulearea">
					<div class="moduleheader">
						Test
					</div>
					<div class="modules">
						tost
					</div>
				</div>
			</div>
		</div>
	</body>
</html>