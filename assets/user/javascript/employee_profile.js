var user_profile_chart;
function __draw_chart(res)
{
var user_chart = document.getElementById('user_prof_chart').getContext('2d');
var color = Chart.helpers.color;
gradient = user_chart.createLinearGradient(0, 0, 0, 600);

gradient.addColorStop(0, '#4b5bf0');
gradient.addColorStop(1, '#ea4776');

    var user_data = [];

    var task_labels = [];
    var user_labels = [];

    var task_time_value = [];
    var user_time_value = [];

    var chart_color = "000000";

var data = JSON.parse(res);
data  = data['data'];
for(var i=0; i<data.length; i++)
{
task_labels[i] = data[i]['task_date'];
task_time_value[i] = data[i]['t_minutes']/60;
}

for(var ind=0; ind<task_time_value.length; ind++)
{
    var task_time_dec = task_time_value[ind] - Math.floor(task_time_value[ind]);
    task_time_dec = task_time_dec.toString().slice(0,4);
    var total_time = Math.floor(task_time_value[ind]) + parseFloat(task_time_dec);
    task_time_value[ind] = total_time;
}
var configs = {
    type: 'bar',
    data: {
        labels: task_labels,
        datasets : [{
        label:"time spent in hrs",
        backgroundColor: gradient,
        borderColor:window.chartColors.green,
        fill: false,
        data: task_time_value
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
if (user_profile_chart) user_profile_chart.destroy();
 user_profile_chart = new Chart(user_chart, configs);
}

$(document).ready(function()
{	
    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/user/user_chart',
        success: function(res) {
        	console.log(res);
            __draw_chart(res);
        }    
    });


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