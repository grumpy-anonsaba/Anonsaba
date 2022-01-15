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
		<link rel="stylesheet" href="/pages/css/newglobal.css">
		<link rel="apple-touch-icon" sizes="180x180" href="/pages/images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/pages/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/pages/images/favicon-16x16.png">
		<!-- Scripts -->
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
		<script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" data-auto-replace-svg="nest"></script>
	</head>
	<body>
		<div class="board-posts-newpost-wrapper">
			<div class="board-posts-newpost">
				<div class="board-posts-newpost-fr">
					<i class="far fa-times-circle" id="board-posts-newpost-close" title="Close"></i>
				</div>
				<div class="board-posts-newpost-box">
					<div class="board-posts-newpost-box-flex">
						<div class="board-posts-newpost-box-flex-flexchild">
							Some text
						</div>
						<div class="board-posts-newpost-box-flex-flexchild">
							Some other text
						</div>
					</div>
				</div>
			</div>
		</div>
		<nav class="board-navigation">
			<div class="board-navigation-home">
				<button id="board-navigation-home-button">Home</button>
			</div>
			<div class="board-navigation-style">
				<button>Styles</button>
				<div class="board-navigation-dropdown">
					<a>Light</a>
					<a>Dark</a>
				</div>
			</div>
			{% for section in boards %}
				<div class="board-navigation-section" id="{{section.name}}">
					<button>{{section.name}}</button>
					<div class="board-navigation-dropdown">
						{% for boards in section.boards %}
							<a title="{{boards.desc}}" href="{{weburl}}{{boards.name}}/">{{boards.desc}}</a>
						{% endfor %}
					</div>
				</div>
			{% endfor %}
		</nav>
		<!-- Board Container -->
		<div class="board-wrapper">
			<div class="board-container">
				<header class="board-head-wrapper">
					<div class="board-head-header">
						<h1>/{{boardname}}/ - {{boarddesc}}</h1>
					</div>
				</header>
				<!-- Thread posts -->
				<div class="board-posts">
					{% for thread_post in thread_posts %}
						{% if thread_post.parent == 0 %}
							<article class="board-posts-thread">
								<div class="board-posts-thread-header">
									<h2>{{thread_post.subject}}</h2>
									<div class="board-posts-thread-header-date">
										&nbsp;-&nbsp;{{thread_post.time|date('m/d/Y h:i', 'America/Chicago')}} {% if thread_post.time|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}
									</div>
									<div class="board-posts-thread-header-fr">
										<i class="fas fa-flag" title="Report"></i>&nbsp;<i class="fas fa-reply" title="Quick Reply"></i>&nbsp;<i class="fas fa-trash-alt" title="Delete"></i>
									</div>
									<br>
									<div class="board-posts-thread-header-postby">
										Posted by <div class="board-posts-thread-header-postname">{{thread_post.name}}</div>
									</div>
								</div>
								<div class="board-posts-thread-post-message">
									{% for thread_file in thread_files %}
										{% if thread_file.id == thread_post.id %}
											<br>
											{% if thread_file.type != 'youtube' %}
												<div class="board-posts-thread-post-image">
													<img src="{{weburl}}board/{{boardname}}/thumb/{{thread_file.file}}" title="{{thread_file.original}}" class="board-posts-thread-post-image">
												</div>
											{% else %}
												<div class="board-posts-thread-post-image">
													<iframe width="512" height="267" src="https://www.youtube.com/embed/{{thread_file.file}}" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
												</div>
											{% endif %}
										{% endif %}
									{% endfor %}
									<br>
									<div class="board-posts-thread-post-replies">
										{% for thread_reply in thread_replies %}
											{% if thread_reply.threadid == thread_post.id %}
												{% if thread_reply.replies >= 1 %}<i class="fas fa-comments" title="View thread"></i> {{thread_reply.replies}}  {% if thread_reply.replies == 1 %}Reply{% else %}Replies{% endif %}{% else %}<i class="fas fa-comment" title="View thread"></i> {{thread_reply.replies}} Replies{% endif %}
											{% endif %}
										{% endfor %}
									</div>
								</div>
							</article>
						{% endif %}
						<div class="board-posts-clear"></div>
					{% endfor %}
				</div>
				<!-- End thread posts -->
			</div>
		</div>
		<!-- End board container -->
		<footer class="board-footer">
			<div class="board-footer-center"><i class="fas fa-share-square" title="New post" id="board-newpost"></i></div>
			<div class="board-footer-fr">{{sitename}} is powered by <a href="https://www.anonsaba.org/" target="_blank">Anonsaba {{version}}</a></div>
		</footer>
		<!-- Scripts -->
		<script>
			function generatePassword() {
				var possible = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789![]{}()%&*$#^<>~@|';
				var text = '';
				for(var i=0; i < 16; i++) {
					text += possible.charAt(Math.floor(Math.random() * possible.length));
				}
				return text;
			}
			$(function() {
				if (document.cookie.match(/^(.*;)?\s*board-posts-password\s*=\s*[^;]+(.*)?$/) === null) {
					document.cookie = "board-posts-password=" + generatePassword() + "; expires=Fri, 31 Dec 9999 23:59:59 GMT"
				} else {
					console.log('Cookie!');
				}
			});
			$('#board-navigation-home-button').click(function () {
				$(location).attr('href', '{{weburl}}')
			});
			$('#board-newpost').click(function() {
				$('.board-posts-newpost-wrapper').fadeIn('slow');
			});
			$('#board-posts-newpost-close').click(function() {
				$('.board-posts-newpost-wrapper').fadeOut('slow');
			});
		</script>
	</body>
</html>