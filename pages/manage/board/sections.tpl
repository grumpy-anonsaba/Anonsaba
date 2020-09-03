{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Sections
	</div>
	<div class="modules">
		<div class="logs" id="logs">
			<div class="fc">
				<input type="submit" value="New Section" onclick="newsectionclick();" />
			</div>
			<br />
			<br />
			<table class="users" cellspacing="1px">
				<col class="col1" /> <col class="col2" />
				<col class="col1" /> <col class="col2" />
				<thead>
					<tr>
						<th>Order</th>
						<th>Abbr</th>
						<th>Name</th>
						<th>Hidden</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					{% for section in sections %}
						<tr>
							<td>{{section.order}}</td>
							<td>{{section.abbr}}</td>
							<td>{{section.name}}</td>
							<td>
								{% if section.hidden == 1 %}
									Yes
								{% else %}
									No
								{% endif %}
							</td>
							<td><input type="submit" value="Edit" onclick="edit('{{section.id}}', '{{section.order}}', '{{section.abbr}}', '{{section.name}}', '{{section.hidden}}');" />&nbsp;<input type="submit" value="Delete" onclick="del('{{section.id}}');" /></td>
						</tr>
					{% endfor %}
				</tbody>
			</table>
		</div>
		<div class="filetype" id="filetype">
				<table cellspacing="1px">
				<input type="hidden" name="id" id="id" />
					<thead>
						<tr>
							<th>Order</th>
							<td><input type="text" name="order" id="order" placeholder="Start at 1" /></td>
						</tr>
						<tr>
							<th>Abbreviation</th>
							<td><input type="text" name="abbr" id="abbr" /></td>
						</tr>
						<tr>
							<th>Name</th>
							<td><input type="text" name="name" id="name" /></td>
						</tr>
						<tr>
							<th>Hidden</th>
							<td><input type="checkbox" name="hidden" id="hidden" /></td>
						</tr>
					</thead>
				</table>
				<div class="fr">
					<input type="submit" value="Submit" onclick="create();" />
				</div>
			</div>
	</div>
	<script src="/pages/javascript/jquery-3.5.1.js" type="text/javascript"></script>
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
		function newsectionclick() {
			document.getElementById("filetype").style.display = "block";
			document.getElementById("logs").style.display = "none";
		}
		function create() {
			var side = getQueryVariable("side");
			var action = getQueryVariable("action");
			var order = document.getElementById("order").value;
			var abbr = document.getElementById("abbr").value;
			var name = document.getElementById("name").value;
			if (document.getElementById("hidden").checked == true) {
				var hidden = 1;
			} else {
				var hidden = 0;
			}
			var id = document.getElementById("id").value;
			let req = new XMLHttpRequest();
			let formData = new FormData();
			formData.append("order", order);
			formData.append("abbr", abbr);
			formData.append("name", name);
			formData.append("hidden", hidden);
			formData.append("id", id);
			req.open("POST", 'index.php?action='+action+'&do=create');
			req.send(formData);
			req.onreadystatechange = function () {
				window.location.replace("index.php?side="+side+"&action="+action);
			}
		}
		function edit(id, order, abbr, name, hidden) {
			newsectionclick();
			document.getElementById("id").value = id;
			document.getElementById("order").value = order;
			document.getElementById("name").value = name;
			document.getElementById("abbr").value = abbr;
			if (hidden == 1) {
				document.getElementById("hidden").checked = true;
			} else {
				document.getElementById("hidden").checked = false;
			}
		}
		function del(id) {
			var side = getQueryVariable("side");
			var action = getQueryVariable("action");
			let req = new XMLHttpRequest();
			req.open("POST", 'index.php?action='+action+'&do=del&id='+id);
			req.send();
			req.onreadystatechange = function () {
				if (req.readyState === 4) {
					window.location.replace("index.php?side="+side+"&action="+action);
				}
			}
		}
	</script>
{% endblock %}