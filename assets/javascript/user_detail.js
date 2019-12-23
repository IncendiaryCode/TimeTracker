var user_Chart;
function __draw_user_chart(res)
{
var user_chart = document.getElementById('user_time_chart').getContext('2d');
var color = Chart.helpers.color;
gradient = user_chart.createLinearGradient(0, 0, 0, 600);

    gradient.addColorStop(0, '#4b5bf0');
    gradient.addColorStop(1, '#ea4776');
if(res['status'] == false)
{
    document.getElementById('user-chart-error').innerHTML = "This project does not have any data.";
    $('#user_chart').hide();
}
else{
    $('#user_chart').show();
    document.getElementById('user-chart-error').innerHTML = " ";
    var user_data = [];

    var task_labels = [];
    var user_labels = [];

    var task_time_value = [];
    var user_time_value = [];

    var chart_color = "000000";

for(var i=0; i<res['result'].length; i++)
{
task_labels[i] = res['result'][i]['user_name'];
task_time_value[i] = res['result'][i]['time_used']/60;
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
        backgroundColor: gradient,
        borderColor:window.chartColors.green,
        fill: false,
        data: task_time_value
    }],
    },
    options: {
        tooltips: {
                enabled: true,
                callbacks: {
                    label: function (tooltipItem, data) {
                        var item = tooltipItem.xLabel;
                        var user = document.getElementById('user_time_chart').value;
                        $('#user_time_chart').unbind().click(function()
                        {
                            var elmnt = document.getElementById(item);
                            elmnt.scrollIntoView();
                        });
                    }
                }
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
                    display: true,//labelString: 'Users',
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
if (user_Chart) user_Chart.destroy();
 user_Chart = new Chart(user_chart, configs);
    }
}

$(document).ready(function() {

    user_id = document.getElementById('user-id').value;
    var curr = new Date();
    var cur_date = curr.getFullYear()+':'+parseInt(curr.getMonth()+1)+':'+curr.getDate();
    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/user_chart',
        data: { 'user_id': user_id , "date": cur_date  },
        success: function(res) {
            console.log(res)
            __draw_user_chart(result);
        }    
    });


});


