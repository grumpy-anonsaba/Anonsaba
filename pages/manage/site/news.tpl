{% extends "/manage/main.tpl" %}
{% block managecontent %}
	<script src="http://code.jquery.com/jquery-1.8.3.js" type="text/javascript"></script>
	<div class="moduleheader">
		News
	</div>
	<div class="modules">
		<div class="newsfaqrules">
			<li>
				Email: <div class="fr"><input type="text" name="email" placeholder="Can be left blank" /></div>
			</li>
			<li>
				Subject: <div class="fr"><input type="text" name="subject" placeholder="Cannot be left blank" /></div>
			</li>
		</div>
		<div class="giphy">
			<form id="gif-form">
				<input type="text" class="search" placeholder="Search for GIFS">
			</form>
			<div class="results"></div>
		</div>
			<div id="editor-container">
				<div id="editor-menu">
					<button id="bold-button" title="Bold"><img src="/pages/images/text-bold-icon.png" /></button>
					<button id="underline-button" title="Underline"><img src="/pages/images/text-underlined-icon.png" /></button>
					<button id="list-button" title="Bullet List"><img src="/pages/images/text-bulletedlist-icon.png" /></button>
					<button id="image-button" title="Picture"><img src="/pages/images/upload-image-icon.png" /></button>
					<button id="gif-button" title="GIF"><img src="/pages/images/gif-icon.png" /></button>
					<div class="fr"><button id="save" title="Save"><img src="/pages/images/save-icon.png" /></button></div>
				</div>
				<div id="editor-text" contenteditable="true" spellcheck="true">
					<!-- come back -->
				</div>
			</div>
	</div>
		<script>
			// Bold menu
			document.querySelector('#bold-button').addEventListener('click', function() {
				document.execCommand('bold');
			});

			// Underline menu
			document.querySelector('#underline-button').addEventListener('click', function() {
				document.execCommand('underline');
			});

			// List menu
			document.querySelector('#list-button').addEventListener('click', function() {
				document.execCommand('insertUnorderedList');
			});

			// Picture menu
			document.querySelector('#image-button').addEventListener('click', function() {
				$('#file-input').trigger('click');
			});
			
			// GIF menu
			document.querySelector('#gif-button').addEventListener('click', function() {
				alert('Yay!');
			});

			// Check menu options to be highlighted on keyup and click event 
			document.querySelector('#editor-text').addEventListener('keyup', FindCurrentTags);
			document.querySelector('#editor-text').addEventListener('click', FindCurrentTags);

			function FindCurrentTags() {
				// Editor container 
				var editor_element = document.querySelector('#editor-text');
				
				// No of ranges
				var num_ranges = window.getSelection().rangeCount;

				// Will hold parent tags of a range
				var range_parent_tags;

				// Will hold parent tags of all ranges
				var all_ranges_parent_tags = [];
					
				// Current menu tags
				var menu_tags = [ 'B', 'UL', 'U' ];
					
				// Will hold common tags from all ranges
				var menu_tags_common = [];

				var start_element,
					end_element,
					cur_element;

				// For all ranges
				for(var i=0; i<num_ranges; i++) {
					// Start container of range
					start_element = window.getSelection().getRangeAt(i).startContainer;
					
					// End container of range
					end_element = window.getSelection().getRangeAt(i).endContainer;
					
					// Will hold parent tags of a range
					range_parent_tags = [];

					// If starting node and final node are the same
					if(start_element.isEqualNode(end_element)) {
						// If the current element lies inside the editor container then don't consider the range
						// This happens when editor container is clicked
						if(editor_element.isEqualNode(start_element)) {
							all_ranges_parent_tags.push([]);
							continue;
						}

						cur_element = start_element.parentNode;
						
						// Get all parent tags till editor container    
						while(!editor_element.isEqualNode(cur_element)) {
							range_parent_tags.push(cur_element.nodeName);
							cur_element = cur_element.parentNode;
						}
					}

					// Push tags of current range 
					all_ranges_parent_tags.push(range_parent_tags);
				}

				// Find common parent tags for all ranges
				for(i=0; i<menu_tags.length; i++) {
					var common_tag = 1;
					for(var j=0; j<all_ranges_parent_tags.length; j++) {
						if(all_ranges_parent_tags[j].indexOf(menu_tags[i]) == -1) {
							common_tag = -1;
							break;
						}
					}

					if(common_tag == 1)
						menu_tags_common.push(menu_tags[i]);
				}
				// Highlight menu for common tags
				if(menu_tags_common.indexOf('B') != -1)
					document.querySelector("#bold-button").classList.add("highight-menu");
				else
					document.querySelector("#bold-button").classList.remove("highight-menu");

				if(menu_tags_common.indexOf('U') != -1)
					document.querySelector("#underline-button").classList.add("highight-menu");
				else
					document.querySelector("#underline-button").classList.remove("highight-menu");

				if(menu_tags_common.indexOf('UL') != -1)
					document.querySelector("#list-button").classList.add("highight-menu");
				else
					document.querySelector("#list-button").classList.remove("highight-menu");
			}
			const gifForm = document.querySelector("#gif-form");
			gifForm.addEventListener("keyup", fetchGiphs);

			fetchGiphstop50();

			function fetchGiphstop50() {
			   
				fetch(`https://api.giphy.com/v1/gifs/trending?&limit=50&api_key=sLMCm8s9H5yxCVgku6bNVIkpvzGt2myz`)
				.then((response) => {return response.json(); })
				.then((resp => {
					// Here we get the data array from the response object
					let dataArray = resp.data
					// We pass the array to showGiphs function
					showGiphs(dataArray);
				}))
				.catch(err => console.log(err)); // We use catch method for Error handling
			}

			function fetchGiphs(e) {
				e.preventDefault();
				var searchTerm = document.querySelector(".search").value;
				
				if (searchTerm == '') {
					fetchGiphstop50();
				} else {
					fetch(`https://api.giphy.com/v1/gifs/search?&q=${searchTerm}&limit=50&api_key=sLMCm8s9H5yxCVgku6bNVIkpvzGt2myz`)
					.then((response) => {return response.json(); })
					.then((resp => {
						// Here we get the data array from the response object
						let dataArray = resp.data
						// We pass the array to showGiphs function
						showGiphs(dataArray);
					}))
					.catch(err => console.log(err)); // We use catch method for Error handling
				}
			}

			function showGiphs(dataArray) {
			  const results = document.querySelector(".results");
			  let output = '<div class="container">';
			  dataArray.forEach((imgData) => {
				output += `
				<li>
					<img src="${imgData.images.fixed_width.url}" width="100" height="100"/>
				</li>
			`;
			  });
			  document.querySelector('.results').innerHTML = output;
			}
		</script>
	</div>
{% endblock %}
