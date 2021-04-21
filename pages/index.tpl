<!DOCTYPE html>
<html>
	<head>
		<!-- Site Title -->
		<title>{{sitename}}</title>
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
		<link rel="stylesheet" href="/pages/css/newglobal.css">
		<link rel="apple-touch-icon" sizes="180x180" href="/pages/images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/pages/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/pages/images/favicon-16x16.png">
		<!-- Scripts -->
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	</head>
	<body>
		<!-- Site container -->
		<div class="front-container">
			<header class="front-head-wrapper">
				<div class="front-sitename">
					<h1>{{sitename}}</h1>
				</div>
				<div class="front-slogan">
					<h2>"{{slogan}}"</h2>
				</div>
			</header>
			<section class="front-content-wrapper">
				<section class="front-site-info-wrapper">
					<section class="front-site-info">
						<nav class="front-navbar">
							<button id="news">News</button>
							<button id="faq">FAQ</button>
							<button id="rules">Rules</button>
						</nav>
						<section class="front-recent-posts">
							<h2>Recent Posts</h2>
							{% for recentpost in recentposts %}
								<article class="front-recent-posts-posts">
									>>>/{{recentpost.boardname}}/{{recentpost.id}} - {{recentpost.message|striptags("")|slice(0, 60)|raw}}{% if recentpost.message|length > 60 %}...{% endif %}
								</article>
							{% endfor %}
						</section>
						<section class="front-site-stats">
							<h2>Site Statistics</h2>
							<article class="front-site-stats-stats">
								<table>
									<tbody>
										<tr>
											<th>Total Posts:</th>
											<td>{{postcount}}</td>
										</tr>
										<tr>
											<th>Unique Users:</th>
											<td>{{uniqueusers}}</td>
										</tr>
										<tr>
											<th>Active Content:</th>
											<td>{{activecontent}}</td>
										</tr>
									</tbody>
								</table>
							</article>
						</section>
					</section>
					<section class="front-site-content" id="front-site-content">
						{% for entry in entries %}
							<article class="front-site-content-content">
								<h3>{{entry.subject}}{% if entry.type == 'news' %} by {{entry.by}} - {{entry.date|date('m/d/Y h:i', 'America/Chicago')}} {% if entry.date|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}{% endif %}</h3>
								<p>
									{{entry.message|raw}}
								</p>
							</article>
						{% endfor %}
						<article class="front-site-content-content-switch">
							{% if not view %}
								{% if pages -1 > 0 %}
									{% for i in range(0, pages ) %}
										{% if page != i %}
											<button id="page-{{i}}" class="front-site-content-content-switch-ns">
												{{i}}
											</button>
										{% else %}
											<button id="current-page" class="front-site-content-content-switch-s" disabled>
												{{i}}
											</button>
										{% endif %}
									{% endfor %}
								{% else %}
									0
								{% endif %}
							{% endif %}
						</article>
					</section>
				</section>
				<section class="front-board-list-wrapper">
					<section class="front-board-list">
						<h1>Boards</h1>
						{% for section in boards %}
							<article class="front-board-list-list">
								<h2>
									{{section.name}}
									<div class="front-board-list-list-fr" id="toggle-{{section.name}}{% if section.hidden == 1 %}-on{% else %}-off{% endif %}">
										{% if section.hidden == 1 %}
											&#43;
										{% else %}
											&#8722;
										{% endif %}
									</div>
								</h2>
								<section id="{{section.name}}" {% if section.hidden == 1 %}style="display: none;"{% endif %}>
									{% for boards in section.boards %}
										<li>{{boards.desc}}</li>
									{% endfor %}
								</section>
							</article><div class="front-board-list-list-clear"></div>
						{% endfor %}
					</section>
				</section>
			</section>
			<footer class="front-footer">
				{{sitename}} is powered by <a href="https://www.anonsaba.org/" target="_blank">Anonsaba {{version}}</a>
			</footer>
		</div>
		<!-- End Site container -->
		<!-- Scripts to run -->
		<script>
			function setCSS(id) {
				$(id).toggleClass("front-site-content-content-switch-s");
				$(id).css("border-width", "3px");
			}
			function removeCSS(id) {
				$(id).removeClass("front-site-content-content-switch-s");
				$(id).removeAttr("style");
			}
			function disableButton(id) {
				$(id).attr("disabled", "true");
			}
			function enableButton(id) {
				$(id).removeAttr("disabled");
			}
			$(function() {
				setCSS("#news");
				disableButton("#news");
				$('[id^="toggle-"]').attr('title', 'Click to show/hide');
			});
			$("#news").click(function() {
				setCSS("#news");
				var others = ["#faq", "#rules"];
				for (i = 0; i < others.length; i++) {
					removeCSS(others[i]);
					enableButton(others[i]);
				}
				$("#front-site-content").load("index.php  #front-site-content >*");
				disableButton("#news");
			});
			$("#faq").click(function() {
				setCSS("#faq");
				var others = ["#news", "#rules"];
				for (i = 0; i < others.length; i++) {
					removeCSS(others[i]);
					enableButton(others[i]);
				}
				$("#front-site-content").load("index.php?view=faq  #front-site-content >*");
				disableButton("#faq");
			});
			$("#rules").click(function() {
				setCSS("#rules");
				var others = ["#faq", "#news"];
				for (i = 0; i < others.length; i++) {
					removeCSS(others[i]);
					enableButton(others[i]);
				}
				$("#front-site-content").load("index.php?view=rules  #front-site-content >*");
				disableButton("#rules");
			});
			$('#front-site-content').on('click', '[id^="page-"]', function(e) {
				var id = e.target.id;
				var page = id.slice(5);
				if (page == 0) {
					$("#front-site-content").load("index.php  #front-site-content >*");
				} else {
					$("#front-site-content").load("index.php?page="+page+"  #front-site-content >*");
				}
				$("#front-site-content").scrollTop(0);
			});
			$('[id^="toggle-"]').click(function(e) {
				var id = e.target.id;
				var section = id.slice(7);
				var sectionname = section.split("-", 1);
				var status = section.split("-", 2);
				var statuscode = status[1];
				if (statuscode == "on") {
					$("#"+sectionname).fadeIn();
					$("#"+id).html("&#8722;");
					$("#"+id).attr("id", "toggle-"+sectionname+"-off");
				} else if (statuscode == "off") {
					$("#"+sectionname).hide();
					$("#"+id).html("&#43;");
					$("#"+id).attr("id", "toggle-"+sectionname+"-on");
				}
			});
		</script>
	</body>
</html>