{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Logs
	</div>
	<div class="modules">
		<div class="logs" id="logs">
			<table class="users" cellspacing="1px">
				<col class="col1" /> <col class="col2" />
				<col class="col1" /> <col class="col2" />
				<thead>
					<tr>
						<th>Time</th>
						<th>User</th>
						<th>Message</th>
					</tr>
				</thead>
				<tbody>
				 {% for log in entry %}
						<tr>
						<td>{{log.time|date('m/d/Y h:i', 'America/Chicago')}} {% if log.time|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}</td>
						<td>{{log.user}}</td>
						<td>{{log.message}}</td>
					</tr>
				{% endfor %}
				</tbody>
			</table>
			<center>
				{% if pages -1 > 0 %}
					{% for i in range(0, pages ) %}
					  [ {% if page != i %}<a href="/manage/index.php?side={{current}}&action=logs&page={{i}}">{% endif %}{{i}}{% if page != i %}</a>{% endif %} ]
					{% endfor %}
				{% else %}
					[ 0 ]
				{% endif %}
			</center>
			<div class="fr">
				<input type="submit" value="Clear all Logs" onclick="clearlog();" />
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript"></script>
	<script>
		function getQueryVariable(variable) {
			var query = window.location.search.substring(1);
			var vars = query.split("&");
			for (var i=0;i<vars.length;i++) {
				var pair = vars[i].split("=");
				if (pair[0] == variable) {
					return pair[1];
				}
			} 
		}
		function clearlog () {
			var side = getQueryVariable("side");
			let req = new XMLHttpRequest();
			req.open("POST", 'index.php?action=logs&do=clearlog');
			req.send();
			req.onreadystatechange = function () {
				if (req.readyState === 4) {
					window.location.replace("index.php?side="+side+"&action=logs");
				}
			}
		}
	</script>
{% endblock %}
