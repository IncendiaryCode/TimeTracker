var user_profile_chart;
function __draw_profile_chart(res) {
    var user_chart = document.getElementById('user_prof_chart').getContext('2d');
    gradient = user_chart.createLinearGradient(0, 0, 0, 600);

    gradient.addColorStop(0, '#4b5bf0');
    gradient.addColorStop(1, '#ea4776');
    var data = JSON.parse(res);
    if (data['status'] == false) {
        $('#user_prof_chart').hide();
        document.getElementById('profile-chart-error').innerHTML = "No work is done in this period";
    }
    else
    {
        document.getElementById('profile-chart-error').innerHTML = " ";
        var configs = {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: "time spent in hrs",
                    backgroundColor: gradient,
                    borderColor: window.chartColors.green,
                    data: data['res']
                }],
            },
            options: {
                tooltips: {
                    enabled: true,
                },
                title: {
                    text: 'User snapshot',
                },
                hover: {
                    mode: "nearest"
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                            beginAtZero: true,
                        },
                        ticks: {
                            display: true,
                            beginAtZero: true,
                            stacked: true
                        },
                        scaleLabel: {
                            display: true,
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: true,
                            drawBorder: true
                        },
                        ticks: {
                            display: true,
                            beginAtZero: true,
                            stacked: true
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Time in hours',
                        }
                    }]
                },
            }
        };
    }   
    if (user_profile_chart) user_profile_chart.destroy();
    user_profile_chart = new Chart(user_chart, configs);
}

function load_year_chart() {
    var year = document.getElementById('year-chart').value;
    if (year == "" || year == " " || year == null) {
        var cur_year = parseInt(new Date().toString().slice(10, 15));
        document.getElementById('year-chart').value = cur_year;
        year = cur_year;
        document.getElementById("year-chart").setAttribute("max", cur_year);
    }
    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/user/user_chart',
        data: { "date": year },
        success: function (res) {
            __draw_profile_chart(res);
        }
    });
}
$(document).ready(function () {

    $("#year-chart").change(function () {
        load_year_chart();
    });
    
    var emailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if(document.getElementById("profile-error"))
    document.getElementById("profile-error").innerHTML = " ";
    
    if(document.getElementById('edit-profile') != undefined)
    {
    var profile = document.getElementById('edit-profile');
    profile.onsubmit = function()
        {
            var user_name = document.getElementById("profile-name").value;
            var user_ph = document.getElementById("profile-ph").value;
            
            if(user_name == " " || user_name == "")
            {
                document.getElementById("profile-error").innerHTML ="Empty name";
                return false;
            }
            if(user_ph.length != 0)
            {
                if(user_ph.length != 10 )
                {
                    document.getElementById("profile-error").innerHTML ="Invalid phone number";
                    return false;
                }
            }
            document.getElementById("profile-error").innerHTML = " ";
                return true;
        }
    }
    if (document.getElementById('year-chart')) {
        load_year_chart();
    }

    if (document.getElementById('dark-mode-checkbox')) {
        $("input:checkbox").change(
            function () {
                var checkbox_status = document.getElementById('dark-mode-checkbox').checked;
                document.getElementById('hidden-status').value = checkbox_status;
                $("#dark-mode").submit();
            });
    }

    // Start upload preview image
var $uploadCrop,
tempFilename,
rawImg,
imageId;
function readFile(input) {
     if (input.files && input.files[0]) {
      var reader = new FileReader();
        reader.onload = function (e) {
            $('.upload-demo').addClass('ready');
            $('#cropImagePop').modal('show');
            rawImg = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
    else {
        swal("Sorry - you're browser doesn't support the FileReader API");
    }
}

$uploadCrop = $('#upload-demo').croppie({
    viewport: {
        width: 150,
        height: 200,
    },
    enforceBoundary: false,
    enableExif: true
});
$('#cropImagePop').on('shown.bs.modal', function(){
    // alert('Shown pop');
    $uploadCrop.croppie('bind', {
        url: rawImg
    }).then(function(){
        console.log('jQuery bind complete');
    });
});

$('.item-img').on('change', function () { imageId = $(this).data('id'); tempFilename = $(this).val();
$('#cancelCropBtn').data('id', imageId); readFile(this); });
$('#cropImageBtn').on('click', function (ev) {
    $uploadCrop.croppie('result', {
        type: 'base64',
        format: 'jpeg',
        size: {width: 200, height: 200}
    }).then(function (resp) {
        $('#item-img-output').attr('src', resp);
        $('#cropImagePop').modal('hide');
    });
});
// End upload preview image


    
});