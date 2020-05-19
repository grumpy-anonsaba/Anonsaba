// Bold menu
document.querySelector('#bold-button').addEventListener('click', function() {
	document.execCommand('bold');
});

// Underline menu
document.querySelector('#underline-button').addEventListener('click', function() {
	document.execCommand('underline');
});

// Italic menu
document.querySelector('#italic-button').addEventListener('click', function() {
	document.execCommand('italic');
});

// Bulleted List menu
document.querySelector('#list-button').addEventListener('click', function() {
	document.execCommand('insertUnorderedList');
});

// Number List menu
document.querySelector('#number-list-button').addEventListener('click', function() {
	document.execCommand('insertOrderedList');
});

// Indent
document.querySelector('#indent-right-button').addEventListener('click', function() {
	document.execCommand('indent');
});

// Outdent
document.querySelector('#indent-left-button').addEventListener('click', function() {
	document.execCommand('outdent');
});

// Align Center
document.querySelector('#align-center-button').addEventListener('click', function() {
	document.execCommand('justifyCenter');
});

// Align Left
document.querySelector('#align-left-button').addEventListener('click', function() {
	document.execCommand('justifyLeft');
});

// Save
document.querySelector('#save').addEventListener('click', function() {
	var sub = document.getElementById("subject").value;
	var ema = document.getElementById("email").value;
	var pos = $('#editor-text').html();
	let req = new XMLHttpRequest();
	let formData = new FormData();
	formData.append("subject", sub);
	formData.append("email", ema);
	formData.append("post", pos);
	req.open("POST", 'index.php?action=news&do=post');
	req.send(formData);
	req.onreadystatechange = function () {
		document.getElementById("editor-text").innerHTML = this.responseText;
	}
});

// Picture menu
document.querySelector('#image-button').addEventListener('click', function() {
	$("#myfile").trigger("click");
	$('#myfile').change(function() {
		var form = $(this).parent(),
		fileInput = $(this),
		selectedFile = fileInput.val();
		var filefield = document.getElementById("myfile");
		if(selectedFile != '') {
			let photo = document.getElementById("myfile").files[0];
			let req = new XMLHttpRequest();
			let formData = new FormData();
			formData.append("photo", photo);                                
			req.open("POST", 'index.php?action=news&do=filesubmit');
			req.send(formData);
			req.onreadystatechange = function() {
				if (req.readyState === 4) {
					var editor = document.getElementById("editor-text");
					editor.focus();
					doRestore();
					document.execCommand('insertImage', false , '/manage/images/'+this.responseText);
				}
			}
			clearInputFile(filefield);
		}
	});
});

function clearInputFile(f){
	if(f.value){
		try{
			f.value = ''; //for IE11, latest Chrome/Firefox/Opera...
		}catch(err){
		}
		if(f.value){ //for IE5 ~ IE10
			var form = document.createElement('form'), ref = f.nextSibling;
			form.appendChild(f);
			form.reset();
			ref.parentNode.insertBefore(f,ref);
		}
	}
}

// Hyperlink
document.querySelector('#hyperlink-button').addEventListener('click', function() {
	var linkURL = prompt('Enter a URL:', 'http://');
	document.execCommand('createLink', true, linkURL);
});

// GIF menu
document.querySelector('#gif-button').addEventListener('click', function() {
	if (document.getElementById("giphy").style.display == "block") {
		document.getElementById("giphy").style.display = "none";
	} else {
		document.getElementById("giphy").style.display = "block";
	}
});
function insertGif(url) {
	var editor = document.getElementById("editor-text");
	editor.focus();
	doRestore();
	document.execCommand('insertImage', false, url);
	document.getElementById("giphy").scrollTop = 0;
	document.getElementById("giphy").style.display = "none";
	document.querySelector(".search").value = '';
	fetchGiphstop50();
}
document.querySelector('#editor-text').addEventListener('keyup', FindCurrentTags);
document.querySelector('#editor-text').addEventListener('click', FindCurrentTags);
document.querySelector('#editor-text').addEventListener('keyup', doSave);
document.querySelector('#editor-text').addEventListener('click', doSave);

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
	var menu_tags = [ 'B', 'UL', 'U', 'I', 'LI' ];
		
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
	if(menu_tags_common.indexOf('I') != -1)
		document.querySelector("#italic-button").classList.add("highight-menu");
	else
		document.querySelector("#italic-button").classList.remove("highight-menu");
	if(menu_tags_common.indexOf('LI') != -1)
		document.querySelector("#number-list-button").classList.add("highight-menu");
	else
		document.querySelector("#number-list-button").classList.remove("highight-menu");
}
var saveSelection, restoreSelection;

if (window.getSelection && document.createRange) {
	saveSelection = function(containerEl) {
		var range = window.getSelection().getRangeAt(0);
		var preSelectionRange = range.cloneRange();
		preSelectionRange.selectNodeContents(containerEl);
		preSelectionRange.setEnd(range.startContainer, range.startOffset);
		var start = preSelectionRange.toString().length;

		return {
			start: start,
			end: start + range.toString().length
		}
	};

	restoreSelection = function(containerEl, savedSel) {
		var charIndex = 0, range = document.createRange();
		range.setStart(containerEl, 0);
		range.collapse(true);
		var nodeStack = [containerEl], node, foundStart = false, stop = false;
		
		while (!stop && (node = nodeStack.pop())) {
			if (node.nodeType == 3) {
				var nextCharIndex = charIndex + node.length;
				if (!foundStart && savedSel.start >= charIndex && savedSel.start <= nextCharIndex) {
					range.setStart(node, savedSel.start - charIndex);
					foundStart = true;
				}
				if (foundStart && savedSel.end >= charIndex && savedSel.end <= nextCharIndex) {
					range.setEnd(node, savedSel.end - charIndex);
					stop = true;
				}
				charIndex = nextCharIndex;
			} else {
				var i = node.childNodes.length;
				while (i--) {
					nodeStack.push(node.childNodes[i]);
				}
			}
		}

		var sel = window.getSelection();
		sel.removeAllRanges();
		sel.addRange(range);
	}
} else if (document.selection && document.body.createTextRange) {
	saveSelection = function(containerEl) {
		var selectedTextRange = document.selection.createRange();
		var preSelectionTextRange = document.body.createTextRange();
		preSelectionTextRange.moveToElementText(containerEl);
		preSelectionTextRange.setEndPoint("EndToStart", selectedTextRange);
		var start = preSelectionTextRange.text.length;

		return {
			start: start,
			end: start + selectedTextRange.text.length
		}
	};

	restoreSelection = function(containerEl, savedSel) {
		var textRange = document.body.createTextRange();
		textRange.moveToElementText(containerEl);
		textRange.collapse(true);
		textRange.moveEnd("character", savedSel.end);
		textRange.moveStart("character", savedSel.start);
		textRange.select();
	};
}

var savedSelection;

function doSave() {
	savedSelection = saveSelection( document.getElementById("editor-text") );
}

function doRestore() {
	if (savedSelection) {
		restoreSelection(document.getElementById("editor-text"), savedSelection);
	}
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
  if (dataArray.length < 1) {
	output += 'No results found!';
  } else {
	  dataArray.forEach((imgData) => {
		output += `
		<li>
			<a onclick="insertGif('${imgData.images.fixed_width.url}')">
				<img src="${imgData.images.fixed_width.url}" width="100" height="100" />
			</a>
		</li>
		`;
	  });
 }
  document.querySelector('.results').innerHTML = output;
}