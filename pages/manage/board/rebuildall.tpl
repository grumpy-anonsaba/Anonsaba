{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		Rebuild all Board pages
	</div>
	<div class="modules">
		<div class="logs" id="logs">
			<div id="message">
				<div class="alert alert-success alert-dismissible fade in">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<div id="confirms">
						<strong>Success!</strong> Ran "Rebuild All" in 
					</div>
				</div>
				<br />
			</div>
			<div class="fc">
				<input type="submit" value="Run" onclick="run();" />
			</div>
		</div>
	</div>
	<script>
		window.onload = function() {
			document.getElementById("message").style.display = "none";
			if (sessionStorage.getItem('done') == "success") {
				document.getElementById("message").style.display = "block";
				document.getElementById("confirms").innerHTML += sessionStorage.getItem('time') + " seconds"
			}
			sessionStorage.setItem('done', '');
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
			req.open("GET", 'index.php?action='+action+'&do=run');
			req.send(formData);
			req.onreadystatechange = function () {
				var obj = JSON.parse(this.responseText);
				sessionStorage.setItem('done', obj.done);
				sessionStorage.setItem('time', obj.time);
				window.location.replace("index.php?side="+side+"&action="+action);
			}
		}
	</script>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
{% endblock %}