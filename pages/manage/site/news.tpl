{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<script src="http://code.jquery.com/jquery-1.8.3.js" type="text/javascript"></script>
	<div class="moduleheader">
		News
	</div>
	<div class="modules">
		<div class="newsfaqrules">
			<table style="width:95%">
				<tr>
					<th>Email</th>
				</tr>
				<tr>
					<td><input type="text" name="email" placeholder="Can be left blank" /></td>
				</tr>
				<tr>
					<th>Subject</th>
				</tr>
				<tr>
					<td><input type="text" name="subject" placeholder="Cannot be left blank" /></td>
				</tr>
			</table>
		</div>
		<div id="editor-container">
			<main>
				<div class="toolbar-container"></div>
				<div class="content-container">
					<div id="editor">
					</div>
				</div>
			</main>
		</div>
	</div>
	<script src="/modules/core/CKEditor/ckeditor.js"></script>

	<script>
		DecoupledEditor
			.create( document.querySelector( '#editor' ), {
				// toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
			} )
			.then( editor => {
				const toolbarContainer = document.querySelector( 'main .toolbar-container' );

				toolbarContainer.prepend( editor.ui.view.toolbar.element );

				window.editor = editor;
			} )
			.catch( err => {
				console.error( err.stack );
			} );
	</script>
{% endblock %}
