//This file is used commonly on all pages through out the application

function check_for_punchIn() {
	if (document.getElementById("stop-time")) {
		if (
			document.getElementById("stop-time").childNodes[1].childNodes[0]
				.classList[2] == "fa-play"
		) {
			// $("#play-timer").modal("show");
			$("#alert-punchin").modal("show");
		} else {
			window.location.href = timeTrackerBaseURL + "user/load_add_task";
		}
	}
}

$(function() {
    //sticky header on scroll
    $(window).on('scroll', function(e){
        // console.log($(window).scrollTop());
        console.log($('header#main-header').outerHeight());
        if($(window).scrollTop() > $('header#main-header').outerHeight()){
            $('header#main-header').addClass('main-header-sticky');
        } else {
            $('header#main-header').removeClass('main-header-sticky');
        }
    });

    $(".edit-date-time").datetimepicker({
		useCurrent: false,
		format: "YYYY-MM-DD hh:mm A"
	});
    
    $(".edit-date").datepicker({
		useCurrent: false,
		format: "yyyy-mm-dd"
	});
    
    $(".edit-time").timepicker({
		useCurrent: false,
		format: "hh:mm:ss"
    });
    
});
