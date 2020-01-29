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
            count[i] = result[i]['tasks_count'];
        }
        var configs = {
            type: 'line',
            data: {
                labels: label,
                datasets: [{
                    type: 'line',
                    label: 'total tasks',
                    backgroundColor: gradient,
                    borderColor: window.chartColors.black,
                    data: count,
                }]
            },
            options: {
                title: {
                    text: 'task snapshot'
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
                            labelString: 'task count',
                        }
                    }]
                },
            }
        };
        if (taskChart) taskChart.destroy();
        taskChart = new Chart(task_chart, configs);
    }
}

function callTaskTableData()
{
        var table = $('#task-lists-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        "scrollX": true,
        "bDestroy": true,
        "ajax": {
            "url": timeTrackerBaseURL + 'index.php/admin/load_snapshot',
            type: "POST",
            data: {'type': "task",  'month': document.getElementById('curr-month').value},
            "dataSrc": function ( json ) {
                //Make your callback here.
                if(json["status"] ==  false)
                {
                document.getElementById('task-tabel-error').innerHTML = "No results found";
                }
                else{
                   document.getElementById('task-tabel-error').innerHTML = " "; 
                }
                return json.data;
            }
        },
        "order": [[ 0, "desc" ]],
        "columnDefs": [{
            "targets": 0,
            "render": function ( data, type, row, meta) {
                return row[0];
            }
        },{
            "targets": 1,
            "render": function ( data, type, row, meta ) {
                return row[1];
            }, "orderable": false,
        },{
            "targets": 2,
            "render": function ( data, type, row, meta ) {
                return '<a href="../admin/load_project_detail?project_id='+row[7]+'">'+row[2]+'</a>';
            }
        },{
            "targets": 3,
            "render": function ( data, type, row, meta ) {
                return row[8];
            }
        },{
            "targets": 4,
            "render": function ( data, type, row, meta ) {
                return row[3];
            }
        },{
            "targets": 5,
            "render": function ( data, type, row, meta ) {
                return row[4];
            }
        },{
            "targets": 6,
            "render": function ( data, type, row, meta ) {
                var task_time_sec = row[5]/60 - Math.floor(row[5]/60);
                task_time_sec = task_time_sec.toString().slice(0, 4);
                var total_time = Math.floor(row[5]/60) + parseFloat(task_time_sec)+' hrs';
                return total_time;
            }
        },{
            "targets": 7,
            "render": function ( data, type, row, meta ) {
                return "<a href='#' data-id='"+ row[6] +"' class='text-danger delete-task' data-toggle='modal' data-target='#delete-task-modal'><i class='fas fa-trash-alt icon-plus del-tasks'></i></a>";
            },
            "orderable": false,
        },]
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

}
$(document).ready(function() {

    //rendering datatable

        //initialize date picker
    var dashPrjDtPicker1 = $("#curr-month").datepicker({
        minViewMode: 1
    });


    if (document.getElementById('task-chart')) {
        if ((document.getElementById('curr-month').value == "") || (document.getElementById('curr-month').value == " ")) {
            var month_no = (new Date().getMonth() + 1).toString();
                if(month_no == 1)
                    {
                        month_no = '0'+month_no;
                    }
            var curr_month = new Date().getFullYear().toString() + '-' + month_no;
            document.getElementById('curr-month').value = curr_month;
        }
        document.getElementById('curr-month').value;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'month': document.getElementById('curr-month').value },
            success: function(res) {
                if(JSON.parse(res)['status'] == false)
                {
                    document.getElementById('task-chart-error').innerHTML = "No data available";
                    $('#task-chart').hide();
                }
                else
                {
                    var result = JSON.parse(res);
                    $('#task-chart').show();
                    __draw_task_chart(result);
                }
            }
        });
     callTaskTableData();
    $('#curr-month').change(function() {
        if (document.getElementById('curr-month').value != '') {
            $.ajax({
                type: 'POST',
                url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
                data: { 'month': document.getElementById('curr-month').value },
                success: function(res) {
                if(JSON.parse(res)['status'] == false)
                    {
                        document.getElementById('task-chart-error').innerHTML = "No data available";
                        $('#task-chart').hide();
                    }
                    else
                    {
                        var result = JSON.parse(res);
                        $('#task-chart').show();
                        __draw_task_chart(result);
                    }
                }
            });
        callTaskTableData();
        }
    });

    var search = document.getElementById("task-lists-datatable_filter").childNodes[0]['control'];
    var att = document.createAttribute("class");       
    att.value = "border";                           
    search.setAttributeNode(att);
}  
});