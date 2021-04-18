{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Rebuild all Board pages
	</div>
	<div class="modules">
		<div class="logs" id="logs">
			<div id="confirm">
				<div class="confirm" id="confirm">
					Successfully ran Rebuild All!
				</div>
				<br /><br />
			</div>
			<div class="fc">
				<input type="submit" value="Run" onclick="run();" />
			</div>
		</div>
	</div>
	<script>
		window.onload = function() {
			document.getElementById("confirm").style.display = "none";
			if (sessionStorage.getItem('success') == "true") {
				document.getElementById("confirm").style.display = "block";
			}
			sessionStorage.setItem('success', 'false');
		}
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
		function run() {
			var side = getQueryVariable("side");
			var action = getQueryVariable("action");
			let req = new XMLHttpRequest();
			let formData = new FormData();
			req.open("POST", 'index.php?action='+action+'&do=run');
			req.send(formData);
			req.onreadystatechange = function () {
				window.location.replace("index.php?side="+side+"&action="+action);
				sessionStorage.setItem('success', 'true');
			}
		}
	</script>
{% endblock %}