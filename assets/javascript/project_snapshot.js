function __draw_project_chart(res)
{
/*var project_chart = document.getElementById('project-chart');
var chartColors = window.chartColors;
var color = Chart.helpers.color;

var data = [];
var project = [];
for(var i=0; i<res.length; i++)
{
	if(res[i]['project_name'] != null)
	{
		data.push(res[i]['t_minutes']/60);
		project.push(res[i]['project_name']);
	}
}

    for(var ind=0; ind<data.length; ind++)
    {
        var task_time_dec = data[ind] - Math.floor(data[ind]);
        task_time_dec = task_time_dec.toString().slice(0,4);
        var total_time = Math.floor(data[ind]) + parseFloat(task_time_dec);
        data[ind] = total_time;
    }



var project_values = {
				datasets: [{
					data: data,
					backgroundColor: [
						color(chartColors.red).alpha(0.5).rgbString(),
						color(chartColors.black).alpha(0.5).rgbString(),
						color(chartColors.yellow).alpha(0.5).rgbString(),
						color(chartColors.green).alpha(0.5).rgbString(),
						color(chartColors.blue).alpha(0.5).rgbString(),
						color(chartColors.orange).alpha(0.5).rgbString(),
						color(chartColors.silver).alpha(0.5).rgbString(),
					],
					label: 'My dataset' // for legend
				}],
				labels: project
			};
		var config = {
			data: project_values,
			options: {
				responsive: true,
				legend: {
					position: 'right',
				},
				title: {
					display: true,
					text: 'Project chart w.r.t. hours'
				},
				scale: {
					reverse: false
				},
				animation: {
					animateRotate: false,
					animateScale: true
				}
			}
		};
		window.myPolarArea = Chart.new(project_chart, config);*/

}

      function drawProjectChart() {

      	var result;
      	$.ajax({
	    type: 'POST',
	    url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
	    data: { 'type': "get_user" },
	    success: function(res) {
	        result = JSON.parse(res);
	        result = result['result'];
	    	console.log(result);
	        }
	    });
        var data = google.visualization.arrayToDataTable([
          ['ID', 'X', 'Y', 'Temperature'],
          ['',   80,  397,      120],
          ['',   79,  136,      130],
          ['',   78,  184,      50],
          ['',   72,  278,      230],
          ['',   81,  200,      210],
          ['',   72,  170,      100],
          ['',   68,  477,      80]
        ]);

        var options = {
          colorAxis: {colors: ['yellow', 'red']}
        };

        var chart = new google.visualization.BubbleChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }

$(document).ready(function()
{
	if(document.getElementById('chart_div'))
	{
	google.charts.load("current", {packages:["corechart"]});
	google.setOnLoadCallback(drawProjectChart);
	}
	$(".project-remove").click(function()
	{
		$("#delete-proj").click(function()
        {
        var project_id = this.childNodes[0].value;
        window.location.reload();
		$.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/delete_project',
            data: { 'project_id': project_id },
            success: function(res) {
                var result = JSON.parse(res);
                window.location.reload();
            }
        });
	});

	})
});