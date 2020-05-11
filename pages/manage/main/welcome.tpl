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
		<canvas id="postper30" height="70%">
			It appears your browser doesn't support canvas ;-;!
		</canvas>
		<script type="text/javascript">
			<!--
				var ctx = document.getElementById('postper30').getContext('2d');
				var chart = new Chart(ctx, {
					// The type of chart we want to create
					type: 'bar',

					// The data for our dataset
					data: {
						labels: ['{{postdate1}}', '{{postdate2}}', '{{postdate3}}', '{{postdate4}}', '{{postdate5}}', '{{postdate6}}', '{{postdate7}}', '{{postdate8}}', '{{postdate9}}', '{{postdate10}}', '{{postdate11}}', '{{postdate12}}', '{{postdate13}}', '{{postdate14}}', '{{postdate15}}', '{{postdate16}}', '{{postdate17}}', '{{postdate18}}', '{{postdate19}}', '{{postdate20}}', '{{postdate21}}', '{{postdate22}}', '{{postdate23}}', '{{postdate24}}', '{{postdate25}}', '{{postdate26}}', '{{postdate27}}', '{{postdate28}}', '{{postdate29}}', '{{postdate30}}'],
						datasets: [{
							label: 'Posts',
							backgroundColor: 'rgb(204, 255, 204)',
							borderColor: 'rgb(204, 255, 204)',
							data: ['{{postlast1}}', '{{postlast2}}', '{{postlast3}}', '{{postlast4}}', '{{postlast5}}', '{{postlast6}}', '{{postlast7}}', '{{postlast8}}', '{{postlast9}}', '{{postlast10}}', '{{postlast11}}', '{{postlast12}}', '{{postlast13}}', '{{postlast14}}', '{{postlast15}}', '{{postlast16}}', '{{postlast17}}', '{{postlast18}}', '{{postlast19}}', '{{postlast20}}', '{{postlast21}}', '{{postlast22}}', '{{postlast23}}', '{{postlast24}}', '{{postlast25}}', '{{postlast26}}', '{{postlast27}}', '{{postlast28}}', '{{postlast29}}', '{{postlast30}}']
						}, {
							label: 'Bans',
							backgroundColor: 'rgb(255, 204, 204)',
							borderColor: 'rgb(255, 204, 204)',
							data: ['{{banlast1}}', '{{banlast2}}', '{{banlast3}}', '{{banlast4}}', '{{banlast5}}', '{{banlast6}}', '{{banlast7}}', '{{banlast8}}', '{{banlast9}}', '{{banlast10}}', '{{banlast11}}', '{{banlast12}}', '{{banlast13}}', '{{banlast14}}', '{{banlast15}}', '{{banlast16}}', '{{banlast17}}', '{{banlast18}}', '{{banlast19}}', '{{banlast20}}', '{{banlast21}}', '{{banlast22}}', '{{banlast23}}', '{{banlast24}}', '{{banlast25}}', '{{banlast26}}', '{{banlast27}}', '{{banlast28}}', '{{banlast29}}', '{{banlast30}}']
						}]
					},

					// Configuration options go here
					options: {
						title: {
							display: true,
							text: 'Last 30 days'
						}
					}
				});
			//-->
		</script>
	</div>
{% endblock %}
