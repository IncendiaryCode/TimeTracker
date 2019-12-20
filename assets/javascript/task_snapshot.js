var taskChart;

function __draw_task_chart(res) {
    var task_chart = document.getElementById('task-chart').getContext('2d');
    var color = Chart.helpers.color;
    var label = [];
    var time = [];
    if (res['status'] == false) {
        document.getElementById('task-chart-error').innerHTML = "This project does not have any data.";
        $('#task-chart').hide();
    } else {
        $('#task-chart').show();
        document.getElementById('task-chart-error').innerHTML = " ";

        var data = res['result'][0];

        for (var i = 0; i < data.length; i++) {
            label[i] = data[i]['task_name'];
            time[i] = data[i]['time_used'] / 60;
        }

        for (var ind = 0; ind < time.length; ind++) {
            var task_time_dec = time[ind] - Math.floor(time[ind]);
            task_time_dec = task_time_dec.toString().slice(0, 4);
            var total_time = Math.floor(time[ind]) + parseFloat(task_time_dec);
            time[ind] = total_time;
        }
        var configs = {
            type: 'bar',
            data: {
                labels: label,
                datasets: [{
                    type: 'bar',
                    label: 'Time used',
                    backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.red,
                    data: time,
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
        if (taskChart) taskChart.destroy();
        taskChart = new Chart(task_chart, configs);
    }
}

$(document).ready(function() {

    //rendering datatable

    $('#task-lists-datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": timeTrackerBaseURL + 'index.php/admin/load_snapshot',
            "data": {'type': "task"}
        },
        "order": [[ 3, "desc" ]],
        "columnDefs": [{
            "targets": 0,
            "render": function ( data, type, row, meta ) {
                return row.task_name;
            }
        },{
            "targets": 1,
            "render": function ( data, type, row, meta ) {
                return row.description;
            }
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                return row.project_name;
            }
        },{
            "targets": 3,
            "render": function ( data, type, row, meta ) {
                return row.start_time;
            }
        },{
            "targets": 4,
            "render": function ( data, type, row, meta ) {
                return row.end_time;
            }
        },{
            "targets": 5,
            "render": function ( data, type, row, meta ) {
                return row.total_minutes;
            }
        },{
            "targets": 6,
            "render": function ( data, type, row, meta ) {
                return "<a href='#' data-id='"+ row.task_id +"' class='text-danger delete-task' data-toggle='modal' data-target='#delete-task-modal'><i class='fas fa-trash-alt icon-plus del-tasks'></i></a>";
            },
            "orderable": false,
        }]
    }).on( 'init.dt', function () {
        $('.delete-task').click(function()
        {
        $('#delete-task').click(function()
            {
            var task_id = this.getAttribute("data-id");
            $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/delete_data',
            data: { 'task_id': task_id },
            success: function(res) {
                var result = JSON.parse(res);
                window.location.reload();
                    }
                });
            })
        })
    });


    if (document.getElementById('task-chart')) {
        if ((document.getElementById('curr-month').value == "") || (document.getElementById('curr-month').value == " ")) {
            var curr_month = new Date().getMonth() + 1;
            document.getElementById('curr-month').value = "2019-12";
        }
        document.getElementById('curr-month').value;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'month': document.getElementById('curr-month').value },
            success: function(res) {
                var result = JSON.parse(res);
                __draw_task_chart(result);
            }
        });
    }
    $('#view-chart').click(function() {
        if (document.getElementById('curr-month').value != '') {
            $.ajax({
                type: 'POST',
                url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
                data: { 'month': document.getElementById('curr-month').value },
                success: function(res) {
                    var result = JSON.parse(res);
                    __draw_task_chart(result);
                }
            });
        }
    })
});