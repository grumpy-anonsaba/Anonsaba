{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<div class="moduleheader">
		News
	</div>
	<div class="modules">
		<div class="newsfaqrules">
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
				Powered by GIPHY!
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
		<div class="newsfaqrulesmain">
			<div class="fc">
				<input type="submit" value="New post" />
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.5.1.js" type="text/javascript"></script>
	<script src="/pages/javascript/wysiwyg.js" type="text/javascript"></script>
{% endblock %}