<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/pages/css/front_anonsaba.css" />
		<link rel="stylesheet" type="text/css" href="/pages/css/global.css" />
		<link rel="apple-touch-icon" sizes="180x180" href="/pages/images/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/pages/images/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/pages/images/favicon-16x16.png">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{sitename}}</title>
		<script type="text/javascript">
			<!--
				function toggle_boards(button, area) {
					var tog=document.getElementById(area);
					if(tog.style.display)    {
						tog.style.display='';
					}    else {
						tog.style.display='none';
					}
					button.innerHTML=(tog.style.display)?'&plus;':'&minus;';
				}
			//-->
		</script>
	</head>
	<body>
		<div id="sitecontainer">
			<div id="headwrapper">
				<div class="sitename">
					{{sitename}}
				</div>
				<div class="slogan">
					"{{slogan}}"
				</div>
			</div>
			<div id="info">
				<a href="/">
					<div class="news{% if not view %}selected{% endif %}">
						News
					</div>
				</a>
				<a href="index.php?view=faq">
					<div class="faq{% if view == 'faq' %}selected{% endif %}">
						FAQ
					</div>
				</a>
				<a href="index.php?view=rules">
					<div class="rules{% if view == 'rules' %}selected{% endif %}">
						Rules
					</div>
				</a>
				<div id="stats">
					<div class="recentpost">
						<div class="recentposthead">
							Recent Posts
						</div>
						{% for recentpost in recentposts %}
							<div class="recentpostposts">
								>>>/{{recentpost.boardname}}/{{recentpost.id}} - {{recentpost.message|striptags("")|slice(0, 60)|raw}}{% if recentpost.message|length > 60 %}...{% endif %}
							</div>
						{% endfor %}
					</div>
					<div class="statinfo">
						<div class="statinfohead">
							Stats
						</div>
						<table cellspacing="1px">
						<input type="hidden" name="id" id="id" />
							<thead>
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
									<td>0 GB</td>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div id="content">
					{% for entry in entries %}
						<div class="title">
							<h3>{{entry.subject}}{% if entry.type == 'news' %} by {{entry.by}} - {{entry.date|date('m/d/Y h:i', 'America/Chicago')}} {% if entry.date|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}{% endif %} <div style="float:right"><a href="id#{{entry.id}}">#</a></div></h3>
						</div>
						<div class="post">
							<p>
								{{entry.message|raw}}
							</p>
						</div>
					{% endfor %}
					<div style="text-align:center; font-size: .9vw;">
						{% if not view %}
							{% if pages -1 > 0 %}
								{% for i in range(0, pages ) %}
									[ {% if page != i %}<a href="/index.php?page={{i}}">{% endif %}{{i}}{% if page != i %}</a>{% endif %} ]
								{% endfor %}
							{% else %}
								[ 0 ]
							{% endif %}
						{% endif %}
					</div>
				</div>
			</div>
			<div id="boardlist">
				<div class="boardtitle">
					Boards
				</div>
				<div id="boards">
					{% for section in boards %}
						<div class="boardwrapper">
							<div class="boardsectionname">
								{{section.name}}
								<span class="section_toggle" onclick="toggle_boards(this, '{{section.name}}');" title="Click to show/hide">
									{% if section.hidden == 1 %}+{% else %}&minus;{% endif %}</span>
								</span>
							</div>
							<div id="{{section.name}}" name="{{section.name}}" {% if section.hidden == 1 %}style="display: none;"{% endif %}>
								<div class="boardnames">
									{% for boards in section.boards %}
										<a href="{{url}}{{boards.name}}/">
											<li>
												{{boards.desc}}{% if boards.locked == 1 %}&nbsp;<img src="/pages/images/lock-icon.png" />{% endif %}
											</li>
										</a>
									{% else %}
										No boards
									{% endfor %}
								</div>
							</div>
						</div>
					{% else %}
						<div style="text-align:center; font-size: .9vw;">Currently no boards</div>
					{% endfor %}
				</div>
			</div>
			<div id="footerwrapper">
				<div class="footer">
					{{sitename}} is proudly powered by <a href="http://www.anonsaba.org/" target="_blank">Anonsaba {{version}}</a>
				</div>
			</div>
		</div>
	</body>
</html>