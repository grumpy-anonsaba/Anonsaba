<!DOCTYPE html>
<html lang="en">
<head>
	<title>Anonsaba Installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" type="text/css" href="/pages/css/img_globals.css">
<link rel="stylesheet" type="text/css" href="/pages/css/front.css" />
<link rel="stylesheet" type="text/css" href="/pages/css/site_front.css" />
<link rel="stylesheet" type="text/css" href="/pages/css/site_global.css">
<link rel="stylesheet" type="text/css" href="/pages/css/menu_global.css" />
<form method="POST">
</head>
<body>
<h2>Anonsaba 2.0 Installation</h2><br /><br />
<form>
{% if success == 0 %}
<label for="installpass">Installation Password</label>
<input type="text" name="installpass" />&nbsp;&nbsp;<input type="submit" name="checkpass" value="Submit">
{% elseif success == 1 %}
<h2>Main Configuration</h2><br />
<label for="sitename">Site Name:</label>
<input type="text" name="sitename" /><br />
<label for="slogan">Slogan:</label>
<input type="text" name="slogan" /><br />
<label for="slogan">IRC:</label>
<input type="text" name="irc" /><br />
<label for="timgh">Thread Image Height:</label>
<input type="text" name="timgh" /><br />
<label for="timgw">Thread Image Width:</label>
<input type="text" name="timgw" /><br />
<label for="rimgh">Reply Image Height:</label>
<input type="text" name="rimgh" /><br />
<label for="rimgw">Reply Image Width:</label>
<input type="text" name="rimgw" /><br />
<label for="bm">Ban Message:</label>
<input type="text" name="bm" /><br /><br />
<h2>Management login</h2><br />
<label for="username">User Name:</label>
<input type="text" name="username" /><br />
<label for="password">Password:</label>
<input type="text" name="password" /><br />&nbsp;&nbsp;<input type="submit" name="submit" value="Submit">
{% elseif success == 2 %}
Anonsaba 2.0 install is complete!
{% endif %}
</body>
</html>
