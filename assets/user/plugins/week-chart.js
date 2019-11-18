    $(document).ready(function() {
        if(document.getElementById('weekly-chart'))
        {
        var weekControl = document.querySelector('input[type="week"]');

        var curr = new Date(); // get current date
        var week = document.getElementById('weekly-chart').value;

        if (week == "" || week == " " || week == null) {
            var weekNumber = curr.getWeek();
            weekControl.value = curr.getFullYear() + '-W' + weekNumber;

            week = curr.getFullYear() + '-W' + weekNumber;
            document.getElementById("weekly-chart").setAttribute("max", week);
            retrieveData(week);
        }
    }
    });

    function weeklyChart() {
        var week = document.getElementById('weekly-chart').value;
        document.getElementById("weekly-chart").setAttribute("max", week);
        retrieveData(week);
    }

    function retrieveData(date) {
        $.ajax({
            type: "GET",
            url: timeTrackerBaseURL + 'php/activity.php',
            data: { 'user': "<?= $_SESSION['user'] ?>", 'date': date },
            dataType: 'json',
            success: function(res) {

            }
        });
    }

    Date.prototype.getWeek = function() {
        var onejan = new Date(this.getFullYear(), 0, 1);
        return Math.ceil((((this - onejan) / 86400000) + onejan.getDay() + 1) / 7);
    };

    if(document.getElementById('weekly'))
    {

    var chart = document.getElementById('weekly').getContext('2d');
        window.onload = function() {
        window.myLine = new Chart(chart, config);

    };
    gradient = chart.createLinearGradient(0, 0, 0, 600);

    gradient.addColorStop(0, '#7078ff');
    gradient.addColorStop(0.5, '#e58dfb');
    gradient.addColorStop(1, '#ffffff');

    var config = {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Day',
                borderColor: "#7078ff",
                backgroundColor: gradient,
                hoverbackground: gradient,
                data: [7, 8, 7, 9, 10]
            }]
        },
        options: {
            responsive: true,
            title: {
                display: false
            },
            tooltips: {
                enabled: true,
                callbacks: {
                    label: function(tooltipItem, data) {
                        var item = tooltipItem.xLabel;
                        var week_count = document.getElementById('weekly-chart').value;
                        weekly.onclick = function() {
                            //console.log('week_count',(week_count.slice(0,4)+week_count.slice(-2)+item));
                            var value = week_count.slice(0, 4) + week_count.slice(-2) + item;
                            window.location.href = 'daily_details.php?value=' + value;
                        }
                    }
                }
            },
            hover: {
                mode: 'index'
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        display: true,
                        suggestedMin: 0,
                        suggestedMax: 15,
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
    }
}
   