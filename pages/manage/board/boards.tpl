{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Boards
	</div>
	<div class="modules">
		<div class="logs" id="logs">
			<div class="fc">
				<input type="submit" value="New Board" onclick="newboardclick();" />
			</div>
			<br />
			<br />
			<table class="users" cellspacing="1px">
				<col class="col1" /> <col class="col2" />
				<col class="col1" /> <col class="col2" />
				<thead>
					<tr>
						<th>Board</th>
						<th>Description</th>
						<th>Total Posts</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					{% for board in boards %}
						<tr>
							<td>/{{board.name}}/</td>
							<td>{{board.desc}}</td>
							<td></td>
							<td><input type="submit" value="Edit" onclick="
																			edit(
																					'{{board.id}}', '{{board.name}}', '{{board.desc}}', '{{board.class}}', '{{board.section}}', '{{board.imagesize}}', '{{board.postperpage}}', '{{board.boardpages}}',
																					'{{board.threadhours}}', '{{board.markpage}}', '{{board.threadreply}}', '{{board.postername}}', '{{board.locked}}', '{{board.email}}',
																					'{{board.ads}}', '{{board.showid}}', '{{board.report}}', '{{board.captcha}}', '{{board.forcedanon}}', '{{board.trial}}', '{{board.popular}}',
																					'{{board.recentpost}}'
																				);" 
																			
																			/>&nbsp;<input type="submit" value="Delete" onclick="del('{{board.id}}');" /></td>
						</tr>
					{% endfor %}
			</table>
		</div>
		<div class="boardopt" id="boardopt">
			<table cellspacing="1px">
			<input type="hidden" name="id" id="id" />
				<thead>
					<tr>
						<th>Board Directory</th>
						<td><input type="text" name="boarddirectory" id="boarddirectory" placeholder="Do not include slashes '/'" /></td>
					</tr>
					<tr>
						<th>Board Name</th>
						<td><input type="text" name="boarddescription" id="boarddescription" /></td>
					</tr>
					<tr>
						<th>Board Type</th>
						<td>
							<select name="type" id="type">
								<option value="sfw">Safe for Work</option>
								<option value="nsfw">Not Safe for Work</option>
							</select>
						</td>
					<tr>
						<th>Board Section</th>
						<td>
							<select name="section" id="section">
								<option value="">Select a section</option>
								{% for section in sections %}
									<option value="{{section.name}}" />{{section.name}}</option>
								{% endfor %}
							</select>
						</td>
					</tr>
					<tr>
						<th>Max Image Size (Bytes)</th>
						<td><input type="text" name="maximagesize" id="maximagesize" value="1024000" /></td>
					</tr>
					<tr>
						<th>Max Posts per Page</th>
						<td><input type="text" name="maxpostperpage" id="maxpostperpage" value="8" /></td>
					</tr>
					<tr>
						<th>Max Board Pages</th>
						<td><input type="text" name="maxboardpages" id="maxboardpages" value="11" /></td>
					</tr>
					<tr>
						<th>Max Thread Hours</th>
						<td><input type="text" name="maxthreadhours" id="maxthreadhours" placeholder="Leave Blank for unlimited" /></td>
					</tr>
					<tr>
						<th>Mark Page</th>
						<td><input type="text" name="markpage" id="markpage" value="9" /></td>
					</tr>
					<tr>
						<th>Max Thread Replies</th>
						<td><input type="text" name="maxthreadreply" id="maxthreadreply" placeholder="Leave Blank for unlimited" /></td>
					</tr>
					<tr>
						<th>Default Poster Name</th>
						<td><input type="text" name="defaultpostername" id="defaultpostername" value="Anonymous" /></td>
					</tr>
					<tr>
						<th>Locked</th>
						<td><input type="checkbox" name="locked" id="locked" /></td>
					</tr>
					<tr>
						<th>Enable Email</th>
						<td><input type="checkbox" name="enableemail" id="enableemail" /></td>
					</tr>
					<tr>
						<th>Enable Ads</th>
						<td><input type="checkbox" name="enableads" id="enableads" /></td>
					</tr>
					<tr>
						<th>Enable IDs</th>
						<td><input type="checkbox" name="enableids" id="enableids" /></td>
					</tr>
					<tr>
						<th>Enable Reporting</th>
						<td><input type="checkbox" name="enablereporting" id="enablereporting" checked="checked" /></td>
					</tr>
					<tr>
						<th>Enable Captcha</th>
						<td><input type="checkbox" name="enablecaptcha" id="enablecaptcha" /></td>
					</tr>
					<tr>
						<th>Forced Anonymous</th>
						<td><input type="checkbox" name="forcedanon" id="forcedanon" /></td>
					</tr>
					<tr>
						<th>Trial Board</th>
						<td><input type="checkbox" name="trialboard" id="trialboard" /></td>
					</tr>
					<tr>
						<th>Popular Board</th>
						<td><input type="checkbox" name="popularboard" id="popularboard" /></td>
					</tr>
					<tr>
						<th>Enable Recent Posts</th>
						<td><input type="checkbox" name="enablerecentpost" id="enablerecentpost" /></td>
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
		function newboardclick() {
			document.getElementById("boardopt").style.display = "block";
			document.getElementById("logs").style.display = "none";
		}
		//This is a whole bunch of variables lol
		function create() {
			var side = getQueryVariable("side");
			var action = getQueryVariable("action");
			var boarddirectory = document.getElementById("boarddirectory").value;
			var boarddescription = document.getElementById("boarddescription").value;
			var type = document.getElementById("type").value;			
			var section = document.getElementById("section").value;
			var maximagesize = document.getElementById("maximagesize").value;
			var maxpostperpage = document.getElementById("maxpostperpage").value;
			var maxboardpages = document.getElementById("maxboardpages").value;
			var maxthreadhours = document.getElementById("maxthreadhours").value;
			var markpage = document.getElementById("markpage").value;
			var maxthreadreply = document.getElementById("maxthreadreply").value;
			var defaultpostername = document.getElementById("defaultpostername").value;
			if (document.getElementById("locked").checked == true) {
				var locked = 1;
			} else {
				var locked = 0;
			}
			if (document.getElementById("enableemail").checked == true) {
				var enableemail = 1;
			} else {
				var enableemail = 0;
			}
			if (document.getElementById("enableads").checked == true) {
				var enableads = 1;
			} else {
				var enableads = 0;
			}
			if (document.getElementById("enableids").checked == true) {
				var enableids = 1;
			} else {
				var enableids = 0;
			}
			if (document.getElementById("enablereporting").checked == true) {
				var enablereporting = 1;
			} else {
				var enablereporting = 0;
			}
			if (document.getElementById("enablecaptcha").checked == true) {
				var enablecaptcha = 1;
			} else {
				var enablecaptcha = 0;
			}
			if (document.getElementById("forcedanon").checked == true) {
				var forcedanon = 1;
			} else {
				var forcedanon = 0;
			}
			if (document.getElementById("trialboard").checked == true) {
				var trialboard = 1;
			} else {
				var trialboard = 0;
			}
			if (document.getElementById("popularboard").checked == true) {
				var popularboard = 1;
			} else {
				var popularboard = 0;
			}
			if (document.getElementById("enablerecentpost").checked == true) {
				var enablerecentpost = 1;
			} else {
				var enablerecentpost = 0;
			}
			var id = document.getElementById("id").value;
			let req = new XMLHttpRequest();
			let formData = new FormData();
			formData.append("boarddirectory", boarddirectory);
			formData.append("boarddescription", boarddescription);
			formData.append("type", type);
			formData.append("section", section);
			formData.append("maximagesize", maximagesize);
			formData.append("maxpostperpage", maxpostperpage);
			formData.append("maxboardpages", maxboardpages);
			formData.append("maxthreadhours", maxthreadhours);
			formData.append("markpage", markpage);
			formData.append("maxthreadreply", maxthreadreply);
			formData.append("defaultpostername", defaultpostername);
			formData.append("locked", locked);
			formData.append("enableemail", enableemail);
			formData.append("enableads", enableads);
			formData.append("enableids", enableids);
			formData.append("enablereporting", enablereporting);
			formData.append("enablecaptcha", enablecaptcha);
			formData.append("forcedanon", forcedanon);
			formData.append("trialboard", trialboard);
			formData.append("popularboard", popularboard);
			formData.append("enablerecentpost", enablerecentpost);
			formData.append("id", id);
			req.open("POST", 'index.php?action='+action+'&do=create');
			req.send(formData);
			req.onreadystatechange = function () {
				window.location.replace("index.php?side="+side+"&action="+action);
			}
		}
		function edit(id, boarddirectory, boarddescription, type, section, maximagesize, maxpostperpage, maxboardpages, maxthreadhours, markpage, maxthreadreply, defaultpostername, locked, enableemail, enableads, enableids, enablereporting, enablecaptcha, forcedanon, trialboard, popularboard, enablerecentpost) {
			newboardclick();
			document.getElementById("id").value = id;
			document.getElementById('boarddirectory').disabled = true;
			document.getElementById("boarddirectory").value = boarddirectory;
			document.getElementById("boarddescription").value = boarddescription;
			document.getElementById("type").value = type;
			document.getElementById("section").value = section;
			document.getElementById("maximagesize").value = maximagesize;
			document.getElementById("maxpostperpage").value = maxpostperpage;
			document.getElementById("maxboardpages").value = maxboardpages;
			if (maxthreadhours == 0) {
			 document.getElementById("maxthreadhours").value = "";
			} else {
				document.getElementById("maxthreadhours").value = maxthreadhours;
			}
			document.getElementById("markpage").value = markpage;
			if (maxthreadreply == 0) {
				document.getElementById("maxthreadreply").value = "";
			} else {
				document.getElementById("maxthreadreply").value = maxthreadreply;
			}
			document.getElementById("defaultpostername").value = defaultpostername;
			if (locked == 1) {
				document.getElementById("locked").checked = true;
			} else {
				document.getElementById("locked").checked = false;
			}
			if (enableemail == 1) {
				document.getElementById("enableemail").checked = true;
			} else {
				document.getElementById("enableemail").checked = false;
			}
			if (enableads == 1) {
				document.getElementById("enableads").checked = true;
			} else {
				document.getElementById("enableads").checked = false;
			}
			if (enableids == 1) {
				document.getElementById("enableids").checked = true;
			} else {
				document.getElementById("enableids").checked = false;
			}
			if (enablereporting == 1) {
				document.getElementById("enablereporting").checked = true;
			} else {
				document.getElementById("enablereporting").checked = false;
			}
			if (enablecaptcha == 1) {
				document.getElementById("enablecaptcha").checked = true;
			} else {
				document.getElementById("enablecaptcha").checked = false;
			}
			if (forcedanon == 1) {
				document.getElementById("forcedanon").checked = true;
			} else {
				document.getElementById("forcedanon").checked = false;
			}
			if (trialboard == 1) {
				document.getElementById("trialboard").checked = true;
			} else {
				document.getElementById("trialboard").checked = false;
			}
			if (popularboard == 1) {
				document.getElementById("popularboard").checked = true;
			} else {
				document.getElementById("popularboard").checked = false;
			}
			if (enablerecentpost == 1) {
				document.getElementById("enablerecentpost").checked = true;
			} else {
				document.getElementById("enablerecentpost").checked = false;
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