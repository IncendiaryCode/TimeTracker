
$(document).ready(function()
{	
	var checkbox_status = document.getElementById('dark-mode-checkbox').checked;
	$('dark-mode-checkbox').attr('data-value', checkbox_status);
    $("input:checkbox").change(
        function()
        {
            $("#dark-mode").submit();
        });
});