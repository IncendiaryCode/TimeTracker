//This file is used commonly on all pages through out the application

function check_for_punchIn() {
	if (check_fr_punchIn == 0) {
		$('#alert-punchin').modal('show');
		document.getElementById('start-login-time').value = moment().format('HH:mm');
		return false;
	}
	if (check_fr_punchOut == 1) {
		$('#play-timer').modal('show');
		return false;
	}
	if (document.getElementById('stop-time')) {
		if (document.getElementById('stop-time').childNodes[1].childNodes[0].classList[2] == 'fa-play') {
			if (stopped == 1) {
				$('#play-timer').modal('show');
				return false;
			}
			if (not_logged == 1) {
				$('#alert-punchin').modal('show');
				document.getElementById('start-login-time').value = moment().format('HH:mm');
				return false;
			}
		} else {
			return true;
		}
	}
}
$('#alert-for-punchin').click(function() {
	$('#play-timer').modal('show');
});

$(function() {
	//sticky header on scroll
	$(window).on('scroll', function(e) {
		if ($(window).scrollTop() > $('header.main-header').outerHeight() - 60) {
			$('header.main-header').addClass('main-header-sticky');
		} else {
			$('header.main-header').removeClass('main-header-sticky');
		}
	});

	$('.edit-date-time').datetimepicker({
		useCurrent: false,
		format: 'YYYY-MM-DD hh:mm A'
	});

	$('.edit-date').datepicker({
		useCurrent: false,
		format: 'yyyy-mm-dd'
	});

	$('.edit-time').timepicker({
		mode: '24hr',
		format: 'HH:MM',
		useCurrent: false
	});

	$('#timerpicker-punchout').timepicker({
		mode: '24hr',
		format: 'HH:MM',
		useCurrent: false
	});
	document.getElementById('timerpicker-punchout').value = moment().format('HH:mm');
	if (document.getElementById('punch-out-action')) {
		var punch_out = document.getElementById('punch-out-action');
		var __element = document.getElementById('punch-out-action');
		punch_out.onsubmit = function() {
			var punch_out = document.getElementById('timerpicker-punchout').value;
			if (punch_out == ' ' || punch_out == '') {
				document.getElementById('punch-out-err').innerHTML = 'enter valid punch out time. ';
				return false;
			} else {
				var serverDate = moment(moment().format('YYYY-MM-DD') + ' ' + punch_out).tz('utc').format('YYYY-MM-DD H:mm:ss');
				document.getElementById('timerpicker-punchout').value = serverDate;
				if (typeof parseInt(serverDate.slice(0, 2)) == 'string') {
					document.getElementById('punch-out-err').innerHTML = 'enter valid punch out time. ';
					return false;
				}
				if (serverDate == 'Invalid date') {
					document.getElementById('punch-out-err').innerHTML = 'enter valid punch out time. ';
					return false;
				}
				return true;
			}
		};
	}
	$('#year-picker .input-group.date').datepicker({
		minViewMode: 2,
		autoclose: true,
		format: "yyyy"
	});
	$('.timerpicker-c').timepicker({
		mode: '24hr',
		format: 'HH:MM',
		uiLibrary: 'bootstrap4'
	});
	$('.user-profile').tooltip('enable');
	var picker_footer = $('.gj-picker')[0].childNodes[2].childNodes[0];
	$(picker_footer).css("background-image", "linear-gradient(to bottom right, #666, #666, #666)");
});
