<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Site Title -->
		<title>{{boarddesc}}</title>
		<!-- Meta declarations -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- You can uncomment these and use them for SEO
		<meta name="description" content="Enter a description for your site!">
		<meta name="keywords" content="Enter some keywords to find your site!">
		-->
		<!-- CSS -->
		<link rel="stylesheet" href="/pages/css/front_anonsaba_light.css">
		<link rel="stylesheet alternate" href="/pages/css/front_anonsaba_dark.css">
		<link rel="stylesheet" href="/pages/css/global.css">
		<link rel="apple-touch-icon" sizes="180x180" href="/pages/images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/pages/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/pages/images/favicon-16x16.png">
		<!-- Scripts -->
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	</head>
	<body>
		<nav class="board-navigation">
			<div class="board-navigation-home">
				<button>Home</button>
			</div>
			{% for section in boards %}
				<div class="board-navigation-section" id="{{section.name}}">
					<button>{{section.name}}</button>
					<div class="board-navigation-boards" id="toggle-{{section.name}}">
						{% for boards in section.boards %}
							<a title="{{boards.desc}}" href="{{weburl}}{{boards.name}}/">{{boards.desc}}</a>
						{% endfor %}
					</div>
				</div>
			{% endfor %}
		</nav>
		<!-- Board Container -->
		<div class="board-container">
			<header class="board-head-wrapper">
				<div class="board-head-header">
					<h1>/{{boardname}}/ - {{boarddesc}}</h1>
				</div>
			</header>
		</div>
		<!-- End board container -->
		<footer class="board-footer">
				{{sitename}} is powered by <a href="https://www.anonsaba.org/" target="_blank">Anonsaba {{version}}</a>
		</footer>
		<!-- Scripts -->
	</body>
</html>