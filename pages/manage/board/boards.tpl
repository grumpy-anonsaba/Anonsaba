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
			</table>
		</div>
		<div class="boardopt" id="boardopt">
			<table cellspacing="1px">
				<thead>
					<tr>
						<th>Board Directory</th>
						<td><input type="text" name="boarddirectory" id="boarddirectory" /></td>
					</tr>
					<tr>
						<th>Board Name</th>
						<td><input type="text" name="boarddescription" id="boarddescription" /></td>
					</tr>
					<tr>
						<th>Board Type</th>
						<td>
							<select name="type">
								<option value="sfw">Safe for Work</option>
								<option value="nsfw">Not Safe for Work</option>
							</select>
						</td>
					<tr>
						<th>Board Section</th>
						<td>
							<select name="section">
								<option value="">Select a section</option>
								{% for section in entry %}
									<option value="{{section.name}}" {% if item.section == section.name %}selected="selected"{% endif %} />{{section.name}}</option>
								{% endfor %}
							</select>
						</td>
					</tr>
					<tr>
						<th>Max Image Size</th>
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
						<td><input type="text" name="maxthreadhours" id="maxthreadhours" value="" /></td>
					</tr>
					<tr>
						<th>Mark Page</th>
						<td><input type="text" name="markpage" id="markpage" value="9" /></td>
					</tr>
					<tr>
						<th>Max Thread Replies</th>
						<td><input type="text" name="maxthreadreply" id="maxthreadreply" value="" /></td>
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
	<script>
		function newboardclick() {
			document.getElementById("boardopt").style.display = "block";
			document.getElementById("logs").style.display = "none";
		}
	</script>
{% endblock %}