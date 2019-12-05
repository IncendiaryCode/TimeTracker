function dark_mode()
{
   $("body").css( "background-image", "linear-gradient(-40deg, #212529 40%, #212529 100%)");
        $(".stop-time").css( "background-image", "linear-gradient(-40deg, #212529 40%, #212529 100%)");
        $(".start-time").css( "background-image", "linear-gradient(-40deg, #212529 40%, #212529 100%)");
        $(".primary-timer").css( "font-size", "100px", "color", "#c0b1f2");
        $(".card-style-1").css( "background-color", "#000000", "color", "#ffffff");
        $(".card-header").css( "background-color", "#000000", "color", "#ffffff");
        $(".card-footer").css( "background-color", "#000000", "color", "#ffffff");
        $(".main-container-employee").css( "background-color", "#000000", "color", "#ffffff");
        $(".main-container").css( "background-color", "#000000", "color", "#ffffff");
        $(".container").css("color", "#ffffff");
        $(".dropdown-menu").css("background-color", "#000000", "color", "#ffffff");
        $(".form-control").css("background-color", "#000000", "color", "#ffffff");
        $(".form-control-file").css("background-color", "#000000", "color", "#ffffff");
        $(".close").css("color", "#ffffff");
        $(".alert").css("background-color", "#000000", "color", "#ffffff");
        $(".modal-transparent").css( "background-image", "linear-gradient(-40deg, #212529 40%, #212529 100%)");
        $(".modal-content").css( "background-image", "linear-gradient(-40deg, #212529 40%, #212529 100%)" ); 
}


$(document).ready(function()
{	
	$('#dark-mode').click(function()
	{
		var dark_mode = document.getElementById('dark-mode').checked;
		console.log(dark_mode);
		if (dark_mode) {
		document.getElementById("dark-mode").checked = true;
		localStorage.setItem("dark_mode", "checked");
	}
	else
	{	
		document.getElementById("dark-mode").checked = false;
		localStorage.setItem("dark_mode", "Not checked");
	}
	});
});