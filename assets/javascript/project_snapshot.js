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
	    var x=10;
	    var y=10;
	    var data = google.visualization.arrayToDataTable([
          ['ID', 'X', 'Y','radius','Time spent'],
          ['', 0, 0,0, 0],]);
	    for(var i=0; i<result.length; i++)
	    {
	    	if(result[i]['project_name'] != null)
	    	{
	    		x= result[i]["t_minutes"]/60;
	    		y= result[i]["t_minutes"]/60;
	    		r= result[i]["t_minutes"]/500;

	    		var value = {'c':[{'v':result[i]["project_name"]},{'v':x},{'v':y},{'v':r},{'v':result[i]["t_minutes"]/60}]};
		    }
		data['wg'][i+1] = (value);
		}
    var options = {
      colorAxis: {colors: ['yellow', 'red']}
    };

    var chart = new google.visualization.BubbleChart(document.getElementById('chart_div'));
    chart.draw(data, options);

        }
    });



  }

$(document).ready(function()
{
	if(document.getElementById('chart_div'))
	{
	google.charts.load("current", {packages:["corechart"]});
	google.setOnLoadCallback(drawProjectChart);
	}
});