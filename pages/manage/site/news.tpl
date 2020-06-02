{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		News
	</div>
	<div class="modules">
		<div class="newsfaqrules" id="newsfaqrules">
			<table style="width:95%">
				<input type="hidden" name="id" id="id" />
				<tr>
					<th>Email</th>
				</tr>
				<tr>
					<td><input type="text" name="email" id="email" placeholder="Can be left blank" /></td>
				</tr>
				<tr>
					<th>Subject</th>
				</tr>
				<tr>
					<td><input type="text" name="subject" id="subject" placeholder="Cannot be left blank" /></td>
				</tr>
			</table>
		</div>
		<div class="giphy" id="giphy">
			<form id="gif-form">
				<input type="text" class="search" placeholder="Search for GIFS">
				<div class="glogo">
					<img src="/pages/images/giphy-logo.png" />
				</div>
			</form>
			<div class="results"></div>
		</div>
		<div id="editor-container">
			<div id="editor-menu">
				<button id="color-button" title="Font Color"><img src="/pages/images/text-color-icon.png" /></button>
				<button id="font-size-button" title="Font Size"><img src="/pages/images/font-size-icon.png" /></button>
				<div class="line">
					<button id="bold-button" title="Bold - CTRL+B"><img src="/pages/images/text-bold-icon.png" /></button>
					<button id="underline-button" title="Underline - CTRL+U"><img src="/pages/images/text-underlined-icon.png" /></button>
					<button id="italic-button" title="Italic - CTRL+I"><img src="/pages/images/italic-text-icon.png" /></button>
				</div>
				<div class="line">
					<button id="list-button" title="Bullet List"><img src="/pages/images/text-bulletedlist-icon.png" /></button>
					<button id="number-list-button" title="Number List"><img src="/pages/images/text-numberlist-icon.png" /></button>
					<button id="indent-right-button" title="Indent Right"><img src="/pages/images/indent-right-icon.png" /></button>
					<button id="indent-left-button" title="Indent Left"><img src="/pages/images/indent-left-icon.png" /></button>
					<button id="align-left-button" title="Align Left"><img src="/pages/images/text-alignleft-icon.png" /></button>
					<button id="align-center-button" title="Align Center"><img src="/pages/images/text-aligncenter-icon.png" /></button>
				</div>
				<div class="line">
					<button id="image-button" title="Picture"><img src="/pages/images/upload-image-icon.png" /></button>
					<button id="gif-button" title="GIF"><img src="/pages/images/gif-icon.png" /></button>
					<button id="hyperlink-button" title="Hyperlink"><img src="/pages/images/hyperlink-icon.png" /></button>
					<button id="youtube-button" title="YouTube"><img src="/pages/images/youtube-icon.png" /></button>
				</div>
				<div class="fr"><button id="save" title="Save"><img src="/pages/images/save-icon.png" /></button></div>
			</div>
			<div id="editor-text" contenteditable="true" spellcheck="true">
			</div>
		</div>
		<input type="file" id="myfile" name="file"/>
		<div class="newsfaqrulesmain" id="newsfaqrulesmain">
			<div class="fc">
				<input type="submit" value="New post" onclick="newpostclick();" />
			</div>
			<br />
			<br />
			<table class="users" cellspacing="1px">
				<col class="col1" /> <col class="col2" />
				<col class="col1" /> <col class="col2" />
				<thead>
					<tr>
						<th>Date Added</th>
						<th>Subject</th>
						<th>Message</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				 {% for news in newspost %}
						<tr>
						<td>{{news.date|date('m/d/Y h:i', 'America/Chicago')}} {% if news.date|date('H', 'America/Chicago') >= 12 %}PM{% else %}AM{% endif %}</td>
						<td>{{ news.subject|raw }}</td>
						<td>{{news.message|striptags("")|slice(0, 60)|raw}}{% if news.message|length > 60 %}...{% endif %}</td>
						<td><input type="submit" value="Edit" onclick="editpost('{{news.email}}', '{{news.subject}}', '{{news.id}}');" />&nbsp;<input type="submit" value="Delete" onclick="delpost('{{news.id}}');" /></td>
					</tr>
				{% endfor %}
				</tbody>
			</table>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript"></script>
	<script src="/pages/javascript/wysiwyg.js" type="text/javascript"></script>
	<script>
		function newpostclick() {
			document.getElementById("editor-container").style.display = "block";
			document.getElementById("newsfaqrules").style.display = "block";
			document.getElementById("newsfaqrulesmain").style.display = "none";
		}
		function editpost (em, sub, id, msg) {
			var action = getQueryVariable("action");
			document.getElementById("editor-container").style.display = "block";
			document.getElementById("newsfaqrules").style.display = "block";
			document.getElementById("newsfaqrulesmain").style.display = "none";
			document.getElementById("email").value = em;
			document.getElementById("subject").value = sub;
			document.getElementById("id").value = id;
			let req = new XMLHttpRequest();
			req.open("POST", 'index.php?action='+action+'&do=getmsg&id='+id);
			req.send();
			req.onreadystatechange = function () {
				if (req.readyState === 4) {
					document.getElementById("editor-text").innerHTML += this.responseText;
				}
			}
		}
		function delpost (id) {
			var side = getQueryVariable("side");
			var action = getQueryVariable("action");
			let req = new XMLHttpRequest();
			req.open("POST", 'index.php?action='+action+'&do=delpost&id='+id);
			req.send();
			req.onreadystatechange = function () {
				if (req.readyState === 4) {
					window.location.replace("index.php?side="+side+"&action="+action);
				}
			}
		}
	</script>
{% endblock %}