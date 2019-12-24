var taskChart;

function __draw_task_chart(res) {
    var result = res['result'];
    var task_chart = document.getElementById('task-chart').getContext('2d');
    gradient = task_chart.createLinearGradient(0, 0, 0, 600);
    gradient.addColorStop(0, '#7077ff');
    gradient.addColorStop(0.5, '#e485fb');
    gradient.addColorStop(1, '#e484fb');
    var color = Chart.helpers.color;
    var label = [];
    var count = [];
    if (res['status'] == false) {
        document.getElementById('task-chart-error').innerHTML = "This project does not have any data.";
        $('#task-chart').hide();
    }
    else {
        $('#task-chart').show();
        document.getElementById('task-chart-error').innerHTML = " ";
        for (var i = 0; i < result.length; i++) {
            label[i] = result[i]['task_date'];
            count[i] = result[i]['tasks_count'] / 60;
        }
        for (var ind = 0; ind < count.length; ind++) {
            var task_time_dec = count[ind]/60 - Math.floor(count[ind]);
            task_time_dec = task_time_dec.toString().slice(0, 4);
            var total_time = Math.floor(count[ind]/60) + parseFloat(task_time_dec);
            count[ind] = total_time;
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
                    data: count,
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
            }, "orderable": false,
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                return row.project;
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
                var task_time_sec = row.total_minutes/60 - Math.floor(row.total_minutes/60);
                task_time_sec = task_time_sec.toString().slice(0, 4);
                var total_time = Math.floor(row.total_minutes/60) + parseFloat(task_time_sec)+' hrs';
                return total_time;
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
        var task_id = this.getAttribute("data-id");
        $('#delete-task').click(function()
            {
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
    });

    var search = document.getElementById("task-lists-datatable_filter").childNodes[0]['control'];
    var att = document.createAttribute("class");       
    att.value = "border";                           
    search.setAttributeNode(att);
}  
});