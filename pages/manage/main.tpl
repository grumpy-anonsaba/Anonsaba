{% block css %}
	<link href="/pages/css/manage.css" rel="stylesheet" type="text/css" />
	<link href="/pages/css/global.css" rel="stylesheet" type="text/css" />
{% endblock %}
{% block content %}
	<title>{{sitename}} - Management</title>
		<section class="content_wrap">
			<header id="">
				<section id="top">
					{{sitename}} Management
				</section>
				<div class="login">
					Logged in as <span class='strong'>{{username}}</span> [<a href="index.php?action=logout">Log Out</a>]
				</div>
				<nav>
					<ul>
						<li class="{% if current == "main" %}selected{% endif %}"><a href="index.php?side=main{% if action != '' %}&action={{action}}{% endif %}">Main</a></li>
							{% if level == "1" %}
								<li class="{% if current == "site" %}selected{% endif %}"><a href="index.php?side=site{% if action != '' %}&action={{action}}{% endif %}">Site Administration</a></li>
							{% endif %}
							{% if level == "2" or level == "1" %}
								<li class="{% if current == "boards" %}selected{% endif %}"><a href="index.php?side=boards{% if action != '' %}&action={{action}}{% endif %}">Boards Administration</a></li>
							{% endif %}
						<li class="{% if current == "mod" %}selected{% endif %}"><a href="index.php?side=mod{% if action != '' %}&action={{action}}{% endif %}">Moderation</a></li>
					</ul>
				</nav>
			</header>
		<section class="content">
			<section class="sidebar">
				<section>
					<h2>{{sectionname}}</h2>
					<ul>
						{% for i in 0..arraynum %}
							<li><a href="index.php?side={{current}}{{urls[i]}}">{{names[i]}}</a></li>
						{% endfor %}
					</ul>
				</section>
			</section>
			<!--<section class="col_r">
				<h1>{% block heading %}{% endblock %}</h1>
			</section>
				{% block managecontent %}
				{% endblock %}
		</section>-->
		<br style="clear: both;" />
	</section>
		<footer>
			<center><font color="#CCC" size="4"><b>- Anonsaba {{version}} -</b></font></center>
		</footer>
	</section>
{% endblock %}
