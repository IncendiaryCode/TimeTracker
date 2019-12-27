var user_Chart;
function __draw_chart(res)
{
var user_chart = document.getElementById('user_time_chart').getContext('2d');
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
        label:"user chart",
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

$(document).ready(function() {
if(document.getElementById('user-id') != null)
{

    var curr = new Date();
    var user_id = document.getElementById('user-id').value;


    var cur_date = curr.getFullYear()+'-'+parseInt(curr.getMonth()+1)+'-'+curr.getDate();
    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/user_chart',
        data: { 'user_id': user_id , "date": cur_date , 'type': "user-chart" },
        success: function(res) {
            __draw_chart(res);
        }    
    });


    $('#user-task-datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": timeTrackerBaseURL + 'index.php/admin/user_task_table',
            "data": {"type": "user_task" , 'user_id': user_id }
        },
        "order": [[ 0, "asc" ]],
        "columnDefs": [{
            "targets": 0,
            "render": function ( data, type, row, meta ) {
                return row.task_name;
            }
        },{
            "targets": 1,
            "render": function ( data, type, row, meta ) {
                return row.project_name;
            },
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                var task_time_sec = row.t_minutes/60 - Math.floor(row.t_minutes/60);
                task_time_sec = task_time_sec.toString().slice(0, 4);
                var total_time = Math.floor(row.t_minutes/60) + parseFloat(task_time_sec)+' hrs';
                return total_time;
            }
        }]
    }).on( 'init.dt', function () {
    });

    $('#user-project-datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": timeTrackerBaseURL + 'index.php/admin/user_project_table',
            "data": {"type":"user_project", 'user_id': user_id}
        },
        "order": [[ 0, "asc" ]],
        "columnDefs": [{
            "targets": 0,
            "render": function ( data, type, row, meta ) {
                return row.project_name;
            }
        },{
            "targets": 1,
            "render": function ( data, type, row, meta ) {
                return row.tasks_count;
            },
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                var task_time_sec = row.t_minutes/60 - Math.floor(row.t_minutes/60);
                task_time_sec = task_time_sec.toString().slice(0, 4);
                var total_time = Math.floor(row.t_minutes/60) + parseFloat(task_time_sec)+' hrs';
                return total_time;
            }
        }]
    });

    var search1 = document.getElementById("user-task-datatable_filter").childNodes[0]['control'];
    var att1 = document.createAttribute("class");       
    att1.value = "border";                           
    search1.setAttributeNode(att1);

    var search2 = document.getElementById("user-project-datatable_filter").childNodes[0]['control'];
    var att2 = document.createAttribute("class");       
    att2.value = "border";                           
    search2.setAttributeNode(att2);
    }

});


