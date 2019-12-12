function __draw_user_chart(res)
{

var user_chart = document.getElementById('user-chart').getContext('2d');
var color = Chart.helpers.color;
var chart_values = {
    labels: ['task1', 'task2', 'task3', 'task4', 'task5', 'task6'],
    datasets: [{
        type: 'bar',
        label: 'Total tasks',
        backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
        borderColor: window.chartColors.red,
        data: [5, 10, 15, 12, 4, 5],
    }, {
        type: 'line',
        label: 'user 1',
        backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
        borderColor: window.chartColors.green,
        fill: false,
        data: [5, 1, 2, 4, 1, 2],
    }, {
        type: 'line',
        label: 'user 2',
        backgroundColor: "red",
        borderColor: window.chartColors.orange,
        fill: false,
        data: [4, 2, 5, 0, 3, 5],
    }, {
        type: 'line',
        label: 'user 3',
        backgroundColor: "red",
        borderColor: window.chartColors.blue,
        fill: false,
        data: [0, 1, 2, 5, 6, 2],
    }]
};
var configs = {
    type: 'bar',
    data: chart_values,
    options: {
        title: {
            text: 'User snapshot'
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
new Chart(user_chart, configs);
}
$(document).ready(function() {
    //var flag = 0;
    $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
        data: { 'type': "get_user" },
        success: function(res) {
            var result = JSON.parse(res);
            console.log(result);
            usernames = result['result'];
            console.log(usernames)

            for (var j = 0; j < usernames.length; j++) {

                var option = $('<option>' + usernames[j]["project_name"] + '</option>');
                $('.project-list').append(option);
                
                }
            }
    	});
        var p_name = document.getElementById('project-list').value;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'project_name': p_name },
            success: function(res) {
            	var result = JSON.parse(res);
            	__draw_user_chart(res);
            }    
        });
    $('#project-list').click(function() {
        var p_name = document.getElementById('project-list').value;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'project_name': p_name },
            success: function(res) {
                var result = JSON.parse(res);
                __draw_user_chart(res);
            }
        });
	});
});
