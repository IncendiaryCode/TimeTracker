var addProject = document.getElementById('add-project');
if (addProject != null) {
addProject.onsubmit = function (e) {
	var task = document.getElementById('project-name').value;
	if (task =="" || task == " ") {
		document.getElementById('project-error').innerHTML = "Enter Project name";
		return false;
	}
	else
		return true;
}
}