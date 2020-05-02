<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{sitename}}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="manifest" href="/site.webmanifest">
<link rel="stylesheet" type="text/css" href="/pages/css/img_globals.css">
<link rel="stylesheet" type="text/css" href="/pages/css/front.css" />
<link rel="stylesheet" type="text/css" href="/pages/css/site_front.css" />
<link rel="stylesheet" type="text/css" href="/pages/css/site_global.css">
<link rel="stylesheet" type="text/css" href="/pages/css/menu_global.css" />
<script type="text/javascript" src="/modules/anonsaba.js"></script>
<form name="homepage" id="homepage" method="get" enctype="multipart/form-data" />
</head>
<body>
  <header role="banner">
     <center><h1>{{sitename}}</h1><br /><h3><i>"{{slogan|raw}}"</i></h3></center>
  </header>
    <br class="clear" />
  <section id="recent">
    <section id="posts">
      <h3>Recent Posts</h3>
			<ul>
      
 {% for post in recentpost %}
<li><a  href="{{url}}/{{post.boardname}}/res/{% if post.parent == 0 %}{{post.id}}.html{% else %}{{post.parent}}.html#{{post.id}}{% endif %}" onclick="return highlight('{{post.id}}', true);">
&gt;&gt;&gt;/{{post.boardname}}/{{post.id}}

</a> -
  {{post.message|striptags("")|slice(0, 60)|raw}}
{% if post.message|length > 60 %}...{% endif %} 

                      {% endfor %}
		</ul>
           </section>
      </section>
    <section id="images">
      <h3>Stats</h3>
			<ul>
                        Total Posts: {{totalposts}}<br />
                        Current Number of Users: {{currentusers}}<br />
                        Active Content: {{content}}
			</ul>
    </section>
    <br class="clear" />
  </section>
  
  




<div class="wrap">
  <section id="news">
    <header>
      <ul>
        <li{% if view == '' %} class="selected"{% endif %}>{% if view != '' %}<a href="/index.php{% if frame %}?frame{% endif %}">{% endif %}News{% if view != '' %}</a>{% endif %}</li>
        <li{% if view == 'faq' %} class="selected"{% endif %}>{% if view != 'faq' %}<a href="/index.php?view=faq{% if frame %}&frame{% endif %}">{% endif %}FAQ{% if view != 'faq' %}</a>{% endif %}</li>
        <li{% if view == 'rules' %} class="selected"{% endif %}>{% if view != 'rules' %}<a href="/index.php?view=rules{% if frame %}&frame{% endif %}">{% endif %}Rules{% if view != 'rules' %}</a>{% endif %}</li>
</ul>
      <br class="clear" />
    </header>
    
{% for item in entries %}
    <article>
      <h4 id="id{{item.id}}">
       <a class="permalink" href="#id{{item.id}}">#</a> <span class="newssub">{{item.subject}} {% if view == 'news' or view == '' %} by {% if item.email != '' %} <a href="mailto:{{item.email}}">{% endif %} {{item.by}} {% if item.email != '' %} </a>{% endif %}  - {{item.date|date('m/d/y @ h:i:s A')}} {% endif %} </span>
      </h4>
      
      <p>
      {{item.message|raw}}
      </p>
    </article>
{% endfor %} 
    
{% if view == '' %}
    <footer>
{% if pages -1 > 0 %}
  {% for i in range(0, pages ) %}
      [ {% if page != i %}<a href="/index.php?page={{i}}{% if frame %}&frame{% endif %}">{% endif %}{{i}}{% if page != i %}</a>{% endif %} ]
  {% endfor %}
{% else %}
[ 0 ]
{% endif %}
    </footer>
{% endif %}
  </section>
  <section id="boardlist">
    <h3>Boards</h3>
{% for section in boards %}

      <h4><span class="section_toggle" onclick="toggle_boards(this, '{{section.abbr}}');" title="Click to show/hide">{% if section.hidden == 1 %}+{% else %}&minus;{% endif %}</span>&nbsp;{{section.name}}</h4>
      <div style="{% if section.hidden == 1 %} display: none;{% endif %}" id="{{section.abbr}}" name="{{section.abbr}}">
      <ul>
{% for board in section.boards %}
        <li><a href="/{{board.name}}/" title="{{sitename}} - {{board.desc}}">{%if brd.trial == 1%}<i>{{board.desc}}</i>{%elseif board.popular ==1%}<b>{{board.desc}}</b>{%else%}{{board.desc}}{%endif%}
        {% if board.locked == 1 %}
               &nbsp;<img src="/pages/css/lock.gif" border="0" alt="Locked">
	{% endif %}
        </a></li>
{% else %}
        <li>No boards</li>
{% endfor %}
      <br style="{% if section.hidden == 1 %} display: none;{% endif %}" class="clear" />
      </ul>
      </div>

{% endfor %}
{%if irc != ''%}
		<h4>&nbsp;IRC</h4>
	<ul>
		<li>{{irc|raw}}</li>
	</ul>
<br />
{%endif%}
</form>
<form name="homepage" id="homepage" method="get" enctype="multipart/form-data" />
  </section>
  </div>
  <div class="wrap hfix">
  <div class="lcol"></div>
  <div class="rcol"></div>
  </div>
  <footer>
{{sitename}} is proudly powered by <a href="http://anonsaba.org/" target="_top">Anonsaba {{version}}</a>
  </footer>
</form>
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
	set_cookie('nav_show_'+area, tog.style.display?'0':'1', 30);
    }
	addpreviewevents();
//-->
</script>
</body>
</html>
