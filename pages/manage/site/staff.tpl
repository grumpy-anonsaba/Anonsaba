{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Staff
	</div>
	<div class="modules">
		<div class="logs" id="logs">
			<div class="fc">
				<input type="submit" value="New User" onclick="newuserclick();" />
			</div>
			<br />
			<br />
			<table class="users" cellspacing="1px">
				<col class="col1" /> <col class="col2" />
				<col class="col1" /> <col class="col2" />
				<thead>
					<tr>
						<th>Username</th>
						<th>Boards</th>
						<th>Last Active</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th colspan="4">Administrators</th>
					</tr>
					{% for staff in entry %}
						{% if staff.level == 1 and staff.suspended == 0 %}
							<tr>
								<td>{{staff.username}}</td>
								<td>All Boards</td>
								<td>{% if staff.active == '0' %}Never{% else %}{{staff.active|date('m/d/Y h:i', 'America/Chicago')}} {% if staff.active|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}{% endif %}</td>
								<td><input type="submit" value="Edit" onclick="edit('{{staff.id}}', '{{staff.username}}', '{{staff.level}}', '{{staff.boards}}');" />&nbsp;<input type="submit" value="Suspend" onclick="suspend('{{staff.id}}');" />&nbsp;<input type="submit" value="Delete" onclick="del('{{staff.id}}');" /></td>
							</tr>
						{% endif %}
					{% endfor %}
					<tr>
						<th colspan="4">Super Moderators</th>
					</tr>
					{% for staff in entry %}
						{% if staff.level == 2 and staff.suspended == 0 %}
							<tr>
								<td>{{staff.username}}</td>
								<td>{% if staff.boards == '' %}None{% elseif staff.boards == 'all' %}All boards{% else %}/{{staff.boards|replace({'|':'/, /'})}}/{% endif %}</td>
								<td>{% if staff.active == '0' %}Never{% else %}{{staff.active|date('m/d/Y h:i', 'America/Chicago')}} {% if staff.active|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}{% endif %}</td>
								<td><input type="submit" value="Edit" onclick="edit('{{staff.id}}', '{{staff.username}}', '{{staff.level}}', '{{staff.boards}}');" />&nbsp;<input type="submit" value="Suspend" onclick="suspend('{{staff.id}}');" />&nbsp;<input type="submit" value="Delete" onclick="del('{{staff.id}}');" /></td>
							</tr>
						{% endif %}
					{% endfor %}
					<tr>
						<th colspan="4">Moderators</th>
					</tr>
					{% for staff in entry %}
						{% if staff.level == 3 and staff.suspended == 0 %}
							<tr>
								<td>{{staff.username}}</td>
								<td>{% if staff.boards == '' %}None{% elseif staff.boards == 'all' %}All boards{% else %}/{{staff.boards|replace({'|':'/, /'})}}/{% endif %}</td>
								<td>{% if staff.active == '0' %}Never{% else %}{{staff.active|date('m/d/Y h:i', 'America/Chicago')}} {% if staff.active|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}{% endif %}</td>
								<td><input type="submit" value="Edit" onclick="edit('{{staff.id}}', '{{staff.username}}', '{{staff.level}}', '{{staff.boards}}');" />&nbsp;<input type="submit" value="Suspend" onclick="suspend('{{staff.id}}');" />&nbsp;<input type="submit" value="Delete" onclick="del('{{staff.id}}');" /></td>
							</tr>
						{% endif %}
					{% endfor %}
					<tr>
						<th colspan="4">Suspended</th>
					</tr>
					{% for staff in entry %}
						{% if staff.suspended == 1 %}
							<tr>
								<td>{{staff.username}}</td>
								<td>{% if staff.boards == '' %}None{% elseif staff.boards == 'all' %}All boards{% else %}/{{staff.boards|replace({'|':'/, /'})}}/{% endif %}</td>
								<td>{% if staff.active == '0' %}Never{% else %}{{staff.active|date('m/d/Y h:i', 'America/Chicago')}} {% if staff.active|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}{% endif %}</td>
								<td><input type="submit" value="Edit" onclick="edit('{{staff.id}}', '{{staff.username}}', '{{staff.level}}', '{{staff.boards}}');" />&nbsp;<input type="submit" value="Unsuspend" onclick="unsuspend('{{staff.id}}');" />&nbsp;<input type="submit" value="Delete" onclick="del('{{staff.id}}');" /></td>
							</tr>
						{% endif %}
					{% endfor %}
				</tbody>
			</table>
		</div>
		<div class="staff" id="staff">
			<table style="width:95%">
				<input type="hidden" name="id" id="id" />
				<tr>
					<th>Username</th>
				</tr>
				<tr>
					<td><input type="text" name="username" id="username" placeholder="Can be left blank" /></td>
				</tr>
				<tr>
					<th>Password</th>
				</tr>
				<tr>
					<td><input type="password" name="password" id="password" placeholder="Cannot be left blank" /></td>
				</tr>
				<tr>
					<th>Staff Level</th>
				</tr>
				<tr>
					<td>
						<select name="level" id="level" onclick="update();">
							<option value="1">Administrator</option>
							<option value="2">Super Moderator</option>
							<option value="3">Moderator</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Boards</th>
				</tr>
				<tr>
					<td>
						<label for="all">All boards </label>
						<input type="checkbox" name="all" id="all" />
						<br />
						<div id="otherboards">
							<b><label for="wut">or</label></b><br />
							{% for board in boards %}
								<label for="mods{{board.name}}">/{{board.name}}/</label>
								<input type="checkbox" name="mods{{board.name}}" id="mods{{board.name}}" /><br />
							{% endfor %}
						</div>
					</td>
				</tr>
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
		document.getElementById("all").checked = true;
		document.getElementById("otherboards").style.display = "none";
		function update() {
			if(document.getElementById('level').value == "1") {
				document.getElementById("all").checked = true;
				document.getElementById("otherboards").style.display = "none";
			} else {
				document.getElementById("all").checked = false;
				document.getElementById("otherboards").style.display = "block";
			}
		}
		function suspend(id) {
			var side = getQueryVariable("side");
			var action = getQueryVariable("action");
			let req = new XMLHttpRequest();
			req.open("POST", 'index.php?action='+action+'&do=suspend&id='+id);
			req.send();
			req.onreadystatechange = function () {
				if (req.readyState === 4) {
					window.location.replace("index.php?side="+side+"&action="+action);
				}
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
		function unsuspend(id) {
			var side = getQueryVariable("side");
			var action = getQueryVariable("action");
			let req = new XMLHttpRequest();
			req.open("POST", 'index.php?action='+action+'&do=unsuspend&id='+id);
			req.send();
			req.onreadystatechange = function () {
				if (req.readyState === 4) {
					window.location.replace("index.php?side="+side+"&action="+action);
				}
			}
		}
		function edit(id, username, level, boards) {
			document.getElementById("staff").style.display = "block";
			document.getElementById("logs").style.display = "none";
			document.getElementById("id").value = id;
			document.getElementById('username').disabled = true;
			document.getElementById("username").value = username;
			document.getElementById("level").value = level;
			update();
			if (boards == "all") {
				document.getElementById("all").checked = true;
			} else {
				var newboards = boards.split("|");
				newboards.forEach(updateBoards);
			}
		}
		function updateBoards (item) {
			document.getElementById("mods"+item).checked = true;
		}
		function create() {
			var side = getQueryVariable("side");
			var action = getQueryVariable("action");
			var username = document.getElementById("username").value;
			var password = document.getElementById("password").value;
			var level = document.getElementById("level").value;
			var id = document.getElementById("id").value;
			if (document.getElementById("all").checked == true) {
				var boards = 'all';
			} else if (document.getElementById("all").checked == false)  {
				var n = $('input:checkbox[id^="mods"]:checked').length;
				if (n > 1) {
					var arr = $('input:checkbox[id^="mods"]:checked').map(function(){
						return $(this).attr("id").substr(4);
					}).get();
					var boards = (arr.join("|"));
				} else {
					var arr = $('input:checkbox[id^="mods"]:checked').map(function(){
						return $(this).attr("id").substr(4);
					}).get();
					var boards = (arr.join(""));
				}
			}
			let req = new XMLHttpRequest();
			let formData = new FormData();
			formData.append("username", username);
			formData.append("password", password);
			formData.append("level", level);
			formData.append("id", id);
			formData.append("boards", boards);
			req.open("POST", 'index.php?action='+action+'&do=create');
			req.send(formData);
			req.onreadystatechange = function () {
				window.location.replace("index.php?side="+side+"&action="+action);
			}
		}
		function newuserclick() {
			document.getElementById("staff").style.display = "block";
			document.getElementById("logs").style.display = "none";
		}
	</script>
{% endblock %}
