{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		File Types
	</div>
	<div class="modules">
		<div class="logs" id="logs">
			<div class="fc">
				<input type="submit" value="New Filetype" onclick="newftclick();" />
			</div>
			<br />
			<br />
			<table class="users" cellspacing="1px">
				<col class="col1" /> <col class="col2" />
				<col class="col1" /> <col class="col2" />
				<thead>
					<tr>
						<th>Type</th>
						<th>Image</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					{% for type in filetype %}
						<tr>
							<td>{{type.name}}</td>
							<td><img src="{{type.image}}" /></td>
							<td><input type="submit" value="Edit" onclick="edit('{{type.id}}', '{{type.name}}', '{{type.image}}');" />&nbsp;<input type="submit" value="Delete" onclick="del('{{type.id}}');" /></td>
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
							<th>Type</th>
							<td><input type="text" name="type" id="type" placeholder="Do not include the period before .rar -> rar" /></td>
						</tr>
						<tr>
							<th>Image (Default: <img src="/pages/images/unknown-file-icon.png" />)</th>
							<td><input type="text" name="image" id="image" placeholder="You can leave this blank to use the default" /></td>
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
		function newftclick() {
			document.getElementById("filetype").style.display = "block";
			document.getElementById("logs").style.display = "none";
		}
		function create() {
			var side = getQueryVariable("side");
			var action = getQueryVariable("action");
			var type = document.getElementById("type").value;
			var image = document.getElementById("image").value;
			var id = document.getElementById("id").value;
			let req = new XMLHttpRequest();
			let formData = new FormData();
			formData.append("image", image);
			formData.append("type", type);
			formData.append("id", id);
			req.open("POST", 'index.php?action='+action+'&do=create');
			req.send(formData);
			req.onreadystatechange = function () {
				window.location.replace("index.php?side="+side+"&action="+action);
			}
		}
		function edit(id, type, image) {
			newftclick();
			document.getElementById("id").value = id;
			document.getElementById('type').disabled = true;
			document.getElementById("type").value = type;
			document.getElementById("image").value = image;
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