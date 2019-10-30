    $(document).ready(function() {
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

    var chart = document.getElementById('weekly').getContext('2d');
    
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
            },
            hover: {
                mode: 'index'
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: true
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: true,
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
            }
        }
    };
    window.onload = function() {
        window.myLine = new Chart(chart, config);
        
    };