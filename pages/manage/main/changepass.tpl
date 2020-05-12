{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Change Account Password
	</div>	
	<div class="modules">
		<table class="changepass">
			<form method="POST">
				<div class="error">
					This is a test error message!
				</div>
				<div class="changepass">
					Old password: <input type="text" name="oldpass" />
					New Password: <input type="text" name="newpass" />
								  <input type="text" name="newpass2" />
					<input type="submit" name="submit" value="Submit" />
				</div>
			</form>
		</table>
	</div>	
{% endblock %}
