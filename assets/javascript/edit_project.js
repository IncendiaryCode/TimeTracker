function autocomplete(inp, arr) {
	var currentFocus;
	inp.addEventListener('input', function(e) {
		var a,
			b,
			i,
			val = this.value;
		closeAllLists();
		if (!val) {
			return false;
		}
		currentFocus = -1;
		a = document.createElement('DIV');
		a.setAttribute('id', this.id + 'autocomplete-list');
		a.setAttribute('class', 'autocomplete-items');
		//this.parentNode.parentNode.parentNode.parentNode.appendChild(a);
		$('#append-list').append(a);
		document.getElementById('user-error').innerHTML = '';
		for (i = 0; i < arr.length; i++) {
			if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
				b = document.createElement('DIV');
				b.innerHTML = '<strong>' + arr[i].substr(0, val.length) + '</strong>';
				b.innerHTML += arr[i].substr(val.length);
				b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
				b.addEventListener('click', function(e) {
					inp.value = this.getElementsByTagName('input')[0].value;
					closeAllLists();
				});
				a.appendChild(b);
			}
		}
	});
	/*execute a function presses a key on the keyboard:*/
	inp.addEventListener('keydown', function(e) {
		var x = document.getElementById(this.id + 'autocomplete-list');
		if (x) x = x.getElementsByTagName('div');
		if (e.keyCode == 40) {
			/*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
			currentFocus++;
			/*and and make the current item more visible:*/
			addActive(x);
		} else if (e.keyCode == 38) {
			//up
			/*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
			currentFocus--;
			/*and and make the current item more visible:*/
			addActive(x);
		} else if (e.keyCode == 13) {
			/*If the ENTER key is pressed, prevent the form from being submitted,*/
			e.preventDefault();
			if (currentFocus > -1) {
				/*and simulate a click on the "active" item:*/
				if (x) x[currentFocus].click();
			}
		}
	});
	function addActive(x) {
		if (!x) return false;
		/*start by removing the "active" class on all items:*/
		removeActive(x);
		if (currentFocus >= x.length) currentFocus = 0;
		if (currentFocus < 0) currentFocus = x.length - 1;
		/*add class "autocomplete-active":*/
		x[currentFocus].classList.add('autocomplete-active');
	}
	function removeActive(x) {
		/*a function to remove the "active" class from all autocomplete items:*/
		for (var i = 0; i < x.length; i++) {
			x[i].classList.remove('autocomplete-active');
		}
	}
	function closeAllLists(elmnt) {
		var x = document.getElementsByClassName('autocomplete-items');
		for (var i = 0; i < x.length; i++) {
			if (elmnt != x[i] && elmnt != inp) {
				x[i].parentNode.removeChild(x[i]);
			}
		}
	}
}

if (typeof usr_arr != 'undefined') {
	autocomplete(document.getElementById('user-assigned'), usr_arr);
}

$(document).ready(function() {
	if (document.getElementById('module-edit')) {
		var pre_edit = document.getElementById('pre-edit-module');
		pre_edit.onsubmit = function() {
			var module_name = document.getElementById('append-module-id').getAttribute('data-item');
			document.getElementById('mdl_id').value = module_name;
			window.location.reload();
			return true;
		};
	}
	$('#append-module').click(function() {
		if (this.parentNode.parentNode.childNodes[1].value != '') {
			document.getElementById('module-error').innerHTML = ' ';
			//call to add to db
			$.ajax({
				type: 'POST',
				url: timeTrackerBaseURL + 'admin/add_module',
				data: { module_name: this.parentNode.parentNode.childNodes[1].value, project_id: document.getElementById('edit_project_id').value },
				dataType: 'json',
				success: function(res) {
					if(res['status'] == true)
					{
						$('.module-lists').append(
							'<li class="list-group-item d-flex justify-content-between align-items-center">' +
							document.getElementById("append-module").parentNode.parentNode.childNodes[1].value +
								'<span><a href="#module-edit" data-toggle="modal" data = ' +
								res['module_id'] +
								' class = "module-edit"><i class="fas fa-pencil-alt"></i></a><a href="#module-delete" data-toggle="modal" class = "module-delete"><i class="fas fa-trash pl-3"></i></a></span></li>'
						);
						document.getElementById("append-module").parentNode.parentNode.childNodes[1].value = '';
						window.location.reload();
					}
					else{
						document.getElementById('module-error').innerHTML = res['msg'];
					}
			}
			});
		} else {
			document.getElementById('module-error').innerHTML = 'Please enter module name';
		}
	});

	$('#append-user').click(function() {
		var flag = 0;
		if (this.parentNode.parentNode.childNodes[1].value != '') {
			document.getElementById('user-error').innerHTML = ' ';
			for (var i = 0; i < usr_arr.length; i++) {
				if (this.parentNode.parentNode.childNodes[1].value == usr_arr[i]) flag = 1; // check whether user is present already or not
			}
			if (flag == 1) {
				$.ajax({
					type: 'POST',
					url: timeTrackerBaseURL + 'admin/add_user_to_project',
					data: { user_id: usr_id[this.parentNode.parentNode.childNodes[1].value], project_id: document.getElementById('edit_project_id').value },
					dataType: 'json',
					success: function(res) {
						if(res['status'] == true)
						{
						$('.user-lists').append(
							'<li class="list-group-item d-flex justify-content-between align-items-center">' +
								document.getElementById("append-user").parentNode.parentNode.childNodes[1].value +
								'<span><a href="#user-delete" data-toggle="modal" data = ' +
								usr_id[document.getElementById("append-user").parentNode.parentNode.childNodes[1].value] +
								' class = "user-delete"><i class="fas fa-trash pl-3"><input type = "hidden" value = ' +
								usr_id[document.getElementById("append-user").parentNode.parentNode.childNodes[1].value] +
								' ></i></a></span></li>'
						);
						document.getElementById("append-user").parentNode.parentNode.childNodes[1].value = '';
						window.location.reload();
					}
					else{
					document.getElementById('user-error').innerHTML = res['msg'];
					$('#append-list').empty();
					}
				}
				});
			} else{
				document.getElementById('user-error').innerHTML = 'Please select existing user';
				$('#append-list').empty();
			} 
		} else {
			document.getElementById('user-error').innerHTML = 'Please enter user name';
		}
	});
	$('.module-edit').click(function() {
		document.getElementById('module-name').value = this.parentNode.parentNode.innerText;
	});

	if(document.getElementById('user-assigned'))
	{
		document.getElementById('user-assigned').addEventListener('click', function(e) {
			closeAllLists(e.target);
		});
	}

	$('.module-delete').click(function() {
		var module_id = this.getAttribute('data');
		var list_element = this.parentNode.parentNode;
		$('#delete-module').click(function() {
			$.ajax({
				type: 'POST',
				url: timeTrackerBaseURL + 'admin/delete_module',
				data: { project_id: document.getElementById('edit_project_id').value, module_id: module_id },
				dataType: 'json',
				success: function(res) {
					if(res["status"] == true)
					{
						$(list_element).remove();
						$('#module-delete').modal('hide')
					}
				}
			});
		});
	});

	$('.user-delete').click(function() {
		var user_id = this.getAttribute('data');
		var list_element = this.parentNode.parentNode;
		$('#delete-user').click(function() {
			$.ajax({
				type: 'POST',
				url: timeTrackerBaseURL + 'admin/unassign_user',
				data: { project_id: document.getElementById('edit_project_id').value, user_id: user_id },
				dataType: 'json',
				success: function(res) {
					if(res["status"] == true)
					{
						$(list_element).remove();
						$('#user-delete').modal('hide')
					}
				}
			});
		});
	});
});
