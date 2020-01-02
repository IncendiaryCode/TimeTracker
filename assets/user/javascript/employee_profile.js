$(document).ready(function()
{	
	if(document.getElementById('dark-mode-checkbox'))
	{
	    $("input:checkbox").change(
	        function()
	        {
				var checkbox_status = document.getElementById('dark-mode-checkbox').checked;
				document.getElementById('hidden-status').value = checkbox_status;
	            $("#dark-mode").submit();
	        });
	}
});