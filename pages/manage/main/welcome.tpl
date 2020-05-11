{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Statistics
	</div>
	<div class="modules">
		<table style="width:95%">
			<tr>
				<th>Install Date</th>
				<th>Anonsaba Version</th>
			</tr>
			<tr>
				<td>
					{% if installdate != 'Today' %}
						{{installdate|date('m/d/y @ h:i:s A')}}
					{% else %}
						{{installdate}}
					{% endif %}
				</td>
				<td>{{version}}</td>
			</tr>
			<tr>
				<th>Twig Version</th>
				<th>Database Type</th>
			</tr>
			<tr>
				<td>{{constant('Twig_Environment::VERSION')}}</td>
				<td>{{databasetype}}</td>
			</tr>
			<tr>
				<th>Number of Boards</th>
				<th>Number of Posts</th>
			</tr>
			<tr>
				<td>{{boardnum}}</td>
				<td>{{numpost}}</td>
			</tr>
		</table>
	</div>
{% endblock %}
