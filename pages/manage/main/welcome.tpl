{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
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
		<canvas id="postper24"></canvas>
		<script type="text/javascript">
			<!--
				var ctx = document.getElementById('postper24').getContext('2d');
				var chart = new Chart(ctx, {
					// The type of chart we want to create
					type: 'bar',

					// The data for our dataset
					data: {
						labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'],
						datasets: [{
							label: 'Posts',
							backgroundColor: 'rgb(255, 99, 132)',
							borderColor: 'rgb(255, 99, 132)',
							data: ['{{postlast1}}', '{{postlast2}}', '{{postlast3}}', '{{postlast4}}', '{{postlast5}}', '{{postlast6}}', '{{postlast7}}', '{{postlast8}}', '{{postlast9}}', '{{postlast10}}', '{{postlast11}}', '{{postlast12}}', '{{postlast13}}', '{{postlast14}}', '{{postlast15}}', '{{postlast16}}', '{{postlast17}}', '{{postlast18}}', '{{postlast19}}', '{{postlast20}}', '{{postlast21}}', '{{postlast22}}', '{{postlast23}}', '{{postlast24}}', '{{postlast25}}', '{{postlast26}}', '{{postlast27}}', '{{postlast28}}', '{{postlast29}}', '{{postlast30}}']
						}]
					},

					// Configuration options go here
					options: {}
				});
			//-->
		</script>
	</div>
{% endblock %}
