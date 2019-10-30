var addProject = document.getElementById('addProject');
addProject.onsubmit = function (e) {
	var task = document.getElementById('project-name').value;
	var logo = document.getElementById('project-logo').value;
	var color = document.getElementById('project-color').value;

	if (task =="" || task == " ") {
		document.getElementById('projectError').innerHTML = "Please enter Project name";
		return false;
	}
	if (logo =="" || logo == " ") {
		document.getElementById('projectError').innerHTML = "Please select Project logo";
		return false;
	}
	if (color =="" || color == " ") {
		document.getElementById('projectError').innerHTML = "Please enter Project color";
		return false;
	}
	else
		return true;
}