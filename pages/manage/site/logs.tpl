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
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				 {% for log in entry %}
						<tr>
						<td>{{log.time|date('m/d/Y h:m', 'America/Chicago')}} {% if log.time|date('H', 'America/Chicago') > 13 %}PM{% else %}AM{% endif %}</td>
						<td>{{log.user}}</td>
						<td>{{log.message}}</td>
						<td><input type="submit" value="Edit" onclick="editpost('{{rules.email}}', '{{rules.subject}}', '{{rules.id}}');" />&nbsp;<input type="submit" value="Delete" onclick="delpost('{{rules.id}}');" /></td>
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
		</div>
	</div>
{% endblock %}
