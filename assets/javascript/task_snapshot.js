var taskChart;
function __draw_task_chart(res)
{
var task_chart = document.getElementById('task-chart').getContext('2d');
var color = Chart.helpers.color;
var label = [];
var time = [];
if(res['status'] == false)
{
    document.getElementById('task-chart-error').innerHTML = "This project does not have any data.";
    $('#task-chart').hide();
}
else{
$('#task-chart').show();
document.getElementById('task-chart-error').innerHTML = " ";

var data = res['result'][0];

for(var i=0;i<data.length; i++)
{
label[i] = data[i]['task_name'];
time[i] = data[i]['time_used']/60;
}

for(var ind=0; ind<time.length; ind++)
    {
        var task_time_dec = time[ind] - Math.floor(time[ind]);
        task_time_dec = task_time_dec.toString().slice(0,4);
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
			},hover: {
                display: false
            },
			scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        beginAtZero: true,
                    },ticks: {
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
    if(taskChart) taskChart.destroy();
	taskChart = new Chart(task_chart, configs);
	}
}

$(document).ready(function()
{
    if(document.getElementById('task-chart'))
    {
	$.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
            data: { 'type': "get_user" },
            success: function(res) {
				var result = JSON.parse(res);
                usernames = result['result'];
		        for (var j = 0; j < usernames.length; j++) {
                    if(usernames[j]["project_name"] != null)
                    {
		            var option = $('<option>' + usernames[j]["project_name"] + '</option>');
		            $('.project-name-list').append(option);
		        	}
                }

                var project_name = document.getElementById('total-project').value;

                $.ajax({
                    type: 'POST',
                    url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
                    data: { 'project_name': project_name },
                    success: function(res) {
                        var result = JSON.parse(res);
                        __draw_task_chart(result);
                    }    
                });
	        }
        });
	}
    $('#total-project').change(function() {
        var project_name = document.getElementById('total-project').value;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/get_graph_data',
            data: { 'project_name': project_name },
            success: function(res) {
                var result = JSON.parse(res);
                __draw_task_chart(result);
            }
        });
	});



    $(".task-remove").click(function()
    {
        var task_id = this.childNodes[0].value;
        $.ajax({
            type: 'POST',
            url: timeTrackerBaseURL + 'index.php/admin/delete_data',
            data: { 'task_id': task_id },
            success: function(res) {
                var result = JSON.parse(res);
                __draw_user_chart(result);
                window.location.reload();
            }
        });
    });


$.ajax({
    type: 'POST',
    url: timeTrackerBaseURL + 'index.php/admin/load_snapshot',
    data: { 'type': "task" },
    success: function(res) {
        var result = JSON.parse(res);

    }
});




  var dataSet = [
    [ "Tiger Nixon", "System Architect", "Edinburgh", "5421", "2011/04/25", "$320,800" ],
    [ "Garrett Winters", "Accountant", "Tokyo", "8422", "2011/07/25", "$170,750" ],
    [ "Ashton Cox", "Junior Technical Author", "San Francisco", "1562", "2009/01/12", "$86,000" ],
    [ "Cedric Kelly", "Senior Javascript Developer", "Edinburgh", "6224", "2012/03/29", "$433,060" ],
    [ "Airi Satou", "Accountant", "Tokyo", "5407", "2008/11/28", "$162,700" ],
    [ "Brielle Williamson", "Integration Specialist", "New York", "4804", "2012/12/02", "$372,000" ],
    [ "Herrod Chandler", "Sales Assistant", "San Francisco", "9608", "2012/08/06", "$137,500" ],
    [ "Rhona Davidson", "Integration Specialist", "Tokyo", "6200", "2010/10/14", "$327,900" ],
    [ "Colleen Hurst", "Javascript Developer", "San Francisco", "2360", "2009/09/15", "$205,500" ],
    [ "Sonya Frost", "Software Engineer", "Edinburgh", "1667", "2008/12/13", "$103,600" ],
    [ "Jena Gaines", "Office Manager", "London", "3814", "2008/12/19", "$90,560" ],
    [ "Quinn Flynn", "Support Lead", "Edinburgh", "9497", "2013/03/03", "$342,000" ],
    [ "Charde Marshall", "Regional Director", "San Francisco", "6741", "2008/10/16", "$470,600" ],
    [ "Haley Kennedy", "Senior Marketing Designer", "London", "3597", "2012/12/18", "$313,500" ],
    [ "Tatyana Fitzpatrick", "Regional Director", "London", "1965", "2010/03/17", "$385,750" ],
    [ "Michael Silva", "Marketing Designer", "London", "1581", "2012/11/27", "$198,500" ],
    [ "Paul Byrd", "Chief Financial Officer (CFO)", "New York", "3059", "2010/06/09", "$725,000" ],
    [ "Gloria Little", "Systems Administrator", "New York", "1721", "2009/04/10", "$237,500" ],
    [ "Bradley Greer", "Software Engineer", "London", "2558", "2012/10/13", "$132,000" ],
    [ "Dai Rios", "Personnel Lead", "Edinburgh", "2290", "2012/09/26", "$217,500" ],
    [ "Jenette Caldwell", "Development Lead", "New York", "1937", "2011/09/03", "$345,000" ],
    [ "Yuri Berry", "Chief Marketing Officer (CMO)", "New York", "6154", "2009/06/25", "$675,000" ],
    [ "Caesar Vance", "Pre-Sales Support", "New York", "8330", "2011/12/12", "$106,450" ],
    [ "Doris Wilder", "Sales Assistant", "Sydney", "3023", "2010/09/20", "$85,600" ],
    [ "Angelica Ramos", "Chief Executive Officer (CEO)", "London", "5797", "2009/10/09", "$1,200,000" ],
    [ "Gavin Joyce", "Developer", "Edinburgh", "8822", "2010/12/22", "$92,575" ],
    [ "Jennifer Chang", "Regional Director", "Singapore", "9239", "2010/11/14", "$357,650" ],
    [ "Brenden Wagner", "Software Engineer", "San Francisco", "1314", "2011/06/07", "$206,850" ],
    [ "Fiona Green", "Chief Operating Officer (COO)", "San Francisco", "2947", "2010/03/11", "$850,000" ],
    [ "Shou Itou", "Regional Marketing", "Tokyo", "8899", "2011/08/14", "$163,000" ],
    [ "Michelle House", "Integration Specialist", "Sydney", "2769", "2011/06/02", "<i class='fas fa-trash-alt icon-plus task-remove text-danger'></i>" ],
    [ "Suki Burks", "Developer", "London", "6832", "2009/10/22", "<i class='fas fa-trash-alt icon-plus task-remove text-danger'></i>" ],
    [ "Prescott Bartlett", "Technical Author", "London", "3606", "2011/05/07", "<i class='fas fa-trash-alt icon-plus task-remove text-danger'></i>" ],
    [ "Gavin Cortez", "Team Leader", "San Francisco", "2860", "2008/10/26", "<i class='fas fa-trash-alt icon-plus task-remove text-danger'></i>" ],
    [ "Martena Mccray", "Post-Sales support", "Edinburgh", "8240", "2011/03/09", "<i class='fas fa-trash-alt icon-plus task-remove text-danger'></i>" ],
    [ "Unity Butler", "Marketing Designer", "San Francisco", "5384", "2009/12/09", "<i class='fas fa-trash-alt icon-plus task-remove text-danger'><input type='hidden' class='user_id' value=''></i>" ]
];

$(document).ready(function() {
    $('#example').DataTable( {
        data: dataSet,
        columns: [
            { title: "Task name" },
            { title: "Description" },
            { title: "Start date" },
            { title: "End date" },
            { title: "Start date" },
            { title: "Time spent" },
            { title: "Action" }
        ]
    } );
} );







})