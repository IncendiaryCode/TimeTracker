function __draw_project_chart(res)
{
var project_chart = document.getElementById('project-chart');
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
		window.myPolarArea = Chart.PolarArea(project_chart, config);
}

$(document).ready(function()
{
	if(document.getElementById('project-chart'))
	{
	$.ajax({
	    type: 'POST',
	    url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
	    data: { 'type': "get_user" },
	    success: function(res) {
	        var result = JSON.parse(res);
	            p_names = result['result'];
	    		__draw_project_chart(p_names);
	        }
	    });
	}
	$(".project-remove").click(function()
	{
		this.parentNode.parentNode.remove();
		/*$.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'project_name': p_name },
            success: function(res) {
                var result = JSON.parse(res);
                __draw_user_chart(result);
                window.location.reload();
            }
        });*/
	});
	})
