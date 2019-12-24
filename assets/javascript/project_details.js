
function __draw_project_chart(res) {
    var result = res['result'];
    var project_chart = document.getElementById('project_time_chart').getContext('2d');
    gradient = project_chart.createLinearGradient(0, 0, 0, 600);
    gradient.addColorStop(0, '#7077ff');
    gradient.addColorStop(0.5, '#e485fb');
    gradient.addColorStop(1, '#e484fb');
    var color = Chart.helpers.color;
    var label = [];
    var hours = [];
        for (var i = 0; i < result.length; i++) {
            label[i] = result[i]['task_date'];
            hours[i] = result[i]['tasks_hours'] / 60;
        }
        for (var ind = 0; ind < hours.length; ind++) {
            var task_time_dec = hours[ind] - Math.floor(hours[ind]);
            task_time_dec = task_time_dec.toString().slice(0, 4);
            var total_time = Math.floor(hours[ind]) + parseFloat(task_time_dec);
            hours[ind] = total_time;
        }
        var configs = {
            type: 'line',
            data: {
                labels: label,
                datasets: [{
                    type: 'line',
                    label: 'Time used',
                    backgroundColor: gradient,
                    borderColor: window.chartColors.black,
                    data: hours,
                }]
            },
            options: {
                title: {
                    text: 'Task snapshot'
                },
                hover: {
                    display: false
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
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false,
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
        if (projectChart) projectChart.destroy();
        projectChart = new Chart(project_chart, configs);
    
}


$(document).ready(function() {

    var project_id = document.getElementById("project_id").value;

    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/user_chart',
        data: { "type": "project_chart" , 'project_id': project_id },
        success: function(res) {
            console.log(res);
            __draw_project_chart(res);
        }    
    });

    $('#project-datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": timeTrackerBaseURL + 'index.php/admin/user_project_table',
            "data": { "type": "project_user" , 'project_id': project_id }
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
                return row.descripiton;
            },
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                return row.start_time;
            }
        },{
            "targets": 3,
            "render": function ( data, type, row, meta ) {
                return row.end_time;
            }
        },{
            "targets": 4,
            "render": function ( data, type, row, meta ) {
                return row.time_spent;
            }
        }]
    }).on( 'init.dt', function () {
    });
    $('#task-datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": timeTrackerBaseURL + 'index.php/admin/user_task_table',
            "data": { "type": "project_task" , 'project_id': project_id }
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
                return row.descripiton;
            },
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                return row.start_time;
            }
        },{
            "targets": 3,
            "render": function ( data, type, row, meta ) {
                return row.end_time;
            }
        },{
            "targets": 4,
            "render": function ( data, type, row, meta ) {
                return row.time_spent;
            }
        }]
    }).on( 'init.dt', function () {
    });

});
