var user_Chart;
function __draw_chart(res)
{


if(res['status'] == "false")
{
     if (user_Chart) user_Chart.destroy();
}
else
{
    $("#user_time_chart").show();
    var user_chart = document.getElementById('user_time_chart').getContext('2d');
    var color = Chart.helpers.color;
    gradient = user_chart.createLinearGradient(0, 0, 0, 300);


    gradient.addColorStop(0, '#4b5bf0');
    gradient.addColorStop(1, '#ea4776');
    var task_labels = [];
    var task_time_value = [];
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
        var total_time = Math.floor(task_time_value[ind]) + (parseFloat(task_time_dec));
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
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';
                        var value = "";
                        if((tooltipItem['value'].split('.')[1]/100*60).toString() != "NaN")
                        {
                        value = tooltipItem['value'].split('.')[1]/100*60;
                        }
                        if (label) {
                            label.split('.')[0] += ':'+value;
                        }
                        var minutes = parseInt(value);
                        if(parseInt(value).toString() == "NaN")
                        {
                            minutes = 0;
                        }
                        if(minutes.toString().length == 1)
                        {
                            minutes = '0'+minutes;
                        }
                        minutes = minutes.toString().slice(0,2);
                        return ("time spent "+tooltipItem['value'].split('.')[0]+':'+minutes+" hrs");
                    }
                }
            },
            title: {
                text: 'User snapshot',
            },
            legend: {
                display: false
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
                }]
            },
        }
    };
    if (user_Chart) user_Chart.destroy();
     user_Chart = new Chart(user_chart, configs);
        
    }
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
            type: "POST",
            "data": {"type": "user_task" , 'user_id': user_id },
             "dataSrc": function ( json ) {
                //Make your callback here.
                if(json["status"] ==  false)
                {
                document.getElementById('search-error').innerHTML = "No results found";
                    $('#user-task-datatable_processing').hide();
                }
                else{
                   document.getElementById('search-error').innerHTML = " "; 
                   
                }
                return json.data;
            }  
        },

        "order": [[ 0, "asc" ]],
        "columnDefs": [{
            "targets": 0,
            "render": function ( data, type, row, meta ) {
                return row[0];
            }
        },{
            "targets": 1,
            "render": function ( data, type, row, meta ) {
                return row[1];
            },
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                return row[2];
            }
        }]
    });

    $('#user-project-datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": timeTrackerBaseURL + 'index.php/admin/user_project_table',
            type: "POST",
            "data": {"type":"user_project", 'user_id': user_id},
             "dataSrc": function ( json ) {
                //call for datatable
                if(json["status"] ==  false)
                {
                document.getElementById('user-project-error').innerHTML = "No results found";
                $('#user-project-datatable_processing').hide();
                }
                else{
                   document.getElementById('user-project-error').innerHTML = " "; 
                   
                }
                return json.data;
            } 
        },
        "order": [[ 0, "asc" ]],
        "columnDefs": [{
            "targets": 0,
            "render": function ( data, type, row, meta ) {
                return row[0];
            }
        },{
            "targets": 1,
            "render": function ( data, type, row, meta ) {
                return row[1];
            },
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                return row[2];
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


