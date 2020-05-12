{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Change Account Password
	</div>	
	<div class="modules">
		<table class="changepass">
			<form method="POST">
				{% if error %}
					<div class="error">
						{{message}}
					</div>
				{% elseif confirm %}
					<div class="confirm">
						{{message}}
					</div>
				{% endif %}
				<div class="changepass">
					<li>
						Old password: <div class="fr"><input type="password" name="oldpass" placeholder="Old Password" /></div>
					</li>
					<li>
						New Password: <div class="fr"><input type="password" name="newpass" placeholder="New Password" /></div>
					</li>
					<li>
						<div class="fr"><input type="password" name="newpass2" placeholder="Confirm Password" /></div>
					</li>
					<li>
						<br />
						<div class="fr"><input type="submit" name="submit" value="Submit" /></div>
					</li>
				</div>
			</form>
		</table>
	</div>	
{% endblock %}
