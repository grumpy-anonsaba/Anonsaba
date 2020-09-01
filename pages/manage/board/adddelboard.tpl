{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Add/Delete Boards
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
	</div>
{% endblock %}