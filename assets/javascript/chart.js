function __project_details(res)
{
    var ctx = document.getElementById('main-chart').getContext('2d'); 
    gradient = ctx.createLinearGradient(0, 0, 0, 600);
    gradient.addColorStop(0, '#7077ff');
    gradient.addColorStop(0.5, '#e485fb');
    gradient.addColorStop(1, '#e484fb');
        console.log(res)
    var project_names = [];
    var data = [];
    for(var i=0;i<res.length; i++)
    {
        if((res[i]["project_name"] != undefined) && (res[i]["project_name"] != null))
                {
        project_names[i] = res[i]['project_name'];
        data[i] = res[i]['t_minutes']/60;
        }
    }
    var project_data = {
            labels: project_names,
            datasets: [{
                label: 'Projects',
                borderColor: "rgb(255, 99, 132)",
                backgroundColor: gradient,
                data: data,
            }]
        };
    var config = {
        type: 'line',
        data: project_data,
        options: {
            responsive: true,
            title: {
                display: true
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
                            labelString: 'Total time in hrs',
                        }
                    }]
            }
        }
    };
    new Chart(ctx, config);
}
    window.onload = function() {
        $.ajax({
        type: 'POST',
        url: timeTrackerBaseURL + 'index.php/admin/get_project_list',
        data: { 'type': "get_user" },
        success: function(res) {
            var result = JSON.parse(res);
                usernames = result['result'];
            __project_details(usernames);
            }
        });
        
    };
