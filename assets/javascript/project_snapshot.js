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