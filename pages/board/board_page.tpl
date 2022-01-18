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
		<script src="https://use.fontawesome.com/releases/v6.0.0-beta3/js/all.js" data-auto-replace-svg="nest"></script>
	</head>
	<body>
		{% for board_info in board %}
			<!-- Post box -->
			<div class="board-posts-newpost-wrapper">
				<div class="board-posts-newpost">
					<div class="board-posts-newpost-fr">
						<i class="fa-regular fa-circle-xmark" id="board-posts-newpost-close" title="Close"></i>
					</div>
					<div class="board-posts-newpost-box">
						<div class="board-posts-newpost-box-flex">
							<div class="board-posts-newpost-box-flex-flexchild">
								<div class="board-posts-postbox">
									<input type="{% if board_info.forcedanon == 1 %}hidden{% else %}text{% endif %}" name="board-posts-newpost-box-username" id="board-posts-newpost-box-username" placeholder="Name" />{% if board_info.forcedanon == 0 %}<br>{% endif %}
									<input type="{% if board_info.email == 0 %}hidden{% else %}text{% endif %}" name="board-posts-newpost-box-email" id="board-posts-newpost-box-email" placeholder="Email" />{% if board_info.email == 1 %}<br>{% endif %}
									<input type="text" name="board-posts-newpost-box-subject" id="board-posts-newpost-box-subject" placeholder="Subject" /><br>
								</div>
								<div class="board-post-newpost-box-wysiwyg-wrapper">
									<div class="board-post-newpost-box-wysiwyg-menu">
										<i class="fa-solid fa-text-height" id="board-posts-newpost-textheight" title="Font size"></i>
										<i class="fa-solid fa-bold" id="board-posts-newpost-boldtext" title="Bold text"></i>
										<i class="fa-solid fa-underline" id="board-posts-newpost-underlinetext" title="Underline text"></i>
										<i class="fa-solid fa-italic" id="board-posts-newpost-italictext" title="Italic text"></i>
										
										<i class="fa-solid fa-list" id="board-posts-newpost-bulletlist" title="Bullet list"></i>
										<i class="fa-solid fa-list-ol" id="board-posts-newpost-numberlist" title="Number list"></i>
										<i class="fa-solid fa-align-center" id="board-posts-newpost-aligncenter" title="Align center"></i>
										<i class="fa-solid fa-code" id="board-posts-newpost-codeblock" title="Code block"></i>
										
										<i class="fa-solid fa-image" id="board-posts-newpost-uploadimage" title="Upload image"></i>
										<i class="fa-solid fa-photo-film" id="board-posts-newpost-gif" title="GIF"></i>
										<i class="fa-brands fa-youtube" id="board-posts-newpost-youtube" title="Youtube video"></i>
										<i class="fa-solid fa-link" id="board-posts-newpost-linktext" title="Link text"></i>
									</div>
									<div class="board-post-newpost-box-wysiwyg-text" id="board-post-newpost-box-wysiwyg-text" contenteditable="true" spellcheck="true"> </div>
								</div><br>
								<input type="password" name="board-posts-newpost-box-password" id="board-posts-newpost-box-password" />
								<div id="modpass">
									<br><input type="password" name="board-posts-newpost-box-modpass" id="board-posts-newpost-box-modpass" placeholder="Mod password" />
								</div>
								<div class="board-post-newpost-box-submit" id="board-post-newpost-box-submit">
									&nbsp;Submit&nbsp;
								</div>
							</div>
							<div class="board-posts-newpost-box-flex-flexchild">
								Some other text
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End post box -->
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
							{% set count = 0 %}
							{% for thread_files_counts in thread_files_count %}
								{% if thread_files_counts.id == thread_post.id %}
									{% set count = thread_files_counts.count %}
								{% endif %}
							{% endfor %}
							{% if thread_post.parent == 0 %}
								<article class="board-posts-thread">
									<div class="board-posts-thread-header">
										<h2>{{thread_post.subject}}</h2>
										<div class="board-posts-thread-header-date">
											&nbsp;-&nbsp;{{thread_post.time|date('m/d/Y h:i', 'America/Chicago')}} {% if thread_post.time|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}
										</div>
										<div class="board-posts-thread-header-fr">
											<i class="fas fa-flag" title="Report" id="report" onclick="reportPost({{thread_post.id}});"></i>&nbsp;<i class="fas fa-reply" title="Quick Reply"></i>&nbsp;<i class="fa-solid fa-trash-can" title="Delete"></i>
										</div>
										<br>
										<div class="board-posts-thread-header-postby">
											Posted by <div class="board-posts-thread-header-postname">{{thread_post.name}}</div> {% if board_info.showid == 1 %}&nbsp;<i class="fa-solid fa-id-badge"></i> {{thread_post.ipid|slice(0,6)}}{% endif %} {% if thread_post.sticky == 1 %}&nbsp;<i class="fa-solid fa-thumbtack"></i>{% endif %}{% if thread_post.lock == 1 %}&nbsp;<i class="fa-solid fa-lock"></i>{% endif %}
										</div>
									</div>
									<div class="board-posts-thread-post-message">
										{% set active = 1 %}
										{% for thread_file in thread_files %}
											{% if thread_file.id == thread_post.id %}
												{% if count == 1 %}
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
												{% else %}
													<br>{{count}} files
												{% endif %}
												{% set active = 0 %}
											{% endif %}
										{% endfor %}
										{% if active %}
											<div class="board-posts-thread-post-postmessage">
												{{ thread_post.message|raw }}
											</div>
										{% endif %}
										<br>
										<div class="board-posts-thread-post-replies">
											{% for thread_reply in thread_replies %}
												{% if thread_reply.threadid == thread_post.id %}
													{% if thread_reply.replies >= 1 %}<i class="fas fa-comments" title="View thread"></i> {{thread_reply.replies}}  {% if thread_reply.replies == 1 %}Reply{% else %}Replies{% endif %}{% else %}<i class="fas fa-comment" title="View thread"></i> {{thread_reply.replies}} Replies{% endif %}
												{% endif %}
											{% endfor %}
										</div>
										<span id="dnb-{{boardname}}-{{thread_post.id}}"></span>
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
				var SESSID = "";
				function generatePassword() {
					var possible = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789![]{}()%&*$#^<>~@|';
					var text = '';
					for(var i=0; i < 16; i++) {
						text += possible.charAt(Math.floor(Math.random() * possible.length));
					}
					return text;
				}
				function getCookie(name) {
					const cookieValue = document.cookie
						  .split('; ')
						  .find(row => row.startsWith(name+'='))
						  .split('=')[1];
					return cookieValue;
				}
				function reportPost(id) {
					alert("Thread ID: "+id);
					event.stopImmediatePropagation();
				}
				if (document.cookie.match(/^(.*;)?\s*mod_cookie\s*=\s*[^;]+(.*)?$/) === null) {
					document.getElementById("modpass").style.display = 'none';
				} else {
					if (getCookie('mod_cookie') == 'allboards') {
						document.getElementById("modpass").style.display = 'block';
						var dnbelements = document.getElementsByTagName('span');
						var dnbelement;
						var dnbinfo;
						for(var i=0; i<dnbelements.length;i++) {
							dnbelement = dnbelements[i];
							if (dnbelement.getAttribute('id').substr(0, 3) == 'dnb') {
								dnbinfo = dnbelement.getAttribute('id').split('-');
								let req = new XMLHttpRequest();
								req.open("GET", "{{weburl}}manage/index.php?action=modgetIP&id="+dnbinfo[2]+"&board="+dnbinfo[1],false);
								req.send();
								var ip = req.responseText;
								dnbelements[i].innerHTML = "[IP: "+ip.replace('::ffff:', '') +" ]";
							}
						}
					}
				}
				if (document.cookie.match(/^(.*;)?\s*PHPSESSID\s*=\s*[^;]+(.*)?$/) === null) {
					// do nothing
				} else {
					SESSID = getCookie('PHPSESSID');
				}
				$(function() {
					if (document.cookie.match(/^(.*;)?\s*board-posts-password\s*=\s*[^;]+(.*)?$/) === null) {
						document.cookie = "board-posts-password=" + generatePassword() + "; expires=Fri, 31 Dec 9999 23:59:59 GMT"
						document.getElementById("board-posts-newpost-box-password").value = getCookie("board-posts-password");
					} else {
						document.getElementById("board-posts-newpost-box-password").value = getCookie("board-posts-password");
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
				$('#board-post-newpost-box-submit').click(function() {
					var username = document.getElementById("board-posts-newpost-box-username").value;
					var email = document.getElementById("board-posts-newpost-box-email").value;
					var subject = document.getElementById("board-posts-newpost-box-subject").value;
					var post = $('#board-post-newpost-box-wysiwyg-text').html();
					var password = document.getElementById("board-posts-newpost-box-password").value;
					var board = "{{boardname}}";
					var modpass = document.getElementById("board-posts-newpost-box-modpass").value;
					let req = new XMLHttpRequest();
					let formData = new FormData();
					formData.append("username", username);
					formData.append("email", email);
					formData.append("subject", subject);
					formData.append("post", post);
					formData.append("password", password);
					formData.append("board", board)
					formData.append("modpass", modpass);
					formData.append("sessid", SESSID);
					req.open("POST", "{{weburl}}board/index.php?action=post");
					req.send(formData);
					req.onreadystatechange = function () {
						if (req.readyState === 4) {
							var obj = JSON.parse(this.responseText);
							if (obj.result == 'success') {
								location.reload();
							} else {
								alert('Error! '+ obj.reason);
							}
						}
					}
				});
			</script>
		{% endfor %}
	</body>
</html>