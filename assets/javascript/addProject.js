var addProject = document.getElementById('addProject');
addProject.onsubmit = function (e) {
	var task = document.getElementById('Projectname').value;
	if (task =="" || task == " ") {
		document.getElementById('projectError').innerHTML = "Enter Project name";
		return false;
	}
	else
		return true;
}